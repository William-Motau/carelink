@extends('layouts.app')

@section('title', 'Driver Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <header class="bg-carelink-navy text-white shadow">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6">
            <div class="flex items-center space-x-2">
                <svg class="h-8 w-8 text-carelink-teal" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                <span class="text-xl font-bold">CareLink <span class="text-carelink-teal">Driver</span></span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="hidden text-sm text-gray-200 sm:inline">{{ auth()->user()->full_name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-white/10 px-3 py-1.5 text-sm font-medium hover:bg-white/20">Sign Out</button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->first_name }}</h1>
            <p class="mt-1 text-gray-600">Here is your schedule and compliance status.</p>
        </div>

        @if(! $driver)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-amber-800">
                No driver profile is linked to your account yet. Please contact your administrator.
            </div>
        @else
            @if(! $driver->isCompliant())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800" role="alert">
                    Your compliance documents need attention. You may not be dispatched until this is resolved.
                </div>
            @endif

            <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Active Jobs</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $bookings->count() }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Completed Today</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $completedToday }}</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Compliance</p>
                    <p class="mt-1 text-3xl font-bold capitalize {{ $driver->isCompliant() ? 'text-green-600' : 'text-red-600' }}">
                        {{ $driver->compliance_status }}
                    </p>
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900">Assigned Jobs</h2>
                </div>
                @if($bookings->isEmpty())
                    <p class="px-6 py-8 text-center text-gray-500">You have no active jobs assigned.</p>
                @else
                    <ul class="divide-y divide-gray-200">
                        @foreach($bookings as $booking)
                            <li class="flex flex-col gap-2 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $booking->reference }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $booking->patient?->full_name ?? 'Patient' }} &middot;
                                        {{ $booking->pickup_address }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-sm text-gray-500">
                                        {{ optional($booking->scheduled_at)->format('d M Y, H:i') }}
                                    </span>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ \App\Support\BookingStatus::badgeClasses($booking->status) }}">
                                        {{ \App\Support\BookingStatus::label($booking->status) }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
    </main>
</div>
@endsection