@extends('layouts.operations')

@section('title', 'Partner Bookings')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Bookings</h1>
        <p class="text-gray-600 mt-1">Transport bookings assigned to your organisation</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-1">No bookings yet</h3>
        <p class="text-gray-500">Bookings assigned to your organisation will appear here.</p>
    </div>
</div>
@endsection