<?php

namespace Tests\Feature;

use App\Models\ConsentRecord;
use App\Models\User;
use App\Services\ConsentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsentTest extends TestCase
{
    use RefreshDatabase;

    private ConsentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ConsentService::class);
    }

    public function test_record_grants_consent(): void
    {
        $user = User::factory()->create();

        $record = $this->service->record($user->id, 'privacy', true);

        $this->assertTrue($record->granted);
        $this->assertNotNull($record->granted_at);
        $this->assertTrue($this->service->hasConsent($user->id, 'privacy'));
    }

    public function test_withdraw_removes_active_consent(): void
    {
        $user = User::factory()->create();
        $this->service->record($user->id, 'marketing', true);

        $this->service->withdraw($user->id, 'marketing');

        $this->assertFalse($this->service->hasConsent($user->id, 'marketing'));
    }

    public function test_declined_consent_is_not_active(): void
    {
        $user = User::factory()->create();

        $this->service->record($user->id, 'terms', false);

        $this->assertFalse($this->service->hasConsent($user->id, 'terms'));
    }

    public function test_record_batch_persists_all_and_updates_user_summary(): void
    {
        $user = User::factory()->create();

        $this->service->recordBatch($user->id, [
            'privacy' => true,
            'personal_info' => true,
            'terms' => true,
            'marketing' => false,
        ]);

        $this->assertDatabaseHas('consent_records', ['user_id' => $user->id, 'consent_type' => 'privacy', 'granted' => true]);
        $this->assertDatabaseHas('consent_records', ['user_id' => $user->id, 'consent_type' => 'marketing', 'granted' => false]);
        $this->assertSame(4, ConsentRecord::where('user_id', $user->id)->count());

        $user->refresh();
        $this->assertNotNull($user->consent_given_at);
        $this->assertSame(ConsentService::VERSION, $user->consent_version);
    }

    public function test_has_consent_respects_version(): void
    {
        $user = User::factory()->create();
        $this->service->record($user->id, 'privacy', true, '1.0');

        $this->assertTrue($this->service->hasConsent($user->id, 'privacy', '1.0'));
        $this->assertFalse($this->service->hasConsent($user->id, 'privacy', '2.0'));
    }

    public function test_booking_step_five_requires_consent_acceptance(): void
    {
        $response = $this->post(route('booking.step.store', ['step' => 5]), [
            'review' => [
                'consents' => [
                    'privacy' => false,
                    'personal_info' => false,
                    'terms' => false,
                ],
            ],
            'g_recaptcha_response' => 'test-token',
        ]);

        $response->assertSessionHasErrors([
            'review.consents.privacy',
            'review.consents.personal_info',
            'review.consents.terms',
        ]);
    }
}