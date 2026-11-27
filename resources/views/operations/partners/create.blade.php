@extends('layouts.operations')

@section('title', 'Register Partner')

@section('content')
<div class="p-6">
    <a href="{{ route('operations.partners.index') }}" class="text-primary-600 hover:underline text-sm">&larr; Back to Partners</a>

    <div class="mt-4 max-w-3xl bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h1 class="text-2xl font-bold text-gray-900">Register Partner</h1>
        <p class="text-gray-600 mt-1">Add a hospital, healthcare practice, caregiver agency, or transport partner and create their administrator login.</p>

        <form method="POST" action="{{ route('operations.partners.store') }}" class="mt-6 space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Organisation Name *</label>
                    <input id="name" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('name') border-red-500 @enderror">
                    @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="partner_category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select id="partner_category" name="partner_category" class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('partner_category') border-red-500 @enderror">
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" @selected(old('partner_category') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('partner_category')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Contact Email *</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('email') border-red-500 @enderror">
                    @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Contact Phone *</label>
                    <input id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('contact_phone') border-red-500 @enderror">
                    @error('contact_phone')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <input id="address" name="address" value="{{ old('address') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>

            <div class="border-t border-gray-200 pt-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-3">Administrator Account</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="admin_first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                        <input id="admin_first_name" name="admin_first_name" value="{{ old('admin_first_name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('admin_first_name') border-red-500 @enderror">
                        @error('admin_first_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="admin_last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                        <input id="admin_last_name" name="admin_last_name" value="{{ old('admin_last_name') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('admin_last_name') border-red-500 @enderror">
                        @error('admin_last_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="admin_email" class="block text-sm font-medium text-gray-700 mb-1">Login Email *</label>
                        <input id="admin_email" name="admin_email" type="email" value="{{ old('admin_email') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('admin_email') border-red-500 @enderror">
                        @error('admin_email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="admin_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input id="admin_phone" name="admin_phone" value="{{ old('admin_phone') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Temporary Password *</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('password') border-red-500 @enderror">
                        @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password *</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('operations.partners.index') }}" class="btn-secondary px-4 py-2 rounded-lg">Cancel</a>
                <button type="submit" class="btn-primary px-4 py-2 rounded-lg text-white font-medium">Register Partner</button>
            </div>
        </form>
    </div>
</div>
@endsection