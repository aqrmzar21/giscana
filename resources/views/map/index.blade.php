@extends('layouts.landing-map')

@section('title', 'Peta Interaktif - Giscana')

@include('map.partials.map-styles', ['mapUiVariant' => 'landing-fs'])

@section('content')

    <div id="map" class="absolute inset-0 h-screen w-screen z-50"></div>

    <!-- Legenda di pojok kiri bawah -->
    <div class="absolute bottom-4 left-4 z-50 p-3">
        @include('map.partials.map-legend-content')
    </div>

    <!-- Wrapper Kontrol Peta -->
    <div x-data="{ openPanel: false }">

        <!-- 🔘 Tombol FAB (Floating Action Button) -->
        <button 
            @click="openPanel = !openPanel" 
            class="fixed bottom-6 right-6 z-40 bg-amber-500 hover:bg-amber-600 text-slate-900 p-3.5 rounded-full shadow-xl transition-transform active:scale-95 flex items-center justify-center focus:outline-none"
            aria-label="Buka Kontrol Peta">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </button>

        <!-- 🗂️ Bottom Sheet Modal (Kontrol Peta) -->
        <div 
            :class="openPanel ? 'translate-y-0 opacity-100 pointer-events-auto' : 'translate-y-full opacity-0 pointer-events-none md:translate-y-0 md:opacity-100 md:pointer-events-auto'"
            class="fixed z-50 transition-all duration-300 ease-in-out bottom-0 left-0 right-0 max-h-[85vh] overflow-y-auto bg-white rounded-t-3xl p-5 shadow-2xl border-t border-slate-200 md:bottom-4 md:right-4 md:left-auto md:w-80 md:rounded-2xl md:border"
            @click.outside="openPanel = false"
            x-cloak>
            
            <!-- Handle Bar (Garis Pegangan Mobile) -->
            <div class="w-12 h-1 bg-slate-300 rounded-full mx-auto mb-4 md:hidden"></div>

            <!-- Header Panel -->
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm">Kontrol Peta</h3>
                <button @click="openPanel = false" class="text-slate-400 hover:text-slate-600 font-semibold text-lg p-1 leading-none">
                    &times;
                </button>
            </div>
            
            <!-- Section 1: Toggle Switch Batas Administrasi -->
            <div class="mb-5">
                @include('map.partials.map-district-checkbox')
            </div>
            
            <!-- Section 2 & 3: Sebaran Rawan Bencana & Slider Opacity -->
            <div class="mb-4">
                @include('map.partials.map-panel-hazard')
            </div>

            <!-- Watermark Footer -->
            <!-- <div class="text-center pt-3 border-t border-slate-100">
                <p class="text-[10px] text-slate-400 font-medium tracking-wide">©2026 Giscana</p>
            </div> -->
        </div>

    </div>
@endsection

@include('map.partials.map-scripts', ['mapUiVariant' => 'landing-fs'])