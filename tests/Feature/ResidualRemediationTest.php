<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Document;
use App\Models\Organisation;
use App\Models\Patient;
use App\Models\User;
use App\Policies\DocumentPolicy;
use Database\Seeders\OrganisationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResidualRemediationTest extends TestCase
{
    use RefreshDatabase;

    public function test_medical_notes_are_encrypted_at_rest(): void
    {
        $this->seed(OrganisationSeeder::class);
        $org = Organisation::where('slug', 'regia-mobility')->first();

        $patient = Patient::create([
            'organisation_id' => $org->id,
            'first_name' => 'A',
            'last_name' => 'B',
            'medical_notes' => 'Confidential diagnosis',
        ]);

        $raw = DB::table('patients')->where('id', $patient->id)->value('medical_notes');

        $this->assertNotSame('Confidential diagnosis', $raw);
        $this->assertSame('Confidential diagnosis', Crypt::decryptString($raw));
        $this->assertSame('Confidential diagnosis', $patient->fresh()->medical_notes);
    }

    public function test_reference_is_not_sequential(): void
    {
        $this->seed(OrganisationSeeder::class);
        $org = Organisation::where('slug', 'regia-mobility')->first();

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

        $this->assertStringContainsString(strtoupper(substr($booking->uuid, 0, 6)), $booking->reference);
        $this->assertStringNotContainsString(str_pad((string) $booking->id, 4, '0', STR_PAD_LEFT), $booking->reference);
    }

    public function test_document_delete_policy_is_org_scoped(): void
    {
        $this->seed(OrganisationSeeder::class);
        $orgA = Organisation::where('slug', 'regia-mobility')->first();
        $orgB = Organisation::where('slug', 'sos-ems')->first();

        $patient = Patient::create(['organisation_id' => $orgA->id, 'first_name' => 'A', 'last_name' => 'B']);

        $document = Document::create([
            'documentable_type' => Patient::class,
            'documentable_id' => $patient->id,
            'type' => 'cofc',
            'file_path' => 'documents/patient/1/x.pdf',
            'original_filename' => 'x.pdf',
            'mime_type' => 'application/pdf',
            'size' => 10,
        ]);

        $policy = app(DocumentPolicy::class);

        $userA = User::factory()->create(['organisation_id' => $orgA->id, 'role' => 'org_admin', 'status' => 'active']);
        $userB = User::factory()->create(['organisation_id' => $orgB->id, 'role' => 'org_admin', 'status' => 'active']);

        $this->assertTrue($policy->delete($userA, $document));
        $this->assertFalse($policy->delete($userB, $document));
    }

    public function test_missing_coordinates_rejected_when_required(): void
    {
        config(['carelink.fare.require_coordinates' => true]);
        $this->seed(OrganisationSeeder::class);

        $wizard = [
            'contact' => ['role' => 'self', 'first_name' => 'A', 'last_name' => 'B', 'phone' => '0821234567', 'email' => 'a@example.test', 'relationship' => 'self'],
            'patient' => ['first_name' => 'A', 'last_name' => 'B'],
            'journey' => [
                'journey_type' => 'one_way',
                'pickup_address' => '1 St', 'pickup_contact_name' => 'A', 'pickup_contact_phone' => '0821234567',
                'destination_address' => '2 St', 'destination_contact_name' => 'B', 'destination_contact_phone' => '0821234567',
                'scheduled_at' => now()->addDay()->toDateTimeString(),
            ],
            'requirements' => ['mobility' => ['wheelchair']],
            'review' => ['consents' => ['privacy' => true, 'personal_info' => true, 'terms' => true]],
        ];

        $response = $this->withSession(['booking_wizard' => $wizard])
            ->post(route('booking.submit'), ['idempotency_key' => 'nocoords']);

        $response->assertRedirect(route('booking.step.show', ['step' => 3]));
        $response->assertSessionHas('error');
        $this->assertSame(0, Booking::count());
    }
}