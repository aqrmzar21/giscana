@php $__isPjax = request()->header('X-PJAX') === 'true'; @endphp
@if(!$__isPjax)
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
    <!-- PJAX Loading Bar -->
    <div id="pjax-progress" style="position:fixed;top:0;left:0;width:0;height:3px;background:linear-gradient(90deg,#3b82f6,#6366f1);z-index:9999;transition:width 0.3s ease,opacity 0.4s ease;opacity:0;pointer-events:none;"></div>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
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
                        </div>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center space-x-4">
                        @auth
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('dashboard') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
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
                            <a href="{{ route('login') }}" class="border-2 border-blue-600 text-blue-600 hover:text-white px-8 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                                Login
                            </a>
                        @endauth
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="flex items-center md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 p-2">
                            <span class="sr-only">Buka menu utama</span>
                            <svg class="h-6 w-6" x-show="!mobileMenuOpen" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg class="h-6 w-6" x-show="mobileMenuOpen" style="display: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu Panel -->
            <div x-show="mobileMenuOpen" style="display: none;" class="md:hidden border-t border-gray-200">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                    <a href="{{ route('home') }}" class="block text-gray-900 hover:text-blue-600 hover:bg-gray-50 px-3 py-2 rounded-md text-base font-medium">
                        Beranda
                    </a>
                    <a href="{{ route('map.index') }}" class="block text-gray-900 hover:text-blue-600 hover:bg-gray-50 px-3 py-2 rounded-md text-base font-medium">
                        Peta Interaktif
                    </a>
                </div>
                <div class="pt-4 pb-3 border-t border-gray-200 px-4">
                    @auth
                        <div class="flex items-center mb-3">
                            <div class="text-base font-medium text-gray-800">{{ Auth::user()->name }}</div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <a href="{{ route('dashboard') }}" class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-md text-base font-medium hover:bg-blue-700 mb-2">
                                Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block w-full">
                                @csrf
                                <button type="submit" class="w-full text-center border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-md text-base font-medium">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center border-2 border-blue-600 text-blue-600 hover:text-white hover:bg-blue-700 px-4 py-2 rounded-md text-base font-medium transition-colors">
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main id="page-content">
@endif
{{-- ═══ KONTEN UTAMA ═══ --}}
            @yield('content')
@if(!$__isPjax)
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

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @stack('scripts')
</body>
</html>
@else
{{-- ═══ PJAX metadata ═══ --}}
<script type="application/json" id="pjax-meta">
@php
    $__sections = \Illuminate\Support\Facades\View::getSections();
    echo json_encode([
        'title'     => strip_tags($__sections['title'] ?? config('app.name', 'Giscana')),
        'pageTitle' => '',
    ]);
@endphp
</script>
@endif
