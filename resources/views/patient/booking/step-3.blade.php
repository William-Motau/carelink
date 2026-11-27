@extends('layouts.guest')

@section('title', 'Step 3: Journey Details - Book Patient Transport')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                @for ($i = 1; $i <= 6; $i++)
                    <div class="flex items-center {{ $i < 6 ? 'flex-1' : '' }}">
                        <div class="relative flex items-center">
                            <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center
                                {{ $i < $step ? 'bg-primary-600 border-primary-600 text-white' : ($i === $step ? 'border-primary-600 text-primary-600 bg-white' : 'border-gray-300 text-gray-400 bg-white') }}
                                {{ $i === 6 ? 'w-10 h-10' : '' }}">
                                @if ($i < $step)
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <span class="text-sm font-semibold">{{ $i }}</span>
                                @endif
                            </div>
                            @if ($i < 6)
                                <div class="hidden sm:block w-full h-1 bg-gray-200 ml-2 mr-2
                                    {{ $i < $step ? 'bg-primary-600' : '' }}"></div>
                            @endif
                        </div>
                        <span class="hidden sm:block text-xs text-center mt-1
                            {{ $i === $step ? 'text-primary-600 font-medium' : 'text-gray-500' }}">
                            {{ $stepTitles[$i] }}
                        </span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
            <div class="mb-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Journey Details</h1>
                <p class="mt-2 text-gray-600">Where and when does the patient need to be transported?</p>
            </div>

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('booking.step.store', ['step' => 3]) }}" class="space-y-6" novalidate>
                @csrf

                <!-- Journey Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Journey Type *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach ($journeyTypes as $value => $label)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="journey[journey_type]" value="{{ $value }}" 
                                    class="sr-only peer"
                                    {{ (old('journey.journey_type', $currentStepData['journey']['journey_type'] ?? 'one_way')) === $value ? 'checked' : '' }}
                                    required>
                                <div class="p-4 border-2 rounded-lg text-center transition-colors border-gray-200 hover:border-gray-300 peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:hover:border-primary-500">
                                    <div class="font-medium text-gray-900">{{ $label }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('journey.journey_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pickup Location -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Pickup Location
                    </h3>

                    <div>
                        <label for="pickup_address" class="block text-sm font-medium text-gray-700 mb-1">Pickup Address *</label>
                        <input type="text" id="pickup_address" name="journey[pickup_address]" 
                            value="{{ old('journey.pickup_address', $currentStepData['journey']['pickup_address'] ?? '') }}"
                            placeholder="Street address, suburb, city"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                            required autocomplete="street-address">
                        @error('journey.pickup_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="pickup_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Contact Person at Pickup *</label>
                            <input type="text" id="pickup_contact_name" name="journey[pickup_contact_name]" 
                                value="{{ old('journey.pickup_contact_name', $currentStepData['journey']['pickup_contact_name'] ?? '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                                required>
                            @error('journey.pickup_contact_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="pickup_contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Contact Phone *</label>
                            <input type="tel" id="pickup_contact_phone" name="journey[pickup_contact_phone]" 
                                value="{{ old('journey.pickup_contact_phone', $currentStepData['journey']['pickup_contact_phone'] ?? '') }}"
                                placeholder="082 123 4567"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                                required>
                            @error('journey.pickup_contact_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="pickup_instructions" class="block text-sm font-medium text-gray-700 mb-1">Special Instructions (Optional)</label>
                        <textarea id="pickup_instructions" name="journey[pickup_instructions]" rows="2"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                            placeholder="e.g., Ring doorbell 3 times, patient in room 204, use side entrance">{{ old('journey.pickup_instructions', $currentStepData['journey']['pickup_instructions'] ?? '') }}</textarea>
                    </div>

                    <!-- Hidden lat/lng fields for geocoding -->
                    <input type="hidden" name="journey[pickup_lat]" id="pickup_lat" value="{{ old('journey.pickup_lat', $currentStepData['journey']['pickup_lat'] ?? '') }}">
                    <input type="hidden" name="journey[pickup_lng]" id="pickup_lng" value="{{ old('journey.pickup_lng', $currentStepData['journey']['pickup_lng'] ?? '') }}">
                </div>

                <!-- Destination Location -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Destination
                    </h3>

                    <div>
                        <label for="destination_address" class="block text-sm font-medium text-gray-700 mb-1">Destination Address *</label>
                        <input type="text" id="destination_address" name="journey[destination_address]" 
                            value="{{ old('journey.destination_address', $currentStepData['journey']['destination_address'] ?? '') }}"
                            placeholder="Street address, suburb, city"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                            required autocomplete="street-address">
                        @error('journey.destination_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label for="destination_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Contact Person at Destination *</label>
                            <input type="text" id="destination_contact_name" name="journey[destination_contact_name]" 
                                value="{{ old('journey.destination_contact_name', $currentStepData['journey']['destination_contact_name'] ?? '') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                                required>
                            @error('journey.destination_contact_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="destination_contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Contact Phone *</label>
                            <input type="tel" id="destination_contact_phone" name="journey[destination_contact_phone]" 
                                value="{{ old('journey.destination_contact_phone', $currentStepData['journey']['destination_contact_phone'] ?? '') }}"
                                placeholder="082 123 4567"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                                required>
                            @error('journey.destination_contact_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="destination_instructions" class="block text-sm font-medium text-gray-700 mb-1">Special Instructions (Optional)</label>
                        <textarea id="destination_instructions" name="journey[destination_instructions]" rows="2"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                            placeholder="e.g., Ward 4B, ask for Sister Jones, parking in basement">{{ old('journey.destination_instructions', $currentStepData['journey']['destination_instructions'] ?? '') }}</textarea>
                    </div>

                    <input type="hidden" name="journey[destination_lat]" id="destination_lat" value="{{ old('journey.destination_lat', $currentStepData['journey']['destination_lat'] ?? '') }}">
                    <input type="hidden" name="journey[destination_lng]" id="destination_lng" value="{{ old('journey.destination_lng', $currentStepData['journey']['destination_lng'] ?? '') }}">
                </div>

                <!-- Scheduling -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Schedule
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">Pickup Date & Time *</label>
                            <input type="datetime-local" id="scheduled_at" name="journey[scheduled_at]" 
                                value="{{ old('journey.scheduled_at', $currentStepData['journey']['scheduled_at'] ?? now()->addHour()->format('Y-m-d\TH:i')) }}"
                                min="{{ now()->addMinutes(30)->format('Y-m-d\TH:i') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                                required>
                            @error('journey.scheduled_at')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">Minimum 30 minutes from now</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Return Journey</label>
                            <label class="flex items-center">
                                <input type="checkbox" name="journey[return_required]" value="1" 
                                    class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                    {{ old('journey.return_required', $currentStepData['journey']['return_required'] ?? false) ? 'checked' : '' }}>
                                <span class="ml-2 text-sm text-gray-700">Patient needs a return trip</span>
                            </label>
                        </div>
                    </div>

                    <div id="return_fields" class="mt-4 {{ (old('journey.return_required', $currentStepData['journey']['return_required'] ?? false)) ? '' : 'hidden' }}">
                        <label for="return_scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">Return Date & Time *</label>
                        <input type="datetime-local" id="return_scheduled_at" name="journey[return_scheduled_at]" 
                            value="{{ old('journey.return_scheduled_at', $currentStepData['journey']['return_scheduled_at'] ?? '') }}"
                            min="{{ old('journey.scheduled_at', $currentStepData['journey']['scheduled_at'] ?? now()->addHour()->format('Y-m-d\TH:i')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                        @error('journey.return_scheduled_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex justify-between pt-4 border-t border-gray-200 mt-8">
                    <a href="{{ route('booking.step.show', ['step' => 2]) }}" class="btn-secondary px-6 py-3">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </a>
                    <button type="submit" class="btn-primary px-8 py-3">
                        Continue to Requirements
                        <svg class="w-5 h-5 ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 text-center">
            <p class="text-gray-600">Need help? <a href="{{ route('contact') }}" class="text-primary-600 hover:underline font-medium">Contact our support team</a></p>
        </div>
    </div>

    <script>
        // Toggle return fields
        document.querySelector('input[name="journey[return_required]"]').addEventListener('change', function() {
            const returnFields = document.getElementById('return_fields');
            if (this.checked) {
                returnFields.classList.remove('hidden');
                document.getElementById('return_scheduled_at').required = true;
            } else {
                returnFields.classList.add('hidden');
                document.getElementById('return_scheduled_at').required = false;
            }
        });
    </script>
</div>
@endsection