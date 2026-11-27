<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organisation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'partner_category',
        'email',
        'contact_phone',
        'address',
        'settings',
        'is_active',
        'commission_frequency',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'organisation_id');
    }

    public function patients()
    {
        return $this->hasMany(Patient::class, 'organisation_id');
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'organisation_id');
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class, 'organisation_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'organisation_id');
    }

    public function partnerBookings()
    {
        return $this->hasMany(Booking::class, 'partner_organisation_id');
    }

    public function pricingRules()
    {
        return $this->hasMany(PricingRule::class, 'organisation_id');
    }

    public function settlements()
    {
        return $this->hasMany(Settlement::class, 'partner_organisation_id');
    }

    public function commissionPayments()
    {
        return $this->hasMany(CommissionPayment::class, 'partner_organisation_id');
    }
}