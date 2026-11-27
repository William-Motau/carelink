<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Organisation;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\OrganisationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OperationsBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_operations_user_can_accept_booking(): void
    {
        $this->seed(OrganisationSeeder::class);
        $org = Organisation::where('slug', 'regia-mobility')->first();

        Role::firstOrCreate(['name' => 'org_admin', 'guard_name' => 'web']);
        $user = User::factory()->create([
            'organisation_id' => $org->id,
            'role' => 'org_admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $user->assignRole('org_admin');

        $patient = Patient::create(['organisation_id' => $org->id, 'first_name' => 'A', 'last_name' => 'B']);
        $booker = User::factory()->create(['role' => 'patient', 'status' => 'active']);

        $booking = Booking::create([
            'uuid' => (string) Str::uuid(),
            'organisation_id' => $org->id,
            'patient_id' => $patient->id,
            'booker_id' => $booker->id,
            'journey_type' => 'one_way',
            'status' => 'pending',
            'pickup_address' => '1 St', 'pickup_contact_name' => 'A', 'pickup_contact_phone' => '0821234567',
            'destination_address' => '2 St', 'destination_contact_name' => 'B', 'destination_contact_phone' => '0821234567',
            'requested_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('operations.bookings.accept', $booking))
            ->assertRedirect();

        $this->assertSame('accepted', $booking->fresh()->status);
    }
}