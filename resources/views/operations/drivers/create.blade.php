@extends('layouts.operations')

@section('title', 'Add Driver')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('operations.drivers.index') }}" class="text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Drivers
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Add Driver</h1>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl">
        <form class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                    <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">License Class *</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Select class</option>
                        <option>Code 10 (C)</option>
                        <option>Code 11 (C1)</option>
                        <option>Code 12 (A)</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('operations.drivers.index') }}" class="btn-secondary px-4 py-2">Cancel</a>
                <button type="button" class="btn-primary px-4 py-2">Save Driver</button>
            </div>
        </form>
    </div>
</div>
@endsection