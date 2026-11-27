@extends('layouts.operations')

@section('title', 'Commissions')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Commissions</h1>
            <p class="text-gray-600 mt-1">Partner commission tracking and calculations</p>
        </div>
        <form method="POST" action="{{ route('operations.commissions.generate') }}">
            @csrf
            <button type="submit" class="btn-primary px-4 py-2">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Generate from Completed
            </button>
        </form>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('success') }}</div>
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
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Partner Payable</p>
            <p class="text-2xl font-bold text-green-600 mt-1">R{{ number_format($totals['payable'], 2) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row items-end gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Booking reference..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="submit" class="btn-primary px-4 py-2">Apply</button>
            <a href="{{ route('operations.commissions.index') }}" class="btn-secondary px-4 py-2">Clear</a>
        </form>
    </div>

    <!-- Commissions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if ($commissions->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No commissions yet</h3>
                <p class="text-gray-500 mb-4">Commission records appear here as journeys are completed. Use "Generate from Completed" to create them.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Calculated</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Gross</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Payable</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Settled</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($commissions as $commission)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    @if ($commission->booking)
                                        <a href="{{ route('operations.bookings.show', $commission->booking) }}" class="font-mono font-medium text-primary-600 hover:underline">{{ $commission->booking->reference }}</a>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $commission->booking->patient->first_name ?? '—' }} {{ $commission->booking->patient->last_name ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $commission->calculated_at ? $commission->calculated_at->format('d M Y') : '—' }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-900">R{{ number_format($commission->gross_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-600">{{ rtrim(rtrim(number_format($commission->commission_rate, 2), '0'), '.') }}%</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-orange-600">R{{ number_format($commission->commission_amount, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-green-600">R{{ number_format($commission->partner_payable, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if ($commission->settlementItems->isNotEmpty())
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Settled</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $commissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection