@extends('layouts.operations')

@section('title', 'Settlements')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Settlements</h1>
        <p class="text-gray-600 mt-1">Your settlement statements and payments</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-1">No settlements yet</h3>
        <p class="text-gray-500">Your settlement statements will appear here once journeys are completed.</p>
    </div>
</div>
@endsection