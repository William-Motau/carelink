@extends('layouts.operations')

@section('title', 'Driver Management')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Driver Management</h1>
            <p class="text-gray-600 mt-1">Manage drivers, qualifications, and documents</p>
        </div>
        <a href="{{ route('operations.drivers.create') }}" class="btn-primary px-4 py-2">Add Driver</a>
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if ($drivers->isEmpty())
            <div class="p-12 text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-1">No drivers found</h3>
                <p class="text-gray-500 mb-4">Add your first driver to start managing your team.</p>
                <a href="{{ route('operations.drivers.create') }}" class="btn-primary px-4 py-2 inline-block">Add Driver</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee #</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">License Class</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Compliance</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">PrDP Expiry</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach ($drivers as $driver)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $driver->user->first_name }} {{ $driver->user->last_name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $driver->employee_number }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $driver->license_class }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $driver->status === 'active' ? 'bg-green-100 text-green-800' : ($driver->status === 'suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $driver->status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full
                                        {{ $driver->compliance_status === 'compliant' ? 'bg-green-100 text-green-800' : ($driver->compliance_status === 'non_compliant' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst(str_replace('_', ' ', $driver->compliance_status)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm {{ $driver->prdp_expiry && $driver->prdp_expiry->lt(now()) ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                    {{ $driver->prdp_expiry ? $driver->prdp_expiry->format('d M Y') : '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('operations.drivers.show', $driver) }}" class="text-sm text-primary-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-gray-200">{{ $drivers->links() }}</div>
        @endif
    </div>
</div>
@endsection