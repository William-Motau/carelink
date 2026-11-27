@extends('layouts.operations')

@section('title', 'Edit Booking')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('operations.bookings.show', $booking) }}" class="text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Booking
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Edit Booking {{ $booking->reference }}</h1>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl">
        <form class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    @foreach (['pending','awaiting_partner_acceptance','accepted','awaiting_vehicle_assignment','awaiting_driver_assignment','dispatched','completed','cancelled'] as $status)
                        <option {{ $booking->status === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    @foreach (['routine','urgent','emergency'] as $priority)
                        <option {{ $booking->priority === $priority ? 'selected' : '' }}>{{ ucfirst($priority) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('operations.bookings.show', $booking) }}" class="btn-secondary px-4 py-2">Cancel</a>
                <button type="button" class="btn-primary px-4 py-2">Update Booking</button>
            </div>
        </form>
    </div>
</div>
@endsection