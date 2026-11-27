@extends('layouts.operations')

@section('title', 'Commission Payments')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Commission Payments</h1>
            <p class="text-gray-600 mt-1">Pay the platform 15% commission on every completed trip.</p>
        </div>
        <form method="POST" action="{{ route('partner.commissions.frequency') }}" class="flex items-center gap-2">
            @csrf
            @method('PATCH')
            <label for="frequency" class="text-sm text-gray-600">Payout frequency</label>
            <select id="frequency" name="frequency" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach(['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $value => $label)
                    <option value="{{ $value }}" @selected($frequency === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-secondary px-3 py-2 rounded-lg text-sm">Save</button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Outstanding commission ({{ ucfirst($frequency) }})</p>
                        <p class="text-3xl font-bold text-primary-600 mt-1">R{{ number_format($outstanding, 2) }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $commissions->count() }} completed trip(s) &middot;
                            {{ $start->format('d M Y') }} – {{ $end->format('d M Y') }}
                        </p>
                    </div>
                    <form method="POST" action="{{ route('partner.commissions.pay') }}">
                        @csrf
                        <button type="submit" class="btn-primary px-4 py-2 rounded-lg text-white font-medium" @disabled($outstanding <= 0)>
                            Pay via PayPal
                        </button>
                    </form>
                </div>

                @if($pending)
                    <div class="mt-4 rounded-lg bg-amber-50 border border-amber-200 p-4">
                        <p class="text-sm text-amber-800">
                            Pending payment of R{{ number_format($pending->amount, 2) }} for
                            {{ $pending->period_start->format('d M Y') }} – {{ $pending->period_end->format('d M Y') }}.
                        </p>
                        <form method="POST" action="{{ route('partner.commissions.paid', $pending) }}" class="mt-2 flex items-center gap-2">
                            @csrf
                            <input name="paypal_reference" placeholder="PayPal reference (optional)" class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm">
                            <button type="submit" class="btn-secondary px-3 py-1.5 rounded-lg text-sm">I have paid</button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">Completed Trips This Period</h2>
                </div>
                @if($commissions->isEmpty())
                    <p class="px-6 py-8 text-center text-gray-500">No completed trips in this period.</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium">Reference</th>
                                <th class="px-6 py-3 text-left font-medium">Completed</th>
                                <th class="px-6 py-3 text-right font-medium">Gross</th>
                                <th class="px-6 py-3 text-right font-medium">Commission (15%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($commissions as $commission)
                                <tr>
                                    <td class="px-6 py-3 font-mono">{{ $commission->booking?->reference }}</td>
                                    <td class="px-6 py-3 text-gray-600">{{ $commission->booking?->actual_dropoff_at?->format('d M Y H:i') ?? $commission->calculated_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-3 text-right">R{{ number_format((float) $commission->gross_amount, 2) }}</td>
                                    <td class="px-6 py-3 text-right font-medium">R{{ number_format((float) $commission->commission_amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Payment History</h2>
            </div>
            <ul class="divide-y divide-gray-100">
                @forelse($payments as $payment)
                    <li class="px-6 py-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-900">R{{ number_format((float) $payment->amount, 2) }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $payment->status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }} capitalize">{{ $payment->status }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $payment->period_start->format('d M Y') }} – {{ $payment->period_end->format('d M Y') }}</p>
                    </li>
                @empty
                    <li class="px-6 py-8 text-center text-gray-500 text-sm">No payments yet.</li>
                @endforelse
            </ul>
            <div class="px-6 py-3">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection