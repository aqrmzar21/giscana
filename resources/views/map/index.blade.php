@extends('layouts.landing-map')

@section('title', 'Peta Interaktif - Giscana')

@include('map.partials.map-styles', ['mapUiVariant' => 'landing-fs'])

@section('content')

<div id="map" class="absolute inset-0 h-screen w-screen z-50"></div>

<!-- Legenda di pojok kiri bawah -->
<div class="absolute bottom-4 left-4 z-50 p-3">
    @include('map.partials.map-legend-content')
</div>

<!-- Wrapper Panel Mobile (Gunakan Alpine.js untuk kontrol state) -->
<div x-data="{ openPanel: false }">

    <!-- 🔘 Tombol FAB Mobile (Sembunyi di Desktop: md:hidden) -->
    <button 
        @click="openPanel = !openPanel" 
        class="md:hidden fixed bottom-6 right-6 z-[1000] bg-amber-500 hover:bg-amber-600 text-slate-900 p-3.5 rounded-full shadow-2xl transition-transform active:scale-95 flex items-center justify-center focus:outline-none"
        aria-label="Buka Kontrol Peta">
        <!-- Ikon Layer Bencana -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
    </button>

    <!-- 📱 Panel Kontrol Melayang Mobile (Off-Canvas Bottom Panel) -->
    <div 
        x-show="openPanel" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-8"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-8"
        @click.outside="openPanel = false"
        class="md:hidden fixed bottom-20 left-4 right-4 z-[1000] bg-white/95 backdrop-blur-md rounded-2xl shadow-2xl p-4 border border-slate-100 max-h-[65vh] overflow-y-auto space-y-4"
        x-cloak>
        
        <!-- Header Panel -->
        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                <span>⚙️</span> Kontrol Peta
            </h3>
            <button @click="openPanel = false" class="text-slate-400 hover:text-slate-600 font-bold text-lg leading-none p-1">
                &times;
            </button>
        </div>

        <!-- Section 1: Batas Administrasi -->
        <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-500">Batas Administrasi</p>
            <div class="flex items-center justify-between bg-slate-50/80 p-2.5 rounded-xl border border-slate-200/60">
                <label for="toggle_district_boundaries" class="text-xs text-slate-700 font-medium cursor-pointer">Batas Kecamatan</label>
                <input type="checkbox" id="toggle_district_boundaries" class="rounded text-blue-600 focus:ring-0 cursor-pointer w-4 h-4" checked>
            </div>
            <div class="flex items-center justify-between bg-slate-50/80 p-2.5 rounded-xl border border-slate-200/60">
                <label for="toggle_village_boundaries" class="text-xs text-slate-700 font-medium cursor-pointer">Batas Desa</label>
                <input type="checkbox" id="toggle_village_boundaries" class="rounded text-blue-600 focus:ring-0 cursor-pointer w-4 h-4">
            </div>
        </div>

        <!-- Section 2: Sebaran Rawan Bencana -->
        <div class="space-y-2">
            <p class="text-[11px] font-semibold text-slate-500">Sebaran Rawan Bencana</p>
            
            <!-- Container tempat fungsi JS buildHazardLayerUI() merender kartu bencana -->
            <div id="hazard_layer_checkboxes" class="grid grid-cols-2 gap-2">
                <!-- Kartu-kartu bencana akan terisi otomatis dari script JS Anda -->
            </div>
        </div>

    </div>

</div>
@endsection

@include('map.partials.map-scripts', ['mapUiVariant' => 'landing-fs'])
