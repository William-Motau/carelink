@extends('layouts.operations')

@section('title', 'Bookings Management')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bookings</h1>
            <p class="text-gray-600 mt-1">Manage and track all patient transport bookings</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('booking.create') }}" class="btn-primary px-4 py-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Booking
            </a>
            <button onclick="refreshBookings()" class="btn-secondary px-4 py-2" type="button">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="mb-6 flex flex-wrap gap-2">
        <a href="{{ route('operations.bookings.index') }}" class="px-3 py-1 text-sm font-medium rounded-lg {{ !request('status') ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            All ({{ $stats['all'] }})
        </a>
        <a href="{{ route('operations.bookings.index', ['status' => 'pending']) }}" class="px-3 py-1 text-sm font-medium rounded-lg {{ request('status') === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Pending ({{ $stats['pending'] }})
        </a>
        <a href="{{ route('operations.bookings.index', ['status' => 'awaiting_partner_acceptance']) }}" class="px-3 py-1 text-sm font-medium rounded-lg {{ request('status') === 'awaiting_partner_acceptance' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Awaiting Acceptance ({{ $stats['awaiting_partner_acceptance'] }})
        </a>
        <a href="{{ route('operations.bookings.index', ['status' => 'accepted']) }}" class="px-3 py-1 text-sm font-medium rounded-lg {{ request('status') === 'accepted' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Accepted ({{ $stats['accepted'] }})
        </a>
        <a href="{{ route('operations.bookings.index', ['status' => 'dispatched']) }}" class="px-3 py-1 text-sm font-medium rounded-lg {{ request('status') === 'dispatched' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            In Transit ({{ $stats['dispatched'] }})
        </a>
        <a href="{{ route('operations.bookings.index', ['status' => 'completed']) }}" class="px-3 py-1 text-sm font-medium rounded-lg {{ request('status') === 'completed' ? 'bg-gray-100 text-gray-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Completed ({{ $stats['completed'] }})
        </a>
        <a href="{{ route('operations.bookings.index', ['status' => 'cancelled']) }}" class="px-3 py-1 text-sm font-medium rounded-lg {{ request('status') === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Cancelled ({{ $stats['cancelled'] }})
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Reference, patient name..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="journey_type" class="block text-sm font-medium text-gray-700 mb-1">Journey Type</label>
                <select id="journey_type" name="journey_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Types</option>
                    <option value="one_way" {{ request('journey_type') === 'one_way' ? 'selected' : '' }}>One Way</option>
                    <option value="return" {{ request('journey_type') === 'return' ? 'selected' : '' }}>Return</option>
                    <option value="scheduled" {{ request('journey_type') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="recurring" {{ request('journey_type') === 'recurring' ? 'selected' : '' }}>Recurring</option>
                    <option value="repatriation" {{ request('journey_type') === 'repatriation' ? 'selected' : '' }}>Repatriation</option>
                </select>
            </div>
            <div>
                <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                <select id="priority" name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All Priorities</option>
                    <option value="routine" {{ request('priority') === 'routine' ? 'selected' : '' }}>Routine</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="emergency" {{ request('priority') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="sm:col-span-2 lg:col-span-5 flex items-end gap-3">
                <button type="submit" class="btn-primary px-4 py-2">Apply Filters</button>
                <a href="{{ route('operations.bookings.index') }}" class="btn-secondary px-4 py-2">Clear</a>
                <a href="{{ route('operations.reports.export') }}?type=bookings&{{ request()->getQueryString() }}" class="btn-secondary px-4 py-2">
                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </a>
            </div>
        </form>
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if ($bookings->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No bookings found</h3>
                <p class="text-gray-500 mb-4">Try adjusting your filters or create a new booking.</p>
                <a href="{{ route('booking.create') }}" class="btn-primary px-4 py-2 inline-block">Create Booking</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Journey Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fare</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($bookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('operations.bookings.show', $booking) }}" class="font-mono font-medium text-primary-600 hover:underline">{{ $booking->reference }}</a>
                                </td>
                                <td class="px-4 py-3">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $booking->patient->first_name }} {{ $booking->patient->last_name }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->patient->phone }}</p>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $booking->journey_type)) }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        {{ $booking->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                           ($booking->status === 'awaiting_partner_acceptance' ? 'bg-blue-100 text-blue-800' :
                                           ($booking->status === 'accepted' ? 'bg-green-100 text-green-800' :
                                           ($booking->status === 'dispatched' ? 'bg-purple-100 text-purple-800' :
                                           ($booking->status === 'en_route_to_pickup' ? 'bg-indigo-100 text-indigo-800' :
                                           ($booking->status === 'patient_onboard' ? 'bg-teal-100 text-teal-800' :
                                           ($booking->status === 'en_route_to_destination' ? 'bg-cyan-100 text-cyan-800' :
                                           ($booking->status === 'completed' ? 'bg-gray-100 text-gray-800' :
                                           ($booking->status === 'cancelled' ? 'bg-red-100 text-red-800' :
                                           'bg-gray-100 text-gray-800')))))))) }}">
                                        {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        {{ $booking->priority === 'emergency' ? 'bg-red-100 text-red-800' :
                                           ($booking->priority === 'urgent' ? 'bg-orange-100 text-orange-800' :
                                           'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($booking->priority) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $booking->scheduled_at ? $booking->scheduled_at->format('d M Y H:i') : 'Unscheduled' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">@if($booking->vehicle){{ $booking->vehicle->registration_number }}@else<span class="text-gray-400">Unassigned</span>@endif</td>
                                <td class="px-4 py-3 text-sm text-gray-600">@if($booking->driver?->user){{ $booking->driver->user->first_name }} {{ $booking->driver->user->last_name }}@else<span class="text-gray-400">Unassigned</span>@endif</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">R{{ number_format($booking->total_amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('operations.bookings.show', $booking) }}" class="text-sm text-primary-600 hover:underline">View</a>
                                        
                                        @if (in_array($booking->status, ['pending', 'awaiting_partner_acceptance']))
                                            <form method="POST" action="{{ route('operations.bookings.accept', $booking) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-sm text-green-600 hover:underline">Accept</button>
                                            </form>
                                        @endif
                                        
                                        @if ($booking->status === 'accepted')
                                            <a href="{{ route('operations.dispatch') }}?booking={{ $booking->id }}" class="text-sm text-blue-600 hover:underline">Dispatch</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-200">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    function refreshBookings() {
        window.location.reload();
    }
</script>
@endsection