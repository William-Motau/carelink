<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettlementItem extends Model
{
    protected $fillable = [
        'settlement_id',
        'booking_id',
        'commission_id',
        'gross_amount',
        'commission_amount',
        'vat_amount',
        'net_amount',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
    ];

    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function commission()
    {
        return $this->belongsTo(Commission::class);
    }
}