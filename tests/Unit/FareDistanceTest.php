<?php

namespace Tests\Unit;

use App\Services\FareCalculatorService;
use Tests\TestCase;

class FareDistanceTest extends TestCase
{
    private FareCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FareCalculatorService();
    }

    public function test_missing_coordinates_return_default_distance(): void
    {
        $this->assertSame(
            FareCalculatorService::DEFAULT_DISTANCE_KM,
            $this->service->calculateDistance(null, null, null, null)
        );
    }

    public function test_partial_coordinates_return_default_distance(): void
    {
        $this->assertSame(
            FareCalculatorService::DEFAULT_DISTANCE_KM,
            $this->service->calculateDistance(-26.2, 28.0, null, 28.0)
        );
    }

    public function test_haversine_distance_between_two_johannesburg_points(): void
    {
        // Johannesburg CBD to Sandton is roughly 10-11 km straight-line.
        $distance = $this->service->calculateDistance(-26.2041, 28.0473, -26.1076, 28.0567);

        $this->assertGreaterThan(9.0, $distance);
        $this->assertLessThan(13.0, $distance);
    }

    public function test_same_point_is_zero_distance(): void
    {
        $this->assertEqualsWithDelta(0.0, $this->service->calculateDistance(-26.2, 28.0, -26.2, 28.0), 0.0001);
    }
}