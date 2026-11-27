<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Organisation;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\OrganisationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SecurityRemediationTest extends TestCase
{
    use RefreshDatabase;

    private function organisation(): Organisation
    {
        $this->seed(OrganisationSeeder::class);

        return Organisation::where('slug', 'regia-mobility')->first();
    }

    public function test_unauthenticated_status_hides_patient_pii(): void
    {
        $org = $this->organisation();

        $patient = Patient::create([
            'organisation_id' => $org->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'phone' => '0821234567',
            'email' => 'jane.secret@example.test',
        ]);
        $booker = User::factory()->create(['role' => 'patient', 'status' => 'active']);

        $booking = Booking::create([
            'uuid' => (string) Str::uuid(),
            'organisation_id' => $org->id,
            'patient_id' => $patient->id,
            'booker_id' => $booker->id,
            'journey_type' => 'one_way',
            'status' => 'pending',
            'mobility_requirements' => ['wheelchair'],
            'pickup_address' => '1 St', 'pickup_contact_name' => 'A', 'pickup_contact_phone' => '0821234567',
            'destination_address' => '2 St', 'destination_contact_name' => 'B', 'destination_contact_phone' => '0821234567',
            'requested_at' => now(),
        ]);

        $this->get(route('booking.status', ['uuid' => $booking->uuid]))
            ->assertOk()
            ->assertDontSee('jane.secret@example.test')
            ->assertDontSee('0821234567');
    }

    public function test_patient_record_not_reused_without_ownership_proof(): void
    {
        $org = $this->organisation();

        Patient::create([
            'organisation_id' => $org->id,
            'first_name' => 'Old',
            'last_name' => 'Patient',
            'id_number' => '1234567890123',
            'date_of_birth' => '1990-01-01',
            'phone' => '0821111111',
            'email' => 'old@example.test',
        ]);

        $wizard = [
            'contact' => ['role' => 'self', 'first_name' => 'New', 'last_name' => 'Person', 'phone' => '0829999999', 'email' => 'new@example.test', 'relationship' => 'self'],
            'patient' => [
                'first_name' => 'New',
                'last_name' => 'Person',
                'id_number' => '1234567890123',
                'date_of_birth' => '2000-05-05',
            ],
            'journey' => [
                'journey_type' => 'one_way',
                'pickup_address' => '1 St', 'pickup_contact_name' => 'A', 'pickup_contact_phone' => '0821234567',
                'destination_address' => '2 St', 'destination_contact_name' => 'B', 'destination_contact_phone' => '0821234567',
                'scheduled_at' => now()->addDay()->toDateTimeString(),
            ],
            'requirements' => ['mobility' => ['wheelchair']],
            'review' => ['consents' => ['privacy' => true, 'personal_info' => true, 'terms' => true]],
        ];

        $this->withSession(['booking_wizard' => $wizard])
            ->post(route('booking.submit'), ['idempotency_key' => 'takeover-attempt'])
            ->assertRedirect(route('booking.confirmed', ['uuid' => Booking::first()->uuid]));

        // A brand-new patient record must have been created (no takeover).
        $this->assertSame(2, Patient::count());
    }

    public function test_guest_does_not_see_staff_portal_link(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Staff Portal');
    }

    public function test_authenticated_user_sees_staff_portal_link(): void
    {
        $user = User::factory()->create(['role' => 'patient', 'status' => 'active']);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Staff Portal');
    }
}