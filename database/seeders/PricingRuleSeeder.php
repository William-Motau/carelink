<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PricingRule;
use App\Models\Organisation;

class PricingRuleSeeder extends Seeder
{
    public function run(): void
    {
        $regia = Organisation::where('slug', 'regia-mobility')->first();

        $rules = [
            [
                'organisation_id' => $regia->id,
                'name' => 'Base Fare - Standard',
                'code' => 'BASE_STANDARD',
                'rule_type' => 'base_fare',
                'value' => 150.00,
                'value_type' => 'fixed',
                'conditions' => ['journey_type' => ['one_way', 'return']],
                'priority' => 10,
                'is_active' => true,
            ],
            [
                'organisation_id' => $regia->id,
                'name' => 'Base Fare - Wheelchair Accessible',
                'code' => 'BASE_WHEELCHAIR',
                'rule_type' => 'base_fare',
                'value' => 250.00,
                'value_type' => 'fixed',
                'conditions' => ['mobility_requirements' => ['wheelchair']],
                'priority' => 10,
                'is_active' => true,
            ],
            [
                'organisation_id' => $regia->id,
                'name' => 'Base Fare - Stretcher',
                'code' => 'BASE_STRETCHER',
                'rule_type' => 'base_fare',
                'value' => 500.00,
                'value_type' => 'fixed',
                'conditions' => ['mobility_requirements' => ['stretcher']],
                'priority' => 10,
                'is_active' => true,
            ],
            [
                'organisation_id' => $regia->id,
                'name' => 'Distance Charge - Urban',
                'code' => 'DIST_URBAN',
                'rule_type' => 'distance',
                'value' => 12.50,
                'value_type' => 'per_km',
                'conditions' => ['zone' => 'urban'],
                'priority' => 20,
                'is_active' => true,
            ],
            [
                'organisation_id' => $regia->id,
                'name' => 'Distance Charge - Rural',
                'code' => 'DIST_RURAL',
                'rule_type' => 'distance',
                'value' => 15.00,
                'value_type' => 'per_km',
                'conditions' => ['zone' => 'rural'],
                'priority' => 20,
                'is_active' => true,
            ],
            [
                'organisation_id' => $regia->id,
                'name' => 'Additional Passenger',
                'code' => 'ADD_PASSENGER',
                'rule_type' => 'additional_passenger',
                'value' => 50.00,
                'value_type' => 'per_passenger',
                'conditions' => [],
                'priority' => 30,
                'is_active' => true,
            ],
            [
                'organisation_id' => $regia->id,
                'name' => 'Special Vehicle Surcharge - Ambulance',
                'code' => 'SPEC_AMBULANCE',
                'rule_type' => 'special_vehicle',
                'value' => 300.00,
                'value_type' => 'fixed',
                'conditions' => ['vehicle_type' => 'ambulance'],
                'priority' => 30,
                'is_active' => true,
            ],
            [
                'organisation_id' => $regia->id,
                'name' => 'Additional Stop',
                'code' => 'ADD_STOP',
                'rule_type' => 'additional_stop',
                'value' => 75.00,
                'value_type' => 'fixed',
                'conditions' => [],
                'priority' => 40,
                'is_active' => true,
            ],
            [
                'organisation_id' => $regia->id,
                'name' => 'Waiting Charge',
                'code' => 'WAITING',
                'rule_type' => 'waiting',
                'value' => 80.00,
                'value_type' => 'per_minute',
                'conditions' => ['after_minutes' => 15],
                'priority' => 40,
                'is_active' => true,
            ],
            [
                'organisation_id' => $regia->id,
                'name' => 'After Hours Surcharge',
                'code' => 'SURCHARGE_AFTER_HOURS',
                'rule_type' => 'surcharge',
                'value' => 25.00,
                'value_type' => 'percentage',
                'conditions' => ['time_range' => '18:00-06:00'],
                'priority' => 50,
                'is_active' => true,
            ],
            [
                'organisation_id' => $regia->id,
                'name' => 'Weekend Surcharge',
                'code' => 'SURCHARGE_WEEKEND',
                'rule_type' => 'surcharge',
                'value' => 15.00,
                'value_type' => 'percentage',
                'conditions' => ['days' => ['saturday', 'sunday']],
                'priority' => 50,
                'is_active' => true,
            ],
            [
                'organisation_id' => $regia->id,
                'name' => 'Public Holiday Surcharge',
                'code' => 'SURCHARGE_HOLIDAY',
                'rule_type' => 'surcharge',
                'value' => 50.00,
                'value_type' => 'percentage',
                'conditions' => ['is_public_holiday' => true],
                'priority' => 50,
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            PricingRule::firstOrCreate(
                ['code' => $rule['code']],
                $rule
            );
        }
    }
}