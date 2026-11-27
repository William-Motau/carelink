<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingStatusHistory extends Model
{
    protected $fillable = [
        'booking_id',
        'previous_status',
        'new_status',
        'actor_id',
        'organisation_id',
        'reason',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
}