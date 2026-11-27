@extends('layouts.operations')

@section('title', 'Driver ' . $driver->user->first_name . ' ' . $driver->user->last_name)

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('operations.drivers.index') }}" class="text-gray-500 hover:text-gray-700 mb-2 inline-flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Drivers
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $driver->user->first_name }} {{ $driver->user->last_name }}</h1>
            <p class="text-gray-600 mt-1">{{ $driver->employee_number }} · {{ $driver->license_class }}</p>
        </div>
        <a href="{{ route('operations.drivers.edit', $driver) }}" class="btn-secondary px-4 py-2">Edit</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Driver Details</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Status</dt><dd class="font-medium capitalize">{{ str_replace('_', ' ', $driver->status) }}</dd></div>
                    <div><dt class="text-gray-500">License Number</dt><dd class="font-medium">{{ $driver->license_number }}</dd></div>
                    <div><dt class="text-gray-500">License Class</dt><dd class="font-medium">{{ $driver->license_class }}</dd></div>
                    <div><dt class="text-gray-500">Employee #</dt><dd class="font-medium">{{ $driver->employee_number }}</dd></div>
                </dl>
            </div>

            <!-- Compliance Dates -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Compliance Dates</h2>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    @foreach (['license_expiry' => 'License', 'prdp_expiry' => 'PrDP', 'medical_cert_expiry' => 'Medical Certificate', 'background_check_expiry' => 'Background Check'] as $field => $label)
                        <div>
                            <dt class="text-gray-500">{{ $label }}</dt>
                            <dd class="font-medium {{ $driver->$field && $driver->$field->lt(now()) ? 'text-red-600' : '' }}">
                                {{ $driver->$field ? $driver->$field->format('d M Y') : 'Not set' }}
                            </dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <!-- Documents -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Documents</h2>

                <div class="space-y-2 mb-6">
                    @forelse ($driver->documents as $doc)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $doc->type)) }}</p>
                                    <p class="text-xs text-gray-500">{{ $doc->original_filename }} @if($doc->expires_at) — expires {{ $doc->expires_at->format('d M Y') }} @endif</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('operations.drivers.documents.delete', [$driver, $doc]) }}" onsubmit="return confirm('Remove this document?')">
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
                <form method="POST" action="{{ route('operations.drivers.documents.upload', $driver) }}" enctype="multipart/form-data" class="space-y-3 border-t border-gray-200 pt-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                <option value="driver_license">Driver License</option>
                                <option value="prdp">PrDP</option>
                                <option value="medical_certificate">Medical Certificate</option>
                                <option value="background_check">Background Check</option>
                                <option value="training_certificate">Training Certificate</option>
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
                    {{ $driver->compliance_status === 'compliant' ? 'bg-green-100 text-green-800' : ($driver->compliance_status === 'non_compliant' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst(str_replace('_', ' ', $driver->compliance_status)) }}
                </span>
                <p class="text-sm text-gray-500 mt-3">{{ $driver->isCompliant() ? 'All required documents are valid.' : 'One or more compliance documents are expired or missing.' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection