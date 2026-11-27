@extends('layouts.operations')

@section('title', 'Compliance Center')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Compliance Center</h1>
        <p class="text-gray-600 mt-1">Track vehicle and driver document compliance (COFC, PrDP, Form MC)</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Expiring Soon</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['expiring'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Expired</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['expired'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Vehicles Compliant</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['vehicle_compliant'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Vehicles Non-Compliant</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['vehicle_non_compliant'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Drivers Compliant</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['driver_compliant'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500">Drivers Non-Compliant</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['driver_non_compliant'] }}</p>
        </div>
    </div>

    <!-- Expired Documents -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="p-4 border-b border-gray-200 bg-red-50 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Expired Documents</h2>
            <span class="px-2 py-0.5 text-xs font-bold bg-red-200 text-red-800 rounded-full">{{ $expiredDocs->count() }}</span>
        </div>
        @if ($expiredDocs->isEmpty())
            <p class="p-6 text-sm text-gray-500 text-center">No expired documents.</p>
        @else
            <div class="divide-y divide-gray-200">
                @foreach ($expiredDocs as $doc)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $doc->type)) }}</p>
                            <p class="text-xs text-gray-500">{{ class_basename($doc->documentable_type) }} #{{ $doc->documentable_id }} · {{ $doc->original_filename }}</p>
                        </div>
                        <span class="text-sm text-red-600 font-medium">Expired {{ $doc->expires_at->format('d M Y') }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Expiring Soon -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200 bg-yellow-50 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">Expiring Within 30 Days</h2>
            <span class="px-2 py-0.5 text-xs font-bold bg-yellow-200 text-yellow-800 rounded-full">{{ $expiringDocs->count() }}</span>
        </div>
        @if ($expiringDocs->isEmpty())
            <p class="p-6 text-sm text-gray-500 text-center">No documents expiring soon.</p>
        @else
            <div class="divide-y divide-gray-200">
                @foreach ($expiringDocs as $doc)
                    <div class="p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $doc->type)) }}</p>
                            <p class="text-xs text-gray-500">{{ class_basename($doc->documentable_type) }} #{{ $doc->documentable_id }} · {{ $doc->original_filename }}</p>
                        </div>
                        <span class="text-sm text-yellow-600 font-medium">Expires {{ $doc->expires_at->format('d M Y') }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection