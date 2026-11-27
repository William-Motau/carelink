<?php

namespace Tests\Unit;

use App\Support\BookingStatus;
use Tests\TestCase;

class BookingStatusTest extends TestCase
{
    public function test_pending_can_progress_to_awaiting_partner_acceptance(): void
    {
        $this->assertTrue(BookingStatus::canTransition(BookingStatus::PENDING, BookingStatus::AWAITING_PARTNER_ACCEPTANCE));
    }

    public function test_pending_cannot_jump_to_completed(): void
    {
        $this->assertFalse(BookingStatus::canTransition(BookingStatus::PENDING, BookingStatus::COMPLETED));
    }

    public function test_dispatched_can_transition_to_en_route_to_pickup(): void
    {
        $this->assertTrue(BookingStatus::canTransition(BookingStatus::DISPATCHED, BookingStatus::EN_ROUTE_TO_PICKUP));
    }

    public function test_dispatched_can_be_cancelled(): void
    {
        $this->assertTrue(BookingStatus::canTransition(BookingStatus::DISPATCHED, BookingStatus::CANCELLED));
    }

    public function test_completed_is_terminal_and_has_no_transitions(): void
    {
        $this->assertTrue(BookingStatus::isTerminal(BookingStatus::COMPLETED));
        $this->assertSame([], BookingStatus::allowedTransitions(BookingStatus::COMPLETED));
    }

    public function test_cancelled_is_terminal(): void
    {
        $this->assertTrue(BookingStatus::isTerminal(BookingStatus::CANCELLED));
    }

    public function test_active_statuses_exclude_terminal_ones(): void
    {
        $this->assertTrue(BookingStatus::isActive(BookingStatus::DISPATCHED));
        $this->assertFalse(BookingStatus::isActive(BookingStatus::COMPLETED));
        $this->assertFalse(BookingStatus::isActive(BookingStatus::CANCELLED));
    }

    public function test_full_happy_path_flow_is_walkable(): void
    {
        $flow = BookingStatus::FLOW;
        for ($i = 0; $i < count($flow) - 1; $i++) {
            $this->assertTrue(
                BookingStatus::canTransition($flow[$i], $flow[$i + 1]),
                "Expected transition {$flow[$i]} -> {$flow[$i + 1]} to be allowed"
            );
        }
    }

    public function test_label_formats_underscored_status(): void
    {
        $this->assertSame('En route to pickup', BookingStatus::label(BookingStatus::EN_ROUTE_TO_PICKUP));
    }
}