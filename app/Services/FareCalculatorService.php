<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\FareCalculation;
use App\Models\PricingRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FareCalculatorService
{
    public const VAT_RATE = 0.15;

    public const DEFAULT_DISTANCE_KM = 10.0;

    /**
     * Calculate the fare for a set of booking parameters.
     *
     * @param  array  $params  pickup_lat, pickup_lng, destination_lat, destination_lng,
     *                         mobility_requirements, journey_type, scheduled_at, organisation_id
     * @return array
     */
    public function calculate(array $params): array
    {
        $organisationId = $params['organisation_id'] ?? null;
        $distanceKm = $this->calculateDistance(
            $params['pickup_lat'] ?? null,
            $params['pickup_lng'] ?? null,
            $params['destination_lat'] ?? null,
            $params['destination_lng'] ?? null,
        );

        $rules = $this->activeRules($organisationId);
        $mobility = $params['mobility_requirements'] ?? [];
        $journeyType = $params['journey_type'] ?? 'one_way';
        $scheduledAt = isset($params['scheduled_at']) ? \Carbon\Carbon::parse($params['scheduled_at']) : now();

        $context = [
            'extra_passengers' => (int) ($params['extra_passengers'] ?? 0),
            'additional_stops' => (int) ($params['additional_stops'] ?? 0),
            'waiting_minutes' => (int) ($params['waiting_minutes'] ?? 0),
            'vehicle_type' => $params['vehicle_type'] ?? null,
            'is_public_holiday' => (bool) ($params['is_public_holiday'] ?? false),
        ];

        $breakdown = [
            'base_fare' => 0.0,
            'distance_charge' => 0.0,
            'additional_passenger_charge' => 0.0,
            'special_vehicle_charge' => 0.0,
            'additional_stop_charge' => 0.0,
            'waiting_charge' => 0.0,
            'surcharges' => 0.0,
            'manual_adjustments' => 0.0,
        ];

        $appliedRules = [];
        $percentageSurcharges = [];

        foreach ($rules as $rule) {
            if (! $this->ruleApplies($rule, $mobility, $journeyType, $scheduledAt, $distanceKm, $context)) {
                continue;
            }

            // Percentage surcharges are applied after the subtotal is known
            if ($rule->rule_type === 'surcharge' && $rule->value_type === 'percentage') {
                $percentageSurcharges[] = $rule;
                continue;
            }

            $amount = $this->ruleAmount($rule, $distanceKm, $scheduledAt, $context);

            match ($rule->rule_type) {
                'base_fare' => $breakdown['base_fare'] = max($breakdown['base_fare'], $amount),
                'distance' => $breakdown['distance_charge'] += $amount,
                'additional_passenger' => $breakdown['additional_passenger_charge'] += $amount,
                'special_vehicle' => $breakdown['special_vehicle_charge'] += $amount,
                'additional_stop' => $breakdown['additional_stop_charge'] += $amount,
                'waiting' => $breakdown['waiting_charge'] += $amount,
                'surcharge' => $breakdown['surcharges'] += $amount,
                'manual_adjustment' => $breakdown['manual_adjustments'] += $amount,
                default => null,
            };

            $appliedRules[] = [
                'code' => $rule->code,
                'name' => $rule->name,
                'rule_type' => $rule->rule_type,
                'amount' => round($amount, 2),
            ];
        }

        // Fallback base fare if no rule matched
        if ($breakdown['base_fare'] === 0.0) {
            $breakdown['base_fare'] = $this->fallbackBaseFare($mobility);
        }

        $subtotal = array_sum($breakdown);

        // Apply percentage surcharges on the running subtotal
        foreach ($percentageSurcharges as $rule) {
            $amount = round($subtotal * (max(0.0, (float) $rule->value) / 100), 2);
            $breakdown['surcharges'] += $amount;
            $subtotal += $amount;
            $appliedRules[] = [
                'code' => $rule->code,
                'name' => $rule->name,
                'rule_type' => $rule->rule_type,
                'amount' => $amount,
            ];
        }

        $vatAmount = round($subtotal * self::VAT_RATE, 2);
        $totalAmount = round($subtotal + $vatAmount, 2);

        return [
            'distance_km' => round($distanceKm, 2),
            'breakdown' => $breakdown,
            'subtotal' => round($subtotal, 2),
            'vat_amount' => $vatAmount,
            'total_amount' => $totalAmount,
            'applied_rules' => $appliedRules,
            'base_fare' => $breakdown['base_fare'],
            'distance_charge' => $breakdown['distance_charge'],
        ];
    }

    /**
     * Calculate and persist the fare for a booking.
     */
    public function calculateForBooking(Booking $booking): FareCalculation
    {
        $result = $this->calculate([
            'pickup_lat' => $booking->pickup_lat,
            'pickup_lng' => $booking->pickup_lng,
            'destination_lat' => $booking->destination_lat,
            'destination_lng' => $booking->destination_lng,
            'mobility_requirements' => $booking->mobility_requirements ?? [],
            'journey_type' => $booking->journey_type,
            'scheduled_at' => $booking->scheduled_at ?? now(),
            'organisation_id' => $booking->organisation_id,
        ]);

        return DB::transaction(function () use ($booking, $result) {
            $booking->update([
                'fare_amount' => $result['subtotal'],
                'vat_amount' => $result['vat_amount'],
                'total_amount' => $result['total_amount'],
            ]);

            return FareCalculation::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'calculation_details' => $result,
                    'base_fare' => $result['breakdown']['base_fare'],
                    'distance_charge' => $result['breakdown']['distance_charge'],
                    'additional_passenger_charge' => $result['breakdown']['additional_passenger_charge'],
                    'special_vehicle_charge' => $result['breakdown']['special_vehicle_charge'],
                    'additional_stop_charge' => $result['breakdown']['additional_stop_charge'],
                    'waiting_charge' => $result['breakdown']['waiting_charge'],
                    'surcharges' => $result['breakdown']['surcharges'],
                    'manual_adjustments' => $result['breakdown']['manual_adjustments'],
                    'subtotal' => $result['subtotal'],
                    'vat_amount' => $result['vat_amount'],
                    'total_amount' => $result['total_amount'],
                    'calculated_by' => auth()->id(),
                ]
            );
        });
    }

    /**
     * Haversine distance in kilometres. Falls back to a default when coords are missing.
     */
    public function calculateDistance(?float $lat1, ?float $lng1, ?float $lat2, ?float $lng2): float
    {
        if ($lat1 === null || $lng1 === null || $lat2 === null || $lng2 === null) {
            return (float) config('carelink.fare.default_distance_km', self::DEFAULT_DISTANCE_KM);
        }

        $earthRadius = 6371.0; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    protected function activeRules(?int $organisationId): Collection
    {
        return PricingRule::query()
            ->where('is_active', true)
            ->where(function ($q) use ($organisationId) {
                $q->whereNull('organisation_id');
                if ($organisationId) {
                    $q->orWhere('organisation_id', $organisationId);
                }
            })
            ->orderBy('priority')
            ->get();
    }

    protected function ruleApplies(PricingRule $rule, array $mobility, string $journeyType, \Carbon\Carbon $scheduledAt, float $distanceKm, array $context): bool
    {
        $conditions = $rule->conditions ?? [];

        if (isset($conditions['journey_type']) && ! in_array($journeyType, (array) $conditions['journey_type'], true)) {
            return false;
        }

        if (isset($conditions['mobility_requirements'])) {
            $required = (array) $conditions['mobility_requirements'];
            if (empty(array_intersect($required, $mobility))) {
                return false;
            }
        }

        if (isset($conditions['days'])) {
            $day = strtolower($scheduledAt->format('l'));
            if (! in_array($day, array_map('strtolower', (array) $conditions['days']), true)) {
                return false;
            }
        }

        if (isset($conditions['time_range'])) {
            if (! $this->timeInRange($scheduledAt->format('H:i'), $conditions['time_range'])) {
                return false;
            }
        }

        if (isset($conditions['zone'])) {
            if ($conditions['zone'] === 'rural' && $distanceKm < 25) {
                return false;
            }
        }

        // Strict handling for quantity/flag-based conditions
        if ($rule->rule_type === 'additional_passenger' && $context['extra_passengers'] < 1) {
            return false;
        }

        if ($rule->rule_type === 'additional_stop' && $context['additional_stops'] < 1) {
            return false;
        }

        if ($rule->rule_type === 'waiting') {
            $after = (int) ($conditions['after_minutes'] ?? 0);
            if ($context['waiting_minutes'] <= $after) {
                return false;
            }
        }

        if (isset($conditions['vehicle_type'])) {
            if ($context['vehicle_type'] !== $conditions['vehicle_type']) {
                return false;
            }
        }

        if (isset($conditions['is_public_holiday']) && $conditions['is_public_holiday'] && ! $context['is_public_holiday']) {
            return false;
        }

        return true;
    }

    protected function ruleAmount(PricingRule $rule, float $distanceKm, \Carbon\Carbon $scheduledAt, array $context): float
    {
        $amount = match ($rule->value_type) {
            'fixed' => (float) $rule->value,
            'per_km' => (float) $rule->value * $distanceKm,
            'per_minute' => (float) $rule->value * max(0, $context['waiting_minutes'] - (int) ($rule->conditions['after_minutes'] ?? 0)),
            'per_passenger' => (float) $rule->value * $context['extra_passengers'],
            'percentage' => 0.0,
            default => (float) $rule->value,
        };

        // Never allow a negative charge/adjustment to reduce the fare below zero.
        return max(0.0, $amount);
    }

    protected function fallbackBaseFare(array $mobility): float
    {
        if (in_array('stretcher', $mobility, true)) {
            return 500.0;
        }
        if (in_array('wheelchair', $mobility, true)) {
            return 250.0;
        }
        return 150.0;
    }

    protected function timeInRange(string $time, string $range): bool
    {
        if (! str_contains($range, '-')) {
            return false;
        }
        [$start, $end] = explode('-', $range, 2);
        // Handle ranges crossing midnight (e.g. 18:00-06:00)
        if ($start > $end) {
            return $time >= $start || $time <= $end;
        }
        return $time >= $start && $time <= $end;
    }
}