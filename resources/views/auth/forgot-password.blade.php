@extends('layouts.guest')

@section('title', 'Forgot Password - CareLink')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Forgot Password</h1>
            <p class="text-gray-600 mt-2">Enter your email to receive a reset link</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            @if (session('status'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                    {{ session('status') }}
                </div>
            @endif
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn-primary w-full py-3">Send Reset Link</button>
            </form>
        </div>
        <p class="text-center text-sm text-gray-600 mt-6">
            <a href="{{ route('login') }}" class="text-primary-600 hover:underline">Back to sign in</a>
        </p>
    </div>
</div>
@endsection