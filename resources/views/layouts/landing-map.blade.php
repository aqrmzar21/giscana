@php $__isPjax = request()->header('X-PJAX') === 'true'; @endphp
@if(!$__isPjax)
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'GIScana')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased h-screen flex flex-col overflow-hidden bg-gray-100">
    <!-- PJAX Loading Bar -->
    <div id="pjax-progress" style="position:fixed;top:0;left:0;width:0;height:3px;background:linear-gradient(90deg,#3b82f6,#6366f1);z-index:9999;transition:width 0.3s ease,opacity 0.4s ease;opacity:0;pointer-events:none;"></div>

    @include('components.landing-nav')

    <main id="page-content" class="flex-1 relative">
        @endif
        {{-- ═══ KONTEN UTAMA ═══ --}}
                @yield('content')
                @if(!$__isPjax)
    </main>
    
    <footer class="inline-flex justify-center text-center py-2 bg-white relative z-50">
        <!-- @yield('map-toolbar') -->
        copyright v1.0 ©2026 || aqrmlhdytllh
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
