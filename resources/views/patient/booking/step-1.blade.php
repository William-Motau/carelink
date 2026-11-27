@extends('layouts.guest')

@section('title', 'Step 1: Booking Contact - Book Patient Transport')

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
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Booking Contact Details</h1>
                <p class="mt-2 text-gray-600">Who is making this booking?</p>
            </div>

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('booking.step.store', ['step' => 1]) }}" class="space-y-6" novalidate>
                @csrf

                <!-- Role Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">I am booking for *</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($relationships as $value => $label)
                            <label class="relative cursor-pointer">
                                <input type="radio" name="contact[role]" value="{{ $value }}" 
                                    class="sr-only peer"
                                    {{ (old('contact.role') ?? $currentStepData['contact']['role'] ?? '') === $value ? 'checked' : '' }}
                                    required>
                                <div class="p-4 border-2 rounded-lg text-center transition-colors border-gray-200 hover:border-gray-300 peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:hover:border-primary-500">
                                    <div class="font-medium text-gray-900">{{ $label }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('contact.role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Form Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input type="text" id="first_name" name="contact[first_name]" 
                            value="{{ old('contact.first_name', $currentStepData['contact']['first_name'] ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                            required autocomplete="given-name">
                        @error('contact.first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input type="text" id="last_name" name="contact[last_name]" 
                            value="{{ old('contact.last_name', $currentStepData['contact']['last_name'] ?? '') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                            required autocomplete="family-name">
                        @error('contact.last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                        <input type="tel" id="phone" name="contact[phone]" 
                            value="{{ old('contact.phone', $currentStepData['contact']['phone'] ?? '') }}"
                            placeholder="082 123 4567"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                            required autocomplete="tel">
                        @error('contact.phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">South African format: 082 123 4567 or +27 82 123 4567</p>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" id="email" name="contact[email]" 
                            value="{{ old('contact.email', $currentStepData['contact']['email'] ?? '') }}"
                            placeholder="you@example.com"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                            required autocomplete="email">
                        @error('contact.email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="relationship" class="block text-sm font-medium text-gray-700 mb-1">Relationship to Patient *</label>
                    <select id="relationship" name="contact[relationship]" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                        required>
                        <option value="">Select relationship</option>
                        <option value="self" {{ (old('contact.relationship', $currentStepData['contact']['relationship'] ?? '') === 'self') ? 'selected' : '' }}>Myself</option>
                        <option value="spouse" {{ (old('contact.relationship', $currentStepData['contact']['relationship'] ?? '') === 'spouse') ? 'selected' : '' }}>Spouse/Partner</option>
                        <option value="parent" {{ (old('contact.relationship', $currentStepData['contact']['relationship'] ?? '') === 'parent') ? 'selected' : '' }}>Parent</option>
                        <option value="child" {{ (old('contact.relationship', $currentStepData['contact']['relationship'] ?? '') === 'child') ? 'selected' : '' }}>Child</option>
                        <option value="sibling" {{ (old('contact.relationship', $currentStepData['contact']['relationship'] ?? '') === 'sibling') ? 'selected' : '' }}>Sibling</option>
                        <option value="guardian" {{ (old('contact.relationship', $currentStepData['contact']['relationship'] ?? '') === 'guardian') ? 'selected' : '' }}>Legal Guardian</option>
                        <option value="caregiver" {{ (old('contact.relationship', $currentStepData['contact']['relationship'] ?? '') === 'caregiver') ? 'selected' : '' }}>Caregiver</option>
                        <option value="social_worker" {{ (old('contact.relationship', $currentStepData['contact']['relationship'] ?? '') === 'social_worker') ? 'selected' : '' }}>Social Worker</option>
                        <option value="healthcare_provider" {{ (old('contact.relationship', $currentStepData['contact']['relationship'] ?? '') === 'healthcare_provider') ? 'selected' : '' }}>Healthcare Provider</option>
                        <option value="other" {{ (old('contact.relationship', $currentStepData['contact']['relationship'] ?? '') === 'other') ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('contact.relationship')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Navigation -->
                <div class="flex justify-end pt-4 border-t border-gray-200 mt-8">
                    <button type="submit" class="btn-primary px-8 py-3">
                        Continue to Patient Information
                        <svg class="w-5 h-5 ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <!-- Help Sidebar -->
        <div class="mt-8 text-center">
            <p class="text-gray-600">Need help? <a href="{{ route('contact') }}" class="text-primary-600 hover:underline font-medium">Contact our support team</a></p>
        </div>
    </div>
</div>
@endsection