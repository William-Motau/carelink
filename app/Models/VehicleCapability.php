<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleCapability extends Model
{
    protected $fillable = [
        'vehicle_id',
        'capability',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}