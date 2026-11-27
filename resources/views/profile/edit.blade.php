@extends('layouts.operations')

@section('title', 'My Profile')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
        <p class="text-gray-600 mt-1">Update your personal details and password.</p>
    </div>

    <div class="max-w-2xl bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="flex items-center gap-4">
                @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="Profile picture" class="h-16 w-16 rounded-full object-cover border border-gray-200">
                @else
                    <span class="h-16 w-16 rounded-full bg-carelink-navy text-white flex items-center justify-center text-lg font-semibold">{{ $user->initials }}</span>
                @endif
                <div>
                    <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">Profile Picture</label>
                    <input id="avatar" name="avatar" type="file" accept="image/*"
                        class="block w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="mt-1 text-xs text-gray-500">JPG, PNG or WEBP, up to 2MB.</p>
                    @error('avatar') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $user->first_name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('first_name') border-red-500 @enderror">
                    @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $user->last_name) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('last_name') border-red-500 @enderror">
                    @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('email') border-red-500 @enderror">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone', $user->phone) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('phone') border-red-500 @enderror">
                @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="border-t border-gray-200 pt-4">
                <h2 class="text-sm font-semibold text-gray-900 mb-3">Change Password <span class="font-normal text-gray-500">(leave blank to keep current)</span></h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 @error('password') border-red-500 @enderror">
                        @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary px-4 py-2 rounded-lg text-white font-medium">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection