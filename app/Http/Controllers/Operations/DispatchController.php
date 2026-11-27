<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\DispatchAssignment;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Support\BookingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DispatchController extends Controller
{
    public function index(Request $request)
    {
        $organisation = auth()->user()->organisation;

        $baseQuery = Booking::where('organisation_id', $organisation->id)
            ->with(['patient', 'vehicle', 'driver.user']);

        $awaitingAssignment = (clone $baseQuery)
            ->whereIn('status', [BookingStatus::ACCEPTED, BookingStatus::AWAITING_VEHICLE_ASSIGNMENT])
            ->orderBy('scheduled_at')
            ->get();

        $readyToDispatch = (clone $baseQuery)
            ->whereIn('status', [BookingStatus::VEHICLE_ASSIGNED, BookingStatus::DRIVER_ASSIGNED, BookingStatus::CONFIRMED])
            ->whereNotNull('vehicle_id')
            ->whereNotNull('driver_id')
            ->orderBy('scheduled_at')
            ->get();

        $inTransit = (clone $baseQuery)
            ->whereIn('status', BookingStatus::IN_TRANSIT_STATUSES)
            ->orderBy('scheduled_at')
            ->get();

        $availableVehicles = Vehicle::where('organisation_id', $organisation->id)
            ->where('status', 'available')
            ->orderBy('registration')
            ->get();

        $activeDrivers = Driver::where('organisation_id', $organisation->id)
            ->where('status', 'active')
            ->with('user')
            ->orderBy('employee_number')
            ->get();

        $selectedBooking = null;
        if ($request->filled('booking')) {
            $selectedBooking = (clone $baseQuery)->find($request->booking);
        }

        return view('operations.dispatch.index', compact(
            'awaitingAssignment',
            'readyToDispatch',
            'inTransit',
            'availableVehicles',
            'activeDrivers',
            'selectedBooking'
        ));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        $organisation = auth()->user()->organisation;

        if ($booking->organisation_id !== $organisation->id) {
            return back()->with('error', 'Booking does not belong to your organisation.');
        }

        return DB::transaction(function () use ($request, $booking, $organisation) {
            $previousStatus = $booking->status;
            $changes = [];

            if ($request->filled('vehicle_id')) {
                $vehicle = Vehicle::where('organisation_id', $organisation->id)->lockForUpdate()->findOrFail($request->vehicle_id);
                if ($vehicle->status !== 'available') {
                    return back()->with('error', 'Selected vehicle is not available.');
                }
                $booking->vehicle_id = $vehicle->id;
                $vehicle->update(['status' => 'assigned']);
                $changes[] = "vehicle {$vehicle->registration}";
            }

            if ($request->filled('driver_id')) {
                $driver = Driver::where('organisation_id', $organisation->id)->lockForUpdate()->findOrFail($request->driver_id);
                if ($driver->status !== 'active') {
                    return back()->with('error', 'Selected driver is not active.');
                }
                $booking->driver_id = $driver->id;
                $driver->update(['status' => 'on_duty']);
                $changes[] = "driver {$driver->user->first_name} {$driver->user->last_name}";
            }

            if (empty($changes)) {
                return back()->with('error', 'No vehicle or driver selected.');
            }

            // Advance status based on what was assigned
            if ($booking->vehicle_id && $booking->driver_id) {
                $booking->status = BookingStatus::DRIVER_ASSIGNED;
            } elseif ($booking->vehicle_id) {
                $booking->status = BookingStatus::VEHICLE_ASSIGNED;
            }

            $booking->save();

            $booking->statusHistory()->create([
                'previous_status' => $previousStatus,
                'new_status' => $booking->status,
                'actor_id' => auth()->id(),
                'organisation_id' => $booking->organisation_id,
                'reason' => 'Dispatch assignment: ' . implode(', ', $changes),
                'metadata' => [
                    'vehicle_id' => $booking->vehicle_id,
                    'driver_id' => $booking->driver_id,
                ],
            ]);

            // Record a dispatch assignment only once both vehicle and driver are set
            if ($booking->vehicle_id && $booking->driver_id) {
                DispatchAssignment::updateOrCreate(
                    ['booking_id' => $booking->id],
                    [
                        'vehicle_id' => $booking->vehicle_id,
                        'driver_id' => $booking->driver_id,
                        'assigned_by' => auth()->id(),
                        'assigned_at' => now(),
                        'status' => 'assigned',
                    ]
                );
            }

            event(new \App\Events\BookingStatusUpdated($booking, 'Dispatch assignment updated'));

            return back()->with('success', 'Assignment saved: ' . implode(', ', $changes));
        });
    }

    public function release(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        $organisation = auth()->user()->organisation;

        if ($booking->organisation_id !== $organisation->id) {
            return back()->with('error', 'Booking does not belong to your organisation.');
        }

        return DB::transaction(function () use ($booking) {
            $previousStatus = $booking->status;

            if ($booking->vehicle_id) {
                Vehicle::where('id', $booking->vehicle_id)->update(['status' => 'available']);
            }
            if ($booking->driver_id) {
                Driver::where('id', $booking->driver_id)->update(['status' => 'active']);
            }

            $booking->update([
                'vehicle_id' => null,
                'driver_id' => null,
                'status' => BookingStatus::AWAITING_VEHICLE_ASSIGNMENT,
            ]);

            $booking->statusHistory()->create([
                'previous_status' => $previousStatus,
                'new_status' => $booking->status,
                'actor_id' => auth()->id(),
                'organisation_id' => $booking->organisation_id,
                'reason' => 'Vehicle and driver released back to pool',
            ]);

            event(new \App\Events\BookingStatusUpdated($booking, 'Assignment released'));

            return back()->with('success', 'Assignment released. Vehicle and driver returned to pool.');
        });
    }
}