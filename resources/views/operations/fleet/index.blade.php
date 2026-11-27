@extends('layouts.operations')

@section('title', 'Fleet Management')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Fleet Management</h1>
            <p class="text-gray-600 mt-1">Manage vehicles, documents, and availability</p>
        </div>
        <a href="{{ route('operations.fleet.create') }}" class="btn-primary px-4 py-2">Add Vehicle</a>
    </div>

    <!-- Status counts -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
        @foreach (['available' => 'green', 'assigned' => 'blue', 'in_service' => 'purple', 'under_maintenance' => 'yellow', 'out_of_service' => 'red', 'compliance_suspended' => 'red'] as $status => $color)
            <a href="{{ route('operations.fleet.index', ['status' => $status]) }}" class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:border-primary-300">
                <p class="text-xs font-medium text-gray-500 capitalize">{{ str_replace('_', ' ', $status) }}</p>
                <p class="text-2xl font-bold text-{{ $color }}-600 mt-1">{{ $statusCounts[$status] ?? 0 }}</p>
            </a>
        @endforeach
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if ($vehicles->isEmpty())
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No vehicles found</h3>
                <p class="text-gray-500 mb-4">Add your first vehicle to start managing your fleet.</p>
                <a href="{{ route('operations.fleet.create') }}" class="btn-primary px-4 py-2 inline-block">Add Vehicle</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registration</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Compliance</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">COFC Expiry</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($vehicles as $vehicle)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $vehicle->registration_number }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst(str_replace('_', ' ', $vehicle->vehicle_type)) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $vehicle->capacity_seated }} seats @if($vehicle->capacity_wheelchair) + {{ $vehicle->capacity_wheelchair }} WC @endif</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $vehicle->status === 'available' ? 'bg-green-100 text-green-800' : ($vehicle->status === 'assigned' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $vehicle->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $vehicle->compliance_status === 'compliant' ? 'bg-green-100 text-green-800' : ($vehicle->compliance_status === 'non_compliant' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $vehicle->compliance_status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm {{ $vehicle->cofc_expiry && $vehicle->cofc_expiry->lt(now()) ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                    {{ $vehicle->cofc_expiry ? $vehicle->cofc_expiry->format('d M Y') : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('operations.fleet.show', $vehicle) }}" class="text-sm text-primary-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200">{{ $vehicles->links() }}</div>
        @endif
    </div>
</div>
@endsection