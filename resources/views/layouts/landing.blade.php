<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Giscana') }} - @yield('title', 'Geographic Information System for Natural Disaster Mitigation')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- TailwindCSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-xl font-bold text-primary-600">
                                <a href="{{ route('home') }}" class="flex items-center">
                                    <svg class="w-8 h-8 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    Giscana
                                </a>
                            </h1>
                        </div>
                        <div class="hidden md:ml-6 md:flex md:space-x-8">
                            <a href="{{ route('home') }}" class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                Beranda
                            </a>
                            <a href="{{ route('map.index') }}" class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                Peta Interaktif
                            </a>
                            <a href="#features" class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                Fitur
                            </a>
                            <a href="#about" class="text-gray-900 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                Tentang
                            </a>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        @auth
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-700">Halo, {{ Auth::user()->name }}</span>
                                <a href="{{ route('map.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                                    Dashboard
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="border-2 border-blue text-blue hover:text-white px-8 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                                Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Giscana</h3>
                        <p class="text-gray-400 mb-4">
                            Geographic Information System for Natural Disaster Mitigation 
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
                            <a href="#features" class="block text-gray-400 hover:text-white">Fitur</a>
                            <a href="#about" class="block text-gray-400 hover:text-white">Tentang</a>
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
