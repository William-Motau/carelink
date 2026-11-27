<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    protected $fillable = [
        'organisation_id',
        'name',
        'code',
        'rule_type',
        'value',
        'value_type',
        'conditions',
        'priority',
        'is_active',
        'effective_from',
        'effective_until',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'conditions' => 'array',
        'effective_from' => 'date',
        'effective_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function versions()
    {
        return $this->hasMany(PricingRuleVersion::class);
    }
}