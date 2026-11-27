<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'title',
        'body',
        'data',
        'channel',
        'status',
        'sent_at',
        'read_at',
        'failure_reason',
        'retry_count',
    ];

    protected $casts = [
        'data' => 'array',
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }
}