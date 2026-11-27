@extends('layouts.operations')

@section('title', 'Booking Details')

@section('content')
<div class="p-6">
    <a href="{{ route('partner.bookings.index') }}" class="text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back to Bookings
    </a>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center mt-4">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Booking Details</h1>
        <p class="text-gray-500">Detailed booking information will appear here.</p>
    </div>
</div>
@endsection