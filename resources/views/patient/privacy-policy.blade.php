@extends('layouts.guest')

@section('title', 'Privacy Policy - CareLink')

@section('content')
<div class="bg-white py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-8">Privacy Policy</h1>
        <div class="prose prose-lg text-gray-600 space-y-6">
            <p class="text-sm text-gray-500">Last updated: {{ now()->format('d F Y') }}</p>
            <p>CareLink is committed to protecting your personal information in accordance with the Protection of Personal Information Act (POPIA).</p>
            <h2 class="text-2xl font-semibold text-gray-900">Information We Collect</h2>
            <p>We collect patient names, contact details, ID numbers, medical notes, mobility requirements, and journey information necessary to provide transport services.</p>
            <h2 class="text-2xl font-semibold text-gray-900">How We Use Your Information</h2>
            <p>Your information is used solely to arrange and provide patient transport, process payments, and comply with legal obligations. We never sell your data.</p>
            <h2 class="text-2xl font-semibold text-gray-900">Data Security</h2>
            <p>All personal and medical data is encrypted in transit and at rest. Access is restricted to authorized personnel on a need-to-know basis.</p>
            <h2 class="text-2xl font-semibold text-gray-900">Your Rights</h2>
            <p>You have the right to access, correct, or delete your personal information, and to withdraw consent at any time. Contact our Information Officer to exercise these rights.</p>
            <h2 class="text-2xl font-semibold text-gray-900">Retention</h2>
            <p>We retain personal information only as long as necessary for the purposes collected or as required by law.</p>
        </div>
    </div>
</div>
@endsection