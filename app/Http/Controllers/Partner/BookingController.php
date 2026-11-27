<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Support\BookingStatus;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('partner_organisation_id', auth()->user()->organisation_id)
            ->with('patient')
            ->latest()
            ->paginate(20);

        return view('partner.bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::where('partner_organisation_id', auth()->user()->organisation_id)
            ->with('patient')
            ->findOrFail($id);

        return view('partner.bookings.show', compact('booking'));
    }

    public function accept(Request $request, $id)
    {
        $booking = Booking::where('partner_organisation_id', auth()->user()->organisation_id)->findOrFail($id);

        if (! in_array($booking->status, ['pending', 'awaiting_partner_acceptance'], true)) {
            return back()->with('error', 'Booking cannot be accepted in its current status.');
        }

        $booking->update(['status' => BookingStatus::ACCEPTED, 'accepted_at' => now()]);
        $booking->statusHistory()->create([
            'previous_status' => 'pending',
            'new_status' => BookingStatus::ACCEPTED,
            'actor_id' => auth()->id(),
            'organisation_id' => $booking->organisation_id,
            'reason' => 'Accepted by partner',
        ]);

        event(new \App\Events\BookingStatusUpdated($booking, 'Booking accepted'));

        return back()->with('success', 'Booking accepted.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $booking = Booking::where('partner_organisation_id', auth()->user()->organisation_id)->findOrFail($id);

        if (! in_array($booking->status, ['pending', 'awaiting_partner_acceptance', 'accepted'], true)) {
            return back()->with('error', 'Booking cannot be rejected in its current status.');
        }

        $previous = $booking->status;
        $booking->update(['status' => BookingStatus::REJECTED]);
        $booking->statusHistory()->create([
            'previous_status' => $previous,
            'new_status' => BookingStatus::REJECTED,
            'actor_id' => auth()->id(),
            'organisation_id' => $booking->organisation_id,
            'reason' => $request->reason,
        ]);

        event(new \App\Events\BookingStatusUpdated($booking, 'Booking rejected'));

        return back()->with('success', 'Booking rejected.');
    }
}