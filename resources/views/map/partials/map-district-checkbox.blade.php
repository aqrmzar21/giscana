<div class="flex flex-col gap-3">
    {{-- Toggle batas wilayah --}}
    <div class="flex flex-wrap gap-x-5 gap-y-2">
        <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer select-none">
            <input type="checkbox" id="toggle_district_boundaries" class="rounded border-gray-300 text-yellow-300 focus:ring-yellow-900" checked>
            <span>Tampilkan Batas Kecamatan</span>
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer select-none">
            <input type="checkbox" id="toggle_village_boundaries" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span>Tampilkan Batas Desa</span>
        </label>
    </div>

    {{-- Panel Layer Rawan Bencana --}}
    <div class="hazard-layer-panel border border-gray-200 rounded-lg p-3 bg-gray-50">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Layer Kawasan Rawan Bencana</span>
            <button type="button" id="toggle_all_hazard_layers"
                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium underline underline-offset-2">
                Semua Aktif
            </button>
        </div>
        <div class="flex flex-wrap gap-2" id="hazard_layer_checkboxes">
            {{-- Dibuat secara dinamis oleh JavaScript setelah fetch metadata --}}
            <span class="text-xs text-gray-400 italic">Memuat layer...</span>
        </div>
    </div>
</div>
