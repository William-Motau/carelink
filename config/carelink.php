<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CareLink Application Configuration
    |--------------------------------------------------------------------------
    */

    // The default organisation that patient bookings are attributed to.
    // Resolved from the seeded "regia-mobility" organisation.
    'regia_org_id' => env('CARELINK_REGIA_ORG_ID', 1),

    // Default organisation slug used to resolve the regia org id at runtime.
    'regia_org_slug' => env('CARELINK_REGIA_ORG_SLUG', 'regia-mobility'),

    // VAT rate applied to fares (South Africa = 15%).
    'vat_rate' => env('CARELINK_VAT_RATE', 0.15),

    // Default commission rate (percentage) applied to partner bookings.
    'default_commission_rate' => env('CARELINK_COMMISSION_RATE', 15.00),

    // Platform payout via PayPal (paid by partners for completed trips).
    'paypal' => [
        'payment_url' => env('PAYPAL_PAYMENT_URL', 'https://www.paypal.com/ncp/payment/CE4DWHSRLVFDA'),
    ],

    // Fare calculation options.
    'fare' => [
        // When true, a booking cannot be submitted without pickup/destination
        // coordinates (prevents forcing the default distance-based fare).
        'require_coordinates' => env('FARE_REQUIRE_COORDINATES', false),
        'default_distance_km' => 10.0,
    ],

    // Booking reference prefix.
    'reference_prefix' => 'CL',

    // Cancellation window (hours before scheduled pickup).
    'cancellation_window_hours' => env('CARELINK_CANCELLATION_HOURS', 2),

    // Compliance warning thresholds (days before expiry).
    'compliance_warning_days' => 30,
    'compliance_critical_days' => 7,

    // Notification channels.
    'notifications' => [
        'sms_enabled' => env('CARELINK_SMS_ENABLED', false),
        'sms_driver' => env('CARELINK_SMS_DRIVER', 'log'),
        'email_enabled' => env('CARELINK_EMAIL_ENABLED', true),
    ],
];