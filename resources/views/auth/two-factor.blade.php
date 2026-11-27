@extends('layouts.guest')

@section('title', 'Two-Factor Authentication - CareLink')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Two-Factor Authentication</h1>
            <p class="text-gray-600 mb-6">Add an extra layer of security to your account.</p>
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('two-factor.store') }}" class="space-y-4">
                @csrf
                <button type="submit" class="btn-primary w-full py-3">Enable Two-Factor</button>
            </form>
            @if (auth()->user()->mfa_enabled)
                <form method="POST" action="{{ route('two-factor.destroy') }}" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger w-full py-3">Disable Two-Factor</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection