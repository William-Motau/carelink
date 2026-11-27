<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Commission;
use App\Models\Organisation;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\OrganisationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PartnerCommissionTest extends TestCase
{
    use RefreshDatabase;

    private function partnerUser(): User
    {
        $this->seed(OrganisationSeeder::class);
        Role::firstOrCreate(['name' => 'partner_admin', 'guard_name' => 'web']);

        $user = User::factory()->create([
            'organisation_id' => Organisation::where('slug', 'sos-ems')->first()->id,
            'role' => 'partner_admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $user->assignRole('partner_admin');

        return $user;
    }

    private function completedBooking(int $orgId, float $total = 100.0, string $status = 'completed'): Booking
    {
        $patient = Patient::create(['organisation_id' => $orgId, 'first_name' => 'A', 'last_name' => 'B']);
        $booker = User::factory()->create(['role' => 'patient', 'status' => 'active']);

        return Booking::create([
            'uuid' => (string) Str::uuid(),
            'organisation_id' => $orgId,
            'patient_id' => $patient->id,
            'booker_id' => $booker->id,
            'journey_type' => 'one_way',
            'status' => $status,
            'pickup_address' => '1 St', 'pickup_contact_name' => 'A', 'pickup_contact_phone' => '0821234567',
            'destination_address' => '2 St', 'destination_contact_name' => 'B', 'destination_contact_phone' => '0821234567',
            'requested_at' => now(),
            'total_amount' => $total,
            'actual_dropoff_at' => now(),
        ]);
    }

    public function test_completing_booking_auto_generates_15_percent_commission(): void
    {
        $this->seed(OrganisationSeeder::class);
        $org = Organisation::where('slug', 'sos-ems')->first();

        $booking = $this->completedBooking($org->id, 200.0, 'pending');
        $booking->update(['status' => 'completed']);

        $commission = Commission::where('booking_id', $booking->id)->first();

        $this->assertNotNull($commission);
        $this->assertSame(15.0, (float) $commission->commission_rate);
        $this->assertSame(30.0, (float) $commission->commission_amount);
    }

    public function test_partner_sees_outstanding_and_pays_via_paypal(): void
    {
        $user = $this->partnerUser();
        $org = $user->organisation;

        Commission::create([
            'booking_id' => $this->completedBooking($org->id, 100.0)->id,
            'partner_organisation_id' => $org->id,
            'gross_amount' => 100,
            'commission_rate' => 15,
            'commission_amount' => 15,
            'partner_payable' => 85,
            'currency' => 'ZAR',
            'calculated_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('partner.commissions.index'))
            ->assertOk()
            ->assertSee('Pay via PayPal')
            ->assertSee('15.00');

        $this->actingAs($user)
            ->post(route('partner.commissions.pay'))
            ->assertRedirect(config('carelink.paypal.payment_url'));

        $this->assertDatabaseHas('commission_payments', [
            'partner_organisation_id' => $org->id,
            'status' => 'pending',
            'amount' => 15,
        ]);
    }

    public function test_partner_can_set_payout_frequency(): void
    {
        $user = $this->partnerUser();

        $this->actingAs($user)
            ->patch(route('partner.commissions.frequency'), ['frequency' => 'weekly'])
            ->assertRedirect();

        $this->assertDatabaseHas('organisations', [
            'id' => $user->organisation_id,
            'commission_frequency' => 'weekly',
        ]);
    }
}