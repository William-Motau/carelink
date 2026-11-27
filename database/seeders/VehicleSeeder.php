<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\Organisation;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $regia = Organisation::where('slug', 'regia-mobility')->first();
        $sos = Organisation::where('slug', 'sos-ems')->first();

        $vehicles = [
            [
                'organisation_id' => $regia->id,
                'registration' => 'GP REG 001',
                'vin' => 'VIN12345678901234',
                'make' => 'Toyota',
                'model' => 'Quantum',
                'year' => 2022,
                'vehicle_type' => 'wheelchair_van',
                'capacity_seated' => 12,
                'capacity_wheelchair' => 2,
                'capacity_stretcher' => 0,
                'fuel_type' => 'diesel',
                'license_expiry' => now()->addMonths(10),
                'cofc_expiry' => now()->addMonths(8),
                'insurance_expiry' => now()->addMonths(6),
                'operating_permit_expiry' => now()->addMonths(4),
                'service_due_km' => 15000,
                'current_km' => 12500,
                'status' => 'available',
                'equipment' => ['wheelchair_ramp', 'wheelchair_lift', 'tie_downs', 'gps_tracker'],
            ],
            [
                'organisation_id' => $regia->id,
                'registration' => 'GP REG 002',
                'vin' => 'VIN12345678901235',
                'make' => 'Mercedes-Benz',
                'model' => 'Sprinter',
                'year' => 2023,
                'vehicle_type' => 'stretcher_van',
                'capacity_seated' => 4,
                'capacity_wheelchair' => 1,
                'capacity_stretcher' => 2,
                'fuel_type' => 'diesel',
                'license_expiry' => now()->addMonths(11),
                'cofc_expiry' => now()->addMonths(9),
                'insurance_expiry' => now()->addMonths(7),
                'operating_permit_expiry' => now()->addMonths(5),
                'service_due_km' => 15000,
                'current_km' => 8200,
                'status' => 'available',
                'equipment' => ['stretcher_mounts', 'oxygen_outlets', 'medical_cabinet', 'gps_tracker'],
            ],
            [
                'organisation_id' => $regia->id,
                'registration' => 'GP REG 003',
                'vin' => 'VIN12345678901236',
                'make' => 'Volkswagen',
                'model' => 'Crafter',
                'year' => 2021,
                'vehicle_type' => 'minibus',
                'capacity_seated' => 16,
                'capacity_wheelchair' => 0,
                'capacity_stretcher' => 0,
                'fuel_type' => 'diesel',
                'license_expiry' => now()->addMonths(3),
                'cofc_expiry' => now()->addMonths(1),
                'insurance_expiry' => now()->addMonths(2),
                'operating_permit_expiry' => now()->addMonths(6),
                'service_due_km' => 15000,
                'current_km' => 45000,
                'status' => 'compliance_suspended',
                'equipment' => ['gps_tracker', 'air_conditioning'],
            ],
            [
                'organisation_id' => $sos->id,
                'registration' => 'GP SOS 001',
                'vin' => 'VIN12345678901237',
                'make' => 'Ford',
                'model' => 'Transit',
                'year' => 2022,
                'vehicle_type' => 'ambulance',
                'capacity_seated' => 2,
                'capacity_wheelchair' => 1,
                'capacity_stretcher' => 2,
                'fuel_type' => 'diesel',
                'license_expiry' => now()->addMonths(8),
                'cofc_expiry' => now()->addMonths(6),
                'insurance_expiry' => now()->addMonths(4),
                'operating_permit_expiry' => now()->addMonths(3),
                'service_due_km' => 15000,
                'current_km' => 22000,
                'status' => 'available',
                'equipment' => ['stretcher_mounts', 'oxygen_outlets', 'defibrillator', 'medical_cabinet', 'siren_lights', 'gps_tracker'],
            ],
            [
                'organisation_id' => $sos->id,
                'registration' => 'GP SOS 002',
                'vin' => 'VIN12345678901238',
                'make' => 'Toyota',
                'model' => 'Hiace',
                'year' => 2020,
                'vehicle_type' => 'wheelchair_van',
                'capacity_seated' => 10,
                'capacity_wheelchair' => 3,
                'capacity_stretcher' => 0,
                'fuel_type' => 'petrol',
                'license_expiry' => now()->addMonths(5),
                'cofc_expiry' => now()->addMonths(2),
                'insurance_expiry' => now()->addMonths(1),
                'operating_permit_expiry' => now()->addMonths(7),
                'service_due_km' => 15000,
                'current_km' => 78000,
                'status' => 'under_maintenance',
                'equipment' => ['wheelchair_ramp', 'tie_downs', 'gps_tracker'],
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::firstOrCreate(
                ['registration' => $vehicle['registration']],
                $vehicle
            );
        }
    }
}