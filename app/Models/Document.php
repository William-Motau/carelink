<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'type',
        'file_path',
        'original_filename',
        'mime_type',
        'size',
        'expires_at',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'expires_at' => 'date',
        'verified_at' => 'datetime',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return now()->gt($this->expires_at);
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return now()->addDays($days)->gte($this->expires_at);
    }
}