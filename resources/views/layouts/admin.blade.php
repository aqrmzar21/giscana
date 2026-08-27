@php $__isPjax = request()->header('X-PJAX') === 'true'; @endphp
@if(!$__isPjax)
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin - Giscana')</title>

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
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 0.375rem;
            z-index: 1;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200" 
      x-data="{ 
          sidebarOpen: false, 
          sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true', 
          darkMode: localStorage.getItem('theme') === 'dark' 
      }"
      x-init="$watch('darkMode', val => { 
          localStorage.setItem('theme', val ? 'dark' : 'light'); 
          if(val) { document.documentElement.classList.add('dark'); } else { document.documentElement.classList.remove('dark'); } 
      }); 
      $watch('sidebarCollapsed', val => { 
          localStorage.setItem('sidebarCollapsed', val); 
      });">
    <!-- PJAX Loading Bar -->
    <div id="pjax-progress" style="position:fixed;top:0;left:0;width:0;height:3px;background:linear-gradient(90deg,#6366f1,#8b5cf6);z-index:9999;transition:width 0.3s ease,opacity 0.4s ease;opacity:0;pointer-events:none;"></div>

    <div class="min-h-screen flex">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900 bg-opacity-75 z-20 md:hidden" 
             @click="sidebarOpen = false"
             style="display: none;"></div>

        <!-- Sidebar -->
        <aside :class="{ 
                    'translate-x-0': sidebarOpen, 
                    '-translate-x-full': !sidebarOpen, 
                    'w-64': !sidebarCollapsed, 
                    'w-20': sidebarCollapsed 
               }"
               class="bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 fixed h-screen overflow-y-auto z-30 transition-all duration-300 ease-in-out transform md:translate-x-0 flex flex-col justify-between">
            <div>
                <!-- Brand Header -->
                <div class="p-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700 h-16">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 overflow-hidden">
                        <svg class="w-8 h-8 shrink-0 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-transition class="text-xl font-bold text-gray-800 dark:text-white whitespace-nowrap">Giscana</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="mt-4 px-2 space-y-1">
                    <div>
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" 
                           :title="sidebarCollapsed ? 'Dashboard' : ''"
                           class="flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/60' }}"
                           :class="{ 'justify-center px-2': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            <span x-show="!sidebarCollapsed" class="ml-3 truncate">Dashboard</span>
                        </a>

                        <!-- Peta Admin -->
                        <a href="{{ route('dashboard.map') }}" 
                           :title="sidebarCollapsed ? 'Peta Admin' : ''"
                           class="flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard.map') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/60' }}"
                           :class="{ 'justify-center px-2': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="currentColor" viewBox="0 0 512 512">
                                <path d="M448 256a192 192 0 1 0 -384 0 192 192 0 1 0 384 0zM0 256a256 256 0 1 1 512 0 256 256 0 1 1 -512 0zm256 80a80 80 0 1 0 0-160 80 80 0 1 0 0 160zm0-224a144 144 0 1 1 0 288 144 144 0 1 1 0-288zM224 256a32 32 0 1 1 64 0 32 32 0 1 1 -64 0z"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="ml-3 truncate">Peta Admin</span>
                        </a>
                        
                        @role('admin')
                        <a href="{{ route('admin.aid-disasters.index') }}" 
                           :title="sidebarCollapsed ? 'Distribusi' : ''"
                           class="flex items-center py-2.5 px-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.aid-disasters.index') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/60' }}"
                           :class="{ 'justify-center px-2': sidebarCollapsed }">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span x-show="!sidebarCollapsed" class="ml-3 truncate">Distribusi</span>
                        </a>
                        @endrole
                    </div>

                    @if (Auth::user()?->isAdmin() || Auth::user()?->isStaff())
                    <div class="pt-4">
                        <p x-show="!sidebarCollapsed" class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Manajemen Data</p>
                        <div x-show="sidebarCollapsed" class="my-2 border-t border-gray-200 dark:border-gray-700"></div>

                        <div class="mt-2 space-y-1">
                            <!-- Zona Bencana -->
                            <div x-data="{ open: {{ request()->routeIs('admin.disaster-zones.*') ? 'true' : 'false' }} }">
                                <button @click="open = !open" 
                                        :title="sidebarCollapsed ? 'Titik Bencana' : ''"
                                        class="w-full flex items-center justify-between py-2.5 px-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.disaster-zones.*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/60' }}"
                                        :class="{ 'justify-center px-2': sidebarCollapsed }">
                                    <div class="flex items-center min-w-0">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <span x-show="!sidebarCollapsed" class="ml-3 truncate">Titik Bencana</span>
                                    </div>
                                    <svg x-show="!sidebarCollapsed" class="w-4 h-4 transition-transform shrink-0" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="open && !sidebarCollapsed" x-collapse class="ml-4 mt-1 space-y-1">
                                    <a href="{{ route('admin.disaster-zones.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.disaster-zones.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40' }}">
                                        Daftar Titik Rawan
                                    </a>
                                    <a href="{{ route('admin.disaster-zones.create') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.disaster-zones.create') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40' }}">
                                        Tambah Titik Bencana
                                    </a>
                                </div>
                            </div>

                            <!-- Rute Evakuasi -->
                            <div x-data="{ open: {{ request()->routeIs('admin.evacuation-routes.*') ? 'true' : 'false' }} }">
                                <button @click="open = !open" 
                                        :title="sidebarCollapsed ? 'Rute Evakuasi' : ''"
                                        class="w-full flex items-center justify-between py-2.5 px-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.evacuation-routes.*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/60' }}"
                                        :class="{ 'justify-center px-2': sidebarCollapsed }">
                                    <div class="flex items-center min-w-0">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                        </svg>
                                        <span x-show="!sidebarCollapsed" class="ml-3 truncate">Rute Evakuasi</span>
                                    </div>
                                    <svg x-show="!sidebarCollapsed" class="w-4 h-4 transition-transform shrink-0" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="open && !sidebarCollapsed" x-collapse class="ml-4 mt-1 space-y-1">
                                    <a href="{{ route('admin.evacuation-routes.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.evacuation-routes.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40' }}">
                                        Daftar Rute
                                    </a>
                                    <a href="{{ route('admin.evacuation-routes.create') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.evacuation-routes.create') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40' }}">
                                        Tambah Rute Baru
                                    </a>
                                </div>
                            </div>

                            <!-- Fasilitas Evakuasi -->
                            <div x-data="{ open: {{ request()->routeIs('admin.evacuation-facilities.*') ? 'true' : 'false' }} }">
                                <button @click="open = !open" 
                                        :title="sidebarCollapsed ? 'Fasilitas Evakuasi' : ''"
                                        class="w-full flex items-center justify-between py-2.5 px-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.evacuation-facilities.*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/60' }}"
                                        :class="{ 'justify-center px-2': sidebarCollapsed }">
                                    <div class="flex items-center min-w-0">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span x-show="!sidebarCollapsed" class="ml-3 truncate">Fasilitas Evakuasi</span>
                                    </div>
                                    <svg x-show="!sidebarCollapsed" class="w-4 h-4 transition-transform shrink-0" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="open && !sidebarCollapsed" x-collapse class="ml-4 mt-1 space-y-1">
                                    <a href="{{ route('admin.evacuation-facilities.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.evacuation-facilities.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40' }}">
                                        Daftar Fasilitas
                                    </a>
                                    <a href="{{ route('admin.evacuation-facilities.create') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.evacuation-facilities.create') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40' }}">
                                        Tambah Fasilitas Baru
                                    </a>
                                </div>
                            </div>

                            <!-- Data Bantuan Bencana -->
                            <div x-data="{ open: request()->routeIs('admin.aid-recipients.*') ? 'true' : 'false' }} }">
                                <button @click="open = !open" 
                                        :title="sidebarCollapsed ? 'Bantuan Bencana' : ''"
                                        class="w-full flex items-center justify-between py-2.5 px-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.aid-disasters.*') || request()->routeIs('admin.aid-recipients.*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/60' }}"
                                        :class="{ 'justify-center px-2': sidebarCollapsed }">
                                    <div class="flex items-center min-w-0">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                        <span x-show="!sidebarCollapsed" class="ml-3 truncate">Bantuan Bencana</span>
                                    </div>
                                    <svg x-show="!sidebarCollapsed" class="w-4 h-4 transition-transform shrink-0" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="open && !sidebarCollapsed" x-collapse class="ml-4 mt-1 space-y-1">
                                    <a href="{{ route('admin.aid-recipients.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.aid-recipients.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40' }}">
                                        Daftar Penerima
                                    </a>
                                    <a href="{{ route('admin.aid-recipients.create') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.aid-recipients.create') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40' }}">
                                        Tambah Penerima Baru
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @role('admin')
                    <div class="pt-4 pb-6">
                        <p x-show="!sidebarCollapsed" class="px-3 text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Manajemen Pengguna</p>
                        <div x-show="sidebarCollapsed" class="my-2 border-t border-gray-200 dark:border-gray-700"></div>

                        <div class="mt-2 space-y-1">
                            <!-- Kelola Staff -->
                            <div x-data="{ open: {{ request()->routeIs('admin.staff.*') ? 'true' : 'false' }} }">
                                <button @click="open = !open" 
                                        :title="sidebarCollapsed ? 'Kelola Staff' : ''"
                                        class="w-full flex items-center justify-between py-2.5 px-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.staff.*') ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/60' }}"
                                        :class="{ 'justify-center px-2': sidebarCollapsed }">
                                    <div class="flex items-center min-w-0">
                                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span x-show="!sidebarCollapsed" class="ml-3 truncate">Kelola Staff</span>
                                    </div>
                                    <svg x-show="!sidebarCollapsed" class="w-4 h-4 transition-transform shrink-0" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                                <div x-show="open && !sidebarCollapsed" x-collapse class="ml-4 mt-1 space-y-1">
                                    <a href="{{ route('admin.staff.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.staff.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40' }}">
                                        Daftar Staff
                                    </a>
                                    <a href="{{ route('admin.staff.create') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.staff.create') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-200 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/40' }}">
                                        Tambah Staff Baru
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endrole
                </nav>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div :class="sidebarCollapsed ? 'md:ml-20' : 'md:ml-64'" 
             class="flex-1 w-full min-w-0 transition-all duration-300 flex flex-col min-h-screen">

            <!-- Top Header Navigation -->
            <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-20 transition-colors duration-200">
                <div class="w-full px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <!-- Mobile Hamburger Button -->
                            <button @click="sidebarOpen = !sidebarOpen" 
                                    type="button"
                                    class="md:hidden mr-3 p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors"
                                    title="Menu Sidebar">
                                <span class="sr-only">Buka sidebar</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <!-- Desktop Hamburger / Collapse Button -->
                            <button @click="sidebarCollapsed = !sidebarCollapsed" 
                                    type="button"
                                    class="hidden md:inline-flex p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors mr-3"
                                    :title="sidebarCollapsed ? 'Perluas Sidebar' : 'Persempit Sidebar'">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>

                            <h2 id="page-title" class="text-xl font-semibold text-gray-800 dark:text-gray-100 truncate">@yield('page-title', 'Dashboard')</h2>
                        </div>

                        <div class="flex items-center space-x-2 sm:space-x-3">
                            <!-- Dark/Light Theme Switcher Button -->
                            <button @click="darkMode = !darkMode" 
                                    type="button" 
                                    class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition-colors"
                                    :title="darkMode ? 'Mode Terang' : 'Mode Gelap'">
                                <template x-if="darkMode">
                                    <!-- Sun Icon -->
                                    <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 2.78a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.414l-.708-.707a1 1 0 010-1.414zm3.56 5.22a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-2.147 5.22a1 1 0 010 1.415l-.707.707a1 1 0 01-1.414-1.414l.707-.708a1 1 0 011.414 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zm-5.22-2.147a1 1 0 010-1.415l.707-.707a1 1 0 111.414 1.414l-.707.708a1 1 0 01-1.414 0zM2 10a1 1 0 011-1h1a1 1 0 110 2H3a1 1 0 01-1-1zm2.78-5.22a1 1 0 011.415 0l.707.707a1 1 0 01-1.414 1.414l-.708-.707a1 1 0 010-1.414zM10 6a4 4 0 100 8 4 4 0 000-8z" clip-rule="evenodd"></path>
                                    </svg>
                                </template>
                                <template x-if="!darkMode">
                                    <!-- Moon Icon -->
                                    <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M17.293 13.293A8 8 0 016.707 2.703a8.001 8.001 0 1010.586 10.586z"></path>
                                    </svg>
                                </template>
                            </button>

                            <!-- Profile Dropdown -->
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 dark:text-gray-200 bg-white dark:bg-gray-800 hover:text-gray-800 dark:hover:text-white focus:outline-none transition ease-in-out duration-150">
                                        <div>{{ Auth::user()?->name }}</div>
                                        <div class="ms-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('profile.edit')">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" data-no-pjax
                                                onclick="event.preventDefault();
                                                            this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </x-slot>
                            </x-dropdown>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content Body -->
            <main id="page-content" class="flex-1 flex flex-col">
            @endif
            {{-- ═══ KONTEN UTAMA — dirender selalu (full page & PJAX) ═══ --}}
                <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 dark:bg-green-900/60 border border-green-400 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove();">
                                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 dark:bg-red-900/60 border border-red-400 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove();">
                                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 dark:bg-red-900/60 border border-red-400 dark:border-red-700 text-red-800 dark:text-red-200 px-4 py-3 rounded-lg relative" role="alert">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove();">
                                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            @if(!$__isPjax)
            </main>
        </div>
    </div>

    @stack('styles')
    @stack('scripts')
</body>
</html>
@else
    {{-- ═══ PJAX metadata — hanya dikirim saat partial request ═══ --}}
    <script type="application/json" id="pjax-meta">
    @php
        $__sections = \Illuminate\Support\Facades\View::getSections();
        echo json_encode([
            'title'     => strip_tags($__sections['title'] ?? config('app.name', 'Giscana')),
            'pageTitle' => strip_tags($__sections['page-title'] ?? ''),
        ]);
    @endphp
    </script>
@endif
