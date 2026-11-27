<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Organisation;
use App\Models\Settlement;
use App\Models\SettlementItem;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettlementService
{
    /**
     * Generate a settlement for a partner organisation over a period.
     * Groups all unsettled commissions in the period into a single settlement.
     */
    public function generate(int $partnerOrganisationId, string $periodStart, string $periodEnd, ?int $generatedBy = null): Settlement
    {
        $lockKey = "settlement:{$partnerOrganisationId}:{$periodStart}:{$periodEnd}";
        $lock = Cache::lock($lockKey, 10);

        $lock->block(5, function () use ($partnerOrganisationId, $periodStart, $periodEnd, $generatedBy) {
            $this->generateLocked($partnerOrganisationId, $periodStart, $periodEnd, $generatedBy);
        });

        return Settlement::where('partner_organisation_id', $partnerOrganisationId)
            ->where('period_start', $periodStart)
            ->where('period_end', $periodEnd)
            ->with('items')
            ->latest('id')
            ->firstOrFail();
    }

    private function generateLocked(int $partnerOrganisationId, string $periodStart, string $periodEnd, ?int $generatedBy): void
    {
        DB::transaction(function () use ($partnerOrganisationId, $periodStart, $periodEnd, $generatedBy) {
            $commissions = Commission::where('partner_organisation_id', $partnerOrganisationId)
                ->whereBetween('calculated_at', [$periodStart . ' 00:00:00', $periodEnd . ' 23:59:59'])
                ->whereDoesntHave('settlementItems')
                ->with('booking')
                ->get();

            $totalGross = 0.0;
            $totalCommission = 0.0;
            $totalVat = 0.0;
            $totalNet = 0.0;

            $settlement = Settlement::create([
                'partner_organisation_id' => $partnerOrganisationId,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'total_gross' => 0,
                'total_commission' => 0,
                'total_vat' => 0,
                'total_net' => 0,
                'status' => 'draft',
                'generated_at' => now(),
                'generated_by' => $generatedBy,
            ]);

            foreach ($commissions as $commission) {
                $gross = (float) $commission->gross_amount;
                $commissionAmount = (float) $commission->commission_amount;
                $vat = $commission->booking ? (float) $commission->booking->vat_amount : 0.0;
                $net = (float) $commission->partner_payable;

                SettlementItem::create([
                    'settlement_id' => $settlement->id,
                    'booking_id' => $commission->booking_id,
                    'commission_id' => $commission->id,
                    'gross_amount' => $gross,
                    'commission_amount' => $commissionAmount,
                    'vat_amount' => $vat,
                    'net_amount' => $net,
                ]);

                $totalGross += $gross;
                $totalCommission += $commissionAmount;
                $totalVat += $vat;
                $totalNet += $net;
            }

            $settlement->update([
                'total_gross' => round($totalGross, 2),
                'total_commission' => round($totalCommission, 2),
                'total_vat' => round($totalVat, 2),
                'total_net' => round($totalNet, 2),
                'status' => 'pending_approval',
            ]);

            return $settlement->fresh('items');
        });
    }

    public function approve(Settlement $settlement, int $approvedBy): void
    {
        $settlement->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
        ]);
    }

    public function markPaid(Settlement $settlement): void
    {
        $settlement->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}