<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\User;
use Database\Seeders\OrganisationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function operationsUser(): User
    {
        $this->seed(OrganisationSeeder::class);
        $organisation = Organisation::where('slug', 'regia-mobility')->first();

        Role::firstOrCreate(['name' => 'org_admin', 'guard_name' => 'web']);

        $user = User::factory()->create([
            'organisation_id' => $organisation->id,
            'role' => 'org_admin',
            'status' => 'active',
        ]);
        $user->assignRole('org_admin');

        return $user;
    }

    public function test_profile_page_renders_for_authenticated_user(): void
    {
        $user = $this->operationsUser();

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee('My Profile');
    }

    public function test_profile_can_be_updated(): void
    {
        $user = $this->operationsUser();

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'first_name' => 'Updated',
            'last_name' => 'Surname',
            'email' => $user->email,
            'phone' => '0821234567',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Updated',
            'last_name' => 'Surname',
            'phone' => '0821234567',
        ]);
    }

    public function test_operations_dashboard_renders_after_profile_route_fix(): void
    {
        $user = $this->operationsUser();

        $this->actingAs($user)
            ->get(route('operations.dashboard'))
            ->assertOk();
    }

    public function test_avatar_can_be_uploaded(): void
    {
        Storage::fake('public');
        $user = $this->operationsUser();

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => UploadedFile::fake()->image('avatar.jpg', 40, 40),
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    public function test_user_can_log_out(): void
    {
        $user = $this->operationsUser();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }
}