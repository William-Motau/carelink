<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataSubjectRequest extends Model
{
    protected $fillable = [
        'request_type',
        'user_id',
        'email',
        'description',
        'status',
        'requested_data',
        'response_data',
        'processed_by',
        'completed_at',
    ];

    protected $casts = [
        'requested_data' => 'array',
        'response_data' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}