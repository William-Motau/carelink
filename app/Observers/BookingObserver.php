<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\CommissionService;
use App\Support\BookingStatus;

class BookingObserver
{
    /**
     * Generate the platform commission automatically whenever a booking is
     * marked completed (drop-off).
     */
    public function updated(Booking $booking): void
    {
        if ($booking->wasChanged('status') && $booking->status === BookingStatus::COMPLETED) {
            app(CommissionService::class)->generateForBooking($booking->fresh());
        }
    }
}