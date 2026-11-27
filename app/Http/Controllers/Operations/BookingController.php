<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Organisation;
use App\Support\BookingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $organisation = auth()->user()->organisation;
        
        $query = Booking::where('organisation_id', $organisation->id)
            ->with(['patient', 'vehicle', 'driver.user', 'booker'])
            ->latest('created_at');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('journey_type')) {
            $query->where('journey_type', $request->journey_type);
        }
        
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('patient', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->paginate(25)->withQueryString();

        // Stats for filter badges
        $stats = [
            'all' => Booking::where('organisation_id', $organisation->id)->count(),
            'pending' => Booking::where('organisation_id', $organisation->id)->where('status', 'pending')->count(),
            'awaiting_partner_acceptance' => Booking::where('organisation_id', $organisation->id)->where('status', 'awaiting_partner_acceptance')->count(),
            'accepted' => Booking::where('organisation_id', $organisation->id)->where('status', 'accepted')->count(),
            'dispatched' => Booking::where('organisation_id', $organisation->id)->whereIn('status', \App\Support\BookingStatus::IN_TRANSIT_STATUSES)->count(),
            'completed' => Booking::where('organisation_id', $organisation->id)->where('status', 'completed')->count(),
            'cancelled' => Booking::where('organisation_id', $organisation->id)->where('status', 'cancelled')->count(),
        ];

        return view('operations.bookings.index', compact('bookings', 'stats'));
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        
        $booking->load([
            'patient', 
            'patient.contacts', 
            'vehicle', 
            'driver.user', 
            'journey',
            'statusHistory.actor',
            'booker',
            'organisation'
        ]);

        return view('operations.bookings.show', compact('booking'));
    }

    public function accept(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        
        if ($booking->status !== 'pending' && $booking->status !== 'awaiting_partner_acceptance') {
            return back()->with('error', 'Booking cannot be accepted in its current status.');
        }

        if (! BookingStatus::canTransition($booking->status, BookingStatus::ACCEPTED)) {
            return back()->with('error', 'Booking cannot transition to accepted from its current status.');
        }

        $booking->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $booking->statusHistory()->create([
            'previous_status' => $booking->getOriginal('status'),
            'new_status' => 'accepted',
            'actor_id' => auth()->id(),
            'organisation_id' => $booking->organisation_id,
            'reason' => 'Booking accepted by operations',
        ]);

        event(new \App\Events\BookingStatusUpdated($booking, 'Booking accepted'));

        return back()->with('success', 'Booking accepted successfully.');
    }

    public function reject(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (!in_array($booking->status, ['pending', 'awaiting_partner_acceptance', 'accepted'])) {
            return back()->with('error', 'Booking cannot be rejected in its current status.');
        }

        if (! BookingStatus::canTransition($booking->status, BookingStatus::REJECTED)) {
            return back()->with('error', 'Booking cannot transition to rejected from its current status.');
        }

        $booking->update([
            'status' => 'rejected',
        ]);

        $booking->statusHistory()->create([
            'previous_status' => $booking->getOriginal('status'),
            'new_status' => 'rejected',
            'actor_id' => auth()->id(),
            'organisation_id' => $booking->organisation_id,
            'reason' => $request->reason,
        ]);

        // Notify patient
        // $booking->patient->contacts->each(function ($contact) use ($booking) {
        //     $contact->notify(new BookingRejectedNotification($booking));
        // });

        return back()->with('success', 'Booking rejected.');
    }

    public function assignVehicle(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        
        $request->validate([
            'vehicle_id' => ['required', Rule::exists('vehicles', 'id')->where('organisation_id', $booking->organisation_id)],
        ]);

        return DB::transaction(function () use ($request, $booking) {
            $vehicle = Vehicle::where('organisation_id', $booking->organisation_id)
                ->lockForUpdate()
                ->findOrFail($request->vehicle_id);

            if ($vehicle->status !== 'available') {
                return back()->with('error', 'Vehicle is not available for assignment.');
            }

            $booking->update([
                'vehicle_id' => $vehicle->id,
                'status' => $booking->status === 'accepted' ? 'vehicle_assigned' : $booking->status,
            ]);

            $vehicle->update(['status' => 'assigned']);

            $booking->statusHistory()->create([
                'previous_status' => $booking->getOriginal('status'),
                'new_status' => $booking->status,
                'actor_id' => auth()->id(),
                'organisation_id' => $booking->organisation_id,
                'reason' => "Vehicle {$vehicle->registration_number} assigned",
                'metadata' => ['vehicle_id' => $vehicle->id],
            ]);

            event(new \App\Events\BookingStatusUpdated($booking, "Vehicle {$vehicle->registration_number} assigned"));

            return back()->with('success', 'Vehicle assigned successfully.');
        });
    }

    public function assignDriver(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        
        $request->validate([
            'driver_id' => ['required', Rule::exists('drivers', 'id')->where('organisation_id', $booking->organisation_id)],
        ]);

        return DB::transaction(function () use ($request, $booking) {
            $driver = Driver::where('organisation_id', $booking->organisation_id)
                ->lockForUpdate()
                ->findOrFail($request->driver_id);

            if ($driver->status !== 'active') {
                return back()->with('error', 'Driver is not available for assignment.');
            }

            // Check driver qualifications for requirements
            $qualificationError = $this->validateDriverQualifications($driver, $booking);
            if ($qualificationError) {
                return back()->with('error', $qualificationError);
            }

            $previousStatus = $booking->status;

            $booking->update([
                'driver_id' => $driver->id,
                'status' => 'driver_assigned',
            ]);

            $driver->update(['status' => 'on_duty']);

            $booking->statusHistory()->create([
                'previous_status' => $previousStatus,
                'new_status' => 'driver_assigned',
                'actor_id' => auth()->id(),
                'organisation_id' => $booking->organisation_id,
                'reason' => "Driver {$driver->user->first_name} {$driver->user->last_name} assigned",
                'metadata' => ['driver_id' => $driver->id],
            ]);

            event(new \App\Events\BookingStatusUpdated($booking, 'Driver assigned'));

            // Create journey record
            $booking->journey()->create([
                'vehicle_id' => $booking->vehicle_id,
                'driver_id' => $driver->id,
                'started_at' => now(),
            ]);

            return back()->with('success', 'Driver assigned successfully.');
        });
    }

    public function dispatch(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        
        if (!in_array($booking->status, ['accepted', 'awaiting_vehicle_assignment', 'vehicle_assigned', 'driver_assigned', 'confirmed'])) {
            return back()->with('error', 'Booking cannot be dispatched in its current status.');
        }

        if (!$booking->vehicle_id || !$booking->driver_id) {
            return back()->with('error', 'Both vehicle and driver must be assigned before dispatch.');
        }

        $booking->update([
            'status' => 'dispatched',
            'dispatched_at' => now(),
        ]);

        $booking->statusHistory()->create([
            'previous_status' => $booking->getOriginal('status'),
            'new_status' => 'dispatched',
            'actor_id' => auth()->id(),
            'organisation_id' => $booking->organisation_id,
            'reason' => 'Booking dispatched by operations',
        ]);

        event(new \App\Events\BookingStatusUpdated($booking, 'Booking dispatched'));

        return back()->with('success', 'Booking dispatched.');
    }

    public function cancel(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (!in_array($booking->status, ['pending', 'awaiting_partner_acceptance', 'accepted', 'awaiting_vehicle_assignment', 'vehicle_assigned', 'driver_assigned', 'confirmed'])) {
            return back()->with('error', 'Booking cannot be cancelled at its current status.');
        }

        if (! BookingStatus::canTransition($booking->status, BookingStatus::CANCELLED)) {
            return back()->with('error', 'Booking cannot transition to cancelled from its current status.');
        }

        $previousStatus = $booking->status;
        
        $booking->update([
            'status' => 'cancelled',
            'cancelled_by' => auth()->id(),
            'cancellation_reason' => $request->reason,
        ]);

        // Release vehicle and driver if assigned
        if ($booking->vehicle_id) {
            Vehicle::where('id', $booking->vehicle_id)->update(['status' => 'available']);
        }
        if ($booking->driver_id) {
            Driver::where('id', $booking->driver_id)->update(['status' => 'active']);
        }

        $booking->statusHistory()->create([
            'previous_status' => $previousStatus,
            'new_status' => 'cancelled',
            'actor_id' => auth()->id(),
            'organisation_id' => $booking->organisation_id,
            'reason' => $request->reason,
        ]);

        event(new \App\Events\BookingStatusUpdated($booking, 'Booking cancelled'));

        return back()->with('success', 'Booking cancelled.');
    }

    public function runSheet(Booking $booking)
    {
        $this->authorize('view', $booking);
        
        $booking->load(['patient', 'vehicle', 'driver.user', 'journey']);
        
        return view('operations.bookings.run-sheet', compact('booking'));
    }

    protected function validateDriverQualifications(Driver $driver, Booking $booking): ?string
    {
        $requirements = $booking->mobility_requirements ?? [];

        if (in_array('stretcher', $requirements, true) && !$driver->has_stretcher_qualification) {
            return 'Driver does not have stretcher qualification.';
        }

        if (in_array('wheelchair', $requirements, true) && !$driver->has_wheelchair_qualification) {
            return 'Driver does not have wheelchair qualification.';
        }

        if (in_array('oxygen', $requirements, true) && !$driver->has_oxygen_qualification) {
            return 'Driver does not have oxygen administration qualification.';
        }

        return null;
    }
}