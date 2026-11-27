@extends('layouts.guest')

@section('title', 'Step 2: Patient Information - Book Patient Transport')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Progress Indicator -->
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
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Patient Information</h1>
                <p class="mt-2 text-gray-600">Details of the patient being transported. {{ $currentStepData['contact']['role'] ?? '' === 'self' ? 'Since you\'re booking for yourself, some fields may be pre-filled.' : '' }}</p>
            </div>

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('booking.step.store', ['step' => 2]) }}" class="space-y-6" novalidate>
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="patient_first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input type="text" id="patient_first_name" name="patient[first_name]" 
                            value="{{ old('patient.first_name', $currentStepData['patient']['first_name'] ?? ($currentStepData['contact']['role'] ?? '' === 'self' ? ($currentStepData['contact']['first_name'] ?? '') : '')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                            required autocomplete="given-name">
                        @error('patient.first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="patient_last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input type="text" id="patient_last_name" name="patient[last_name]" 
                            value="{{ old('patient.last_name', $currentStepData['patient']['last_name'] ?? ($currentStepData['contact']['role'] ?? '' === 'self' ? ($currentStepData['contact']['last_name'] ?? '') : '')) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                            required autocomplete="family-name">
                        @error('patient.last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="id_number" class="block text-sm font-medium text-gray-700 mb-1">South African ID Number</label>
                        <input type="text" id="id_number" name="patient[id_number]" 
                            value="{{ old('patient.id_number', $currentStepData['patient']['id_number'] ?? '') }}"
                            placeholder="8001015009087"
                            maxlength="13"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                            autocomplete="off">
                        @error('patient.id_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">13-digit SA ID number (optional but recommended for medical aid)</p>
                    </div>

                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="patient[date_of_birth]" 
                            value="{{ old('patient.date_of_birth', $currentStepData['patient']['date_of_birth'] ?? '') }}"
                            max="{{ now()->format('Y-m-d') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                        @error('patient.date_of_birth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="patient_phone" class="block text-sm font-medium text-gray-700 mb-1">Patient Phone Number</label>
                        <input type="tel" id="patient_phone" name="patient[phone]" 
                            value="{{ old('patient.phone', $currentStepData['patient']['phone'] ?? ($currentStepData['contact']['role'] ?? '' === 'self' ? ($currentStepData['contact']['phone'] ?? '') : '')) }}"
                            placeholder="082 123 4567"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                            autocomplete="tel">
                        @error('patient.phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="patient_email" class="block text-sm font-medium text-gray-700 mb-1">Patient Email</label>
                        <input type="email" id="patient_email" name="patient[email]" 
                            value="{{ old('patient.email', $currentStepData['patient']['email'] ?? ($currentStepData['contact']['role'] ?? '' === 'self' ? ($currentStepData['contact']['email'] ?? '') : '')) }}"
                            placeholder="patient@example.com"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                            autocomplete="email">
                        @error('patient.email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Navigation -->
                <div class="flex justify-between pt-4 border-t border-gray-200 mt-8">
                    <a href="{{ route('booking.step.show', ['step' => 1]) }}" class="btn-secondary px-6 py-3">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </a>
                    <button type="submit" class="btn-primary px-8 py-3">
                        Continue to Journey Details
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
</div>
@endsection