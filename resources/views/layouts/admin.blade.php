<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin - Giscana')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg fixed h-screen overflow-y-auto">
            <div class="p-4">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                    <x-application-logo class="block h-8 w-auto fill-current text-gray-800" />
                    <span class="text-xl font-bold text-gray-800">Giscana</span>
                </a>
            </div>
            <nav class="mt-5 px-2">
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('map.index') }}" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('map.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        Peta Interaktif
                    </a>
                </div>

                @if (Auth::user()->isAdmin())
                <div class="mt-8">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Manajemen Data</p>
                    <div class="mt-2 space-y-1">
                        <!-- Zona Bencana -->
                        <div x-data="{ open: {{ request()->routeIs('admin.disaster-zones.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.disaster-zones.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    Zona Bencana
                                </div>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
                                <a href="{{ route('admin.disaster-zones.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.disaster-zones.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                    Daftar Zona
                                </a>
                                <a href="{{ route('admin.disaster-zones.create') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.disaster-zones.create') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                    Tambah Zona Baru
                                </a>
                            </div>
                        </div>

                        <!-- Rute Evakuasi -->
                        <div x-data="{ open: {{ request()->routeIs('admin.evacuation-routes.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.evacuation-routes.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    Rute Evakuasi
                                </div>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
                                <a href="{{ route('admin.evacuation-routes.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.evacuation-routes.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                    Daftar Rute
                                </a>
                                <a href="{{ route('admin.evacuation-routes.create') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.evacuation-routes.create') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                    Tambah Rute Baru
                                </a>
                            </div>
                        </div>

                        <!-- Fasilitas Evakuasi -->
                        <div x-data="{ open: {{ request()->routeIs('admin.evacuation-facilities.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.evacuation-facilities.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    Fasilitas Evakuasi
                                </div>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
                                <a href="{{ route('admin.evacuation-facilities.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.evacuation-facilities.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                    Daftar Fasilitas
                                </a>
                                <a href="{{ route('admin.evacuation-facilities.create') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.evacuation-facilities.create') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                    Tambah Fasilitas Baru
                                </a>
                            </div>
                        </div>

                        <!-- Titik Distribusi Bantuan -->
                        <div x-data="{ open: {{ request()->routeIs('admin.aid-distribution-points.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('admin.aid-distribution-points.*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    Titik Distribusi Bantuan
                                </div>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
                                <a href="{{ route('admin.aid-distribution-points.index') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.aid-distribution-points.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                    Daftar Titik
                                </a>
                                <a href="{{ route('admin.aid-distribution-points.create') }}" class="flex items-center px-4 py-2 text-sm rounded-lg {{ request()->routeIs('admin.aid-distribution-points.create') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                                    Tambah Titik Baru
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 ml-64">
            <!-- Top Navigation -->
            <nav class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                        </div>
                        <div class="flex items-center">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                        <div>{{ Auth::user()->name }}</div>
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

                                    <!-- Authentication -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
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

            <!-- Breadcrumb -->
            @hasSection('breadcrumb')
            <div class="bg-white border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-3">
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>
            </div>
            @endif

            <!-- Page Content -->
            <main class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove();">
                                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                            <button type="button" class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.remove();">
                                <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
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
            </main>
        </div>
    </div>

    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
