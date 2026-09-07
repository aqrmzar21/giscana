<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'GIScana')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-row sm:justify-center items-center bg-white-100">
            <!-- Kolom kiri: login -->
            <div class="flex items-center justify-center p-12">
                <div class="px-6 py-4 bg-white overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>

            <!-- Kolom kanan: gambar/peta -->
            <div class="hidden md:flex flex-1 max-screen">
                <img src="{{ asset('images/map-gorontalo.png') }}" alt="Map Gorontalo" class="object-cover w-full h-screen">
            </div>
        </div>
    </body>
