<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\FareCalculation;
use App\Models\Organisation;
use App\Models\Patient;
use App\Models\PricingRule;
use App\Models\User;
use App\Services\FareCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FareCalculationTest extends TestCase
{
    use RefreshDatabase;

    private function makeBooking(array $overrides = []): Booking
    {
        $org = Organisation::create(['name' => 'Regia', 'slug' => 'regia-mobility', 'type' => 'regia_mobility']);
        $user = User::factory()->create(['organisation_id' => $org->id]);
        $patient = Patient::create([
            'organisation_id' => $org->id,
            'first_name' => 'Test',
            'last_name' => 'Patient',
        ]);

        return Booking::create(array_merge([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'organisation_id' => $org->id,
            'patient_id' => $patient->id,
            'booker_id' => $user->id,
            'journey_type' => 'one_way',
            'pickup_address' => '1 Main St',
            'destination_address' => '2 Side St',
            'pickup_lat' => -26.2041,
            'pickup_lng' => 28.0473,
            'destination_lat' => -26.1076,
            'destination_lng' => 28.0567,
        ], $overrides));
    }

    public function test_fallback_base_fare_applies_with_no_rules(): void
    {
        $result = app(FareCalculatorService::class)->calculate([
            'pickup_lat' => -26.2,
            'pickup_lng' => 28.0,
            'destination_lat' => -26.1,
            'destination_lng' => 28.1,
            'mobility_requirements' => [],
        ]);

        $this->assertEquals(150.0, $result['base_fare']);
        $this->assertEqualsWithDelta(150.0, $result['subtotal'], 0.001);
        $this->assertEqualsWithDelta(22.5, $result['vat_amount'], 0.001);
        $this->assertEqualsWithDelta(172.5, $result['total_amount'], 0.001);
    }

    public function test_wheelchair_mobility_uses_higher_base_fare(): void
    {
        $result = app(FareCalculatorService::class)->calculate([
            'mobility_requirements' => ['wheelchair'],
        ]);

        $this->assertEquals(250.0, $result['base_fare']);
    }

    public function test_stretcher_mobility_uses_highest_base_fare(): void
    {
        $result = app(FareCalculatorService::class)->calculate([
            'mobility_requirements' => ['stretcher'],
        ]);

        $this->assertEquals(500.0, $result['base_fare']);
    }

    public function test_percentage_surcharge_applies_on_subtotal(): void
    {
        PricingRule::create([
            'name' => 'Weekend surcharge',
            'code' => 'WKND',
            'rule_type' => 'surcharge',
            'value' => 10,
            'value_type' => 'percentage',
            'conditions' => [],
            'priority' => 1,
            'is_active' => true,
        ]);

        $result = app(FareCalculatorService::class)->calculate([
            'mobility_requirements' => [],
        ]);

        // base 150 + 10% surcharge (15) = 165 subtotal; VAT 15% = 24.75; total 189.75
        $this->assertEqualsWithDelta(165.0, $result['subtotal'], 0.001);
        $this->assertEqualsWithDelta(15.0, $result['breakdown']['surcharges'], 0.001);
        $this->assertEqualsWithDelta(189.75, $result['total_amount'], 0.001);
    }

    public function test_calculate_for_booking_persists_fare_and_updates_totals(): void
    {
        $booking = $this->makeBooking();

        $fare = app(FareCalculatorService::class)->calculateForBooking($booking);

        $this->assertInstanceOf(FareCalculation::class, $fare);
        $this->assertDatabaseHas('fare_calculations', ['booking_id' => $booking->id]);

        $booking->refresh();
        $this->assertEqualsWithDelta($fare->subtotal, (float) $booking->fare_amount, 0.001);
        $this->assertEqualsWithDelta($fare->total_amount, (float) $booking->total_amount, 0.001);
    }
}