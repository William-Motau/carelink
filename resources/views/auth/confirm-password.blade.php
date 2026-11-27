@extends('layouts.guest')

@section('title', 'Confirm Password - CareLink')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Confirm Password</h1>
            <p class="text-gray-600 mb-6">Please confirm your password to continue.</p>
            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn-primary w-full py-3">Confirm</button>
            </form>
        </div>
    </div>
</div>
@endsection