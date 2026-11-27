<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'organisation_id',
        'first_name',
        'last_name',
        'id_number',
        'date_of_birth',
        'phone',
        'email',
        'mobility_requirements',
        'medical_notes',
        'special_requirements',
        'emergency_contacts',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'mobility_requirements' => 'array',
        'special_requirements' => 'array',
        'emergency_contacts' => 'array',
        'medical_notes' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function contacts()
    {
        return $this->hasMany(PatientContact::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}