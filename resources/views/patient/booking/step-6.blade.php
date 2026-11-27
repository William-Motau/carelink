@extends('layouts.guest')

@section('title', 'Step 6: Submit - Book Patient Transport')

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
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8 text-center">
            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-primary-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Submitting Your Booking...</h1>
            <p class="text-gray-600 mb-6">Please wait while we process your booking request. This may take a few seconds.</p>
            
            <div class="max-w-md mx-auto text-left bg-gray-50 rounded-lg p-4">
                <div class="flex items-center text-sm text-gray-600 mb-2">
                    <svg class="w-5 h-5 text-primary-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>Validating booking details</span>
                </div>
                <div class="flex items-center text-sm text-gray-600 mb-2">
                    <svg class="w-5 h-5 text-primary-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>Checking availability</span>
                </div>
                <div class="flex items-center text-sm text-gray-600 mb-2">
                    <svg class="w-5 h-5 text-primary-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>Creating booking record</span>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-5 h-5 text-primary-600 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span>Sending confirmations</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('booking.submit') }}" id="submit-form" class="hidden">
            @csrf
            <input type="hidden" name="idempotency_key" value="{{ Str::uuid() }}">
        </form>
    </div>

    <script>
        // Auto-submit to the submit endpoint
        document.addEventListener('DOMContentLoaded', function() {
            // Small delay to show the loading state
            setTimeout(function() {
                document.getElementById('submit-form').submit();
            }, 1500);
        });
    </script>
</div>
@endsection