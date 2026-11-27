<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Services\CommissionService;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        $organisation = auth()->user()->organisation;

        $query = Commission::where('partner_organisation_id', $organisation->id)
            ->with('booking.patient', 'partnerOrganisation', 'settlementItems')
            ->latest('calculated_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('booking', function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%");
            });
        }

        $commissions = $query->paginate(25)->withQueryString();

        $totals = [
            'gross' => Commission::where('partner_organisation_id', $organisation->id)->sum('gross_amount'),
            'commission' => Commission::where('partner_organisation_id', $organisation->id)->sum('commission_amount'),
            'payable' => Commission::where('partner_organisation_id', $organisation->id)->sum('partner_payable'),
        ];

        return view('operations.commissions.index', compact('commissions', 'totals'));
    }

    public function generate(Request $request, CommissionService $service)
    {
        $count = $service->generateForCompletedBookings(auth()->user()->organisation_id);

        return back()->with('success', "Generated {$count} commission(s) for completed bookings.");
    }
}