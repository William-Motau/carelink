<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchAssignment extends Model
{
    protected $fillable = [
        'booking_id',
        'vehicle_id',
        'driver_id',
        'assigned_by',
        'assigned_at',
        'acknowledged_at',
        'released_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}