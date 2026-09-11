{{-- Section Sebaran Rawan Bencana --}}
<div class="space-y-3">
    <p class="text-xs font-bold text-slate-800 uppercase tracking-wider">Sebaran Rawan Bencana</p>

    {{-- Grid 2x2 Kartu Bencana (Kontainer ID tetap dipertahankan untuk JS) --}}
    <div id="hazard_layer_checkboxes" class="grid grid-cols-2 gap-2">
        <!-- <button type="button" data-hazard="banjir" class="hazard-card active flex items-center gap-2.5 p-2.5 rounded-xl border-2 border-blue-600 bg-blue-50/40 text-left transition-all">
            <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-bold text-slate-800 leading-tight">Banjir</div>
                <div class="text-[10px] font-semibold text-blue-600 flex items-center gap-0.5 mt-0.5">
                    <span>Ditampilkan</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
        </button>
        <button type="button" data-hazard="gempa" class="hazard-card inactive flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 bg-slate-100/80 text-left transition-all hover:bg-slate-100">
            <div class="w-8 h-8 rounded-lg bg-orange-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-bold text-slate-800 leading-tight">Gempa</div>
                <div class="text-[10px] font-medium text-slate-400 mt-0.5">Inactive</div>
            </div>
        </button>
        <button type="button" data-hazard="gelombang" class="hazard-card inactive flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 bg-slate-100/80 text-left transition-all hover:bg-slate-100">
            <div class="w-8 h-8 rounded-lg bg-cyan-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 001.09-.12M3 15a4 4 0 014-4h9a5 5 0 011.09.12M3 15V9m18 6V9" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-bold text-slate-800 leading-tight">Gelombang</div>
                <div class="text-[10px] font-medium text-slate-400 mt-0.5">Inactive</div>
            </div>
        </button>
        <button type="button" data-hazard="longsor" class="hazard-card inactive flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-200 bg-slate-100/80 text-left transition-all hover:bg-slate-100">
            <div class="w-8 h-8 rounded-lg bg-lime-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-bold text-slate-800 leading-tight">Longsor</div>
                <div class="text-[10px] font-medium text-slate-400 mt-0.5">Inactive</div>
            </div>
        </button> -->
    </div>

    {{-- Slider Opacity --}}
    <div class="pt-2">
        <div class="flex items-center justify-between mb-1">
            <span class="text-xs font-semibold text-slate-700">Opacity</span>
        </div>
        <input 
            type="range" 
            id="hazard_opacity_slider"
            min="0" 
            max="100" 
            value="80" 
            class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-slate-600 focus:outline-none">
    </div>
</div>