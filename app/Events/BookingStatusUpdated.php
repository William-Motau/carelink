<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class BookingStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public int $bookingId;
    public string $status;
    public ?string $message;
    public string $reference;

    public function __construct(Booking $booking, ?string $message = null)
    {
        // Only scalar identifiers are kept on the event; the full model (with
        // patient PII) is never serialised into the queue/broadcast payload.
        $this->bookingId = $booking->id;
        $this->status = $booking->status;
        $this->message = $message;
        $this->reference = $booking->reference;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('booking.' . $this->reference),
            new PrivateChannel('operations.bookings'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'BookingStatusUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'status' => $this->status,
            'message' => $this->message,
            'reference' => $this->reference,
        ];
    }
}