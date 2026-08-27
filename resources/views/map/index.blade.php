@extends('layouts.landing-map')

@section('title', 'Peta Interaktif - Giscana')

@include('map.partials.map-styles', ['mapUiVariant' => 'landing-fs'])

@section('content')

<div id="map" class="absolute inset-0 h-screen w-screen z-50"></div>

    <!-- Legenda di pojok kiri bawah -->
    <div class="absolute bottom-4 left-4 z-50 p-3">
        @include('map.partials.map-legend-content')
    </div>

    <!-- Layer Control Modern -->
    <div id="layer-control" class="absolute right-4 top-6 z-50">
        <div id="layer-control-card" class="map-card hidden md:block w-80 bg-white/75 backdrop-blur-sm border border-white/30 rounded-2xl shadow-lg p-3">
            <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                </div>
                <div>
                <div class="text-sm font-semibold text-gray-800">Layer Peta</div>
                <div class="text-xs text-gray-500">Pilih base map dan overlay</div>
                </div>
            </div>
            <button id="layer-control-toggle" class="md:hidden inline-flex items-center justify-center p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            </div>

            <div id="layer-control-body" class="mt-3 space-y-3">
            <!-- Base map segmented control -->
            <div>
                <div class="text-xs text-gray-500 mb-2">Peta Dasar</div>
                <div class="flex gap-2">
                <button data-basemap="osm" class="basemap-btn flex-1 text-xs py-2 rounded-lg bg-white/60 hover:bg-white/80 text-gray-700 border border-transparent">Peta Jalan</button>
                <button data-basemap="topo" class="basemap-btn flex-1 text-xs py-2 rounded-lg bg-white/60 hover:bg-white/80 text-gray-700 border border-transparent">Topografi</button>
                <button data-basemap="esriSat" class="basemap-btn flex-1 text-xs py-2 rounded-lg bg-white/60 hover:bg-white/80 text-gray-700 border border-transparent">Satelit</button>
                </div>
            </div>

            <!-- Overlay toggles -->
            <div>
                <div class="text-xs text-gray-500 mb-2">Overlay</div>
                <div id="overlay-list" class="space-y-2 text-sm">
                <!-- item akan di-generate oleh JS -->
                </div>
            </div>

            <!-- small hint -->
            <div class="text-xs text-gray-400">Ketuk nama layer untuk menyalakan / mematikan. Hanya satu Zona Bencana aktif sekaligus.</div>
            </div>
        </div>

        <!-- Mobile floating button -->
        <button id="layer-control-fab" class="md:hidden inline-flex items-center gap-2 px-4 py-2 rounded-full bg-indigo-600 text-white shadow-lg focus:outline-none">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
            <span class="text-sm">Layer</span>
        </button>
    </div>
    
    <!-- Toolbar melayang transparan -->
    <div class="absolute bottom-6 right-0 z-50">
        <div class="flex flex-col lg:flex-row items-end justify-end gap-6 items-end px-6">
            
            <!-- Panel Legenda -->
            <!-- <div class="bg-white/80 backdrop-blur-md rounded-lg shadow-lg p-4 h-auto w-auto">
            </div> -->

            <!-- Panel Filter + Checkbox -->
            <div class="bg-white/80 backdrop-blur-md rounded-lg shadow-lg p-4 space-y-4 h-auto">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Filter Peta</h3>
                @include('map.partials.map-filters-fields')

                <div class="border-t border-gray-200 pt-3">
                    @include('map.partials.map-district-checkbox')
                </div>
            </div>

            <!-- Panel Layer Rawan Bencana -->
            <div class="bg-white/80 backdrop-blur-md rounded-lg shadow-lg p-4 space-y-4 h-auto">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Layer Rawan Bencana</h3>
                @include('map.partials.map-panel-hazard')
            </div>

        </div>
    </div>
@endsection

@include('map.partials.map-scripts', ['mapUiVariant' => 'landing-fs'])
