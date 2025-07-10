<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>Simulation App</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (app()->environment('local'))
        @vite(['resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('build/assets/app-DL65rIRg.css') }}">
        <link rel="stylesheet" href="{{ asset('build/assets/app-YAddkS1L.css') }}">
        <script src="{{ asset('build/assets/app-BX5JErE7.js') }}" type="module"></script>
    @endif
</head>
<body>
    @inertia
</body>
</html>
