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

        {{-- Badge Mode Routing --}}
        <!-- <div id="navModeBadgeContainer" class="p-3 rounded-xl border transition-all">
        </div> -->

        {{-- Metrik Utama: Jarak & Estimasi --}}

        {{-- Panduan Arah (Daftar Instruksi Langkah Demi Langkah) --}}
        <div class="pt-1">
            <div class="flex items-center justify-between mb-2 pb-1 border-b border-slate-100 dark:border-slate-800">
                <h5 class="text-xs font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider flex items-center gap-1.5">
                    <span>🧭</span> Panduan Arah Evakuasi
                </h5>
                <span id="navStepsCount" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300">
                    0 Langkah
                </span>
            </div>

            <div id="navInstructionsList" class="space-y-2 max-h-64 md:max-h-80 overflow-y-auto pr-1 text-xs scrollbar-thin">
                {{-- Diisi secara dinamis oleh JavaScript --}}
            </div>
        </div>

    </div>
</div>
