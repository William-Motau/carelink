<?php

namespace App\Listeners;

use App\Events\BookingStatusUpdated;
use App\Models\Booking;
use App\Notifications\BookingStatusNotification;
use Illuminate\Support\Facades\Notification;

class SendBookingStatusNotification
{
    public function handle(BookingStatusUpdated $event): void
    {
        if (! config('carelink.notifications.email_enabled')) {
            return;
        }

        $booking = Booking::with(['patient', 'booker'])->find($event->bookingId);
        if (! $booking) {
            return;
        }

        $notified = [];

        $patientEmail = $booking->patient?->email;
        if ($patientEmail) {
            Notification::route('mail', $patientEmail)
                ->notify(new BookingStatusNotification($booking, $event->message));
            $notified[] = $patientEmail;
        }

        $booker = $booking->booker;
        if ($booker?->email && !in_array($booker->email, $notified, true)) {
            $booker->notify(new BookingStatusNotification($booking, $event->message));
        }
    }
}