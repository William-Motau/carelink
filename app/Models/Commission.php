<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'booking_id',
        'partner_organisation_id',
        'commission_payment_id',
        'gross_amount',
        'adjustments',
        'commission_rate',
        'commission_amount',
        'partner_payable',
        'currency',
        'pricing_rule_version_id',
        'calculated_at',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'adjustments' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'partner_payable' => 'decimal:2',
        'calculated_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function partnerOrganisation()
    {
        return $this->belongsTo(Organisation::class, 'partner_organisation_id');
    }

    public function pricingRuleVersion()
    {
        return $this->belongsTo(PricingRuleVersion::class);
    }

    public function settlementItems()
    {
        return $this->hasMany(SettlementItem::class);
    }

    public function payment()
    {
        return $this->belongsTo(CommissionPayment::class, 'commission_payment_id');
    }
}