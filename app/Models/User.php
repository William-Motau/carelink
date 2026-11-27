<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'organisation_id',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'avatar_path',
        'role',
        'status',
        'consent_given_at',
        'consent_version',
        'data_retention_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'mfa_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'mfa_enabled' => 'boolean',
        'consent_given_at' => 'datetime',
        'data_retention_until' => 'datetime',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar_path)
            : null;
    }

    public function getInitialsAttribute(): string
    {
        $parts = array_filter([$this->first_name, $this->last_name]);

        return strtoupper(collect($parts)->map(fn ($p) => mb_substr($p, 0, 1))->implode(''));
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'org_admin', 'partner_admin']);
    }

    public function isDispatcher(): bool
    {
        return in_array($this->role, ['dispatch_manager', 'partner_dispatcher']);
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isPartner(): bool
    {
        return in_array($this->role, [
            'partner_admin',
            'partner_operations_manager',
            'partner_dispatcher',
            'partner_finance_officer',
            'partner_read_only_auditor',
        ], true);
    }

    public function driverProfile()
    {
        return $this->hasOne(Driver::class);
    }
}