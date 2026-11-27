@extends('layouts.operations')

@section('title', 'Settings')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
        <p class="text-gray-600 mt-1">Manage organisation settings, pricing rules, and system configuration</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Organisation Profile</h2>
            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Organisation Name</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                    <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <button type="button" class="btn-primary px-4 py-2">Save Changes</button>
            </form>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Pricing Rules</h2>
            <p class="text-sm text-gray-500 mb-4">Manage base fares, distance charges, and surcharges.</p>
            <button type="button" class="btn-secondary px-4 py-2">Manage Pricing</button>
        </div>
    </div>
</div>
@endsection