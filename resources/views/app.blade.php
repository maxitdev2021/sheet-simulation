<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>Simulation App</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (app()->environment('local'))
        {{-- Development (uses Vite server) --}}
        @vite(['resources/js/app.js'])
    @else
        {{-- Production (uses built static assets) --}}
        <link rel="stylesheet" href="{{ asset('build/assets/app-YAddkS1L.css') }}">
        <script src="{{ asset('build/assets/app-Cpkp2jct.js') }}" defer></script>
    @endif
</head>
<body>
    @inertia
</body>
</html>
