<?php

namespace App\Support;

class BookingStatus
{
    public const PENDING = 'pending';
    public const AWAITING_PARTNER_ACCEPTANCE = 'awaiting_partner_acceptance';
    public const ACCEPTED = 'accepted';
    public const REJECTED = 'rejected';
    public const AWAITING_VEHICLE_ASSIGNMENT = 'awaiting_vehicle_assignment';
    public const VEHICLE_ASSIGNED = 'vehicle_assigned';
    public const DRIVER_ASSIGNED = 'driver_assigned';
    public const CONFIRMED = 'confirmed';
    public const DISPATCHED = 'dispatched';
    public const EN_ROUTE_TO_PICKUP = 'en_route_to_pickup';
    public const PATIENT_ONBOARD = 'patient_onboard';
    public const IN_TRANSIT = 'in_transit';
    public const ARRIVED_AT_DESTINATION = 'arrived_at_destination';
    public const COMPLETED = 'completed';
    public const CANCELLED = 'cancelled';
    public const FAILED = 'failed';
    public const NO_SHOW = 'no_show';

    public const ALL = [
        self::PENDING,
        self::AWAITING_PARTNER_ACCEPTANCE,
        self::ACCEPTED,
        self::REJECTED,
        self::AWAITING_VEHICLE_ASSIGNMENT,
        self::VEHICLE_ASSIGNED,
        self::DRIVER_ASSIGNED,
        self::CONFIRMED,
        self::DISPATCHED,
        self::EN_ROUTE_TO_PICKUP,
        self::PATIENT_ONBOARD,
        self::IN_TRANSIT,
        self::ARRIVED_AT_DESTINATION,
        self::COMPLETED,
        self::CANCELLED,
        self::FAILED,
        self::NO_SHOW,
    ];

    public const TERMINAL = [
        self::COMPLETED,
        self::CANCELLED,
        self::REJECTED,
        self::FAILED,
        self::NO_SHOW,
    ];

    public const ACTIVE = [
        self::PENDING,
        self::AWAITING_PARTNER_ACCEPTANCE,
        self::ACCEPTED,
        self::AWAITING_VEHICLE_ASSIGNMENT,
        self::VEHICLE_ASSIGNED,
        self::DRIVER_ASSIGNED,
        self::CONFIRMED,
        self::DISPATCHED,
        self::EN_ROUTE_TO_PICKUP,
        self::PATIENT_ONBOARD,
        self::IN_TRANSIT,
        self::ARRIVED_AT_DESTINATION,
    ];

    public const IN_TRANSIT_STATUSES = [
        self::DISPATCHED,
        self::EN_ROUTE_TO_PICKUP,
        self::PATIENT_ONBOARD,
        self::IN_TRANSIT,
        self::ARRIVED_AT_DESTINATION,
    ];

    public const AWAITING_DISPATCH = [
        self::ACCEPTED,
        self::AWAITING_VEHICLE_ASSIGNMENT,
        self::VEHICLE_ASSIGNED,
        self::DRIVER_ASSIGNED,
        self::CONFIRMED,
    ];

    /**
     * The canonical forward progression of a booking.
     */
    public const FLOW = [
        self::PENDING,
        self::AWAITING_PARTNER_ACCEPTANCE,
        self::ACCEPTED,
        self::AWAITING_VEHICLE_ASSIGNMENT,
        self::VEHICLE_ASSIGNED,
        self::DRIVER_ASSIGNED,
        self::CONFIRMED,
        self::DISPATCHED,
        self::EN_ROUTE_TO_PICKUP,
        self::PATIENT_ONBOARD,
        self::IN_TRANSIT,
        self::ARRIVED_AT_DESTINATION,
        self::COMPLETED,
    ];

    /**
     * Allowed transitions from a given status.
     */
    public static function allowedTransitions(string $from): array
    {
        return match ($from) {
            self::PENDING => [self::AWAITING_PARTNER_ACCEPTANCE, self::ACCEPTED, self::REJECTED, self::CANCELLED],
            self::AWAITING_PARTNER_ACCEPTANCE => [self::ACCEPTED, self::REJECTED, self::CANCELLED],
            self::ACCEPTED => [self::AWAITING_VEHICLE_ASSIGNMENT, self::CANCELLED, self::REJECTED],
            self::AWAITING_VEHICLE_ASSIGNMENT => [self::VEHICLE_ASSIGNED, self::CANCELLED],
            self::VEHICLE_ASSIGNED => [self::DRIVER_ASSIGNED, self::AWAITING_VEHICLE_ASSIGNMENT, self::CANCELLED],
            self::DRIVER_ASSIGNED => [self::CONFIRMED, self::DISPATCHED, self::CANCELLED],
            self::CONFIRMED => [self::DISPATCHED, self::CANCELLED],
            self::DISPATCHED => [self::EN_ROUTE_TO_PICKUP, self::CANCELLED, self::NO_SHOW, self::FAILED],
            self::EN_ROUTE_TO_PICKUP => [self::PATIENT_ONBOARD, self::NO_SHOW, self::FAILED, self::CANCELLED],
            self::PATIENT_ONBOARD => [self::IN_TRANSIT, self::FAILED],
            self::IN_TRANSIT => [self::ARRIVED_AT_DESTINATION, self::FAILED],
            self::ARRIVED_AT_DESTINATION => [self::COMPLETED],
            default => [],
        };
    }

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::allowedTransitions($from), true);
    }

    public static function isTerminal(string $status): bool
    {
        return in_array($status, self::TERMINAL, true);
    }

    public static function isActive(string $status): bool
    {
        return in_array($status, self::ACTIVE, true);
    }

    public static function label(string $status): string
    {
        return ucfirst(str_replace('_', ' ', $status));
    }

    public static function badgeClasses(string $status): string
    {
        return match ($status) {
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::AWAITING_PARTNER_ACCEPTANCE => 'bg-blue-100 text-blue-800',
            self::ACCEPTED, self::CONFIRMED => 'bg-green-100 text-green-800',
            self::AWAITING_VEHICLE_ASSIGNMENT, self::VEHICLE_ASSIGNED, self::DRIVER_ASSIGNED => 'bg-orange-100 text-orange-800',
            self::DISPATCHED => 'bg-purple-100 text-purple-800',
            self::EN_ROUTE_TO_PICKUP => 'bg-indigo-100 text-indigo-800',
            self::PATIENT_ONBOARD => 'bg-teal-100 text-teal-800',
            self::IN_TRANSIT => 'bg-cyan-100 text-cyan-800',
            self::ARRIVED_AT_DESTINATION => 'bg-sky-100 text-sky-800',
            self::COMPLETED => 'bg-gray-100 text-gray-800',
            self::CANCELLED, self::REJECTED, self::FAILED, self::NO_SHOW => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}