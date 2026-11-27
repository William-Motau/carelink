<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organisation;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $regia = Organisation::where('slug', 'regia-mobility')->first();
        $sos = Organisation::where('slug', 'sos-ems')->first();

        // Generate a single random temporary password for the seeded demo
        // accounts instead of shipping a hardcoded, publicly-known password.
        // It is only applied when an account is first created. The password is
        // intentionally NOT logged; set/reset it out-of-band (e.g. tinker).
        $temporaryPassword = Str::password(16);

        // Create permissions
        $permissions = [
            // Organisation management
            'view organisations',
            'create organisations',
            'edit organisations',
            'delete organisations',
            
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage user roles',
            
            // Booking management
            'view bookings',
            'create bookings',
            'edit bookings',
            'delete bookings',
            'manage booking status',
            'accept bookings',
            'reject bookings',
            'assign vehicle',
            'assign driver',
            'dispatch bookings',
            
            // Fleet management
            'view vehicles',
            'create vehicles',
            'edit vehicles',
            'delete vehicles',
            'manage vehicle compliance',
            
            // Driver management
            'view drivers',
            'create drivers',
            'edit drivers',
            'delete drivers',
            'manage driver compliance',
            
            // Dispatch
            'view dispatch board',
            'manage dispatch',
            
            // Journeys
            'view journeys',
            'manage journeys',
            
            // Reports
            'view reports',
            'export reports',
            'view financial reports',
            
            // Commissions & Settlements
            'view commissions',
            'manage commissions',
            'view settlements',
            'manage settlements',
            'approve settlements',
            
            // Compliance
            'view compliance dashboard',
            'manage compliance documents',
            
            // Audit & Security
            'view audit logs',
            'view security incidents',
            
            // Settings
            'manage system settings',
            
            // Patient booking
            'submit booking',
            'view own bookings',
            'cancel own bookings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create roles and assign permissions
        $rolePermissions = [
            'super_admin' => $permissions,
            'org_admin' => [
                'view organisations',
                'view users',
                'create users',
                'edit users',
                'manage user roles',
                'view bookings',
                'create bookings',
                'edit bookings',
                'manage booking status',
                'accept bookings',
                'reject bookings',
                'assign vehicle',
                'assign driver',
                'dispatch bookings',
                'view vehicles',
                'create vehicles',
                'edit vehicles',
                'manage vehicle compliance',
                'view drivers',
                'create drivers',
                'edit drivers',
                'manage driver compliance',
                'view dispatch board',
                'manage dispatch',
                'view journeys',
                'manage journeys',
                'view reports',
                'export reports',
                'view financial reports',
                'view commissions',
                'manage commissions',
                'view settlements',
                'manage settlements',
                'approve settlements',
                'view compliance dashboard',
                'manage compliance documents',
                'view audit logs',
                'manage system settings',
            ],
            'dispatch_manager' => [
                'view bookings',
                'manage booking status',
                'accept bookings',
                'reject bookings',
                'assign vehicle',
                'assign driver',
                'dispatch bookings',
                'view vehicles',
                'edit vehicles',
                'view drivers',
                'edit drivers',
                'view dispatch board',
                'manage dispatch',
                'view journeys',
                'manage journeys',
                'view reports',
                'view compliance dashboard',
            ],
            'finance_officer' => [
                'view bookings',
                'view reports',
                'export reports',
                'view financial reports',
                'view commissions',
                'manage commissions',
                'view settlements',
                'manage settlements',
                'approve settlements',
            ],
            'compliance_officer' => [
                'view vehicles',
                'view drivers',
                'view compliance dashboard',
                'manage compliance documents',
                'view audit logs',
            ],
            'read_only_auditor' => [
                'view organisations',
                'view users',
                'view bookings',
                'view vehicles',
                'view drivers',
                'view journeys',
                'view reports',
                'view financial reports',
                'view commissions',
                'view settlements',
                'view compliance dashboard',
                'view audit logs',
            ],
            'partner_admin' => [
                'view users',
                'create users',
                'edit users',
                'manage user roles',
                'view bookings',
                'create bookings',
                'edit bookings',
                'manage booking status',
                'accept bookings',
                'reject bookings',
                'assign vehicle',
                'assign driver',
                'dispatch bookings',
                'view vehicles',
                'create vehicles',
                'edit vehicles',
                'manage vehicle compliance',
                'view drivers',
                'create drivers',
                'edit drivers',
                'manage driver compliance',
                'view dispatch board',
                'manage dispatch',
                'view journeys',
                'manage journeys',
                'view reports',
                'export reports',
                'view commissions',
                'view settlements',
                'view compliance dashboard',
                'manage compliance documents',
            ],
            'partner_operations_manager' => [
                'view bookings',
                'manage booking status',
                'accept bookings',
                'reject bookings',
                'assign vehicle',
                'assign driver',
                'dispatch bookings',
                'view vehicles',
                'edit vehicles',
                'view drivers',
                'edit drivers',
                'view dispatch board',
                'manage dispatch',
                'view journeys',
                'manage journeys',
                'view reports',
                'view compliance dashboard',
            ],
            'partner_dispatcher' => [
                'view bookings',
                'manage booking status',
                'accept bookings',
                'reject bookings',
                'assign vehicle',
                'assign driver',
                'dispatch bookings',
                'view vehicles',
                'view drivers',
                'view dispatch board',
                'manage dispatch',
                'view journeys',
                'manage journeys',
                'view compliance dashboard',
            ],
            'driver' => [
                'view bookings',
                'view vehicles',
            ],
            'partner_finance_officer' => [
                'view bookings',
                'view reports',
                'export reports',
                'view commissions',
                'view settlements',
            ],
            'partner_read_only_auditor' => [
                'view bookings',
                'view vehicles',
                'view drivers',
                'view journeys',
                'view reports',
                'view commissions',
                'view settlements',
                'view compliance dashboard',
            ],
            'patient' => [
                'submit booking',
                'view own bookings',
                'cancel own bookings',
            ],
            'caregiver' => [
                'submit booking',
                'view own bookings',
                'cancel own bookings',
            ],
            'family_member' => [
                'submit booking',
                'view own bookings',
                'cancel own bookings',
            ],
            'booking_contact' => [
                'submit booking',
                'view own bookings',
                'cancel own bookings',
            ],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }

        // Create Regia Mobility users
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@regia.test'],
            [
                'organisation_id' => $regia->id,
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => Hash::make($temporaryPassword),
                'phone' => '+27 82 123 4567',
                'role' => 'super_admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('super_admin');

        $opsAdmin = User::firstOrCreate(
            ['email' => 'opsadmin@regia.test'],
            [
                'organisation_id' => $regia->id,
                'first_name' => 'Ops',
                'last_name' => 'Admin',
                'password' => Hash::make($temporaryPassword),
                'phone' => '+27 82 234 5678',
                'role' => 'org_admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $opsAdmin->assignRole('org_admin');

        $dispatcher = User::firstOrCreate(
            ['email' => 'dispatcher@regia.test'],
            [
                'organisation_id' => $regia->id,
                'first_name' => 'Dispatch',
                'last_name' => 'Manager',
                'password' => Hash::make($temporaryPassword),
                'phone' => '+27 82 345 6789',
                'role' => 'dispatch_manager',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $dispatcher->assignRole('dispatch_manager');

        // Create SOS EMS users
        $partnerAdmin = User::firstOrCreate(
            ['email' => 'partneradmin@sos.test'],
            [
                'organisation_id' => $sos->id,
                'first_name' => 'Partner',
                'last_name' => 'Admin',
                'password' => Hash::make($temporaryPassword),
                'phone' => '+27 83 123 4567',
                'role' => 'partner_admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $partnerAdmin->assignRole('partner_admin');

        $partnerDispatcher = User::firstOrCreate(
            ['email' => 'dispatcher@sos.test'],
            [
                'organisation_id' => $sos->id,
                'first_name' => 'Partner',
                'last_name' => 'Dispatcher',
                'password' => Hash::make($temporaryPassword),
                'phone' => '+27 83 234 5678',
                'role' => 'partner_dispatcher',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $partnerDispatcher->assignRole('partner_dispatcher');

        $driver = User::firstOrCreate(
            ['email' => 'driver@sos.test'],
            [
                'organisation_id' => $sos->id,
                'first_name' => 'John',
                'last_name' => 'Driver',
                'password' => Hash::make($temporaryPassword),
                'phone' => '+27 83 345 6789',
                'role' => 'driver',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $driver->assignRole('driver');

        // Create driver record for the driver user
        \App\Models\Driver::firstOrCreate(
            ['user_id' => $driver->id],
            [
                'organisation_id' => $sos->id,
                'employee_number' => 'DRV001',
                'license_number' => 'DL123456789',
                'license_class' => 'C1',
                'license_expiry' => now()->addYears(5),
                'prdp_expiry' => now()->addYears(2),
                'medical_cert_expiry' => now()->addYear(),
                'background_check_expiry' => now()->addYears(2),
                'status' => 'active',
            ]
        );
    }
}