<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Support\BookingStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookingStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Booking $booking,
        public ?string $eventMessage = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $booking = $this->booking;
        $patient = $booking->patient;
        $statusLabel = BookingStatus::label($booking->status);
        $greetingName = $patient->first_name ?? 'there';

        $mail = (new MailMessage)
            ->subject("Carelink Booking {$booking->reference} — {$statusLabel}")
            ->greeting("Hi {$greetingName},")
            ->line("Your transport booking has been updated.")
            ->line("**Reference:** {$booking->reference}")
            ->line("**Status:** {$statusLabel}");

        if ($this->eventMessage) {
            $mail->line("**Update:** {$this->eventMessage}");
        }

        if ($booking->scheduled_at) {
            $mail->line("**Scheduled:** " . $booking->scheduled_at->format('d M Y, H:i'));
        }

        if ($booking->pickup_address) {
            $mail->line("**Pickup:** {$booking->pickup_address}");
        }

        if ($booking->destination_address) {
            $mail->line("**Destination:** {$booking->destination_address}");
        }

        $viewUrl = $booking->uuid ? route('booking.status', ['uuid' => $booking->uuid]) : url('/');

        return $mail
            ->action('View Booking', $viewUrl)
            ->line('Thank you for choosing Carelink.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'reference' => $this->booking->reference,
            'status' => $this->booking->status,
            'message' => $this->eventMessage,
        ];
    }
}