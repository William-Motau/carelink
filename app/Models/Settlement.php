<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settlement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'partner_organisation_id',
        'period_start',
        'period_end',
        'total_gross',
        'total_commission',
        'total_vat',
        'total_net',
        'status',
        'pdf_path',
        'csv_path',
        'generated_at',
        'sent_at',
        'paid_at',
        'generated_by',
        'approved_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'total_gross' => 'decimal:2',
        'total_commission' => 'decimal:2',
        'total_vat' => 'decimal:2',
        'total_net' => 'decimal:2',
        'generated_at' => 'datetime',
        'sent_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function partnerOrganisation()
    {
        return $this->belongsTo(Organisation::class, 'partner_organisation_id');
    }

    public function items()
    {
        return $this->hasMany(SettlementItem::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function transactions()
    {
        return $this->hasMany(FinancialTransaction::class);
    }
}