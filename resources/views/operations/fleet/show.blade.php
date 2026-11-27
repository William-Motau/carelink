@extends('layouts.operations')

@section('title', 'Vehicle ' . $vehicle->registration_number)

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('operations.fleet.index') }}" class="text-gray-500 hover:text-gray-700 mb-2 inline-flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Fleet
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $vehicle->registration_number }}</h1>
            <p class="text-gray-600 mt-1">{{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year }})</p>
        </div>
        <a href="{{ route('operations.fleet.edit', $vehicle) }}" class="btn-secondary px-4 py-2">Edit</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Vehicle Details</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Type</dt><dd class="font-medium">{{ ucfirst(str_replace('_', ' ', $vehicle->vehicle_type)) }}</dd></div>
                    <div><dt class="text-gray-500">Status</dt><dd class="font-medium capitalize">{{ str_replace('_', ' ', $vehicle->status) }}</dd></div>
                    <div><dt class="text-gray-500">Seated Capacity</dt><dd class="font-medium">{{ $vehicle->capacity_seated }}</dd></div>
                    <div><dt class="text-gray-500">Wheelchair Capacity</dt><dd class="font-medium">{{ $vehicle->capacity_wheelchair }}</dd></div>
                    <div><dt class="text-gray-500">Fuel Type</dt><dd class="font-medium">{{ ucfirst($vehicle->fuel_type) }}</dd></div>
                    <div><dt class="text-gray-500">Current KM</dt><dd class="font-medium">{{ number_format($vehicle->current_km) }}</dd></div>
                </dl>
            </div>

            <!-- Compliance Dates -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Compliance Dates</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    @foreach (['license_expiry' => 'License', 'cofc_expiry' => 'COFC', 'insurance_expiry' => 'Insurance', 'operating_permit_expiry' => 'Operating Permit'] as $field => $label)
                        <div>
                            <dt class="text-gray-500">{{ $label }}</dt>
                            <dd class="font-medium {{ $vehicle->$field && $vehicle->$field->lt(now()) ? 'text-red-600' : '' }}">
                                {{ $vehicle->$field ? $vehicle->$field->format('d M Y') : 'Not set' }}
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Documents</h2>
                </div>

                <div class="space-y-2 mb-6">
                    @forelse ($vehicle->documents as $doc)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $doc->type)) }}</p>
                                    <p class="text-xs text-gray-500">{{ $doc->original_filename }} @if($doc->expires_at) — expires {{ $doc->expires_at->format('d M Y') }} @endif</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('operations.fleet.documents.delete', [$vehicle, $doc]) }}" onsubmit="return confirm('Remove this document?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No documents uploaded yet.</p>
                    @endforelse
                </div>

                <!-- Upload form -->
                <form method="POST" action="{{ route('operations.fleet.documents.upload', $vehicle) }}" enctype="multipart/form-data" class="space-y-3 border-t border-gray-200 pt-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="vehicle_license">Vehicle License</option>
                                <option value="cofc">COFC</option>
                                <option value="insurance">Insurance</option>
                                <option value="operating_permit">Operating Permit</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expires At</label>
                            <input type="date" name="expires_at" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">File (PDF/JPG/PNG, max 10MB)</label>
                            <input type="file" name="document" required class="w-full text-sm border border-gray-300 rounded-lg p-1">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary px-4 py-2 text-sm">Upload Document</button>
                </form>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">Compliance Status</h2>
                <span class="px-3 py-1 text-sm font-medium rounded-full
                    {{ $vehicle->compliance_status === 'compliant' ? 'bg-green-100 text-green-800' : ($vehicle->compliance_status === 'non_compliant' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst(str_replace('_', ' ', $vehicle->compliance_status)) }}
                </span>
                <p class="text-sm text-gray-500 mt-3">{{ $vehicle->isCompliant() ? 'All required documents are valid.' : 'One or more compliance documents are expired or missing.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection