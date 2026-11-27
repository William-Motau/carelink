@extends('layouts.operations')

@section('title', 'Booking {{ $booking->reference }}')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('operations.bookings.index') }}" class="text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Bookings
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Booking {{ $booking->reference }}</h1>
            <p class="text-gray-600 mt-1">Patient transport booking details and management</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('operations.bookings.run-sheet', $booking) }}" target="_blank" class="btn-secondary px-4 py-2" type="button">
                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Run Sheet
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="mb-6 flex items-center gap-3">
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
        
        @if ($booking->priority === 'emergency')
            <span class="inline-flex px-3 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                Emergency
            </span>
        @endif
        
        <span class="inline-flex px-3 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">
            {{ ucfirst($booking->priority) }} Priority
        </span>
    </div>

    <!-- Actions -->
    @if (in_array($booking->status, ['pending', 'awaiting_partner_acceptance']))
        <div class="mb-6">
            <form method="POST" action="{{ route('operations.bookings.accept', $booking) }}" class="inline mr-2">
                @csrf
                <button type="submit" class="btn-success px-4 py-2">
                    Accept Booking
                </button>
            </form>
            <form method="POST" action="{{ route('operations.bookings.reject', $booking) }}" class="inline">
                @csrf
                <textarea name="reason" placeholder="Reason for rejection..." rows="1" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm w-48"></textarea>
                <button type="submit" class="btn-danger px-4 py-2 ml-2">
                    Reject
                </button>
            </form>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
        <!-- Left Column: Patient & Journey -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Patient Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Patient</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $booking->patient->first_name }} {{ $booking->patient->last_name }}</p>
                            @if ($booking->patient->id_number)
                                <p class="text-xs text-gray-500">ID: {{ substr($booking->patient->id_number, 0, 6) }}****{{ substr($booking->patient->id_number, -1) }}</p>
                            @endif
                            @if ($booking->patient->date_of_birth)
                                <p class="text-xs text-gray-500">DOB: {{ \Carbon\Carbon::parse($booking->patient->date_of_birth)->format('d M Y') }}</p>
                            @endif
                        </div>
                    </div>
                    @if ($booking->patient->phone)
                        <div class="flex items-center gap-4 text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 011 1v5.216a2 2 0 012 1.791d .05.895-.614 1.79-1.794 2.347a1 1 0 01-1.07-.125l-3 3a1 1 0 01-1.414-1.414l3-3a1 1 0 01.109-.134c.44-.189.804-.58.997-1.134a2 2 0 011.134-.788V7.5a1 1 0 011-1h4a1 1 0 011 1v10.618c0 .975-.43 1.899-1.145 2.571a1 1 0 01-1.49-.113l-3-2.999a1 1 0 01-.134-1.109c.554-.432.921-.878.997-1.195a2 2 0 011.557-.853V7.5a2 2 0 012-2z"/>
                            </svg>
                            <a href="tel:{{ $booking->patient->phone }}" class="hover:text-primary-600">{{ $booking->patient->phone }}</a>
                        </div>
                    @endif
                    @if ($booking->patient->email)
                        <div class="flex items-center gap-4 text-gray-600">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22-2L11 8a2 2 0 004-4V6a2 2 0 012 2v5a2 2 0 01-2 2 2 2 0 01-2-2v-1a2 2 0 00-2-2 2 2 0 00-2-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v1a2 2 0 00-2 2v5a2 2 0 012-2z"/>
                            </svg>
                            <a href="mailto:{{ $booking->patient->email }}" class="hover:text-primary-600">{{ $booking->patient->email }}</a>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Mobility Requirements -->
            @if ($booking->mobility_requirements && count($booking->mobility_requirements) > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Mobility Requirements</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($booking->mobility_requirements as $req)
                            <span class="px-3 py-1 bg-primary-100 text-primary-700 text-xs rounded-full font-medium">
                                {{ ucfirst(str_replace('_', ' ', $req)) }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Medical Notes -->
            @if ($booking->medical_notes)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Medical Notes</h2>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $booking->medical_notes }}</p>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Confidential - shared only with assigned transport crew per POPIA</p>
                </div>
            @endif

            <!-- Special Requirements -->
            @if ($booking->special_requirements)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Special Requirements</h2>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $booking->special_requirements }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Journey & Assignment -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Journey Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Journey Details</h2>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="font-medium text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Pickup
                        </h3>
                        <dl class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Address:</dt>
                                <dd class="font-medium">{{ $booking->pickup_address }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Contact:</dt>
                                <dd>{{ $booking->pickup_contact_name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Phone:</dt>
                                <dd>{{ $booking->pickup_contact_phone }}</dd>
                            </div>
                            @if ($booking->pickup_instructions)
                                <div class="flex justify-between mt-2">
                                    <dt class="text-gray-500">Instructions:</dt>
                                    <dd class="text-xs text-gray-500">{{ $booking->pickup_instructions }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="font-medium text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            Destination
                        </h3>
                        <dl class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Address:</dt>
                                <dd class="font-medium">{{ $booking->destination_address }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Contact:</dt>
                                <dd>{{ $booking->destination_contact_name }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Phone:</dt>
                                <dd>{{ $booking->destination_contact_phone }}</dd>
                            </div>
                            @if ($booking->destination_instructions)
                                <div class="flex justify-between mt-2">
                                    <dt class="text-gray-500">Instructions:</dt>
                                    <dd class="text-xs text-gray-500">{{ $booking->destination_instructions }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="font-medium text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Schedule
                        </h3>
                        <dl class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Journey Type:</dt>
                                <dd class="font-medium">{{ ucfirst(str_replace('_', ' ', $booking->journey_type)) }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-gray-500">Scheduled Pickup:</dt>
                                <dd class="font-medium">{{ $booking->scheduled_at ? $booking->scheduled_at->format('d M Y \a\t H:i') : 'Not scheduled' }}</dd>
                            </div>
                            @if ($booking->estimated_pickup_at && $booking->estimated_pickup_at != $booking->scheduled_at)
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Estimated Pickup:</dt>
                                    <dd class="font-medium">{{ $booking->estimated_pickup_at->format('d M Y \a\t H:i') }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Vehicle & Driver Assignment -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Vehicle & Driver</h2>
                
                <div class="space-y-4">
                    @if ($booking->vehicle)
                        <div class="flex items-center gap-4 p-4 bg-primary-50 border border-primary-200 rounded-lg">
                            <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $booking->vehicle->vehicle_type }}</h4>
                                <p class="text-sm text-gray-600">{{ $booking->vehicle->registration_number }}</p>
                                <p class="text-xs text-gray-500">{{ $booking->vehicle->capacity_seated }} seated, {{ $booking->vehicle->capacity_wheelchair }} wheelchair spots</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Unassigned Vehicle</h4>
                                <p class="text-sm text-gray-600">No vehicle assigned yet</p>
                            </div>
                        </div>
                    @endif

                    @if ($booking->driver)
                        <div class="flex items-center gap-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $booking->driver->user->first_name }} {{ $booking->driver->user->last_name }}</h4>
                                <p class="text-sm text-gray-600">{{ $booking->driver->user->phone }}</p>
                                <p class="text-xs text-gray-500">{{ $booking->driver->license_class }} License</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-center gap-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Unassigned Driver</h4>
                                <p class="text-sm text-gray-600">No driver assigned yet</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Assignment Actions -->
                @if (!in_array($booking->status, ['cancelled', 'completed', 'rejected']))
                    <div class="mt-6 border-t border-gray-200 pt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <button onclick="openAssignVehicleModal()" class="btn-secondary w-full py-2">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Assign Vehicle
                        </button>
                        <button onclick="openAssignDriverModal()" class="btn-secondary w-full py-2">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Assign Driver
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Fare Summary -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Fare Summary</h2>
        <dl class="space-y-3 text-sm">
            <div class="flex justify-between">
                <dt class="text-gray-600">Base Fare:</dt>
                <dd class="font-medium">R{{ number_format($booking->fare_amount, 2) }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="text-gray-600">Commission:</dt>
                <dd class="font-medium">{{ $booking->commission_rate }}% (R{{ number_format($booking->commission_amount, 2) }})</dd>
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
                <dd class="capitalize font-medium">{{ str_replace('_', ' ', $booking->payment_status) }}</dd>
            </div>
        </dl>
    </div>

    <!-- Status History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
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

    <!-- Cancellation -->
    @if (in_array($booking->status, ['pending', 'awaiting_partner_acceptance', 'accepted', 'awaiting_vehicle_assignment', 'awaiting_driver_assignment']))
        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <form method="POST" action="{{ route('operations.bookings.cancel', $booking) }}" class="inline">
                @csrf
                @method('POST')
                <label class="block text-sm font-medium text-red-900 mb-2">Cancel Booking</label>
                <textarea name="reason" placeholder="Reason for cancellation..." rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 mb-3"></textarea>
                <button type="submit" onclick="return confirm('Are you sure you want to cancel this booking?')" class="btn-danger px-4 py-2">
                    Confirm Cancellation
                </button>
            </form>
        </div>
    @endif
</div>

<!-- Assign Vehicle Modal -->
<div id="assign-vehicle-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-lg w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Assign Vehicle</h3>
        <form method="POST" action="{{ route('operations.bookings.assign-vehicle', $booking) }}">
            @csrf
            @method('POST')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Vehicle</label>
                <select name="vehicle_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Select an available vehicle</option>
                    @foreach (\App\Models\Vehicle::where('organisation_id', auth()->user()->organisation_id)->where('status', 'available')->get() as $vehicle)
                        <option value="{{ $vehicle->id }}">{{ $vehicle->registration_number }} - {{ $vehicle->vehicle_type }} ({{ $vehicle->capacity_seated }} seats)</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeAssignVehicleModal()" class="btn-secondary px-4 py-2">Cancel</button>
                <button type="submit" class="btn-primary px-4 py-2">Assign</button>
            </div>
        </form>
    </div>
</div>

<!-- Assign Driver Modal -->
<div id="assign-driver-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl max-w-lg w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Assign Driver</h3>
        <form method="POST" action="{{ route('operations.bookings.assign-driver', $booking) }}">
            @csrf
            @method('POST')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Driver</label>
                <select name="driver_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Select an available driver</option>
                    @foreach (\App\Models\Driver::where('organisation_id', auth()->user()->organisation_id)->where('status', 'active')->get() as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->user->first_name }} {{ $driver->user->last_name }} - {{ $driver->license_class }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeAssignDriverModal()" class="btn-secondary px-4 py-2">Cancel</button>
                <button type="submit" class="btn-primary px-4 py-2">Assign</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAssignVehicleModal() {
        document.getElementById('assign-vehicle-modal').classList.remove('hidden');
        document.getElementById('assign-vehicle-modal').classList.add('flex');
    }
    
    function closeAssignVehicleModal() {
        document.getElementById('assign-vehicle-modal').classList.add('hidden');
        document.getElementById('assign-vehicle-modal').classList.remove('flex');
    }
    
    function openAssignDriverModal() {
        document.getElementById('assign-driver-modal').classList.remove('hidden');
        document.getElementById('assign-driver-modal').classList.add('flex');
    }
    
    function closeAssignDriverModal() {
        document.getElementById('assign-driver-modal').classList.add('hidden');
        document.getElementById('assign-driver-modal').classList.remove('flex');
    }
    
    // Close modal on backdrop click
    [document.getElementById('assign-vehicle-modal'), document.getElementById('assign-driver-modal')].forEach(function(modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                this.classList.remove('flex');
            }
        });
    });
</script>
@endsection