<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityIncident extends Model
{
    protected $fillable = [
        'incident_type',
        'severity',
        'description',
        'affected_data',
        'affected_users',
        'status',
        'reported_by',
        'assigned_to',
        'detected_at',
        'contained_at',
        'resolved_at',
    ];

    protected $casts = [
        'affected_data' => 'array',
        'affected_users' => 'array',
        'detected_at' => 'datetime',
        'contained_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}