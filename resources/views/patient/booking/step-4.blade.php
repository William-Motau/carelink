@extends('layouts.guest')

@section('title', 'Step 4: Requirements - Book Patient Transport')

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
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Medical & Mobility Requirements</h1>
                <p class="mt-2 text-gray-600">Help us ensure the right vehicle and crew are assigned for the patient's needs.</p>
            </div>

            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('booking.step.store', ['step' => 4]) }}" class="space-y-6" novalidate>
                @csrf

                <!-- Mobility Requirements -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Mobility Requirements * <span class="text-red-500">(select at least one)</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($mobilityOptions as $value => $label)
                            <label class="relative cursor-pointer group">
                                <input type="checkbox" name="requirements[mobility][]" value="{{ $value }}" 
                                    class="sr-only peer"
                                    {{ in_array($value, old('requirements.mobility', $currentStepData['requirements']['mobility'] ?? [])) ? 'checked' : '' }}>
                                <div class="p-4 border-2 rounded-lg transition-all border-gray-200 hover:border-gray-300 peer-checked:border-primary-500 peer-checked:bg-primary-50 peer-checked:hover:border-primary-500">
                                    <div class="font-medium text-gray-900">{{ $label }}</div>
                                    @if ($value === 'wheelchair')
                                        <div class="text-xs text-gray-500 mt-1">Standard wheelchair accessible vehicle</div>
                                    @elseif ($value === 'stretcher')
                                        <div class="text-xs text-gray-500 mt-1">Ambulance/stretcher vehicle with medical crew</div>
                                    @elseif ($value === 'oxygen')
                                        <div class="text-xs text-gray-500 mt-1">Oxygen equipment and monitoring</div>
                                    @elseif ($value === 'escort_required')
                                        <div class="text-xs text-gray-500 mt-1">Qualified medical escort required</div>
                                    @elseif ($value === 'bariatric')
                                        <div class="text-xs text-gray-500 mt-1">Bariatric equipment and reinforced vehicle</div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('requirements.mobility')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Medical Notes -->
                <div>
                    <label for="medical_notes" class="block text-sm font-medium text-gray-700 mb-1">Medical Notes (Optional)</label>
                    <textarea id="medical_notes" name="requirements[medical_notes]" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                        placeholder="Any relevant medical information for the transport crew (e.g., condition, medications, allergies, recent surgery, infection risk)">{{ old('requirements.medical_notes', $currentStepData['requirements']['medical_notes'] ?? '') }}</textarea>
                    @error('requirements.medical_notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">This information is shared only with the assigned transport crew and is kept confidential per POPIA.</p>
                </div>

                <!-- Special Requirements -->
                <div>
                    <label for="special" class="block text-sm font-medium text-gray-700 mb-1">Special Requirements (Optional)</label>
                    <textarea id="special" name="requirements[special]" rows="3"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                        placeholder="e.g., Needs assistance with stairs, prefers female crew, specific hospital entrance, language preference">{{ old('requirements.special', $currentStepData['requirements']['special'] ?? '') }}</textarea>
                    @error('requirements.special')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Navigation -->
                <div class="flex justify-between pt-4 border-t border-gray-200 mt-8">
                    <a href="{{ route('booking.step.show', ['step' => 3]) }}" class="btn-secondary px-6 py-3">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back
                    </a>
                    <button type="submit" class="btn-primary px-8 py-3">
                        Continue to Review
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