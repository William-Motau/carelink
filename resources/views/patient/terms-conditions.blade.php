@extends('layouts.guest')

@section('title', 'Terms & Conditions - CareLink')

@section('content')
<div class="bg-white py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Terms & Conditions</h1>
        <div class="prose prose-lg text-gray-600 space-y-6">
            <p class="text-sm text-gray-500">Last updated: {{ now()->format('d F Y') }}</p>
            <p>By using CareLink services, you agree to these terms.</p>
            <h2 class="text-2xl font-semibold text-gray-900">Bookings</h2>
            <p>All bookings are subject to availability and confirmation. A booking reference is issued upon successful submission.</p>
            <h2 class="text-2xl font-semibold text-gray-900">Cancellations</h2>
            <p>Free cancellation is available up to 2 hours before scheduled pickup. Late cancellations may incur a fee.</p>
            <h2 class="text-2xl font-semibold text-gray-900">Payment</h2>
            <p>Payment is due after journey completion. Final fares may vary based on actual distance and additional services.</p>
            <h2 class="text-2xl font-semibold text-gray-900">Liability</h2>
            <p>We transport patients with reasonable care and skill. Patients must disclose all relevant medical information for safe transport.</p>
            <h2 class="text-2xl font-semibold text-gray-900">Privacy</h2>
            <p>Your use of our services is governed by our Privacy Policy, which complies with POPIA.</p>
        </div>
    </div>
</div>
@endsection