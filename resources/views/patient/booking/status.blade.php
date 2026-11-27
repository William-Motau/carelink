@extends('layouts.guest')

@section('title', 'Booking Status - {{ $booking->reference }}')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="/" class="text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Home
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Booking Status</h1>
                    <p class="text-gray-600 mt-1">Reference: <span class="font-mono font-semibold text-primary-600">{{ $booking->reference }}</span></p>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full
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
                </div>
            </div>
        </div>

        <!-- Live Status Indicator -->
        <div id="live-status" class="mb-6 p-4 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div id="status-pulse" class="w-3 h-3 rounded-full bg-gray-400 mr-3"></div>
                    <div>
                        <p class="font-medium text-gray-900">Live Tracking</p>
                        <p id="connection-status" class="text-sm text-gray-500">Connecting...</p>
                    </div>
                </div>
                <button onclick="refreshStatus()" class="btn-secondary px-4 py-2 text-sm" type="button">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Progress Timeline -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Journey Progress</h2>
                    
                    <div class="relative">
                        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                        
                        @php
                            $statusOrder = [
                                'pending' => 'Booking Submitted',
                                'awaiting_partner_acceptance' => 'Awaiting Provider Acceptance',
                                'accepted' => 'Provider Accepted',
                                'awaiting_vehicle_assignment' => 'Assigning Vehicle',
                                'vehicle_assigned' => 'Vehicle Assigned',
                                'driver_assigned' => 'Driver Assigned',
                                'confirmed' => 'Confirmed',
                                'dispatched' => 'Dispatched',
                                'en_route_to_pickup' => 'En Route to Pickup',
                                'patient_onboard' => 'Patient Onboard',
                                'in_transit' => 'En Route to Destination',
                                'arrived_at_destination' => 'Arrived at Destination',
                                'completed' => 'Journey Completed',
                            ];
                            
                            $currentIndex = array_search($booking->status, array_keys($statusOrder));
                        @endphp
                        
                        @foreach ($statusOrder as $statusKey => $statusLabel)
                            @php
                                $isCompleted = $currentIndex !== false && array_search($statusKey, array_keys($statusOrder)) <= $currentIndex;
                                $isCurrent = $statusKey === $booking->status;
                            @endphp
                            <div class="relative pl-14 pb-8 {{ $loop->last ? 'pb-0' : '' }}">
                                <div class="absolute left-6 top-0 w-3 h-3 rounded-full border-3 flex items-center justify-center
                                    {{ $isCompleted ? 'bg-primary-600 border-primary-600' : 'bg-white border-gray-300' }}
                                    {{ $isCurrent ? 'ring-4 ring-primary-200' : '' }}">
                                    @if ($isCompleted)
                                        <svg class="w-2 h-2 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="{{ $isCurrent ? 'text-primary-600 font-medium' : ($isCompleted ? 'text-gray-900' : 'text-gray-400') }}">
                                    <p class="font-medium">{{ $statusLabel }}</p>
                                    @php($history = $booking->statusHistory->where('new_status', $statusKey)->first())
                                    @if ($history)
                                        <p class="text-sm">{{ $history->created_at->format('d M Y H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Journey Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Journey Details</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-medium text-gray-900 mb-3">Pickup</h3>
                            <address class="text-sm text-gray-600 not-italic space-y-1">
                                <p>{{ $booking->pickup_address }}</p>
                                @if($canViewFull)
                                    <p>Contact: {{ $booking->pickup_contact_name }} - {{ $booking->pickup_contact_phone }}</p>
                                @endif
                                @if ($booking->pickup_instructions)
                                    <p class="text-xs text-gray-500">{{ $booking->pickup_instructions }}</p>
                                @endif
                            </address>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 mb-3">Destination</h3>
                            <address class="text-sm text-gray-600 not-italic space-y-1">
                                <p>{{ $booking->destination_address }}</p>
                                @if($canViewFull)
                                    <p>Contact: {{ $booking->destination_contact_name }} - {{ $booking->destination_contact_phone }}</p>
                                @endif
                                @if ($booking->destination_instructions)
                                    <p class="text-xs text-gray-500">{{ $booking->destination_instructions }}</p>
                                @endif
                            </address>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 mb-3">Schedule</h3>
                            <dl class="text-sm text-gray-600 space-y-1">
                                <div class="flex justify-between">
                                    <dt>Journey Type:</dt>
                                    <dd class="font-medium">{{ ucfirst(str_replace('_', ' ', $booking->journey_type)) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt>Scheduled Pickup:</dt>
                                    <dd class="font-medium">{{ $booking->scheduled_at ? $booking->scheduled_at->format('d M Y \a\t H:i') : 'Not scheduled' }}</dd>
                                </div>
                                @if ($booking->estimated_pickup_at && $booking->estimated_pickup_at != $booking->scheduled_at)
                                    <div class="flex justify-between">
                                        <dt>Estimated Pickup:</dt>
                                        <dd class="font-medium">{{ $booking->estimated_pickup_at->format('d M Y \a\t H:i') }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 mb-3">Requirements</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($booking->mobility_requirements as $req)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ ucfirst(str_replace('_', ' ', $req)) }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assigned Resources -->
                @if ($booking->vehicle || $booking->driver)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Assigned Resources</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @if ($booking->vehicle)
                                <div class="bg-primary-50 border border-primary-200 rounded-lg p-4">
                                    <h3 class="font-medium text-primary-900 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        Vehicle Assigned
                                    </h3>
                                    <dl class="text-sm space-y-1">
                                        <div class="flex justify-between">
                                            <dt class="text-primary-700">Registration:</dt>
                                            <dd class="font-medium">{{ $booking->vehicle->registration_number }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-primary-700">Type:</dt>
                                            <dd>{{ $booking->vehicle->vehicle_type }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-primary-700">Capacity:</dt>
                                            <dd>{{ $booking->vehicle->capacity_seated }} seated, {{ $booking->vehicle->capacity_wheelchair }} wheelchair</dd>
                                        </div>
                                    </dl>
                                </div>
                            @endif
                            
                            @if ($booking->driver)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <h3 class="font-medium text-green-900 mb-2 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Driver Assigned
                                    </h3>
                                    <dl class="text-sm space-y-1">
                                        <div class="flex justify-between">
                                            <dt class="text-green-700">Name:</dt>
                                            <dd class="font-medium">{{ $booking->driver->user->first_name }} {{ $booking->driver->user->last_name }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-green-700">Phone:</dt>
                                            <dd>{{ $canViewFull ? $booking->driver->user->phone : '••••••' }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-green-700">Rating:</dt>
                                            <dd>{{ $booking->driver->rating_avg ?? 'N/A' }} / 5.0</dd>
                                        </div>
                                    </dl>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Status History -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Status History</h2>
                    <div class="space-y-3">
                        @foreach ($booking->statusHistory->sortByDesc('created_at') as $history)
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $history->new_status)) }}</p>
                                    <p class="text-sm text-gray-500">{{ $history->created_at->format('d M Y \a\t H:i') }}</p>
                                    @if ($history->reason)
                                        <p class="text-sm text-gray-600 mt-1">{{ $history->reason }}</p>
                                    @endif
                                    @if ($history->actor)
                                        <p class="text-xs text-gray-400 mt-1">By: {{ $history->actor->first_name }} {{ $history->actor->last_name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Patient Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Patient</h2>
                    @if($canViewFull)
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="text-gray-500">Name</dt>
                                <dd class="font-medium">{{ $booking->patient->first_name }} {{ $booking->patient->last_name }}</dd>
                            </div>
                            @if ($booking->patient->phone)
                                <div>
                                    <dt class="text-gray-500">Phone</dt>
                                    <dd class="font-medium">{{ $booking->patient->phone }}</dd>
                                </div>
                            @endif
                            @if ($booking->patient->email)
                                <div>
                                    <dt class="text-gray-500">Email</dt>
                                    <dd class="font-medium">{{ $booking->patient->email }}</dd>
                                </div>
                            @endif
                        </dl>
                    @else
                        <p class="text-sm text-gray-500">Sign in to view patient details.</p>
                    @endif
                </div>

                <!-- Fare Info -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Fare Summary</h2>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-600">Estimated Fare:</dt>
                            <dd class="font-medium">R{{ number_format($booking->fare_amount, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-600">VAT (15%):</dt>
                            <dd class="font-medium">R{{ number_format($booking->vat_amount, 2) }}</dd>
                        </div>
                        <div class="border-t border-gray-200 pt-2 flex justify-between">
                            <dt class="font-semibold text-gray-900">Total:</dt>
                            <dd class="font-semibold text-lg text-primary-600">R{{ number_format($booking->total_amount, 2) }}</dd>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <dt>Payment Status:</dt>
                            <dd class="capitalize">{{ str_replace('_', ' ', $booking->payment_status) }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions</h2>
                    <div class="space-y-3">
                        @if($canViewFull && in_array($booking->status, ['pending', 'awaiting_partner_acceptance', 'accepted', 'awaiting_vehicle_assignment']))
                            <form method="POST" action="{{ route('booking.cancel', ['uuid' => $booking->uuid]) }}" class="inline">
                                @csrf
                                @method('POST')
                                <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')" class="w-full btn-danger px-4 py-2">
                                    Cancel Booking
                                </button>
                            </form>
                            
                            <button onclick="openRescheduleModal()" class="w-full btn-secondary px-4 py-2">
                                Reschedule
                            </button>
                        @endif
                        
                        <a href="{{ route('home') }}" class="w-full btn-secondary px-4 py-2 text-center block">
                            New Booking
                        </a>
                    </div>
                </div>

                <!-- Emergency Contact -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h3 class="font-semibold text-red-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        24/7 Emergency Support
                    </h3>
                    <p class="text-sm text-red-700">For urgent issues during transport:</p>
                    <a href="tel:0800227354" class="text-lg font-bold text-red-900 block mt-1">0800 227 354</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reschedule Modal -->
    <div id="reschedule-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Reschedule Booking</h3>
            <form method="POST" action="{{ route('booking.reschedule', ['uuid' => $booking->uuid]) }}">
                @csrf
                @method('POST')
                <div class="mb-4">
                    <label for="new_scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">New Pickup Date & Time *</label>
                    <input type="datetime-local" id="new_scheduled_at" name="new_scheduled_at" 
                        min="{{ now()->addMinutes(30)->format('Y-m-d\TH:i') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        required>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeRescheduleModal()" class="btn-secondary px-4 py-2">Cancel</button>
                    <button type="submit" class="btn-primary px-4 py-2">Reschedule</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Reverb/Echo connection for live updates
        let echo = null;
        let channel = null;

        function initializeReverb() {
            // Check if Laravel Echo and Pusher are available
            if (typeof Echo !== 'undefined' && window.LaravelEchoConfig) {
                echo = new Echo({
                    broadcaster: 'pusher',
                    key: window.LaravelEchoConfig.key,
                    cluster: window.LaravelEchoConfig.cluster,
                    wsHost: window.LaravelEchoConfig.wsHost,
                    wsPort: window.LaravelEchoConfig.wsPort,
                    wssPort: window.LaravelEchoConfig.wssPort,
                    forceTLS: window.LaravelEchoConfig.forceTLS,
                    enabledTransports: ['ws', 'wss'],
                });

                channel = echo.private('booking.' + '{{ $booking->uuid }}');
                
                channel.listen('BookingStatusUpdated', (e) => {
                    updateStatus(e.status, e.message);
                });

                channel.listen('VehicleLocationUpdated', (e) => {
                    updateVehicleLocation(e.lat, e.lng);
                });

                echo.connector.pusher.connection.bind('connected', () => {
                    document.getElementById('status-pulse').className = 'w-3 h-3 rounded-full bg-green-500 mr-3 animate-pulse';
                    document.getElementById('connection-status').textContent = 'Connected - Live updates enabled';
                });

                echo.connector.pusher.connection.bind('disconnected', () => {
                    document.getElementById('status-pulse').className = 'w-3 h-3 rounded-full bg-red-500 mr-3';
                    document.getElementById('connection-status').textContent = 'Disconnected - Refresh to reconnect';
                });
            } else {
                // Fallback: polling
                document.getElementById('status-pulse').className = 'w-3 h-3 rounded-full bg-yellow-500 mr-3 animate-pulse';
                document.getElementById('connection-status').textContent = 'Polling for updates...';
                setInterval(refreshStatus, 30000);
            }
        }

        function updateStatus(newStatus, message) {
            // Update status badge
            const badge = document.querySelector('.inline-flex.px-3.py-1');
            if (badge) {
                badge.className = 'inline-flex px-3 py-1 text-sm font-medium rounded-full ' + getStatusClasses(newStatus);
                badge.textContent = formatStatus(newStatus);
            }
            // Refresh page to get full update
            setTimeout(() => window.location.reload(), 1000);
        }

        function updateVehicleLocation(lat, lng) {
            // Update map or location display if present
            console.log('Vehicle location:', lat, lng);
        }

        function getStatusClasses(status) {
            const classes = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'awaiting_partner_acceptance': 'bg-blue-100 text-blue-800',
                'accepted': 'bg-green-100 text-green-800',
                'dispatched': 'bg-purple-100 text-purple-800',
                'en_route_to_pickup': 'bg-indigo-100 text-indigo-800',
                'patient_onboard': 'bg-teal-100 text-teal-800',
                'en_route_to_destination': 'bg-cyan-100 text-cyan-800',
                'completed': 'bg-gray-100 text-gray-800',
                'cancelled': 'bg-red-100 text-red-800',
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        }

        function formatStatus(status) {
            return status.split('_').map(s => s.charAt(0).toUpperCase() + s.slice(1)).join(' ');
        }

        function refreshStatus() {
            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                // Could parse and update specific elements
                // For now, just reload
                window.location.reload();
            })
            .catch(() => {
                console.error('Failed to refresh status');
            });
        }

        function openRescheduleModal() {
            document.getElementById('reschedule-modal').classList.remove('hidden');
            document.getElementById('reschedule-modal').classList.add('flex');
        }

        function closeRescheduleModal() {
            document.getElementById('reschedule-modal').classList.add('hidden');
            document.getElementById('reschedule-modal').classList.remove('flex');
        }

        // Close modal on backdrop click
        document.getElementById('reschedule-modal').addEventListener('click', function(e) {
            if (e.target === this) closeRescheduleModal();
        });

        // Initialize on load
        document.addEventListener('DOMContentLoaded', initializeReverb);
    </script>
</div>
@endsection