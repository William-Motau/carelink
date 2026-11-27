<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientContact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id',
        'user_id',
        'first_name',
        'last_name',
        'relationship',
        'phone',
        'email',
        'is_primary',
        'can_book',
        'can_view_status',
        'receives_notifications',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'can_book' => 'boolean',
        'can_view_status' => 'boolean',
        'receives_notifications' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}