@extends('layouts.landing-map')

@section('title', 'Peta Interaktif | Giscana')

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
                <span>📍 Cari Titik Kumpul</span>
            </button>
        </div>
    </div>

    <!-- Modal Uji Coba Cek Kawasan Aman / Rawan Bencana (Input Manual Koordinat) -->
    <div id="userHazardModal" class="fixed inset-0 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm z-[9999] hidden">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-80 md:w-96 relative border border-slate-100">
            <!-- Tombol Close (X) Modal -->
            <button id="closeUserHazardModal" type="button" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold text-xl leading-none">
                &times;
            </button>

            <div class="flex items-center gap-3 mb-4">
                <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Cek Status Kawasan</h2>
                    <p class="text-xs text-slate-500">Uji coba simulasi koordinat posisi Anda</p>
                </div>
            </div>

            <div class="mb-3">
                <label for="hazardLat" class="block text-xs font-semibold text-slate-600 mb-1">Latitude</label>
                <input type="text" id="hazardLat" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all" value="0.45" placeholder="Contoh: 0.45">
            </div>

            <div class="mb-5">
                <label for="hazardLng" class="block text-xs font-semibold text-slate-600 mb-1">Longitude</label>
                <input type="text" id="hazardLng" class="w-full border border-slate-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all" value="123.2" placeholder="Contoh: 123.2">
            </div>

            <button id="btnCheckHazardStatus" type="button" class="w-full bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-semibold py-2.5 rounded-xl shadow-md transition-all text-sm flex items-center justify-center gap-2 cursor-pointer">
                <span>🛡️ Cek Status Kawasan</span>
            </button>
        </div>
    </div>

    <!-- Peta Utama (Diakses Selalu Terbuka) -->
    <div id="map" class="absolute inset-0 h-screen w-screen z-0"></div>

    <!-- Panel Navigasi Hybrid Evakuasi -->
    @include('map.partials.navigation-panel')

    <!-- Tombol Pintas Quick Tool (Melayang di Kanan Atas Peta) -->
    <!-- Untuk tombol akses modal input manual lokasi dan modal status kawasan -->
    <div class="absolute top-20 right-5 z-50 grid grid-rows-2 gap-2">
        <button id="btnMapLocationTool" title="Input Lokasi Saat Ini" type="button" class="w-9 h-9 bg-white hover:bg-slate-50 text-slate-700 rounded-xl shadow-md border border-slate-200 flex items-center justify-center transition-all hover:scale-105 active:scale-95 group">
            <svg class="w-7 h-7 text-indigo-600 group-hover:text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </button>
        <!-- Tombol untuk Modal Status Kawasan Aman / Rawan Bencana -->
        <button id="btnOpenUserHazardModal" title="Status Kawasan Aman / Rawan Bencana" type="button" class="w-9 h-9 bg-white hover:bg-slate-50 text-slate-700 rounded-xl shadow-md border border-slate-200 flex items-center justify-center transition-all hover:scale-105 active:scale-95 group">
            <svg class="w-5 h-5 text-indigo-600 group-hover:text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>
        </button>
    </div>

    <div class="hidden md:flex absolute md:bottom-4 bottom-16 left-4 z-50 md:p-2 pb-1 group">
        @include('map.partials.map-legend-content')
    </div>

    <!-- Wrapper Kontrol Peta (Alpine.js) -->
    <div x-data="{ openPanel: true }">

        <div 
            :class="openPanel ? 'translate-y-0 md:translate-x-0' : 'translate-y-[calc(100%-2.75rem)] md:translate-y-0 md:translate-x-[calc(100%+1rem)]'"
            class="fixed z-50 transition-transform duration-300 ease-in-out
                   /* Mobile Styling (Bottom Sheet Slide Down) */
                   bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl border-t border-slate-200 px-5 pt-3 pb-4 max-h-[80vh] overflow-y-auto
                   /* Desktop Styling (Slide Right Floating Card) */
                   md:bottom-12 md:right-4 md:top-auto md:left-auto md:w-80 md:rounded-2xl md:border md:shadow-xl md:overflow-visible"
            x-cloak>
            
            <!-- 📐 Handle Bar / Strip Bawah (Mobile Only) -->
            <div 
                @click="openPanel = !openPanel" 
                class="w-full flex flex-col items-center cursor-pointer pb-2 pt-1 md:hidden group">
                <div class="w-12 h-1.5 bg-slate-300 group-hover:bg-slate-400 rounded-full transition-colors"></div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-1" x-text="openPanel ? 'Tutup Kontrol' : 'Buka Kontrol Peta'"></span>
            </div>

            <!-- 🗂️ Tombol Strip Geser Samping (Desktop Only) -->
            <button 
                @click="openPanel = !openPanel" 
                type="button"
                class="hidden md:flex absolute -left-8 top-1/2 -translate-y-1/2 w-8 h-12 bg-white hover:bg-slate-50 border-l border-y border-slate-200/90 shadow-md rounded-l-xl items-center justify-center text-slate-600 hover:text-indigo-600 transition-colors focus:outline-none cursor-pointer group"
                :title="openPanel ? 'Sembunyikan Panel Kontrol' : 'Tampilkan Panel Kontrol'">
                <svg class="w-5 h-5 transition-transform duration-300" :class="openPanel ? 'rotate-0' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <!-- Header Panel -->
            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5 dark:text-white/80">
                    <span>⚙️</span> Kontrol Peta
                </h3>
                <button @click="openPanel = false" class="md:hidden text-slate-400 hover:text-slate-600 font-semibold text-lg p-1 leading-none">
                    &times;
                </button>
            </div>
            
            <!-- Section 0: Toggle Switch Posisi Saya -->
            <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider block py-2 dark:text-white/80">LOKASI SAYA</span>
            <div class="mb-4">
                <div class="grid grid-cols-[auto_1fr_1fr] gap-2 items-center">
                    <!-- // Tombol 1: Posisi Saya (Toggle ON/OFF) -->
                    <button id="btnToggleUserPosition" type="button" class="w-9 h-9 bg-white hover:bg-white-700 text-slate-700 rounded-xl shadow-md border border-slate-200 flex items-center justify-center transition-all hover:scale-105 active:scale-95 group">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm8.94 3A8.994 8.994 0 0013 3.06V1h-2v2.06A8.994 8.994 0 003.06 11H1v2h2.06A8.994 8.994 0 0011 20.94V23h2v-2.06A8.994 8.994 0 0020.94 13H23v-2h-2.06zM12 19c-3.87 0-7-3.13-7-7s3.13-7 7-7 7 3.13 7 7-3.13 7-7 7z"/></svg>
                        <!-- <span id="btnToggleUserPositionText">Posisi Saya</span> -->
                    </button>
                    <!-- // Tombol 2: Cari Titik Kumpul (Hanya aktif jika lokasi user tersedia) -->
                    <button id="btnFindNearestFacility" type="button" class="w-full bg-emerald-600 hover:bg-blue-700 active:scale-[0.98] text-white font-bold py-2.5 px-3 rounded-xl shadow-md transition-all text-xs flex items-center justify-center gap-2 opacity-50 cursor-not-allowed" disabled>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.414 10l4.244 4.243a1 1 0 01-.707 1.707l-8.486 0M13.414 10l2.122-2.122a1 1 0 011.414 0L20 10"/>
                        </svg>
                        <span class="text-sm">Cari</span>
                    </button>

                    <!-- // Tombol 3: Reset Rute (Menghapus routing tanpa menghapus marker user) -->
                    <button id="btnResetRoute" type="button" class="w-full bg-white hover:bg-slate-100 active:scale-[0.98] text-slate-600 font-semibold py-2 px-3 rounded-xl border border-slate-200 shadow-md transition-all text-xs flex items-center justify-center gap-2 opacity-50 cursor-not-allowed" disabled>
                        <span class="text-sm">⟲</span>
                        <span>Reset</span>
                    </button>
                </div>
            </div>

            <!-- Section 1: Toggle Switch Batas Administrasi -->
            <h6 class="text-xs font-bold text-slate-800 uppercase tracking-wider py-2 dark:text-white/80">BATAS ADMINITSRASI</h6>
            <div class="mb-4">
                @include('map.partials.map-district-checkbox')
            </div>
            
            <!-- Section 2 & 3: Sebaran Rawan Bencana & Slider Opacity -->
            <h6 class="text-xs font-bold text-slate-800 uppercase tracking-wider py-2 dark:text-white/80">SEBARAN RAWAN BENCANA</h6>
            <div class="mb-4">
                @include('map.partials.map-panel-hazard')
            </div>

            <!-- Watermark Footer -->
            <div class="text-center pt-3 border-t border-slate-100 block md:hidden">
                <p class="text-[10px] text-slate-400 font-medium tracking-wide">©2026 Giscana</p>
            </div>
        </div>
        <audio id="alert-sound" src="{{ asset('sound/alert_alarm_tikum.mp3') }}" preload="auto"></audio>

    </div>
@endsection

@include('map.partials.map-scripts', ['mapUiVariant' => 'landing-fs'])