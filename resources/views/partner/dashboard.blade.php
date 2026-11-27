@extends('layouts.operations')

@section('title', 'Partner Dashboard')

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Partner Dashboard</h1>
        <p class="text-gray-600 mt-1">Overview of your transport partnership with CareLink</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach (['Pending Bookings' => 0, 'Active Jobs' => 0, 'Completed' => 0, 'Revenue' => 'R0.00'] as $label => $value)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $value }}</p>
            </div>
        @endforeach
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('partner.bookings.index') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-primary-50 text-center text-sm font-medium">View Bookings</a>
            <a href="{{ route('partner.settlements.index') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-primary-50 text-center text-sm font-medium">Settlements</a>
            <a href="{{ route('partner.fleet.index') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-primary-50 text-center text-sm font-medium">My Fleet</a>
            <a href="{{ route('partner.drivers.index') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-primary-50 text-center text-sm font-medium">My Drivers</a>
        </div>
    </div>
</div>
@endsection