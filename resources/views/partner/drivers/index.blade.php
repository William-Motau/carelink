@extends('layouts.operations')

@section('title', 'My Drivers')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Drivers</h1>
        <p class="text-gray-600 mt-1">Drivers registered under your organisation</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
        <h3 class="text-lg font-medium text-gray-900 mb-1">No drivers registered</h3>
        <p class="text-gray-500">Your drivers will appear here.</p>
    </div>
</div>
@endsection