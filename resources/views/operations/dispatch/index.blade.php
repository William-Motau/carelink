@extends('layouts.operations')

@section('title', 'Dispatch Board')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dispatch Board</h1>
            <p class="text-gray-600 mt-1">Assign vehicles and drivers, then dispatch bookings</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                Live
            </span>
            <button onclick="refreshBoard()" class="btn-secondary px-4 py-2" type="button">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Awaiting Assignment -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col">
            <div class="p-4 border-b border-gray-200 bg-orange-50 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Awaiting Assignment</h2>
                <span class="px-2 py-0.5 text-xs font-bold bg-orange-200 text-orange-800 rounded-full">{{ $awaitingAssignment->count() }}</span>
            </div>
            <div class="p-4 space-y-3 flex-1 overflow-y-auto" style="max-height: 70vh;">
                @forelse ($awaitingAssignment as $booking)
                    <div class="border border-gray-200 rounded-lg p-3 hover:border-primary-300 transition">
                        <div class="flex items-start justify-between mb-2">
                            <a href="{{ route('operations.bookings.show', $booking) }}" class="font-mono font-medium text-primary-600 hover:underline text-sm">{{ $booking->reference }}</a>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ \App\Support\BookingStatus::badgeClasses($booking->status) }}">{{ \App\Support\BookingStatus::label($booking->status) }}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">{{ $booking->patient->first_name }} {{ $booking->patient->last_name }}</p>
                        <p class="text-xs text-gray-500">{{ $booking->scheduled_at ? $booking->scheduled_at->format('d M Y H:i') : 'Unscheduled' }}</p>
                        <p class="text-xs text-gray-500 mt-1 truncate">{{ $booking->pickup_address }}</p>
                        <button onclick="selectBooking({{ $booking->id }})" class="mt-2 w-full btn-primary px-3 py-1.5 text-xs">Assign Resources</button>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-8">No bookings awaiting assignment</p>
                @endforelse
            </div>
        </div>

        <!-- Ready to Dispatch -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col">
            <div class="p-4 border-b border-gray-200 bg-blue-50 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Ready to Dispatch</h2>
                <span class="px-2 py-0.5 text-xs font-bold bg-blue-200 text-blue-800 rounded-full">{{ $readyToDispatch->count() }}</span>
            </div>
            <div class="p-4 space-y-3 flex-1 overflow-y-auto" style="max-height: 70vh;">
                @forelse ($readyToDispatch as $booking)
                    <div class="border border-gray-200 rounded-lg p-3 hover:border-primary-300 transition">
                        <div class="flex items-start justify-between mb-2">
                            <a href="{{ route('operations.bookings.show', $booking) }}" class="font-mono font-medium text-primary-600 hover:underline text-sm">{{ $booking->reference }}</a>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ \App\Support\BookingStatus::badgeClasses($booking->status) }}">{{ \App\Support\BookingStatus::label($booking->status) }}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">{{ $booking->patient->first_name }} {{ $booking->patient->last_name }}</p>
                        <div class="mt-1 text-xs text-gray-500 space-y-0.5">
                            <p>Vehicle: {{ $booking->vehicle->registration_number ?? '—' }}</p>
                            <p>Driver: {{ $booking->driver ? ($booking->driver->user->first_name . ' ' . $booking->driver->user->last_name) : '—' }}</p>
                        </div>
                        <div class="mt-2 flex gap-2">
                            <form method="POST" action="{{ route('operations.bookings.dispatch', $booking) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full btn-success px-3 py-1.5 text-xs">Dispatch</button>
                            </form>
                            <form method="POST" action="{{ route('operations.dispatch.release') }}">
                                @csrf
                                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                <button type="submit" class="btn-secondary px-3 py-1.5 text-xs">Release</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-8">No bookings ready to dispatch</p>
                @endforelse
            </div>
        </div>

        <!-- In Transit -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col">
            <div class="p-4 border-b border-gray-200 bg-purple-50 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">In Transit</h2>
                <span class="px-2 py-0.5 text-xs font-bold bg-purple-200 text-purple-800 rounded-full">{{ $inTransit->count() }}</span>
            </div>
            <div class="p-4 space-y-3 flex-1 overflow-y-auto" style="max-height: 70vh;">
                @forelse ($inTransit as $booking)
                    <div class="border border-gray-200 rounded-lg p-3">
                        <div class="flex items-start justify-between mb-2">
                            <a href="{{ route('operations.bookings.show', $booking) }}" class="font-mono font-medium text-primary-600 hover:underline text-sm">{{ $booking->reference }}</a>
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ \App\Support\BookingStatus::badgeClasses($booking->status) }}">{{ \App\Support\BookingStatus::label($booking->status) }}</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">{{ $booking->patient->first_name }} {{ $booking->patient->last_name }}</p>
                        <div class="mt-1 text-xs text-gray-500 space-y-0.5">
                            <p>Vehicle: {{ $booking->vehicle->registration_number ?? '—' }}</p>
                            <p>Driver: {{ $booking->driver ? ($booking->driver->user->first_name . ' ' . $booking->driver->user->last_name) : '—' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-8">No bookings in transit</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Assignment Modal -->
<div id="assign-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-lg w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Assign Resources</h3>
            <button onclick="closeAssignModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('operations.dispatch.assign') }}">
            @csrf
            <input type="hidden" name="booking_id" id="assign-booking-id">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle</label>
                    <select name="vehicle_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Select a vehicle</option>
                        @foreach ($availableVehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->registration_number }} — {{ ucfirst(str_replace('_', ' ', $vehicle->vehicle_type)) }} ({{ $vehicle->capacity_seated }} seats)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Driver</label>
                    <select name="driver_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Select a driver</option>
                        @foreach ($activeDrivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->user->first_name }} {{ $driver->user->last_name }} — {{ $driver->license_class }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeAssignModal()" class="btn-secondary px-4 py-2">Cancel</button>
                <button type="submit" class="btn-primary px-4 py-2">Save Assignment</button>
            </div>
        </form>
    </div>
</div>

<script>
    function selectBooking(id) {
        document.getElementById('assign-booking-id').value = id;
        const modal = document.getElementById('assign-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    function closeAssignModal() {
        const modal = document.getElementById('assign-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    function refreshBoard() {
        window.location.reload();
    }
    document.getElementById('assign-modal').addEventListener('click', function(e) {
        if (e.target === this) closeAssignModal();
    });
    // Auto-refresh every 30s
    setInterval(refreshBoard, 30000);
</script>
@endsection