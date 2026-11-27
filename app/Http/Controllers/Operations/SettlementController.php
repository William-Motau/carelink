<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Settlement;
use App\Services\SettlementService;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function index(Request $request)
    {
        $organisation = auth()->user()->organisation;

        $query = Settlement::where('partner_organisation_id', $organisation->id)
            ->with('partnerOrganisation', 'generatedBy', 'approvedBy')
            ->withCount('items')
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $settlements = $query->paginate(20)->withQueryString();

        $totals = [
            'gross' => Settlement::where('partner_organisation_id', $organisation->id)->sum('total_gross'),
            'commission' => Settlement::where('partner_organisation_id', $organisation->id)->sum('total_commission'),
            'net' => Settlement::where('partner_organisation_id', $organisation->id)->sum('total_net'),
        ];

        return view('operations.settlements.index', compact('settlements', 'totals'));
    }

    public function generate(Request $request, SettlementService $service)
    {
        $request->validate([
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after:period_start'],
        ]);

        $settlement = $service->generate(
            auth()->user()->organisation_id,
            $request->period_start,
            $request->period_end,
            auth()->id()
        );

        return back()->with('success', "Settlement generated with {$settlement->items->count()} item(s). Total net: R" . number_format($settlement->total_net, 2));
    }

    public function approve(Settlement $settlement, SettlementService $service)
    {
        $this->authorizeFinance();
        abort_unless($settlement->partner_organisation_id === auth()->user()->organisation_id, 403);

        if ($settlement->status !== 'pending_approval') {
            return back()->with('error', 'Only settlements pending approval can be approved.');
        }

        $service->approve($settlement, auth()->id());

        return back()->with('success', 'Settlement approved.');
    }

    public function markPaid(Settlement $settlement, SettlementService $service)
    {
        $this->authorizeFinance();
        abort_unless($settlement->partner_organisation_id === auth()->user()->organisation_id, 403);

        if ($settlement->status !== 'approved' && $settlement->status !== 'partially_paid') {
            return back()->with('error', 'Only approved settlements can be marked as paid.');
        }

        $service->markPaid($settlement);

        return back()->with('success', 'Settlement marked as paid.');
    }

    /**
     * Only finance/admin roles may approve or pay settlements.
     */
    private function authorizeFinance(): void
    {
        abort_unless(
            in_array(auth()->user()->role, ['super_admin', 'org_admin', 'finance_officer'], true),
            403,
            'You are not allowed to approve or pay settlements.'
        );
    }
}