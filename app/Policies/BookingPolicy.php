<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    /**
     * Determine whether the user can view the booking.
     *
     * Operators may view bookings within their own organisation; patients and
     * contacts may only view bookings they created or are linked to.
     */
    public function view(User $user, Booking $booking): bool
    {
        if ($this->isOperator($user)) {
            return $user->organisation_id !== null
                && $booking->organisation_id === $user->organisation_id;
        }

        return $this->isOwner($user, $booking);
    }

    /**
     * Determine whether the user can update (act on) the booking.
     */
    public function update(User $user, Booking $booking): bool
    {
        return $this->view($user, $booking);
    }

    private function isOperator(User $user): bool
    {
        return in_array($user->role, [
            'super_admin',
            'org_admin',
            'dispatch_manager',
            'finance_officer',
            'compliance_officer',
            'read_only_auditor',
        ], true);
    }

    private function isOwner(User $user, Booking $booking): bool
    {
        return $booking->booker_id === $user->id
            || $booking->patient?->user_id === $user->id;
    }
}