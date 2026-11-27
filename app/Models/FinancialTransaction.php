<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    protected $fillable = [
        'transaction_reference',
        'organisation_id',
        'partner_organisation_id',
        'booking_id',
        'settlement_id',
        'transaction_type',
        'direction',
        'amount',
        'currency',
        'balance_after',
        'payment_method',
        'payment_reference',
        'metadata',
        'created_by',
        'transacted_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
        'transacted_at' => 'datetime',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function partnerOrganisation()
    {
        return $this->belongsTo(Organisation::class, 'partner_organisation_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function settlement()
    {
        return $this->belongsTo(Settlement::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}