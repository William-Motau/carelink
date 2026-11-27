@extends('layouts.operations')

@section('title', 'Operations Dashboard')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Operations Dashboard</h1>
            <p class="text-gray-600 mt-1">Real-time overview of bookings, fleet, and operations</p>
        </div>
        <div class="flex items-center gap-3">
            <span id="live-indicator" class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                Live
            </span>
            <button onclick="refreshDashboard()" class="btn-secondary px-4 py-2" type="button">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Bookings</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $kpis['active_bookings'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Acceptance</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $kpis['pending_acceptance'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Awaiting Dispatch</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1">{{ $kpis['awaiting_dispatch'] }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">In Transit</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1">{{ $kpis['in_transit'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17V9m4 8V9m-6 10h-6a2 2 0 01-2-2V5a2 2 0 012-2h6a2 2 0 012 2v2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Revenue Today</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">R{{ number_format($kpis['total_revenue_today'], 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Available Vehicles</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $kpis['available_vehicles'] }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Drivers</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $kpis['active_drivers'] }}</p>
                </div>
                <div class="w-12 h-12 bg-teal-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Compliance Alerts</p>
                    <p class="text-3xl font-bold {{ $kpis['compliance_alerts'] > 0 ? 'text-red-600' : 'text-green-600' }} mt-1">{{ $kpis['compliance_alerts'] }}</p>
                </div>
                <div class="w-12 h-12 {{ $kpis['compliance_alerts'] > 0 ? 'bg-red-100' : 'bg-green-100' }} rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 {{ $kpis['compliance_alerts'] > 0 ? 'text-red-600' : 'text-green-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Completed Today</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $kpis['completed_today'] }}</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Bookings & Charts -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Revenue (Last 7 Days)</h2>
                    <div class="flex items-center gap-2">
                        <select id="revenue-period" class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                            <option value="7">Last 7 Days</option>
                            <option value="30">Last 30 Days</option>
                            <option value="90">Last 90 Days</option>
                        </select>
                    </div>
                </div>
                <canvas id="revenue-chart" height="200"></canvas>
            </div>

            <!-- Booking Status Distribution -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Booking Status Distribution</h2>
                <canvas id="status-chart" height="200"></canvas>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Bookings</h2>
                    <a href="{{ route('operations.bookings.index') }}" class="text-sm text-primary-600 hover:underline">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scheduled</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($recentBookings as $booking)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <a href="{{ route('operations.bookings.show', $booking) }}" class="font-mono font-medium text-primary-600 hover:underline">{{ $booking->reference }}</a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $booking->patient->first_name }} {{ $booking->patient->last_name }}</td>
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
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $booking->scheduled_at ? $booking->scheduled_at->format('d M Y H:i') : 'Unscheduled' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $booking->vehicle?->registration_number ?? 'Unassigned' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('operations.bookings.show', $booking) }}" class="text-sm text-primary-600 hover:underline">View</a>
                                            @if (in_array($booking->status, ['pending', 'awaiting_partner_acceptance']))
                                                <form method="POST" action="{{ route('operations.bookings.accept', $booking) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-sm text-green-600 hover:underline">Accept</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if ($recentBookings->isEmpty())
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No recent bookings</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Upcoming & Fleet -->
        <div class="space-y-6">
            <!-- Upcoming Bookings -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Upcoming Scheduled</h2>
                    <a href="{{ route('operations.bookings.index') }}" class="text-sm text-primary-600 hover:underline">View All</a>
                </div>
                <div class="divide-y divide-gray-200">
                    @foreach ($upcomingBookings as $booking)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <a href="{{ route('operations.bookings.show', $booking) }}" class="font-medium text-primary-600 hover:underline">{{ $booking->reference }}</a>
                                    <p class="text-sm text-gray-600 mt-1">{{ $booking->patient->first_name }} {{ $booking->patient->last_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->scheduled_at->format('d M Y \a\t H:i') }}</p>
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                    @if ($upcomingBookings->isEmpty())
                        <div class="p-8 text-center text-gray-500">No upcoming bookings</div>
                    @endif
                </div>
            </div>

            <!-- Vehicle Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Fleet Status</h2>
                    <a href="{{ route('operations.fleet.index') }}" class="text-sm text-primary-600 hover:underline">Manage Fleet</a>
                </div>
                <div class="p-4 space-y-3">
                    @foreach (['available', 'assigned', 'maintenance', 'offline'] as $status)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full {{ 
                                    $status === 'available' ? 'bg-green-500' : 
                                    ($status === 'assigned' ? 'bg-blue-500' : 
                                    ($status === 'maintenance' ? 'bg-yellow-500' : 'bg-gray-400')) }}"></div>
                                <span class="text-sm font-medium capitalize">{{ $status }}</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">{{ $vehicleStatus[$status] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('booking.create') }}" class="p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition text-center">
                        <svg class="w-8 h-8 mx-auto text-primary-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-900">New Booking</p>
                    </a>
                    <a href="{{ route('operations.dispatch') }}" class="p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition text-center">
                        <svg class="w-8 h-8 mx-auto text-primary-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5V3a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-2M13 5l7 7-7 7M5 5l7 7-7 7"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-900">Dispatch Board</p>
                    </a>
                    <a href="{{ route('operations.fleet.index') }}" class="p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition text-center">
                        <svg class="w-8 h-8 mx-auto text-primary-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-900">Manage Fleet</p>
                    </a>
                    <a href="{{ route('operations.drivers.index') }}" class="p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition text-center">
                        <svg class="w-8 h-8 mx-auto text-primary-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-900">Manage Drivers</p>
                    </a>
                    <a href="{{ route('operations.compliance.index') }}" class="p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition text-center">
                        <svg class="w-8 h-8 mx-auto text-primary-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-900">Compliance</p>
                    </a>
                    <a href="{{ route('operations.reports.index') }}" class="p-4 border border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition text-center">
                        <svg class="w-8 h-8 mx-auto text-primary-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-medium text-gray-900">Reports</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenue-chart').getContext('2d');
    const revenueData = @json($revenueData);
    
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: Object.keys(revenueData).map(d => {
                const date = new Date(d);
                return date.toLocaleDateString('en-ZA', { weekday: 'short', day: 'numeric', month: 'short' });
            }),
            datasets: [{
                label: 'Revenue (ZAR)',
                data: Object.values(revenueData),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('status-chart').getContext('2d');
    const statusData = @json($bookingStatus);
    const statusOrder = ['pending', 'awaiting_partner_acceptance', 'accepted', 'awaiting_vehicle_assignment', 'awaiting_driver_assignment', 'dispatched', 'en_route_to_pickup', 'arrived_at_pickup', 'patient_onboard', 'en_route_to_destination', 'arrived_at_destination', 'completed', 'cancelled'];
    const statusColors = {
        'pending': '#f59e0b',
        'awaiting_partner_acceptance': '#3b82f6',
        'accepted': '#22c55e',
        'awaiting_vehicle_assignment': '#f97316',
        'awaiting_driver_assignment': '#f97316',
        'dispatched': '#a855f7',
        'en_route_to_pickup': '#6366f1',
        'arrived_at_pickup': '#6366f1',
        'patient_onboard': '#14b8a6',
        'en_route_to_destination': '#06b6d4',
        'arrived_at_destination': '#06b6d4',
        'completed': '#6b7280',
        'cancelled': '#ef4444',
    };

    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusOrder.map(s => s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
            datasets: [{
                data: statusOrder.map(s => statusData[s] || 0),
                backgroundColor: statusOrder.map(s => statusColors[s] || '#9ca3af'),
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        font: { size: 10 },
                        padding: 10,
                        usePointStyle: true,
                    }
                }
            }
        }
    });

    function refreshDashboard() {
        window.location.reload();
    }

    // Auto-refresh every 30 seconds
    setInterval(refreshDashboard, 30000);
</script>
@endsection