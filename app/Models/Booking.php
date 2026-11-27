<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'organisation_id',
        'partner_organisation_id',
        'patient_id',
        'booker_id',
        'journey_type',
        'status',
        'priority',
        'pickup_address',
        'pickup_lat',
        'pickup_lng',
        'pickup_contact_name',
        'pickup_contact_phone',
        'pickup_instructions',
        'destination_address',
        'destination_lat',
        'destination_lng',
        'destination_contact_name',
        'destination_contact_phone',
        'destination_instructions',
        'mobility_requirements',
        'medical_notes',
        'special_requirements',
        'requested_at',
        'scheduled_at',
        'estimated_pickup_at',
        'actual_pickup_at',
        'actual_dropoff_at',
        'fare_amount',
        'commission_rate',
        'commission_amount',
        'vat_amount',
        'total_amount',
        'payment_status',
        'payment_method',
        'payment_reference',
        'vehicle_id',
        'driver_id',
        'dispatched_at',
        'accepted_at',
        'created_by',
        'updated_by',
        'cancelled_by',
        'cancellation_reason',
        'metadata',
    ];

    protected $casts = [
        'pickup_lat' => 'decimal:8',
        'pickup_lng' => 'decimal:8',
        'destination_lat' => 'decimal:8',
        'destination_lng' => 'decimal:8',
        'mobility_requirements' => 'array',
        'metadata' => 'array',
        'medical_notes' => 'encrypted',
        'requested_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'estimated_pickup_at' => 'datetime',
        'actual_pickup_at' => 'datetime',
        'actual_dropoff_at' => 'datetime',
        'fare_amount' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'dispatched_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function partnerOrganisation()
    {
        return $this->belongsTo(Organisation::class, 'partner_organisation_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function booker()
    {
        return $this->belongsTo(User::class, 'booker_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    public function journey()
    {
        return $this->hasOne(Journey::class);
    }

    public function fareCalculation()
    {
        return $this->hasOne(FareCalculation::class);
    }

    public function commission()
    {
        return $this->hasOne(Commission::class);
    }

    public function dispatchAssignment()
    {
        return $this->hasOne(DispatchAssignment::class);
    }

    public function getReferenceAttribute(): string
    {
        $date = $this->requested_at ? $this->requested_at->format('Ymd') : now()->format('Ymd');

        // Use a random UUID segment instead of a sequential id so references
        // cannot be enumerated to discover other bookings.
        $token = $this->uuid
            ? strtoupper(substr((string) $this->uuid, 0, 6))
            : str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);

        return 'CL-' . $date . '-' . $token;
    }

    public function getReferenceNumberAttribute(): string
    {
        return $this->reference;
    }

    public function getQrCodeDataAttribute(): string
    {
        return route('booking.status', ['uuid' => $this->uuid]);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', \App\Support\BookingStatus::ACTIVE);
    }

    public function scopeForOrganisation($query, int $organisationId)
    {
        return $query->where('organisation_id', $organisationId);
    }
}