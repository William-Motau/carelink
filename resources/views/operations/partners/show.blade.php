@extends('layouts.operations')

@section('title', $partner->name)

@section('content')
<div class="p-6">
    <a href="{{ route('operations.partners.index') }}" class="text-primary-600 hover:underline text-sm">&larr; Back to Partners</a>

    <div class="mt-4 bg-white rounded-xl border border-gray-200 shadow-sm p-6 max-w-2xl">
        <h1 class="text-2xl font-bold text-gray-900">{{ $partner->name }}</h1>
        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-500">Type</dt><dd class="capitalize">{{ str_replace('_', ' ', $partner->type) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Email</dt><dd>{{ $partner->email }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Phone</dt><dd>{{ $partner->contact_phone }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Address</dt><dd>{{ $partner->address }}</dd></div>
        </dl>
    </div>
</div>
@endsection