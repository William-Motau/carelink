<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\User;
use Database\Seeders\OrganisationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NotificationTest extends TestCase
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

    private function sendNotification(User $user, string $message): void
    {
        $user->notify(new class($message) extends Notification {
            public function __construct(private string $message)
            {
            }

            public function toArray(object $notifiable): array
            {
                return ['message' => $this->message, 'reference' => 'CL-20261127-ABC123'];
            }

            public function via(object $notifiable): array
            {
                return ['database'];
            }
        });
    }

    public function test_notification_index_lists_notifications(): void
    {
        $user = $this->operationsUser();
        $this->sendNotification($user, 'Booking confirmed');

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Booking confirmed')
            ->assertSee('CL-20261127-ABC123');
    }

    public function test_mark_all_read_clears_unread_count(): void
    {
        $user = $this->operationsUser();
        $this->sendNotification($user, 'Booking confirmed');

        $this->assertSame(1, $user->fresh()->unreadNotifications->count());

        $this->actingAs($user)
            ->post(route('notifications.read-all'))
            ->assertRedirect();

        $this->assertSame(0, $user->fresh()->unreadNotifications->count());
    }

    public function test_mark_single_notification_read(): void
    {
        $user = $this->operationsUser();
        $this->sendNotification($user, 'Booking confirmed');

        $notification = $user->fresh()->notifications()->first();

        $this->actingAs($user)
            ->post(route('notifications.read', $notification->id))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
    }
}