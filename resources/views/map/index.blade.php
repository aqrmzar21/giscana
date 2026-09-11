@extends('layouts.landing-map')

@section('title', 'Peta Interaktif - Giscana')

@include('map.partials.map-styles', ['mapUiVariant' => 'landing-fs'])

@section('content')

    <!-- Peta Utama -->
    <div id="map" class="absolute inset-0 h-screen w-screen z-0"></div>

    <!-- 🔝 Header Bar Melayang (Mobile & Desktop) -->
    <div class="fixed top-4 left-4 right-4 z-40 flex items-center justify-between bg-white/95 backdrop-blur-md px-4 py-2.5 rounded-2xl shadow-md border border-slate-200/80 md:w-auto md:right-auto">
        <div class="flex items-center gap-3">
            <button class="text-slate-600 hover:text-slate-900 focus:outline-none" aria-label="Menu Utama">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex items-center gap-2 font-bold text-slate-800 text-sm">
                <div class="w-6 h-6 rounded-md bg-indigo-600 flex items-center justify-center text-white text-xs font-black shadow-sm">
                    G
                </div>
                <span>Giscana - Bone Bolango</span>
            </div>
        </div>
    </div>

    <!-- Wrapper Kontrol Peta (Alpine.js) -->
    <div x-data="{ openPanel: true }">

        <!-- 🗂️ Panel Utama -->
        <!-- Mobile: Bottom Sheet berpindah ke bawah menyisakan strip (translate-y) -->
        <!-- Desktop: Selalu tampil melayang di pojok kanan atas (md:translate-y-0) -->
        <div 
            :class="openPanel ? 'translate-y-0' : 'translate-y-[calc(100%-2.75rem)]'"
            class="fixed z-50 transition-transform duration-300 ease-in-out
                   /* Mobile Styling (Bottom Sheet) */
                   bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl border-t border-slate-200 px-5 pt-3 pb-4 max-h-[80vh] overflow-y-auto
                   /* Desktop Styling (Always Open Top-Right Card) */
                   md:top-4 md:right-4 md:bottom-auto md:left-auto md:w-80 md:rounded-2xl md:border md:shadow-xl md:translate-y-0"
            x-cloak>
            
            <!-- 📐 Handle Bar / Strip Bawah (Klik/Tarik untuk Buka-Tutup di Mobile) -->
            <div 
                @click="openPanel = !openPanel" 
                class="w-full flex flex-col items-center cursor-pointer pb-2 pt-1 md:hidden group">
                <div class="w-12 h-1.5 bg-slate-300 group-hover:bg-slate-400 rounded-full transition-colors"></div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1" x-text="openPanel ? 'Tutup Kontrol' : 'Tarik Kontrol Peta'"></span>
            </div>

            <!-- Header Panel -->
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                    <span>⚙️</span> Kontrol Peta
                </h3>
                <!-- Tombol Tutup (Hanya Mobile) -->
                <button @click="openPanel = false" class="md:hidden text-slate-400 hover:text-slate-600 font-semibold text-lg p-1 leading-none">
                    &times;
                </button>
            </div>
            
            <!-- Section 1: Toggle Switch Batas Administrasi -->
            <div class="mb-4">
                @include('map.partials.map-district-checkbox')
            </div>
            
            <!-- Section 2 & 3: Sebaran Rawan Bencana & Slider Opacity -->
            <div class="mb-4">
                @include('map.partials.map-panel-hazard')
            </div>

            <!-- Watermark Footer -->
            <div class="text-center pt-3 border-t border-slate-100">
                <p class="text-[10px] text-slate-400 font-medium tracking-wide">©2026 Giscana</p>
            </div>
        </div>

    </div>
@endsection

@include('map.partials.map-scripts', ['mapUiVariant' => 'landing-fs'])