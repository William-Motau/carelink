@extends('layouts.guest')

@section('title', 'About Us - CareLink')

@section('content')
<div class="bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h1 class="text-4xl font-bold text-gray-900 mb-6">About CareLink</h1>
        <div class="prose prose-lg text-gray-600 space-y-6">
            <p>CareLink is South Africa's dedicated patient transport platform, connecting patients, caregivers, and healthcare facilities with reliable, safe, and compliant medical transport services.</p>
            <h2 class="text-2xl font-semibold text-gray-900">Our Mission</h2>
            <p>To ensure every patient has access to safe, dignified, and timely medical transport, regardless of their mobility needs or location.</p>
            <h2 class="text-2xl font-semibold text-gray-900">Why Choose CareLink</h2>
            <ul class="list-disc pl-6 space-y-2">
                <li>POPIA-compliant handling of all patient information</li>
                <li>Verified vehicles with valid COFC and roadworthy certificates</li>
                <li>Qualified drivers with valid PrDP and medical transport training</li>
                <li>Real-time booking tracking and status updates</li>
                <li>Transparent, upfront pricing with no hidden fees</li>
                <li>24/7 operations support</li>
            </ul>
        </div>
    </div>
</div>
@endsection