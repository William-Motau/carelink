@extends('layouts.guest')

@section('title', 'How It Works - CareLink')

@section('content')
<div class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4 text-center">How It Works</h1>
        <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto">Booking patient transport has never been easier. Follow these simple steps.</p>
        <div class="space-y-8">
            @foreach ([
                ['1', 'Book Online', 'Fill in the 6-step booking form with patient and journey details.'],
                ['2', 'We Match a Vehicle', 'Our operations team assigns a suitable vehicle and qualified crew.'],
                ['3', 'Get Confirmed', 'Receive SMS/email confirmation with your booking reference.'],
                ['4', 'Track Live', 'Follow your transport in real time using your booking reference.'],
                ['5', 'Safe Arrival', 'The crew ensures the patient arrives safely at the destination.'],
            ] as $step)
                <div class="flex items-start gap-4 bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-xl font-bold text-primary-600">{{ $step[0] }}</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $step[1] }}</h3>
                        <p class="text-gray-600 mt-1">{{ $step[2] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-12 text-center">
            <a href="{{ route('booking.create') }}" class="btn-primary px-8 py-3 inline-block">Start Booking</a>
        </div>
    </div>
</div>
@endsection