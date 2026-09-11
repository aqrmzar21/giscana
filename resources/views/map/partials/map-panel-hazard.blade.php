
    {{-- Hint Teks --}}
    <!-- <p class="hazard-panel-hint text-xs text-gray-400 italic mb-3">💡 Pilih satu jenis bencana untuk menampilkan zona rawan pada peta.</p> -->

    {{-- Badge Grid Container --}}
    <div class="hazard-badges-grid grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[200px] overflow-y-auto pr-1" id="hazard_layer_checkboxes">
        {{-- Loading State --}}
        <div class="col-span-full flex items-center justify-center gap-2 py-4 text-xs text-gray-500">
            <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span>Memuat layer bencana...</span>
        </div>
    </div>