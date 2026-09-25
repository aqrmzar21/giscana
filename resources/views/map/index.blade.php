@extends('layouts.landing-map')

@section('title', 'Peta Interaktif - Giscana')

@include('map.partials.map-styles', ['mapUiVariant' => 'landing-fs'])

@section('content')

    <!-- Modal Input Lokasi (Pure Tailwind CSS Overlay) -->
    <div id="locationModal" class="fixed inset-0 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm z-[9999] hidden">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-80 md:w-96 relative border border-slate-100">
            <!-- Tombol Close (X) Modal -->
            <button id="closeLocationModal" type="button" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold text-xl leading-none">
                &times;
            </button>

            <div class="flex items-center gap-3 mb-4">
                <div class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Masukkan Lokasi Anda</h2>
                    <p class="text-xs text-slate-500">Cari titik kumpul evakuasi terdekat</p>
                </div>
            </div>

            <div class="mb-3">
                <label for="lat" class="block text-xs font-semibold text-slate-600 mb-1">Latitude</label>
                <input type="text" id="lat" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all" value="-7.25">
            </div>

            <div class="mb-5">
                <label for="lng" class="block text-xs font-semibold text-slate-600 mb-1">Longitude</label>
                <input type="text" id="lng" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-all" value="112.75">
            </div>

            <button id="setLocation" type="button" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-xl shadow-md transition-all text-sm flex items-center justify-center gap-2">
                <span>📍 Set Lokasi & Cari Titik Kumpul</span>
            </button>
        </div>
    </div>

    <!-- Peta Utama (Diakses Selalu Terbuka) -->
    <div id="map" class="absolute inset-0 h-screen w-screen z-0"></div>

    <!-- Tombol Pintas Quick Tool (Melayang di Kiri Atas Peta) -->
    <div class="absolute top-20 left-3.5 z-40">
        <button id="btnMapLocationTool" title="Input Lokasi Saat Ini" type="button" class="w-9 h-9 bg-white hover:bg-slate-50 text-slate-700 rounded-xl shadow-md border border-slate-200 flex items-center justify-center transition-all hover:scale-105 active:scale-95 group">
            <svg class="w-5 h-5 text-indigo-600 group-hover:text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </button>
    </div>

    <div class="absolute md:bottom-4 bottom-10 left-4 z-50 md:p-2 pb-12">
        @include('map.partials.map-legend-content')
    </div>
    
    <!-- Header Bar Melayang -->
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

        <div 
            :class="openPanel ? 'translate-y-0' : 'translate-y-[calc(100%-2.75rem)]'"
            class="fixed z-50 transition-transform duration-300 ease-in-out
                   bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl border-t border-slate-200 px-5 pt-3 pb-4 max-h-[80vh] overflow-y-auto
                   md:bottom-12 md:right-4 md:top-auto md:left-auto md:w-80 md:rounded-2xl md:border md:shadow-xl md:translate-y-0"
            x-cloak>
            
            <div 
                @click="openPanel = !openPanel" 
                class="w-full flex flex-col items-center cursor-pointer pb-2 pt-1 md:hidden group">
                <div class="w-12 h-1.5 bg-slate-300 group-hover:bg-slate-400 rounded-full transition-colors"></div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1" x-text="openPanel ? 'Tutup Kontrol' : 'Tarik Kontrol Peta'"></span>
            </div>

            <!-- Header Panel -->
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                    <span>⚙️</span> Kontrol Peta
                </h3>
                <button @click="openPanel = false" class="md:hidden text-slate-400 hover:text-slate-600 font-semibold text-lg p-1 leading-none">
                    &times;
                </button>
            </div>
            
            <!-- 📍 SECTION BARU: Panel Opsi Navigasi Lokasi User -->
            <div class="mb-4 bg-slate-50 p-3 rounded-2xl border border-slate-200/80">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">LOKASI SAYA</span>
                    <span id="userLocationStatus" class="text-[10px] font-semibold text-slate-500 bg-slate-200/80 px-2 py-0.5 rounded-full">Belum Diatur</span>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <!-- 🎯 TOMBOL BARU: Deteksi GPS Otomatis -->
                    <button id="btnGetCurrentLocation" type="button" class="w-full bg-emerald-50 hover:bg-emerald-100 active:scale-[0.98] text-emerald-700 font-bold py-2.5 px-3 rounded-xl border border-emerald-200 shadow-xs transition-all text-xs flex items-center justify-center gap-1">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/>
                        </svg>
                        <span>Set Lokasi</span>
                        <!-- <span>Gunakan Lokasi GPS Saya Saat Ini</span> -->
                    </button>
                    <!-- <button id="btnOpenLocationModal" type="button" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs py-2 px-3 rounded-xl shadow-xs transition-all flex items-center justify-center gap-1">
                        <span>📍 Atur Lokasi</span>
                    </button> -->
                    <button id="btnResetLocation" type="button" class="w-full bg-white hover:bg-rose-50 text-slate-500 hover:text-rose-600 font-semibold text-xs py-2 px-3 rounded-xl border border-slate-200 hover:border-rose-200 transition-all flex items-center justify-center gap-1 opacity-50 cursor-not-allowed" disabled>
                        <span>❌ Reset</span>
                    </button>
                </div>
            </div>

            <!-- Section 1: Toggle Switch Batas Administrasi -->
            <div class="mb-4">
                <h6 class="text-xs font-bold text-slate-800 uppercase tracking-wider py-2">FILTER BATAS ADMINITSRASI</h6>
                @include('map.partials.map-district-checkbox')
            </div>
            
            <!-- Section 2 & 3: Sebaran Rawan Bencana & Slider Opacity -->
            <div class="mb-4">
                @include('map.partials.map-panel-hazard')
            </div>

            <!-- Watermark Footer -->
            <div class="text-center pt-3 border-t border-slate-100 block md:hidden">
                <p class="text-[10px] text-slate-400 font-medium tracking-wide">©2026 Giscana</p>
            </div>
        </div>

    </div>
@endsection

@include('map.partials.map-scripts', ['mapUiVariant' => 'landing-fs'])