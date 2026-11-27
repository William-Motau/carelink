@extends('layouts.operations')

@section('title', 'Partners')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Partners</h1>
            <p class="text-gray-600 mt-1">Partner and emergency-service organisations.</p>
        </div>
        @if(auth()->user()?->role === 'super_admin')
            <a href="{{ route('operations.partners.create') }}" class="btn-primary px-4 py-2 rounded-lg text-white font-medium">Register Partner</a>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        @forelse($partners as $partner)
            <a href="{{ route('operations.partners.show', $partner) }}" class="flex items-center justify-between px-6 py-4 border-b border-gray-100 last:border-0 hover:bg-gray-50">
                <div>
                    <p class="font-medium text-gray-900">{{ $partner->name }}</p>
                    <p class="text-sm text-gray-500">{{ $partner->contact_phone }}</p>
                </div>
                <div class="flex items-center gap-3">
                    @if($partner->partner_category)
                        <span class="text-xs px-2 py-1 rounded-full bg-primary-50 text-primary-700 capitalize">{{ str_replace('_', ' ', $partner->partner_category) }}</span>
                    @endif
                    <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-700 capitalize">{{ str_replace('_', ' ', $partner->type) }}</span>
                </div>
            </a>
        @empty
            <p class="px-6 py-8 text-center text-gray-500">No partners configured.</p>
        @endforelse
    </div>
</div>
@endsection