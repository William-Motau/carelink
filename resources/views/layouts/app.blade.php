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
<body class="@yield('body-class', 'antialiased')">
    @yield('content')
    
    @stack('scripts')
    
    <!-- Cookie Consent Banner -->
    @include('components.cookie-consent')
    
    <!-- Livewire/Alpine scripts -->
    <script>
        window.csrfToken = '{{ csrf_token() }}';
        window.appUrl = '{{ url('/') }}';
    </script>
</body>
</html>