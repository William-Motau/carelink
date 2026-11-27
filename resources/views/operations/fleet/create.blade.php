@extends('layouts.operations')

@section('title', 'Add Vehicle')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <a href="{{ route('operations.fleet.index') }}" class="text-gray-500 hover:text-gray-700 mb-4 inline-flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Fleet
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Add Vehicle</h1>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl">
        <form class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number *</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Type *</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                        <option value="">Select type</option>
                        <option>Wheelchair Accessible</option>
                        <option>Stretcher / Ambulance</option>
                        <option>Standard Sedan</option>
                        <option>Bariatric</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seated Capacity *</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Wheelchair Capacity</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('operations.fleet.index') }}" class="btn-secondary px-4 py-2">Cancel</a>
                <button type="button" class="btn-primary px-4 py-2">Save Vehicle</button>
            </div>
        </form>
    </div>
</div>
@endsection