@extends('layouts.guest')

@section('title', 'Our Services - CareLink')

@section('content')
<div class="bg-white py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4 text-center">Our Services</h1>
        <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto">Comprehensive patient transport solutions for every need.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ([
                ['Wheelchair Accessible Transport', 'Vehicles equipped with ramps and securement for wheelchair users.', 'wheelchair'],
                ['Stretcher / Ambulance Transport', 'Fully equipped ambulances with medical crew for non-ambulatory patients.', 'stretcher'],
                ['Medical Escort Services', 'Qualified escorts for patients requiring monitoring during transport.', 'escort'],
                ['Repatriation', 'Long-distance and cross-border patient repatriation services.', 'repatriation'],
                ['Scheduled & Recurring', 'Regular transport for dialysis, chemotherapy, and ongoing treatment.', 'scheduled'],
                ['Emergency Transport', 'Urgent patient transport with priority dispatch.', 'emergency'],
            ] as $service)
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $service[0] }}</h3>
                    <p class="text-gray-600 text-sm">{{ $service[1] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection