<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organisation_id',
        'registration',
        'vin',
        'make',
        'model',
        'year',
        'vehicle_type',
        'capacity_seated',
        'capacity_wheelchair',
        'capacity_stretcher',
        'fuel_type',
        'license_expiry',
        'cofc_expiry',
        'insurance_expiry',
        'operating_permit_expiry',
        'service_due_km',
        'current_km',
        'status',
        'tracking_device_id',
        'gps_last_ping_at',
        'equipment',
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'cofc_expiry' => 'date',
        'insurance_expiry' => 'date',
        'operating_permit_expiry' => 'date',
        'gps_last_ping_at' => 'datetime',
        'equipment' => 'array',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function capabilities()
    {
        return $this->hasMany(VehicleCapability::class);
    }

    public function driver()
    {
        return $this->hasOne(Driver::class, 'current_vehicle_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function dispatchAssignments()
    {
        return $this->hasMany(DispatchAssignment::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function locations()
    {
        return $this->hasMany(VehicleLocation::class);
    }

    public function latestLocation()
    {
        return $this->hasOne(VehicleLocation::class)->latest('recorded_at');
    }

    public function getRegistrationNumberAttribute(): string
    {
        return (string) ($this->registration ?? '');
    }

    public function isCompliant(): bool
    {
        $now = now();
        return $this->license_expiry && $this->license_expiry->gt($now)
            && $this->cofc_expiry && $this->cofc_expiry->gt($now)
            && $this->insurance_expiry && $this->insurance_expiry->gt($now)
            && $this->operating_permit_expiry && $this->operating_permit_expiry->gt($now)
            && $this->status !== 'compliance_suspended';
    }

    public function getComplianceStatusAttribute(): string
    {
        if (!$this->isCompliant()) {
            return 'non_compliant';
        }

        $now = now();
        $daysUntilLicenseExpiry = $now->diffInDays($this->license_expiry, false);
        $daysUntilCoFCExpiry = $now->diffInDays($this->cofc_expiry, false);
        $daysUntilInsuranceExpiry = $now->diffInDays($this->insurance_expiry, false);
        $daysUntilPermitExpiry = $now->diffInDays($this->operating_permit_expiry, false);

        $minDays = min(
            $daysUntilLicenseExpiry ?? PHP_INT_MAX,
            $daysUntilCoFCExpiry ?? PHP_INT_MAX,
            $daysUntilInsuranceExpiry ?? PHP_INT_MAX,
            $daysUntilPermitExpiry ?? PHP_INT_MAX
        );

        if ($minDays <= 7) {
            return 'critical';
        } elseif ($minDays <= 14) {
            return 'warning';
        } elseif ($minDays <= 30) {
            return 'attention';
        }

        return 'compliant';
    }
}