{{-- Panel Navigasi Evakuasi Modern (Tailwind CSS) --}}
<div id="evacuationNavigationPanel"
     class="hidden fixed z-40 transition-all duration-300 ease-in-out
            /* Mobile: Slide-up floating sheet */
            bottom-16 left-3 right-3 max-h-[80vh]
            /* Desktop: Floating Card di Kiri Atas */
            md:bottom-auto md:left-5 md:top-24 md:right-auto md:w-96 md:max-w-md md:max-h-[86vh]
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

    {{-- Body Panel: Berisi Panduan Arah Evakuasi (Hasil Pertukaran dari Pop-up) --}}
    <div id="navPanelBody" class="p-4 space-y-3 overflow-y-auto max-h-[calc(82vh-4rem)] md:max-h-[calc(86vh-4.5rem)] scrollbar-thin scrollbar-thumb-slate-300 dark:scrollbar-thumb-slate-700">

        {{-- Kartu Info Tujuan --}}
        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-between">
            <div class="min-w-0 pr-2">
                <span class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">TITIK KUMPUL TUJUAN</span>
                <div id="navDestinationName" class="text-xs font-black text-slate-800 dark:text-slate-100 truncate">Titik Kumpul</div>
            </div>
            <button id="btnFocusNavigationRoute" type="button" title="Fokuskan Rute pada Peta" class="px-2 py-1 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300 text-[10px] font-bold flex items-center gap-1 border border-indigo-200 dark:border-indigo-800 transition-colors shrink-0">
                <span>🎯</span> Fokus
            </button>
        </div>

        {{-- Opsi Pilihan Rute: Jalur Umum (Google Maps) vs Jalur Resmi BPBD (Ungu) --}}
        <div id="navRouteOptionsContainer" class="space-y-1.5">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">PILIHAN RUTE EVAKUASI</span>
                <span class="text-[9px] text-slate-400">Klik rute atau kartu</span>
            </div>

            <div class="grid grid-cols-2 gap-2" id="navRouteOptionCards">
                {{-- Opsi 1: Jalur Umum (Google Maps Style - Biru) --}}
                <button type="button" id="btnSelectRoutePublic" onclick="window.selectEvacuationRoute && window.selectEvacuationRoute('public')"
                    class="p-2.5 rounded-xl border text-left transition-all duration-200 cursor-pointer focus:outline-none relative overflow-hidden bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-blue-400 shadow-xs">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[11px] font-bold flex items-center gap-1 text-blue-600 dark:text-blue-400">
                            <span>🛣️</span> Jalur Umum
                        </span>
                        <span id="badgeActivePublic" class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300">Pilih</span>
                    </div>
                    <div class="text-xs font-black text-slate-800 dark:text-slate-100" id="cardPublicDist">--</div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center justify-between mt-0.5">
                        <span id="cardPublicDur">--</span>
                        <span class="text-[9px] text-blue-600 dark:text-blue-400 font-semibold flex items-center gap-0.5">
                            <span class="inline-block w-2 h-2 rounded-full bg-blue-500"></span> Google
                        </span>
                    </div>
                </button>

                {{-- Opsi 2: Jalur Resmi BPBD (Ungu) --}}
                <button type="button" id="btnSelectRouteBpbd" onclick="window.selectEvacuationRoute && window.selectEvacuationRoute('bpbd')"
                    class="p-2.5 rounded-xl border text-left transition-all duration-200 cursor-pointer focus:outline-none relative overflow-hidden bg-purple-50/70 dark:bg-purple-950/40 border-purple-400 dark:border-purple-600 shadow-xs">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[11px] font-bold flex items-center gap-1 text-purple-700 dark:text-purple-300">
                            <span>🛡️</span> Jalur BPBD
                        </span>
                        <span id="badgeActiveBpbd" class="text-[9px] font-bold px-1.5 py-0.5 rounded-full bg-purple-200 text-purple-800 dark:bg-purple-800 dark:text-purple-200">Aktif ✓</span>
                    </div>
                    <div class="text-xs font-black text-purple-950 dark:text-purple-100" id="cardBpbdDist">--</div>
                    <div class="text-[10px] text-purple-700/80 dark:text-purple-300 flex items-center justify-between mt-0.5">
                        <span id="cardBpbdDur">--</span>
                        <span class="text-[9px] text-purple-700 dark:text-purple-300 font-semibold flex items-center gap-0.5">
                            <span class="inline-block w-2 h-2 rounded-full" style="background-color:rgb(153, 0, 255);"></span> Resmi
                        </span>
                    </div>
                </button>
            </div>

            <p class="text-[9px] text-slate-400 dark:text-slate-500 italic flex items-center gap-1 pt-0.5">
                <span>💡</span> Klik garis rute pada peta atau tombol di atas untuk beralih rute
            </p>
        </div>

        {{-- Panduan Arah (Daftar Instruksi Langkah Demi Langkah) --}}
        <div class="pt-1">
            <div class="flex items-center justify-between mb-2 pb-1 border-b border-slate-100 dark:border-slate-800">
                <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-1.5">
                    <span>🧭</span> Panduan Langkah Navigasi
                </h5>
                <span id="navStepsCount" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">
                    0 Langkah
                </span>
            </div>

            <div id="navInstructionsList" class="space-y-2 max-h-60 md:max-h-72 overflow-y-auto pr-1 text-xs scrollbar-thin">
                {{-- Diisi secara dinamis oleh JavaScript --}}
            </div>
        </div>

    </div>
</div>
