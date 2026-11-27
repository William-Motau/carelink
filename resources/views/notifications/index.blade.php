@extends('layouts.operations')

@section('title', 'Notifications')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
            <p class="text-gray-600 mt-1">Your recent updates.</p>
        </div>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf
                <button type="submit" class="btn-secondary px-4 py-2 rounded-lg text-sm">Mark all as read</button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm divide-y divide-gray-100">
        @forelse($notifications as $notification)
            <div class="flex items-start justify-between gap-4 px-6 py-4 {{ $notification->read_at ? '' : 'bg-primary-50/50' }}">
                <div class="flex items-start gap-3">
                    <span class="mt-1 h-2 w-2 rounded-full {{ $notification->read_at ? 'bg-gray-300' : 'bg-primary-600' }}"></span>
                    <div>
                        <p class="font-medium text-gray-900">
                            {{ $notification->data['message'] ?? 'Booking update' }}
                        </p>
                        <p class="text-sm text-gray-600">
                            @if(!empty($notification->data['reference']))
                                Reference: <span class="font-mono">{{ $notification->data['reference'] }}</span>
                            @endif
                            @if(!empty($notification->data['status']))
                                <span class="ml-2 capitalize">{{ str_replace('_', ' ', $notification->data['status']) }}</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @if(! $notification->read_at)
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                        @csrf
                        <button type="submit" class="text-xs text-primary-600 hover:underline">Mark read</button>
                    </form>
                @endif
            </div>
        @empty
            <p class="px-6 py-10 text-center text-gray-500">You have no notifications.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div>
@endsection