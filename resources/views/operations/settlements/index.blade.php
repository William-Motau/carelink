@extends('layouts.operations')

@section('title', 'Settlements')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Settlements</h1>
            <p class="text-gray-600 mt-1">Review, approve, and process partner settlements</p>
        </div>
        <button type="button" onclick="document.getElementById('generateModal').classList.remove('hidden')" class="btn-primary px-4 py-2">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Generate Settlement
        </button>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <!-- Totals -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Gross</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">R{{ number_format($totals['gross'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Commission</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">R{{ number_format($totals['commission'], 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Net</p>
            <p class="text-2xl font-bold text-green-600 mt-1">R{{ number_format($totals['net'], 2) }}</p>
        </div>
    </div>

    <!-- Status Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row items-end gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Statuses</option>
                    @foreach (['draft', 'pending_approval', 'approved', 'partially_paid', 'paid', 'cancelled'] as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary px-4 py-2">Apply</button>
            <a href="{{ route('operations.settlements.index') }}" class="btn-secondary px-4 py-2">Clear</a>
        </form>
    </div>

    <!-- Settlements Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if ($settlements->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No settlements found</h3>
                <p class="text-gray-500 mb-4">Generate a settlement batch for a period to group unsettled commissions.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gross</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">VAT</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Net</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($settlements as $settlement)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $settlement->period_start->format('d M Y') }} – {{ $settlement->period_end->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-500">{{ $settlement->items_count ?? $settlement->items()->count() }} item(s)</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        {{ $settlement->status === 'paid' ? 'bg-green-100 text-green-800' :
                                           ($settlement->status === 'approved' ? 'bg-blue-100 text-blue-800' :
                                           ($settlement->status === 'partially_paid' ? 'bg-indigo-100 text-indigo-800' :
                                           ($settlement->status === 'pending_approval' ? 'bg-yellow-100 text-yellow-800' :
                                           ($settlement->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                                           'bg-gray-100 text-gray-800')))) }}">
                                        {{ ucfirst(str_replace('_', ' ', $settlement->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm text-gray-900">R{{ number_format($settlement->total_gross, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm text-orange-600">R{{ number_format($settlement->total_commission, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-600">R{{ number_format($settlement->total_vat, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-green-600">R{{ number_format($settlement->total_net, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $settlement->approvedBy->first_name ?? '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($settlement->status === 'pending_approval')
                                            <form method="POST" action="{{ route('operations.settlements.approve', $settlement) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-sm text-green-600 hover:underline">Approve</button>
                                            </form>
                                        @endif
                                        @if (in_array($settlement->status, ['approved', 'partially_paid']))
                                            <form method="POST" action="{{ route('operations.settlements.mark-paid', $settlement) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-sm text-blue-600 hover:underline">Mark Paid</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $settlements->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Generate Settlement Modal -->
<div id="generateModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Generate Settlement</h3>
            <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('operations.settlements.generate') }}" class="px-6 py-4 space-y-4">
            @csrf
            <div>
                <label for="period_start" class="block text-sm font-medium text-gray-700 mb-1">Period Start</label>
                <input type="date" id="period_start" name="period_start" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="period_end" class="block text-sm font-medium text-gray-700 mb-1">Period End</label>
                <input type="date" id="period_end" name="period_end" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <p class="text-xs text-gray-500">Groups all unsettled commissions calculated within this period into a single batch.</p>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('generateModal').classList.add('hidden')" class="btn-secondary px-4 py-2">Cancel</button>
                <button type="submit" class="btn-primary px-4 py-2">Generate</button>
            </div>
        </form>
    </div>
</div>
@endsection