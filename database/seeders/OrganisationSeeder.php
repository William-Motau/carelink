<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Organisation;

class OrganisationSeeder extends Seeder
{
    public function run(): void
    {
        Organisation::firstOrCreate(
            ['slug' => 'regia-mobility'],
            [
                'name' => 'Regia Mobility',
                'type' => 'regia_mobility',
                'settings' => [
                    'branding' => [
                        'primary_color' => '#1e3a5f',
                        'secondary_color' => '#0d9488',
                        'logo_url' => null,
                    ],
                    'notification_preferences' => [
                        'email' => true,
                        'sms' => true,
                        'push' => true,
                    ],
                    'fare_rules' => [],
                    'commission_rates' => [
                        'sos_ems' => 15.00,
                    ],
                    'compliance_rules' => [
                        'vehicle_license_alert_days' => [30, 14, 7],
                        'cofc_alert_days' => [30, 14, 7],
                        'insurance_alert_days' => [30, 14, 7],
                        'operating_permit_alert_days' => [30, 14, 7],
                        'driver_license_alert_days' => [30, 14, 7],
                        'prdp_alert_days' => [30, 14, 7],
                        'medical_cert_alert_days' => [30, 14, 7],
                        'background_check_alert_days' => [30, 14, 7],
                    ],
                ],
                'is_active' => true,
            ]
        );

        Organisation::firstOrCreate(
            ['slug' => 'sos-ems'],
            [
                'name' => 'SOS EMS',
                'type' => 'sos_ems',
                'settings' => [
                    'branding' => [
                        'primary_color' => '#dc2626',
                        'secondary_color' => '#1e3a5f',
                        'logo_url' => null,
                    ],
                    'notification_preferences' => [
                        'email' => true,
                        'sms' => true,
                        'push' => true,
                    ],
                    'fare_rules' => [],
                    'commission_rates' => [],
                    'compliance_rules' => [
                        'vehicle_license_alert_days' => [30, 14, 7],
                        'cofc_alert_days' => [30, 14, 7],
                        'insurance_alert_days' => [30, 14, 7],
                        'operating_permit_alert_days' => [30, 14, 7],
                        'driver_license_alert_days' => [30, 14, 7],
                        'prdp_alert_days' => [30, 14, 7],
                        'medical_cert_alert_days' => [30, 14, 7],
                        'background_check_alert_days' => [30, 14, 7],
                    ],
                ],
                'is_active' => true,
            ]
        );
    }
}