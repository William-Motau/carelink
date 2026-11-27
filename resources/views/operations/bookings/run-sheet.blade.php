@extends('layouts.operations')

@section('title', 'Run Sheet - {{ $booking->reference }}')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Run Sheet</h1>
            <p class="text-gray-600 mt-1">Booking {{ $booking->reference }}</p>
        </div>
        <button onclick="window.print()" class="btn-secondary px-4 py-2">
            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"/>
            </svg>
            Print
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
        <!-- Patient -->
        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Patient</h2>
            <p class="text-gray-700">{{ $booking->patient->first_name }} {{ $booking->patient->last_name }}</p>
            <p class="text-sm text-gray-500">{{ $booking->patient->phone }}</p>
        </div>

        <!-- Journey -->
        <div class="border-t border-gray-200 pt-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Journey</h2>
            <dl class="space-y-1 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Pickup:</dt><dd>{{ $booking->pickup_address }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Destination:</dt><dd>{{ $booking->destination_address }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Scheduled:</dt><dd>{{ $booking->scheduled_at ? $booking->scheduled_at->format('d M Y \a\t H:i') : 'Unscheduled' }}</dd></div>
            </dl>
        </div>

        <!-- Vehicle & Driver -->
        <div class="border-t border-gray-200 pt-4">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Assigned Resources</h2>
            <dl class="space-y-1 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Vehicle:</dt><dd>{{ $booking->vehicle->registration_number ?? 'Unassigned' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Driver:</dt><dd>{{ $booking->driver ? ($booking->driver->user->first_name . ' ' . $booking->driver->user->last_name) : 'Unassigned' }}</dd></div>
            </dl>
        </div>

        <!-- Requirements -->
        @if ($booking->mobility_requirements && count($booking->mobility_requirements) > 0)
            <div class="border-t border-gray-200 pt-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Requirements</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach ($booking->mobility_requirements as $req)
                        <span class="px-3 py-1 bg-primary-100 text-primary-700 text-xs rounded-full">{{ ucfirst(str_replace('_', ' ', $req)) }}</span>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection