<?php

namespace Tests\Feature;

use App\Models\Booking;
use Database\Seeders\OrganisationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingSubmitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(OrganisationSeeder::class);
    }

    /**
     * A complete, valid wizard payload ready for final submission.
     */
    private function wizardData(string $scheduledAt = null): array
    {
        return [
            'contact' => [
                'role' => 'self',
                'first_name' => 'Thandi',
                'last_name' => 'Mokoena',
                'phone' => '0821234567',
                'email' => 'thandi@example.test',
                'relationship' => 'self',
            ],
            'patient' => [
                'first_name' => 'Thandi',
                'last_name' => 'Mokoena',
            ],
            'journey' => [
                'journey_type' => 'one_way',
                'pickup_address' => '12 Main Road, Johannesburg',
                'pickup_contact_name' => 'Thandi Mokoena',
                'pickup_contact_phone' => '0821234567',
                'destination_address' => 'Charlotte Maxeke Hospital',
                'destination_contact_name' => 'Thandi Mokoena',
                'destination_contact_phone' => '0821234567',
                'scheduled_at' => $scheduledAt ?? now()->addDay()->setTime(9, 0)->toDateTimeString(),
            ],
            'requirements' => [
                'mobility' => ['wheelchair'],
            ],
            'review' => [
                'consents' => [
                    'privacy' => true,
                    'personal_info' => true,
                    'terms' => true,
                ],
            ],
            'fare_estimate' => [
                'subtotal' => 100,
                'vat_amount' => 15,
                'total_amount' => 115,
            ],
        ];
    }

    public function test_submit_creates_booking_and_redirects_to_confirmation(): void
    {
        $response = $this->withSession(['booking_wizard' => $this->wizardData()])
            ->post(route('booking.submit'), ['idempotency_key' => 'key-001']);

        $booking = Booking::first();

        $this->assertNotNull($booking);
        $this->assertSame(1, Booking::count());
        $this->assertSame('pending', $booking->status);
        $response->assertRedirect(route('booking.confirmed', ['uuid' => $booking->uuid]));

        // The idempotency key is persisted on the booking metadata.
        $this->assertSame('key-001', $booking->metadata['idempotency_key'] ?? null);
    }

    public function test_duplicate_idempotency_key_returns_same_booking(): void
    {
        $first = $this->withSession(['booking_wizard' => $this->wizardData()])
            ->post(route('booking.submit'), ['idempotency_key' => 'key-dup']);

        $original = Booking::first();
        $this->assertNotNull($original);

        // Simulate a double-submit: the wizard session is still present and the
        // same idempotency key is replayed.
        $second = $this->withSession(['booking_wizard' => $this->wizardData()])
            ->post(route('booking.submit'), ['idempotency_key' => 'key-dup']);

        $this->assertSame(1, Booking::count());
        $second->assertRedirect(route('booking.confirmed', ['uuid' => $original->uuid]));
        $first->assertRedirect(route('booking.confirmed', ['uuid' => $original->uuid]));
    }

    public function test_past_scheduled_at_is_rejected_at_submit(): void
    {
        $response = $this->withSession([
            'booking_wizard' => $this->wizardData(now()->subHour()->toDateTimeString()),
        ])->post(route('booking.submit'), ['idempotency_key' => 'key-past']);

        $this->assertSame(0, Booking::count());
        $response->assertRedirect(route('booking.step.show', ['step' => 3]));
        $response->assertSessionHas('error');
    }
}