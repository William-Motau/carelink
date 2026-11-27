<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Commission;
use App\Models\CommissionPayment;
use App\Support\BookingStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Generate a commission record for a completed booking.
     * Idempotent: returns the existing commission if one already exists.
     */
    public function generateForBooking(Booking $booking, ?float $rateOverride = null): Commission
    {
        return DB::transaction(function () use ($booking, $rateOverride) {
            $existing = Commission::where('booking_id', $booking->id)->first();
            if ($existing) {
                return $existing;
            }

            $rate = $rateOverride ?? (float) config('carelink.default_commission_rate', 10.00);
            $gross = (float) $booking->total_amount;
            $commissionAmount = round($gross * $rate / 100, 2);
            $partnerPayable = round($gross - $commissionAmount, 2);

            $commission = Commission::create([
                'booking_id' => $booking->id,
                'partner_organisation_id' => $booking->partner_organisation_id ?? $booking->organisation_id,
                'gross_amount' => $gross,
                'adjustments' => 0,
                'commission_rate' => $rate,
                'commission_amount' => $commissionAmount,
                'partner_payable' => $partnerPayable,
                'currency' => 'ZAR',
                'calculated_at' => now(),
            ]);

            $booking->update([
                'commission_rate' => $rate,
                'commission_amount' => $commissionAmount,
            ]);

            return $commission;
        });
    }

    /**
     * Generate commissions for all completed bookings that don't yet have one.
     *
     * @return int Number of commissions generated.
     */
    public function generateForCompletedBookings(?int $organisationId = null): int
    {
        $query = Booking::where('status', BookingStatus::COMPLETED)
            ->whereDoesntHave('commission');

        if ($organisationId) {
            $query->where('organisation_id', $organisationId);
        }

        $count = 0;
        $query->chunkById(100, function ($bookings) use (&$count) {
            foreach ($bookings as $booking) {
                $this->generateForBooking($booking);
                $count++;
            }
        });

        return $count;
    }

    /**
     * Resolve the [start, end] period for a payout frequency.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public function periodForFrequency(string $frequency, ?Carbon $now = null): array
    {
        $now = $now ?? Carbon::now();

        return match ($frequency) {
            'daily' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'weekly' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    /**
     * Commissions for a partner that are not yet attached to a payout.
     */
    public function outstandingCommissions(int $partnerOrganisationId, Carbon $start, Carbon $end)
    {
        return Commission::where('partner_organisation_id', $partnerOrganisationId)
            ->whereNull('commission_payment_id')
            ->whereBetween('calculated_at', [$start, $end])
            ->with('booking')
            ->get();
    }

    /**
     * Create (or return the existing pending) payout for the current period,
     * attaching all outstanding commissions to it.
     */
    public function createOrFindPayment(int $partnerOrganisationId, string $frequency, ?Carbon $now = null): CommissionPayment
    {
        [$start, $end] = $this->periodForFrequency($frequency, $now);

        $existing = CommissionPayment::where('partner_organisation_id', $partnerOrganisationId)
            ->where('status', 'pending')
            ->where('period_start', $start->toDateString())
            ->where('period_end', $end->toDateString())
            ->first();

        if ($existing) {
            return $existing;
        }

        $commissions = $this->outstandingCommissions($partnerOrganisationId, $start, $end);
        $amount = round((float) $commissions->sum('commission_amount'), 2);

        return DB::transaction(function () use ($partnerOrganisationId, $frequency, $start, $end, $amount, $commissions) {
            $payment = CommissionPayment::create([
                'partner_organisation_id' => $partnerOrganisationId,
                'period_start' => $start->toDateString(),
                'period_end' => $end->toDateString(),
                'frequency' => $frequency,
                'amount' => $amount,
                'currency' => 'ZAR',
                'status' => 'pending',
                'paypal_url' => config('carelink.paypal.payment_url'),
            ]);

            if ($commissions->isNotEmpty()) {
                Commission::whereIn('id', $commissions->pluck('id'))
                    ->update(['commission_payment_id' => $payment->id]);
            }

            return $payment;
        });
    }

    public function markPaid(CommissionPayment $payment, ?string $reference = null): void
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paypal_reference' => $reference,
        ]);
    }
}