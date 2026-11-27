<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FareCalculation extends Model
{
    protected $fillable = [
        'booking_id',
        'calculation_details',
        'base_fare',
        'distance_charge',
        'additional_passenger_charge',
        'special_vehicle_charge',
        'additional_stop_charge',
        'waiting_charge',
        'surcharges',
        'manual_adjustments',
        'subtotal',
        'vat_amount',
        'total_amount',
        'pricing_rule_version',
        'calculated_by',
        'calculated_at',
    ];

    protected $casts = [
        'calculation_details' => 'array',
        'base_fare' => 'decimal:2',
        'distance_charge' => 'decimal:2',
        'additional_passenger_charge' => 'decimal:2',
        'special_vehicle_charge' => 'decimal:2',
        'additional_stop_charge' => 'decimal:2',
        'waiting_charge' => 'decimal:2',
        'surcharges' => 'decimal:2',
        'manual_adjustments' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'calculated_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function calculatedBy()
    {
        return $this->belongsTo(User::class, 'calculated_by');
    }
}