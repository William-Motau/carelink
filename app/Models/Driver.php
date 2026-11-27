<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organisation_id',
        'user_id',
        'employee_number',
        'license_number',
        'license_class',
        'license_expiry',
        'prdp_expiry',
        'medical_cert_expiry',
        'background_check_expiry',
        'training_certificates',
        'status',
        'availability_schedule',
        'current_vehicle_id',
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'prdp_expiry' => 'date',
        'medical_cert_expiry' => 'date',
        'background_check_expiry' => 'date',
        'training_certificates' => 'array',
        'availability_schedule' => 'array',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currentVehicle()
    {
        return $this->belongsTo(Vehicle::class, 'current_vehicle_id');
    }

    public function dispatchAssignments()
    {
        return $this->hasMany(DispatchAssignment::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function hasQualification(string $qualification): bool
    {
        $certificates = $this->training_certificates ?? [];
        if (is_array($certificates)) {
            return in_array($qualification, $certificates, true)
                || in_array(strtolower($qualification), array_map('strtolower', $certificates), true);
        }
        return false;
    }

    public function getHasStretcherQualificationAttribute(): bool
    {
        return $this->hasQualification('stretcher');
    }

    public function getHasWheelchairQualificationAttribute(): bool
    {
        return $this->hasQualification('wheelchair');
    }

    public function getHasOxygenQualificationAttribute(): bool
    {
        return $this->hasQualification('oxygen');
    }

    public function isCompliant(): bool
    {
        $now = now();
        return $this->license_expiry && $this->license_expiry->gt($now)
            && (!$this->prdp_expiry || $this->prdp_expiry->gt($now))
            && (!$this->medical_cert_expiry || $this->medical_cert_expiry->gt($now))
            && (!$this->background_check_expiry || $this->background_check_expiry->gt($now))
            && $this->status === 'active';
    }

    public function getComplianceStatusAttribute(): string
    {
        if (!$this->isCompliant()) {
            return 'non_compliant';
        }

        $now = now();
        $daysUntilLicenseExpiry = $now->diffInDays($this->license_expiry, false);
        $daysUntilPrdpExpiry = $this->prdp_expiry ? $now->diffInDays($this->prdp_expiry, false) : PHP_INT_MAX;
        $daysUntilMedicalExpiry = $this->medical_cert_expiry ? $now->diffInDays($this->medical_cert_expiry, false) : PHP_INT_MAX;
        $daysUntilBackgroundExpiry = $this->background_check_expiry ? $now->diffInDays($this->background_check_expiry, false) : PHP_INT_MAX;

        $minDays = min(
            $daysUntilLicenseExpiry ?? PHP_INT_MAX,
            $daysUntilPrdpExpiry ?? PHP_INT_MAX,
            $daysUntilMedicalExpiry ?? PHP_INT_MAX,
            $daysUntilBackgroundExpiry ?? PHP_INT_MAX
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