<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Operations Portal') - CareLink</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
        <!-- Sidebar -->
        <aside 
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:z-auto"
            aria-label="Sidebar navigation"
        >
            <div class="flex h-16 items-center justify-between px-4 border-b border-gray-200">
                <a href="{{ route('operations.dashboard') }}" class="flex items-center space-x-2">
                    <svg class="h-8 w-8 text-carelink-navy" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <span class="text-xl font-bold text-carelink-navy">CareLink</span>
                </a>
                <button 
                    @click="sidebarOpen = false" 
                    class="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100"
                    aria-label="Close sidebar"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1" aria-label="Main navigation">
                <div>
                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Main</h3>
                    <a href="{{ route('operations.dashboard') }}" class="nav-link {{ request()->routeIs('operations.dashboard') ? 'active' : '' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 hover:text-carelink-navy transition-colors">
                        <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        Dashboard
                    </a>
                </div>
                
                @auth
                    @if(auth()->user()->isPartner())
                    <div>
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Partner Portal</h3>
                        <a href="{{ route('partner.dashboard') }}" class="nav-link {{ request()->routeIs('partner.dashboard') ? 'active' : '' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 hover:text-carelink-navy transition-colors">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            Dashboard
                        </a>
                        <a href="{{ route('partner.commissions.index') }}" class="nav-link {{ request()->routeIs('partner.commissions.*') ? 'active' : '' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 hover:text-carelink-navy transition-colors">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Commissions
                        </a>
                        <a href="{{ route('partner.settlements.index') }}" class="nav-link {{ request()->routeIs('partner.settlements.*') ? 'active' : '' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 hover:text-carelink-navy transition-colors">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m13 2v-2a4 4 0 00-3.194-3.911M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Settlements
                        </a>
                    </div>
                    @endif
                @endauth

                @auth
                    @if(auth()->user()->hasRole(['super_admin', 'org_admin', 'dispatch_manager']))
                    <div>
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Bookings</h3>
                        <a href="{{ route('operations.bookings.index') }}" class="nav-link {{ request()->routeIs('operations.bookings.*') ? 'active' : '' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 hover:text-carelink-navy transition-colors">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            All Bookings
                        </a>
                        <a href="{{ route('operations.dispatch') }}" class="nav-link {{ request()->routeIs('operations.dispatch') ? 'active' : '' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 hover:text-carelink-navy transition-colors">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Dispatch Board
                        </a>
                    @endif
                @endauth
                
                @auth
                    @if(auth()->user()->hasRole(['super_admin', 'org_admin', 'dispatch_manager', 'partner_admin', 'partner_operations_manager']))
                    <div>
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Fleet & Drivers</h3>
                        <a href="{{ route('operations.fleet.index') }}" class="nav-link {{ request()->routeIs('operations.fleet.*') ? 'active' : '' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 hover:text-carelink-navy transition-colors">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Fleet Register
                        </a>
                        <a href="{{ route('operations.drivers.index') }}" class="nav-link {{ request()->routeIs('operations.drivers.*') ? 'active' : '' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 hover:text-carelink-navy transition-colors">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Drivers
                        </a>
                    @endif
                @endauth
                
                @auth
                    @if(auth()->user()->hasRole(['super_admin', 'org_admin', 'finance_officer']))
                    <div>
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Financial</h3>
                        <a href="{{ route('operations.commissions.index') }}" class="nav-link {{ request()->routeIs('operations.commissions.*') ? 'active' : '' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 hover:text-carelink-navy transition-colors">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Commissions
                        </a>
                        <a href="{{ route('operations.settlements.index') }}" class="nav-link {{ request()->routeIs('operations.settlements.*') ? 'active' : '' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 hover:text-carelink-navy transition-colors">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Settlements
                        </a>
                    @endif
                @endauth
                
                @auth
                    @if(auth()->user()->hasRole(['super_admin', 'org_admin', 'compliance_officer']))
                    <div>
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Compliance</h3>
                        <a href="{{ route('operations.compliance.index') }}" class="nav-link {{ request()->routeIs('operations.compliance.*') ? 'active' : '' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 hover:text-carelink-navy transition-colors">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Compliance Dashboard
                        </a>
                    @endif
                @endauth
                
                @auth
                    @if(auth()->user()->hasRole(['super_admin', 'org_admin', 'dispatch_manager', 'finance_officer']))
                    <div>
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Reports</h3>
                        <a href="{{ route('operations.reports.index') }}" class="nav-link {{ request()->routeIs('operations.reports.*') ? 'active' : '' }} flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 hover:text-carelink-navy transition-colors">
                            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Reports & Analytics
                        </a>
                    @endif
                @endauth
            </nav>
            
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center px-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-full bg-carelink-navy flex items-center justify-center text-white font-medium">
                            {{ strtoupper(auth()->user()->first_name[0]) }}
                        </div>
                    </div>
                    <div class="ml-3 flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->full_name }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                </div>
                <div class="mt-3 px-3 space-y-1">
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Profile</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">Sign Out</button>
                    </form>
                </div>
            </div>
        </aside>
        
        <!-- Sidebar overlay for mobile -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
            @click="sidebarOpen = false"
            aria-hidden="true"
        ></div>
        
        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden lg:ml-64">
            <!-- Top bar -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6">
                    <button 
                        @click="sidebarOpen = true" 
                        class="lg:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100"
                        aria-label="Open sidebar"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <div class="flex-1 lg:flex-none">
                        @yield('page-title')
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        @php
                            $notificationItems = auth()->user()?->notifications()->latest()->take(5)->get() ?? collect();
                            $unreadCount = auth()->user()?->unreadNotifications()->count() ?? 0;
                        @endphp
                        <div class="relative" x-data="{ open: false }" @keydown.escape="open = false">
                            <button type="button" @click="open = !open" class="p-2 rounded-full text-gray-500 hover:bg-gray-100 relative" aria-label="Notifications" :aria-expanded="open">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                @if($unreadCount > 0)
                                    <span class="absolute top-1 right-1 h-4 min-w-4 px-1 bg-red-500 rounded-full text-xs text-white flex items-center justify-center">{{ $unreadCount }}</span>
                                @endif
                            </button>
                            <div x-show="open" x-cloak @click.outside="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                    <span class="font-semibold text-gray-900">Notifications</span>
                                    @if($unreadCount > 0)
                                        <form method="POST" action="{{ route('notifications.read-all') }}">
                                            @csrf
                                            <button type="submit" class="text-xs text-primary-600 hover:underline">Mark all read</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="max-h-96 overflow-y-auto divide-y divide-gray-100">
                                    @forelse($notificationItems as $n)
                                        <div class="px-4 py-3 {{ $n->read_at ? '' : 'bg-primary-50/50' }}">
                                            <p class="text-sm font-medium text-gray-900">{{ $n->data['message'] ?? 'Booking update' }}</p>
                                            @if(!empty($n->data['reference']))
                                                <p class="text-xs text-gray-500 font-mono">{{ $n->data['reference'] }}</p>
                                            @endif
                                            <p class="text-xs text-gray-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                                        </div>
                                    @empty
                                        <p class="px-4 py-6 text-center text-sm text-gray-500">No notifications</p>
                                    @endforelse
                                </div>
                                <a href="{{ route('notifications.index') }}" class="block px-4 py-3 text-center text-sm text-primary-600 hover:bg-gray-50 border-t border-gray-100">View all notifications</a>
                            </div>
                        </div>
                        <!-- User menu -->
                        @auth
                        <div class="relative" x-data="{ open: false }" @keydown.escape="open = false">
                            <button type="button" @click="open = !open" class="flex items-center gap-2 rounded-full hover:bg-gray-100 p-1" aria-label="Account menu" :aria-expanded="open">
                                @if(auth()->user()->avatar_url)
                                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="h-8 w-8 rounded-full object-cover border border-gray-200">
                                @else
                                    <span class="h-8 w-8 rounded-full bg-carelink-navy text-white flex items-center justify-center text-sm font-semibold">{{ auth()->user()->initials }}</span>
                                @endif
                                <span class="hidden sm:inline text-sm font-medium text-gray-700 max-w-[10rem] truncate">{{ auth()->user()->full_name }}</span>
                                <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-cloak @click.outside="open = false" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 z-50 overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->full_name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">My Profile</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">Log Out</button>
                                </form>
                            </div>
                        </div>
                        @endauth
                    </div>
                </div>
            </header>
            
            <!-- Page content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                    <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4" role="alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4" role="alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10 7.293 11.293a1 1 0 001.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-800">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    
    <!-- Cookie Consent Banner -->
    @include('components.cookie-consent')
    
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        window.appUrl = '{{ url('/') }}';
    </script>
</body>
</html>