@extends('layouts.guest')

@section('title', 'FAQ - CareLink')

@section('content')
<div class="bg-gray-50 py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-8 text-center">Frequently Asked Questions</h1>
        <div class="space-y-4" x-data="{ open: null }">
            @foreach ([
                ['How do I book transport?', 'Click "Book Now" and complete the 6-step booking form. You will receive a booking reference upon submission.'],
                ['What payment methods are accepted?', 'We accept EFT, credit/debit cards, and medical aid claims where applicable. Payment is due after journey completion.'],
                ['Can I cancel or reschedule?', 'Yes. Free cancellation and rescheduling is available up to 2 hours before the scheduled pickup time via your booking status page.'],
                ['Are your vehicles and drivers compliant?', 'All vehicles have valid COFC and roadworthy certificates. All drivers hold valid PrDP and relevant medical transport qualifications.'],
                ['How is my information protected?', 'We comply fully with POPIA. All personal and medical information is encrypted and handled confidentially.'],
                ['What if I need help during the journey?', 'Call our 24/7 operations line on 0800 227 354 for urgent assistance during transport.'],
            ] as $faq)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <button @click="open === {{ $loop->index }} ? open = null : open = {{ $loop->index }}" class="w-full text-left px-6 py-4 flex items-center justify-between">
                        <span class="font-medium text-gray-900">{{ $faq[0] }}</span>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open === {{ $loop->index }} }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-12 12-7-7"/>
                        </svg>
                    </button>
                    <div x-show="open === {{ $loop->index }}" x-collapse class="px-6 pb-4 text-gray-600 text-sm">
                        {{ $faq[1] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection