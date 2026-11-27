<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingRuleVersion extends Model
{
    protected $fillable = [
        'pricing_rule_id',
        'version',
        'rule_snapshot',
        'created_by',
    ];

    protected $casts = [
        'rule_snapshot' => 'array',
    ];

    public function pricingRule()
    {
        return $this->belongsTo(PricingRule::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}