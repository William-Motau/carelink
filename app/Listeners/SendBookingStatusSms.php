<?php

namespace App\Listeners;

use App\Events\BookingStatusUpdated;
use App\Models\Booking;
use App\Services\SmsService;
use App\Support\BookingStatus;

class SendBookingStatusSms
{
    public function __construct(private SmsService $sms)
    {
    }

    public function handle(BookingStatusUpdated $event): void
    {
        if (! config('carelink.notifications.sms_enabled')) {
            return;
        }

        $booking = Booking::with(['patient', 'booker'])->find($event->bookingId);
        if (! $booking) {
            return;
        }

        $message = $this->compose($event, $booking);
        $notified = [];

        $patientPhone = $booking->patient?->phone;
        if ($patientPhone) {
            $this->sms->send($patientPhone, $message);
            $notified[] = $patientPhone;
        }

        $bookerPhone = $booking->booker?->phone;
        if ($bookerPhone && ! in_array($bookerPhone, $notified, true)) {
            $this->sms->send($bookerPhone, $message);
        }
    }

    private function compose(BookingStatusUpdated $event, Booking $booking): string
    {
        $status = BookingStatus::label($booking->status);

        $parts = ["Carelink booking {$booking->reference}: {$status}."];
        if ($event->message) {
            $parts[] = $event->message.'.';
        }
        if ($booking->scheduled_at) {
            $parts[] = 'Scheduled '.$booking->scheduled_at->format('d M Y H:i').'.';
        }

        return implode(' ', $parts);
    }
}