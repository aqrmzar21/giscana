<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Giscana') }} - @yield('title', 'GIScana')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- TailwindCSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        @include('components.landing-nav')

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Gisnater</h3>
                        <p class="text-gray-400 mb-4">
                            Geographic Information System for Natural Disaster  
                            di Kawasan Pesisir Bone, Kabupaten Bone Bolango, Provinsi Gorontalo.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Kontak</h3>
                        <div class="text-gray-400 space-y-2">
                            <p>BPBD Bone Bolango</p>
                            <p>Email: info@giscana.local</p>
                            <p>Telp: +62 812 3456 7890</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Link Cepat</h3>
                        <div class="space-y-2">
                            <a href="{{ route('map.index') }}" class="block text-gray-400 hover:text-white">Peta Interaktif</a>
                            <a href="{{ route('home') }}#features" class="block text-gray-400 hover:text-white">Fitur</a>
                            <a href="{{ route('home') }}#about" class="block text-gray-400 hover:text-white">Tentang</a>
                        </div>
                    </div>
                </div>
                <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                    <p>&copy; {{ date('Y') }} Giscana. Hak Cipta Dilindungi.</p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
