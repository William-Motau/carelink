<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CareLink') - Patient Transport Booking</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="@yield('body-class', 'antialiased bg-gray-50')">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" aria-label="Main navigation">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2" aria-label="CareLink Home">
                        <svg class="h-8 w-8 text-carelink-navy" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        <span class="text-xl font-bold text-carelink-navy">CareLink</span>
                    </a>
                </div>
                
                <div class="hidden md:flex md:items-center md:space-x-8">
                    <a href="#about" class="text-sm font-medium text-gray-700 hover:text-carelink-navy transition-colors">About</a>
                    <a href="#services" class="text-sm font-medium text-gray-700 hover:text-carelink-navy transition-colors">Services</a>
                    <a href="#how-it-works" class="text-sm font-medium text-gray-700 hover:text-carelink-navy transition-colors">How It Works</a>
                    @auth
                    <a href="{{ route('login') }}" class="text-sm font-medium text-carelink-navy hover:underline transition-colors">Staff Portal</a>
                    @endauth
                    <a href="{{ route('booking.create') }}" class="btn-primary">Book Transport</a>
                </div>
                
                <div class="md:hidden">
                    <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:bg-gray-100" aria-expanded="false" aria-controls="mobile-menu" aria-label="Toggle menu">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </nav>
        
        <!-- Mobile menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="#about" class="text-base font-medium text-gray-700 hover:text-carelink-navy block px-3 py-2">About</a>
                <a href="#services" class="text-base font-medium text-gray-700 hover:text-carelink-navy block px-3 py-2">Services</a>
                <a href="#how-it-works" class="text-base font-medium text-gray-700 hover:text-carelink-navy block px-3 py-2">How It Works</a>
                @auth
                <a href="{{ route('login') }}" class="text-base font-medium text-carelink-navy hover:underline block px-3 py-2">Staff Portal</a>
                @endauth
                <a href="{{ route('booking.create') }}" class="btn-primary block text-center mx-3 mt-2">Book Transport</a>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-carelink-navy text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">CareLink</h3>
                    <p class="text-gray-300 text-sm">Reliable patient transport services connecting patients with safe, comfortable transportation.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li><a href="#about" class="hover:text-white transition-colors">About Us</a></li>
                        <li><a href="#services" class="hover:text-white transition-colors">Services</a></li>
                        <li><a href="{{ route('booking.create') }}" class="hover:text-white transition-colors">Book Transport</a></li>
                        @auth
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Staff Portal</a></li>
                        @endauth
                        <li><a href="{{ route('home') }}#track" class="hover:text-white transition-colors">Track Booking</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Legal</h3>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li><a href="{{ route('privacy.policy') }}" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('cookie.policy') }}" class="hover:text-white transition-colors">Cookie Policy</a></li>
                        <li><a href="{{ route('terms.conditions') }}" class="hover:text-white transition-colors">Terms & Conditions</a></li>
                        <li><a href="{{ route('accessibility') }}" class="hover:text-white transition-colors">Accessibility</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <ul class="space-y-2 text-sm text-gray-300">
                        <li>Email: info@carelink.example.com</li>
                        <li>Phone: 0800 123 456</li>
                        <li>Hours: Mon-Fri 8am-6pm</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} CareLink. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
    
    <!-- Cookie Consent Banner -->
    @include('components.cookie-consent')
    
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        window.appUrl = '{{ url('/') }}';
        
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuButton = document.querySelector('[aria-controls="mobile-menu"]');
            const mobileMenu = document.getElementById('mobile-menu');
            if (menuButton && mobileMenu) {
                menuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                    this.setAttribute('aria-expanded', mobileMenu.classList.contains('hidden') ? 'false' : 'true');
                });
            }
        });
    </script>
</body>
</html>