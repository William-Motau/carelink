<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsentRecord extends Model
{
    protected $fillable = [
        'user_id',
        'consent_type',
        'version',
        'granted',
        'metadata',
        'ip_address',
        'user_agent',
        'granted_at',
        'withdrawn_at',
    ];

    protected $casts = [
        'granted' => 'boolean',
        'metadata' => 'array',
        'granted_at' => 'datetime',
        'withdrawn_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}