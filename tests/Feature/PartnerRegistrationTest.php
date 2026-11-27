<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\User;
use App\Models\Vehicle;
use Database\Seeders\OrganisationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PartnerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role, array $attrs = []): User
    {
        Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        $user = User::factory()->create(array_merge([
            'role' => $role,
            'status' => 'active',
            'email_verified_at' => now(),
        ], $attrs));
        $user->assignRole($role);

        return $user;
    }

    public function test_super_admin_can_register_partner_with_admin_account(): void
    {
        $this->seed(OrganisationSeeder::class);
        $admin = $this->userWithRole('super_admin', [
            'organisation_id' => Organisation::where('slug', 'regia-mobility')->first()->id,
        ]);

        $this->actingAs($admin)->post(route('operations.partners.store'), [
            'name' => 'City Hospital',
            'partner_category' => 'hospital',
            'email' => 'ops@cityhospital.test',
            'contact_phone' => '0211234567',
            'address' => '1 Hospital Rd',
            'admin_first_name' => 'Nurse',
            'admin_last_name' => 'Admin',
            'admin_email' => 'admin@cityhospital.test',
            'admin_phone' => '0211234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('operations.partners.index'));

        $this->assertDatabaseHas('organisations', [
            'name' => 'City Hospital',
            'partner_category' => 'hospital',
            'type' => 'sos_ems',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'admin@cityhospital.test',
            'role' => 'partner_admin',
        ]);
    }

    public function test_non_super_admin_cannot_register_partner(): void
    {
        $this->seed(OrganisationSeeder::class);
        $admin = $this->userWithRole('org_admin', [
            'organisation_id' => Organisation::where('slug', 'regia-mobility')->first()->id,
        ]);

        $this->actingAs($admin)
            ->get(route('operations.partners.create'))
            ->assertForbidden();
    }

    public function test_super_admin_can_view_vehicle_from_another_organisation(): void
    {
        $this->seed(OrganisationSeeder::class);
        $admin = $this->userWithRole('super_admin');

        $vehicle = Vehicle::create([
            'organisation_id' => Organisation::where('slug', 'sos-ems')->first()->id,
            'registration' => 'TEST 001',
            'make' => 'Toyota',
            'model' => 'Quantum',
            'year' => 2022,
            'vehicle_type' => 'minibus',
            'capacity_seated' => 10,
            'fuel_type' => 'diesel',
            'status' => 'available',
        ]);

        $this->actingAs($admin)
            ->get(route('operations.fleet.show', $vehicle))
            ->assertOk();
    }
}