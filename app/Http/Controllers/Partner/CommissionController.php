<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\CommissionPayment;
use App\Services\CommissionService;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index(CommissionService $service)
    {
        $organisation = auth()->user()->organisation;
        $frequency = $organisation->commission_frequency ?? 'monthly';

        [$start, $end] = $service->periodForFrequency($frequency);
        $commissions = $service->outstandingCommissions($organisation->id, $start, $end);
        $outstanding = round((float) $commissions->sum('commission_amount'), 2);

        $pending = CommissionPayment::where('partner_organisation_id', $organisation->id)
            ->where('status', 'pending')
            ->first();

        $payments = CommissionPayment::where('partner_organisation_id', $organisation->id)
            ->latest()
            ->paginate(10);

        return view('partner.commissions.index', compact(
            'frequency', 'start', 'end', 'commissions', 'outstanding', 'pending', 'payments'
        ));
    }

    public function updateFrequency(Request $request)
    {
        $data = $request->validate([
            'frequency' => ['required', 'in:daily,weekly,monthly'],
        ]);

        auth()->user()->organisation->update(['commission_frequency' => $data['frequency']]);

        return back()->with('success', 'Payout frequency updated.');
    }

    public function pay(CommissionService $service)
    {
        $organisation = auth()->user()->organisation;

        $payment = $service->createOrFindPayment(
            $organisation->id,
            $organisation->commission_frequency ?? 'monthly'
        );

        $url = $payment->paypal_url ?: config('carelink.paypal.payment_url');

        return redirect()->away($url);
    }

    public function markPaid(Request $request, CommissionPayment $payment, CommissionService $service)
    {
        abort_unless($payment->partner_organisation_id === auth()->user()->organisation_id, 403);

        $service->markPaid($payment, $request->input('paypal_reference'));

        return back()->with('success', 'Commission payment recorded as paid.');
    }
}