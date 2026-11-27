<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journey extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'journey_reference',
        'booking_id',
        'organisation_id',
        'vehicle_id',
        'driver_id',
        'scheduled_pickup_at',
        'actual_pickup_at',
        'journey_start_at',
        'destination_arrival_at',
        'completed_at',
        'journey_status',
        'operational_notes',
        'exception_records',
    ];

    protected $casts = [
        'scheduled_pickup_at' => 'datetime',
        'actual_pickup_at' => 'datetime',
        'journey_start_at' => 'datetime',
        'destination_arrival_at' => 'datetime',
        'completed_at' => 'datetime',
        'exception_records' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function events()
    {
        return $this->hasMany(JourneyEvent::class);
    }

    public function getReferenceNumberAttribute(): string
    {
        return 'JR-' . $this->created_at->format('Ymd') . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}