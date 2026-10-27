{{-- Panel Navigasi Evakuasi Modern (Tailwind CSS) --}}
<div id="evacuationNavigationPanel"
     class="hidden fixed z-40 transition-all duration-300 ease-in-out
            /* Mobile: Slide-up floating sheet */
            bottom-4 left-3 right-3 max-h-[82vh]
            /* Desktop: Floating Card di Kiri Atas */
            md:bottom-auto md:left-5 md:top-20 md:right-auto md:w-96 md:max-w-md md:max-h-[88vh]
            bg-white/95 dark:bg-slate-900/95 backdrop-blur-md rounded-2xl shadow-2xl border border-slate-200/90 dark:border-slate-800 flex flex-col overflow-hidden text-slate-800 dark:text-slate-100"
     style="box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.25);">

    {{-- Header Panel --}}
    <div class="px-4 py-3 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white flex items-center justify-between border-b border-white/10 shrink-0">
        <div class="flex items-center gap-2.5">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
            </span>
            <div>
                <h3 class="text-xs font-black tracking-wider uppercase text-emerald-400">RUTE EVAKUASI AKTIF</h3>
                <p id="navSubtitleStatus" class="text-[10px] text-slate-300 font-medium">Panduan Arah Menuju Titik Aman</p>
            </div>
        </div>
        
        <div class="flex items-center gap-1">
            <button id="btnToggleNavCollapse" type="button" title="Perkecil / Perbesar Panel" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-white/10 transition-colors">
                <svg id="iconNavCollapse" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <button id="btnCloseNavPanel" type="button" title="Tutup Panel Navigasi" class="p-1 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-white/10 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Body Panel (Dapat di-collapse) --}}
    <div id="navPanelBody" class="p-4 space-y-3.5 overflow-y-auto max-h-[calc(82vh-4rem)] md:max-h-[calc(88vh-4.5rem)] scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-700">

        {{-- Badge Mode Routing --}}
        <div id="navModeBadgeContainer" class="p-3 rounded-xl border transition-all">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <span class="text-[10px] font-bold tracking-wider uppercase opacity-80" id="navModePrefix">MODE SISTEM</span>
                    <h4 id="navModeTitle" class="text-xs font-extrabold flex items-center gap-1.5 mt-0.5">
                        <span id="navModeIcon">🛣</span>
                        <span id="navModeLabel">Jalur Evakuasi Resmi</span>
                    </h4>
                </div>
                <span id="navModeTag" class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">
                    Aktif
                </span>
            </div>
            <p id="navModeDescription" class="text-[11px] mt-1.5 opacity-90 leading-snug">
                Rute langsung mengikuti jalur evakuasi resmi BPBD menuju titik kumpul terdekat.
            </p>
        </div>

        {{-- Kartu Lokasi & Titik Kumpul --}}
        <div class="space-y-2 text-xs">
            {{-- Lokasi User --}}
            <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-800">
                <div class="p-1.5 bg-rose-100 dark:bg-rose-950/80 text-rose-600 dark:text-rose-400 rounded-lg shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-bold text-slate-500 uppercase">Lokasi Saat Ini</span>
                    <p id="navUserCoord" class="font-semibold text-slate-800 dark:text-slate-100 truncate">
                        Mendeteksi...
                    </p>
                </div>
            </div>

            {{-- Titik Kumpul Tujuan --}}
            <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-emerald-50/80 dark:bg-emerald-950/30 border border-emerald-200/80 dark:border-emerald-800/50">
                <div class="p-1.5 bg-emerald-100 dark:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 rounded-lg shrink-0 mt-0.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-400 uppercase">Titik Kumpul</span>
                    <h5 id="navFacilityName" class="font-bold text-emerald-950 dark:text-emerald-200 text-sm truncate">
                        Memuat...
                    </h5>
                    <p id="navFacilityDetail" class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 truncate">
                        Kapasitas & Alamat
                    </p>
                </div>
            </div>
        </div>

        {{-- Metrik Utama: Jarak & Estimasi --}}
        <div class="grid grid-cols-2 gap-2.5">
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100/60 dark:from-slate-800 dark:to-slate-800/80 p-3 rounded-xl border border-indigo-100 dark:border-slate-700">
                <div class="flex items-center gap-1.5 text-indigo-700 dark:text-indigo-400 mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Jarak</span>
                </div>
                <div id="navDistanceValue" class="text-lg font-black text-slate-900 dark:text-white">
                    0 m
                </div>
                <span class="text-[10px] text-slate-500 dark:text-slate-400">Total rute evakuasi</span>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-amber-100/60 dark:from-slate-800 dark:to-slate-800/80 p-3 rounded-xl border border-amber-100 dark:border-slate-700">
                <div class="flex items-center gap-1.5 text-amber-700 dark:text-amber-400 mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-[10px] font-bold uppercase tracking-wider">Estimasi</span>
                </div>
                <div id="navDurationValue" class="text-lg font-black text-slate-900 dark:text-white">
                    0 Menit
                </div>
                <span class="text-[10px] text-slate-500 dark:text-slate-400">Kecepatan jalan kaki</span>
            </div>
        </div>

        {{-- Daftar Instruksi Langkah Demi Langkah --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <h5 class="text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider flex items-center gap-1.5">
                    <span>🧭</span> Instruksi Arah
                </h5>
                <span id="navStepsCount" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                    0 Langkah
                </span>
            </div>

            <div id="navInstructionsList" class="space-y-2 max-h-48 overflow-y-auto pr-1 text-xs">
                {{-- Diisi secara dinamis oleh JavaScript --}}
            </div>
        </div>

        {{-- Tombol Aksi Bawah --}}
        <div class="pt-2 border-t border-slate-100 dark:border-slate-800 grid grid-cols-2 gap-2">
            <button id="btnFocusNavigationRoute" type="button" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 shadow-sm active:scale-[0.98]">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                <span>Fokus Rute</span>
            </button>

            <button id="btnResetNavFromPanel" type="button" class="w-full bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-rose-950/40 dark:hover:text-rose-400 font-semibold py-2 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-1.5 border border-slate-200/80 dark:border-slate-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>Reset Rute</span>
            </button>
        </div>
    </div>
</div>
