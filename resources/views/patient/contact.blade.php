@extends('layouts.guest')

@section('title', 'Contact Us - CareLink')

@section('content')
<div class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4 text-center">Contact Us</h1>
        <p class="text-gray-600 text-center mb-12">We are here to help 24/7.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 space-y-4">
                <div>
                    <h3 class="font-semibold text-gray-900">24/7 Operations</h3>
                    <a href="tel:0800227354" class="text-primary-600 text-xl font-bold">0800 227 354</a>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Email</h3>
                    <a href="mailto:support@carelink.co.za" class="text-primary-600">support@carelink.co.za</a>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Address</h3>
                    <p class="text-gray-600">Johannesburg, Gauteng, South Africa</p>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>
                    <button type="button" class="btn-primary px-6 py-2 w-full">Send Message</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection