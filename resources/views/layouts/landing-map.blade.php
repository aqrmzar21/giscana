@php $__isPjax = request()->header('X-PJAX') === 'true'; @endphp
@if(!$__isPjax)
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'GIScana | Peta Interaktif Spasial')</title>

    <!-- Dark Mode Initializer Script (prevents theme flicker) -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased h-screen flex flex-col overflow-hidden bg-slate-100 dark:bg-slate-900 text-slate-800 dark:text-slate-100 transition-colors duration-200"
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

    @include('components.landing-nav')

    <main id="page-content" class="flex-1 relative overflow-hidden">
        @endif
        {{-- ═══ KONTEN UTAMA ═══ --}}
        @yield('content')
        @if(!$__isPjax)
    </main>
        
    <footer class="text-sm font-medium inline-flex justify-center text-center py-2 bg-white relative z-50 hidden md:flex">
         <!-- @yield('map-toolbar') -->
        ©{{ date('Y') }} All right reserved  |  Giscana
    </footer>
    @stack('scripts')
    
</body>
</html>
@else
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
