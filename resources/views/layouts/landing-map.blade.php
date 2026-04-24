<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Giscana') }} - @yield('title', 'GIScana')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased h-screen flex flex-col overflow-hidden bg-gray-100">
    @include('components.landing-nav')

    <main class="flex flex-1 flex-col min-h-0 min-w-0">
        @yield('content')
    </main>

    <footer class="shrink-0 z-40 border-t border-gray-200 bg-white shadow-[0_-4px_24px_rgba(0,0,0,0.08)] max-h-[42vh] overflow-y-auto overscroll-contain">
        @yield('map-toolbar')
    </footer>

    @stack('scripts')
</body>
</html>
