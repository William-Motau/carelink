<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionPayment extends Model
{
    protected $fillable = [
        'partner_organisation_id',
        'period_start',
        'period_end',
        'frequency',
        'amount',
        'currency',
        'status',
        'paypal_url',
        'paypal_reference',
        'paid_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function partnerOrganisation()
    {
        return $this->belongsTo(Organisation::class, 'partner_organisation_id');
    }

    public function commissions()
    {
        return $this->hasMany(Commission::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
}