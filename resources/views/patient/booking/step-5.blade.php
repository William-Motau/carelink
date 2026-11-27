@extends('layouts.guest')

@section('title', 'Step 5: Review & Consent - Book Patient Transport')

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
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Review & Consent</h1>
                <p class="mt-2 text-gray-600">Please review your booking details and provide the required consents.</p>
            </div>

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('booking.step.store', ['step' => 5]) }}" class="space-y-6" novalidate>
                @csrf

                <!-- Review Sections -->
                <div class="space-y-6">
                    <!-- Contact Details -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Booking Contact
                        </h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                            <dt class="text-gray-500">Name:</dt>
                            <dd class="font-medium">{{ $currentStepData['contact']['first_name'] ?? '' }} {{ $currentStepData['contact']['last_name'] ?? '' }}</dd>
                            <dt class="text-gray-500">Relationship:</dt>
                            <dd class="font-medium">{{ $relationships[$currentStepData['contact']['role'] ?? ''] ?? $currentStepData['contact']['relationship'] ?? '' }}</dd>
                            <dt class="text-gray-500">Phone:</dt>
                            <dd class="font-medium">{{ $currentStepData['contact']['phone'] ?? '' }}</dd>
                            <dt class="text-gray-500">Email:</dt>
                            <dd class="font-medium">{{ $currentStepData['contact']['email'] ?? '' }}</dd>
                        </dl>
                    </div>

                    <!-- Patient Details -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Patient
                        </h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                            <dt class="text-gray-500">Name:</dt>
                            <dd class="font-medium">{{ $currentStepData['patient']['first_name'] ?? '' }} {{ $currentStepData['patient']['last_name'] ?? '' }}</dd>
                            @if (!empty($currentStepData['patient']['id_number']))
                                <dt class="text-gray-500">ID Number:</dt>
                                <dd class="font-medium">{{ substr($currentStepData['patient']['id_number'], 0, 6) }}****{{ substr($currentStepData['patient']['id_number'], -1) }}</dd>
                            @endif
                            @if (!empty($currentStepData['patient']['date_of_birth']))
                                <dt class="text-gray-500">Date of Birth:</dt>
                                <dd class="font-medium">{{ \Carbon\Carbon::parse($currentStepData['patient']['date_of_birth'])->format('d M Y') }}</dd>
                            @endif
                            @if (!empty($currentStepData['patient']['phone']))
                                <dt class="text-gray-500">Phone:</dt>
                                <dd class="font-medium">{{ $currentStepData['patient']['phone'] }}</dd>
                            @endif
                        </dl>
                    </div>

                    <!-- Journey Details -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            </svg>
                            Journey
                        </h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                            <dt class="text-gray-500">Type:</dt>
                            <dd class="font-medium">{{ $journeyTypes[$currentStepData['journey']['journey_type'] ?? ''] ?? $currentStepData['journey']['journey_type'] ?? '' }}</dd>
                            <dt class="text-gray-500">Pickup:</dt>
                            <dd class="font-medium">{{ $currentStepData['journey']['pickup_address'] ?? '' }}</dd>
                            <dt class="text-gray-500">Destination:</dt>
                            <dd class="font-medium">{{ $currentStepData['journey']['destination_address'] ?? '' }}</dd>
                            <dt class="text-gray-500">Scheduled:</dt>
                            <dd class="font-medium">{{ ($currentStepData['journey']['scheduled_at'] ?? null) ? \Carbon\Carbon::parse($currentStepData['journey']['scheduled_at'])->format('d M Y H:i') : 'Not set' }}</dd>
                            @if (!empty($currentStepData['journey']['return_required']))
                                <dt class="text-gray-500">Return:</dt>
                                <dd class="font-medium">{{ ($currentStepData['journey']['return_scheduled_at'] ?? null) ? \Carbon\Carbon::parse($currentStepData['journey']['return_scheduled_at'])->format('d M Y H:i') : 'Not set' }}</dd>
                            @endif
                        </dl>
                    </div>

                    <!-- Requirements -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                            <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Requirements
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($currentStepData['requirements']['mobility'] ?? [] as $mobility)
                                <span class="px-3 py-1 bg-primary-100 text-primary-700 text-sm rounded-full">
                                    {{ $mobilityOptions[$mobility] ?? $mobility }}
                                </span>
                            @endforeach
                        </div>
                        @if (!empty($currentStepData['requirements']['medical_notes']))
                            <p class="mt-2 text-sm text-gray-600"><strong>Medical Notes:</strong> {{ $currentStepData['requirements']['medical_notes'] }}</p>
                        @endif
                        @if (!empty($currentStepData['requirements']['special']))
                            <p class="mt-2 text-sm text-gray-600"><strong>Special:</strong> {{ $currentStepData['requirements']['special'] }}</p>
                        @endif
                    </div>

                    <!-- Fare Estimate -->
                    @if (!empty($currentStepData['fare_estimate']))
                        <div class="bg-primary-50 border border-primary-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-900 mb-3 flex items-center">
                                <svg class="w-5 h-5 text-primary-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Estimated Fare
                            </h3>
                            <dl class="grid grid-cols-2 gap-2 text-sm">
                                <dt class="text-gray-600">Base Fare:</dt>
                                <dd class="font-medium">R{{ number_format($currentStepData['fare_estimate']['base_fare'], 2) }}</dd>
                                <dt class="text-gray-600">Distance Charge:</dt>
                                <dd class="font-medium">R{{ number_format($currentStepData['fare_estimate']['distance_charge'], 2) }}</dd>
                                <dt class="text-gray-600">Subtotal:</dt>
                                <dd class="font-medium">R{{ number_format($currentStepData['fare_estimate']['subtotal'], 2) }}</dd>
                                <dt class="text-gray-600">VAT (15%):</dt>
                                <dd class="font-medium">R{{ number_format($currentStepData['fare_estimate']['vat_amount'], 2) }}</dd>
                                <dt class="text-gray-600 font-semibold">Total:</dt>
                                <dd class="font-semibold text-lg text-primary-600">R{{ number_format($currentStepData['fare_estimate']['total_amount'], 2) }}</dd>
                            </dl>
                            <p class="mt-2 text-xs text-gray-500">Final fare may vary based on actual distance and any additional services. Payment is due after journey completion.</p>
                        </div>
                    @endif
                </div>

                <!-- Consents -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Required Consents *</h3>
                    <div class="space-y-4">
                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-start">
                                <input type="checkbox" name="review[consents][privacy]" id="consent_privacy" value="1" 
                                    class="w-4 h-4 mt-0.5 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                    required
                                    {{ old('review.consents.privacy', $currentStepData['review']['consents']['privacy'] ?? false) ? 'checked' : '' }}>
                                <label for="consent_privacy" class="ml-3 text-sm text-gray-700">
                                    <strong>Privacy Notice Consent:</strong> I acknowledge I have read and understood the 
                                    <a href="{{ route('privacy.policy') }}" class="text-primary-600 hover:underline" target="_blank">Privacy Policy</a> 
                                    and consent to the processing of my personal information as described.
                                </label>
                            </div>
                            @error('review.consents.privacy')
                                <p class="mt-1 text-sm text-red-600 ml-7">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-start">
                                <input type="checkbox" name="review[consents][personal_info]" id="consent_personal" value="1" 
                                    class="w-4 h-4 mt-0.5 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                    required
                                    {{ old('review.consents.personal_info', $currentStepData['review']['consents']['personal_info'] ?? false) ? 'checked' : '' }}>
                                <label for="consent_personal" class="ml-3 text-sm text-gray-700">
                                    <strong>Personal Information Processing:</strong> I consent to the collection, use, and disclosure of the patient's personal and health information for the purpose of arranging and providing medical transport services, in accordance with POPIA.
                                </label>
                            </div>
                            @error('review.consents.personal_info')
                                <p class="mt-1 text-sm text-red-600 ml-7">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-start">
                                <input type="checkbox" name="review[consents][terms]" id="consent_terms" value="1" 
                                    class="w-4 h-4 mt-0.5 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                    required
                                    {{ old('review.consents.terms', $currentStepData['review']['consents']['terms'] ?? false) ? 'checked' : '' }}>
                                <label for="consent_terms" class="ml-3 text-sm text-gray-700">
                                    <strong>Terms & Conditions:</strong> I accept the 
                                    <a href="{{ route('terms.conditions') }}" class="text-primary-600 hover:underline" target="_blank">Terms and Conditions</a> 
                                    for using CareLink patient transport services.
                                </label>
                            </div>
                            @error('review.consents.terms')
                                <p class="mt-1 text-sm text-red-600 ml-7">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="flex items-start">
                                <input type="checkbox" name="review[consents][marketing]" id="consent_marketing" value="1" 
                                    class="w-4 h-4 mt-0.5 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                    {{ old('review.consents.marketing', $currentStepData['review']['consents']['marketing'] ?? false) ? 'checked' : '' }}>
                                <label for="consent_marketing" class="ml-3 text-sm text-gray-700">
                                    <strong>Marketing Communications (Optional):</strong> I agree to receive updates, promotions, and service information from CareLink via email/SMS. You can unsubscribe at any time.
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- reCAPTCHA -->
                <div class="border-t border-gray-200 pt-6">
                    <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <div id="recaptcha-container"></div>
                        <input type="hidden" name="g_recaptcha_response" id="g_recaptcha_response">
                        @error('g_recaptcha_response')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex justify-between pt-4 border-t border-gray-200 mt-8">
                    <a href="{{ route('booking.step.show', ['step' => 4]) }}" class="btn-secondary px-6 py-3">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </a>
                    <button type="submit" class="btn-primary px-8 py-3">
                        Continue to Submit
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

    <script src="https://www.google.com/recaptcha/api.js?render=explicit" async defer></script>
    <script>
        (function () {
            var form = document.querySelector('form');
            if (!form) return;

            var siteKey = '{{ config('services.recaptcha.site_key') }}';
            var hasRecaptcha = typeof grecaptcha !== 'undefined' && siteKey;

            form.addEventListener('submit', function (e) {
                // No reCAPTCHA configured (or failed to load): submit normally.
                if (!hasRecaptcha) {
                    return;
                }

                e.preventDefault();

                var submitted = false;
                var finish = function (token) {
                    if (submitted) return;
                    submitted = true;
                    var el = document.getElementById('g_recaptcha_response');
                    if (el && token) {
                        el.value = token;
                    }
                    form.submit();
                };

                try {
                    grecaptcha.ready(function () {
                        grecaptcha.execute(siteKey, { action: 'booking_submit' })
                            .then(finish)
                            .catch(finish);
                    });
                } catch (err) {
                    finish();
                }

                // Safety net: never block submission for more than 3s.
                setTimeout(finish, 3000);
            });
        })();
    </script>
</div>
@endsection