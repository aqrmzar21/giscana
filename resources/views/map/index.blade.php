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

        <!-- 🔘 Tombol FAB (Hanya muncul di Mobile: md:hidden) -->
        <button 
            @click="openPanel = !openPanel" 
            class="md:hidden fixed bottom-6 right-6 z-[1000] bg-amber-500 hover:bg-amber-600 text-slate-900 p-3.5 rounded-full shadow-2xl transition-transform active:scale-95 flex items-center justify-center focus:outline-none"
            aria-label="Buka Kontrol Peta">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </button>

        <!-- 🗂️ Panel Utama (Mobile: Bottom Sheet | Desktop: Floating Card Top-Right) -->
        <div 
            :class="openPanel ? 'translate-y-0 opacity-100 pointer-events-auto' : 'translate-y-full opacity-0 pointer-events-none md:translate-y-0 md:opacity-100 md:pointer-events-auto'"
            class="fixed z-[1000] transition-all duration-300 ease-in-out
                /* Mobile Styling */
                bottom-20 left-4 right-4 max-h-[60vh] overflow-y-auto bg-white/95 backdrop-blur-md rounded-2xl p-4 shadow-2xl border border-slate-200
                /* Desktop Styling (Selalu Tampil di Pojok Kanan Atas) */
                md:top-4 md:right-4 md:bottom-auto md:left-auto md:w-80 md:max-h-[85vh] md:rounded-xl md:shadow-xl md:bg-white/90"
            @click.outside="openPanel = false"
            x-cloak>
            
            <!-- Header Panel -->
            <div class="flex items-center justify-between pb-2 mb-3 border-b border-slate-200">
                <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <span>⚙️</span> Kontrol Peta
                </h3>
                <!-- Tombol Tutup (Hanya Mobile) -->
                <button @click="openPanel = false" class="md:hidden text-slate-400 hover:text-slate-600 font-bold text-lg leading-none p-1">
                    &times;
                </button>
            </div>

            <!-- Section 1: Sebaran Rawan Bencana (Dari Database) -->
            <div class="space-y-2 mb-4">
                <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Sebaran Rawan Bencana</p>
                
                <!-- Tempat fungsi buildHazardLayerUI() merender kartu bencana -->
                <div id="hazard_layer_checkboxes" class="grid grid-cols-2 gap-2">
                    <!-- Kartu Bencana Di-generate oleh JavaScript -->
                </div>
            </div>

        </div>

    </div>
@endsection

@include('map.partials.map-scripts', ['mapUiVariant' => 'landing-fs'])
