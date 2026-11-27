@extends('layouts.operations')

@section('title', 'Reports')

@section('content')
<div class="p-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
            <p class="text-gray-600 mt-1">Operational summary for your organisation.</p>
        </div>
        <a href="{{ route('operations.reports.export') }}" class="btn-primary px-4 py-2 rounded-lg text-white font-medium">Export CSV</a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-sm text-gray-500">Total Bookings</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-sm text-gray-500">Completed</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ number_format($stats['completed']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-sm text-gray-500">Cancelled</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ number_format($stats['cancelled']) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <p class="text-sm text-gray-500">Revenue (ZAR)</p>
            <p class="text-3xl font-bold text-primary-600 mt-1">R{{ number_format($stats['revenue'], 2) }}</p>
        </div>
    </div>
</div>
@endsection