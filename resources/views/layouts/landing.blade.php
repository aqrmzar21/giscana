@php $__isPjax = request()->header('X-PJAX') === 'true'; @endphp
@if(!$__isPjax)
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'GIScana - Sistem Informasi Geografis Bencana Bone Bolango')</title>

    <!-- Dark Mode Initializer Script (prevents theme flicker) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- TailwindCSS & App JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 selection:bg-blue-500 selection:text-white transition-colors duration-200"
      x-data="{ 
          mobileMenuOpen: false,
          darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
      }"
      x-init="$watch('darkMode', val => { 
          localStorage.setItem('theme', val ? 'dark' : 'light'); 
          if(val) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark'); } 
      });">
      
    <!-- PJAX Loading Bar -->
    <div id="pjax-progress" style="position:fixed;top:0;left:0;width:0;height:3px;background:linear-gradient(90deg,#3b82f6,#6366f1,#a855f7);z-index:9999;transition:width 0.3s ease,opacity 0.4s ease;opacity:0;pointer-events:none;"></div>
    
    <div class="min-h-screen flex flex-col justify-between bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
        <!-- Modern Glassmorphism Navigation -->
        <nav class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800/80 sticky top-0 z-50 transition-all duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-violet-600 flex items-center justify-center shadow-lg shadow-blue-500/20 group-hover:scale-105 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <span class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                                GIScana
                            </span>
                        </a>
                        
                        <div class="hidden md:flex items-center space-x-1 ml-8 pl-6 border-l border-slate-200 dark:border-slate-800">
                            <a href="{{ route('home') }}" class="text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/70 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                Beranda
                            </a>
                            <a href="{{ route('map.index') }}" class="text-slate-700 dark:text-slate-200 hover:text-emerald-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/70 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                Peta Interaktif
                            </a>
                            <a href="#features" class="text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/70 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200">
                                Fitur Utama
                            </a>
                            <a href="#about" class="text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800/70 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200">
                                Tentang
                            </a>
                        </div>
                    </div>

                    <!-- Desktop Right Menu -->
                    <div class="hidden md:flex items-center space-x-3">
                        <!-- Dark / Light Theme Switcher Button -->
                        <button @click="darkMode = !darkMode" 
                                type="button" 
                                class="p-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition-all duration-200 border border-slate-200 dark:border-slate-700/60 shadow-sm"
                                :title="darkMode ? 'Beralih ke Mode Terang' : 'Beralih ke Mode Gelap'">
                            <template x-if="darkMode">
                                <!-- Sun Icon -->
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 2.78a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.414l-.708-.707a1 1 0 010-1.414zm3.56 5.22a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-2.147 5.22a1 1 0 010 1.415l-.707.707a1 1 0 01-1.414-1.414l.707-.708a1 1 0 011.414 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-5.22-2.147a1 1 0 010-1.415l.707-.707a1 1 0 111.414 1.414l-.707.708a1 1 0 01-1.414 0zM2 10a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm2.78-5.22a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.414l-.708-.707a1 1 0 010-1.414zM10 6a4 4 0 100 8 4 4 0 000-8z" clip-rule="evenodd"></path>
                                </svg>
                            </template>
                            <template x-if="!darkMode">
                                <!-- Moon Icon -->
                                <svg class="w-5 h-5 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.703a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>
                            </template>
                        </button>

                        @auth
                            <a href="{{ route('dashboard') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-blue-600/30 transition-all duration-200 hover:scale-[1.02]">
                                Dashboard
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white px-3 py-2 text-sm font-medium transition-colors">
                                    Logout
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="relative group overflow-hidden rounded-xl p-px font-semibold text-sm">
                                <span class="absolute inset-0 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl transition-all duration-300 group-hover:opacity-100"></span>
                                <span class="relative block px-6 py-2.5 rounded-[11px] bg-white dark:bg-slate-900 text-slate-800 dark:text-white transition-colors duration-200 group-hover:bg-opacity-0 group-hover:text-white">
                                    Login Sistem
                                </span>
                            </a>
                        @endauth
                    </div>

                    <!-- Mobile Controls (Theme Switcher + Menu Button) -->
                    <div class="flex items-center space-x-2 md:hidden">
                        <button @click="darkMode = !darkMode" 
                                type="button" 
                                class="p-2.5 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none transition-all duration-200 border border-slate-200 dark:border-slate-700/60"
                                :title="darkMode ? 'Mode Terang' : 'Mode Gelap'">
                            <template x-if="darkMode">
                                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 2.78a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.414l-.708-.707a1 1 0 010-1.414zm3.56 5.22a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-2.147 5.22a1 1 0 010 1.415l-.707.707a1 1 0 01-1.414-1.414l.707-.708a1 1 0 011.414 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-5.22-2.147a1 1 0 010-1.415l.707-.707a1 1 0 111.414 1.414l-.707.708a1 1 0 01-1.414 0zM2 10a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm2.78-5.22a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.414l-.708-.707a1 1 0 010-1.414zM10 6a4 4 0 100 8 4 4 0 000-8z" clip-rule="evenodd"></path>
                                </svg>
                            </template>
                            <template x-if="!darkMode">
                                <svg class="w-5 h-5 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.703a8.001 8.001 0 1010.586 10.586z"></path>
                                </svg>
                            </template>
                        </button>

                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white focus:outline-none p-2 rounded-xl bg-slate-100 dark:bg-slate-800/80">
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
            <div x-show="mobileMenuOpen" x-collapse style="display: none;" class="md:hidden border-t border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/95 backdrop-blur-lg">
                <div class="px-4 pt-3 pb-4 space-y-2">
                    <a href="{{ route('home') }}" class="block text-slate-800 dark:text-slate-100 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-slate-100 dark:hover:bg-slate-800 px-4 py-2.5 rounded-xl text-base font-medium">
                        Beranda
                    </a>
                    <a href="{{ route('map.index') }}" class="block text-slate-800 dark:text-slate-100 hover:text-emerald-600 dark:hover:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 px-4 py-2.5 rounded-xl text-base font-medium">
                        Peta Interaktif
                    </a>
                    <a href="#features" class="block text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 px-4 py-2.5 rounded-xl text-base font-medium">
                        Fitur Utama
                    </a>
                    <a href="#about" class="block text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 px-4 py-2.5 rounded-xl text-base font-medium">
                        Tentang GIScana
                    </a>
                </div>
                <div class="pt-4 pb-5 border-t border-slate-200 dark:border-slate-800 px-4">
                    @auth
                        <div class="flex items-center mb-3">
                            <div class="text-base font-medium text-slate-800 dark:text-slate-200">{{ Auth::user()?->name }}</div>
                        </div>
                        <div class="space-y-2">
                            <a href="{{ route('dashboard') }}" class="block w-full text-center bg-blue-600 text-white px-4 py-3 rounded-xl text-base font-semibold hover:bg-blue-500">
                                Dashboard Admin
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block w-full">
                                @csrf
                                <button type="submit" class="w-full text-center border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 px-4 py-2.5 rounded-xl text-base font-medium">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center bg-blue-600 text-white px-4 py-3 rounded-xl text-base font-semibold hover:bg-blue-500 transition-colors">
                            Login Sistem
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Main Content Wrapper -->
        <main id="page-content" class="flex-1">
@endif
{{-- ═══ KONTEN UTAMA ═══ --}}
            @yield('content')
@if(!$__isPjax)
        </main>

        <!-- Modern Footer -->
        <footer class="bg-slate-900 dark:bg-slate-950 text-slate-400 border-t border-slate-800/80">
            <div class="max-w-7xl mx-auto py-14 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                    <div class="md:col-span-5">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold">
                                G
                            </div>
                            <span class="text-xl font-bold text-white">GIScana</span>
                        </div>
                        <p class="text-slate-400 text-sm leading-relaxed mb-4 max-w-md">
                            WebGIS & SIMBA — Sistem Informasi Geografis & Manajemen Bantuan Bencana Bencana Alam 
                            Kabupaten Bone Bolango, Provinsi Gorontalo. Mendukung kesiapsiagaan dan mitigasi risiko wilayah pesisir.
                        </p>
                        <div class="flex items-center space-x-2 text-xs text-slate-500">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Sistem Aktif & Terhubung</span>
                        </div>
                    </div>
                    
                    <div class="md:col-span-3">
                        <h3 class="text-sm font-semibold text-slate-200 uppercase tracking-wider mb-4">Link Cepat</h3>
                        <ul class="space-y-2.5 text-sm">
                            <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda Utama</a></li>
                            <li><a href="{{ route('map.index') }}" class="hover:text-emerald-400 transition-colors">Peta Risk & Evakuasi</a></li>
                            <li><a href="#features" class="hover:text-white transition-colors">Fitur Sistem GIS</a></li>
                            <li><a href="#about" class="hover:text-white transition-colors">Tentang BPBD & SIMBA</a></li>
                        </ul>
                    </div>

                    <div class="md:col-span-4">
                        <h3 class="text-sm font-semibold text-slate-200 uppercase tracking-wider mb-4">Kontak Darurat & Operasional</h3>
                        <div class="text-sm space-y-2 text-slate-400">
                            <p class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>BPBD Kabupaten Bone Bolango, Provinsi Gorontalo</span>
                            </p>
                            <p class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>info@giscana.bonebolangokab.go.id</span>
                            </p>
                            <p class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                <span>Call Center BPBD: +62 812 3456 7890</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="border-t border-slate-800 mt-12 pt-8 flex flex-col sm:flex-row justify-between items-center text-xs text-slate-500">
                    <p>&copy; {{ date('Y') }} GIScana. BPBD Kabupaten Bone Bolango. Hak Cipta Dilindungi.</p>
                    <p class="mt-2 sm:mt-0">Sistem Informasi Geografis & Management Logistics</p>
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
@else
{{-- ═══ PJAX metadata ═══ --}}
<script type="application/json" id="pjax-meta">
@php
    $__sections = \Illuminate\Support\Facades\View::getSections();
    echo json_encode([
        'title'     => strip_tags($__sections['title'] ?? config('app.name', 'GIScana')),
        'pageTitle' => '',
    ]);
@endphp
</script>
@endif
