@extends('layouts.guest')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="relative bg-carelink-navy text-white overflow-hidden" aria-labelledby="hero-heading">
    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.03"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
    
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-20 sm:pt-32 sm:pb-52">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="max-w-3xl">
            <h1 id="hero-heading" class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                Reliable Patient Transport<br class="hidden sm:block">When You Need It Most
            </h1>
            <p class="text-lg sm:text-xl text-gray-200 mb-8 max-w-2xl">
                CareLink connects patients, caregivers, and healthcare providers with safe, comfortable, and timely transportation — from hospital discharge to everyday appointments. Book with confidence and track in real-time.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('booking.create') }}" class="btn bg-white text-carelink-navy hover:bg-gray-100 text-lg px-8 py-3 w-full sm:w-auto">
                    <svg class="h-5 w-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Book Transport Now
                </a>
                <a href="#how-it-works" class="btn bg-transparent border-2 border-white text-white hover:bg-white/10 text-lg px-8 py-3 w-full sm:w-auto">
                    How It Works
                </a>
            </div>
            </div>

            <div class="hidden lg:flex justify-center">
                <img src="{{ asset('images/hero-transport.jpg') }}" alt="Accessible CareLink patient transport van assisting a wheelchair user" class="w-full max-w-md rounded-2xl object-cover shadow-2xl" width="480" height="360">
            </div>
        </div>
    </div>
    
    <!-- Trust Indicators -->
    <div class="relative sm:absolute sm:bottom-0 sm:left-0 sm:right-0 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-8 mt-10 sm:mt-0">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                <div class="text-2xl font-bold text-white">{{ \App\Models\Booking::where('status', 'completed')->count() + 1250 }}</div>
                <div class="text-sm text-gray-300">Journeys Completed</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                <div class="text-2xl font-bold text-white">{{ \App\Models\Vehicle::where('status', 'available')->count() + 15 }}</div>
                <div class="text-sm text-gray-300">Vehicles Available</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                <div class="text-2xl font-bold text-white">24/7</div>
                <div class="text-sm text-gray-300">Booking Available</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                <div class="text-2xl font-bold text-white">POPIA</div>
                <div class="text-sm text-gray-300">Compliant</div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section id="how-it-works" class="py-16 sm:py-24 bg-white scroll-mt-16" aria-labelledby="how-it-works-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 id="how-it-works-heading" class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Book patient transport in three simple steps</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="relative">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-16 h-16 bg-carelink-navy/10 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-carelink-navy">1</span>
                </div>
                <div class="pt-10 text-center">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Enter Details</h3>
                    <p class="text-gray-600">Provide pickup and destination addresses, patient details, and mobility requirements through our guided booking wizard.</p>
                </div>
            </div>
            
            <div class="relative">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-16 h-16 bg-carelink-navy/10 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-carelink-navy">2</span>
                </div>
                <div class="pt-10 text-center">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Get Confirmed</h3>
                    <p class="text-gray-600">Receive instant booking confirmation with a unique reference number. Our operations team matches you with the right vehicle and driver.</p>
                </div>
            </div>
            
            <div class="relative">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-16 h-16 bg-carelink-navy/10 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-carelink-navy">3</span>
                </div>
                <div class="pt-10 text-center">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Track & Travel</h3>
                    <p class="text-gray-600">Track your journey in real-time with live updates. Share status with family. Driver arrives, assists patient, and completes the journey safely.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services -->
<section id="services" class="py-16 sm:py-24 bg-gray-50" aria-labelledby="services-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 id="services-heading" class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Our Transport Services</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">Specialized vehicles and trained staff for every patient need</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['icon' => 'wheelchair', 'title' => 'Wheelchair Accessible', 'desc' => 'Vehicles equipped with ramps, lifts, and secure tie-downs for wheelchair users.', 'image' => 'service-wheelchair.jpg'],
                ['icon' => 'stretcher', 'title' => 'Stretcher Transport', 'desc' => 'Medical-grade stretchers with oxygen outlets and monitoring equipment for critical care.', 'image' => 'service-stretcher.jpg'],
                ['icon' => 'walking', 'title' => 'Walking Assistance', 'desc' => 'Door-to-door service with trained staff to assist patients with mobility challenges.', 'image' => 'service-walking.jpg'],
                ['icon' => 'scheduled', 'title' => 'Scheduled & Recurring', 'desc' => 'Regular appointments for dialysis, therapy, or check-ups booked in advance.', 'image' => 'service-scheduled.jpg'],
            ] as $service)
                <article class="card overflow-hidden hover:shadow-md transition-shadow">
                    <img src="{{ asset('images/' . $service['image']) }}" alt="{{ $service['title'] }}" class="w-full h-36 object-cover" width="200" height="140" loading="lazy">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $service['title'] }}</h3>
                        <p class="text-gray-600 text-sm">{{ $service['desc'] }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<!-- Hospital Discharge Transport -->
<section id="discharge" class="py-16 sm:py-24 bg-white" aria-labelledby="discharge-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="relative">
                <img src="{{ asset('images/discharge-transport.jpg') }}" alt="Nurse assisting a patient being discharged from hospital" class="rounded-2xl shadow-xl w-full h-80 object-cover" width="600" height="400" loading="lazy">
            </div>
            <div>
                <span class="inline-block px-3 py-1 text-xs font-semibold text-carelink-teal bg-carelink-teal/10 rounded-full mb-4">Hospital Discharge</span>
                <h2 id="discharge-heading" class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">Discharged from hospital? We'll get you home safely.</h2>
                <p class="text-lg text-gray-600 mb-6">
                    CareLink specialises in post-discharge patient transport — bridging the gap between hospital and home with trained staff and the right vehicle for every patient.
                </p>
                <ul class="space-y-3 text-gray-700 mb-8">
                    @foreach([
                        'Safe, comfortable transport immediately after discharge',
                        'Wheelchair, stretcher and walking-assistance vehicles',
                        'Trained staff to assist patients and caregivers door-to-door',
                        'Live journey tracking shared with family and care teams',
                        'Scheduled and recurring journeys for ongoing treatment',
                    ] as $point)
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-carelink-teal flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="ml-2">{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('booking.create') }}" class="btn-primary px-8 py-3 text-lg">
                    <svg class="h-5 w-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Book a Discharge Journey
                </a>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 sm:py-24 bg-carelink-navy text-white" aria-labelledby="cta-heading">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
        <h2 id="cta-heading" class="text-3xl sm:text-4xl font-bold mb-4">Ready to Book Patient Transport?</h2>
        <p class="text-lg text-gray-200 mb-8 max-w-2xl mx-auto">Get started in minutes. No account required for booking. Track your journey in real-time.</p>
        <a href="{{ route('booking.create') }}" class="btn bg-white text-carelink-navy hover:bg-gray-100 text-lg px-8 py-3">
            <svg class="h-5 w-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Book Transport Now
        </a>
    </div>
</section>
@endsection