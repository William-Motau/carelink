@extends('layouts.guest')

@section('title', 'Verify Email - CareLink')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Verify Your Email</h1>
            <p class="text-gray-600 mb-6">We've sent a verification link to your email. Please check your inbox.</p>
            @if (session('status') === 'verification-link-sent')
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                    A new verification link has been sent.
                </div>
            @endif
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-primary w-full py-3">Resend Verification Email</button>
            </form>
        </div>
    </div>
</div>
@endsection