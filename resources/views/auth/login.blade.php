@extends('layouts.guest')

@section('title', 'Login - CareLink Operations')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Operations Portal</h1>
            <p class="text-gray-600 mt-2">Sign in to manage patient transport</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="mfa_code" class="block text-sm font-medium text-gray-700 mb-1">Two-Factor Code <span class="font-normal text-gray-500">(if enabled)</span></label>
                    <input type="text" id="mfa_code" name="mfa_code" inputmode="numeric" autocomplete="one-time-code" placeholder="123456"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    @error('mfa_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-primary-600 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-primary-600 hover:underline">Forgot password?</a>
                </div>
                <button type="submit" class="btn-primary w-full py-3">Sign In</button>
            </form>
        </div>
        <p class="text-center text-sm text-gray-600 mt-6">
            Patient? <a href="{{ route('home') }}" class="text-primary-600 hover:underline">Book transport here</a>
        </p>
    </div>
</div>
@endsection