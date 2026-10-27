{{-- Peta dashboard admin: filter di atas peta, checkbox di bawah filter, legenda di dalam peta --}}
@include('map.partials.map-styles', ['mapUiVariant' => 'admin'])

<div class="map-page map-ui--admin space-y-3">
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 sm:p-5">
        <h3 class="text-base font-semibold text-gray-900 mb-1">Peta Interaktif Admin</h3>
        <p class="text-xs text-gray-500 mb-4">Wilayah: <span class="font-medium text-gray-700">Kabupaten Bone Bolango</span>, Prov. Gorontalo</p>
        @include('map.partials.map-filters-fields')
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm px-4 py-3 sm:px-5">
        <!-- {{-- Toggle batas wilayah --}} -->
        <div class="flex flex-wrap items-center gap-x-6 gap-y-3">
            <label class="flex items-center cursor-pointer">
                <input type="checkbox" id="toggle_district_boundaries" class="sr-only peer" checked>
                <div class="w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-yellow-400 relative transition">
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-5"></div>
                </div>
                <span class="ml-3 text-sm text-gray-700">Batas Kecamatan</span>
            </label>

            <label class="flex items-center cursor-pointer">
                <input type="checkbox" id="toggle_village_boundaries" class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-green-500 relative transition">
                    <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-5"></div>
                </div>
                <span class="ml-3 text-sm text-gray-700">Batas Desa</span>
            </label>
        </div>
                
    </div>

    <!-- {{-- Panel Layer Rawan Bencana --}} -->
    <div class="hazard-layer-panel">
        {{-- Header dengan toggle switch master --}}
        <div class="hazard-panel-header">
            <div class="hazard-panel-title">
                <h3 class="text-base font-semibold text-gray-900 mb-1">Sebaran Kawasan Rawan Bencana</h3>
            </div>
        </div>

        <!-- {{-- Hint teks --}} -->
        <p class="hazard-panel-hint mx-4">Pilih satu jenis bencana untuk ditampilkan di peta</p>

        <!-- {{-- Badge grid per bencana --}} -->
        <div class="hazard-badges-grid" id="hazard_layer_checkboxes">
            <span class="hazard-loading">
                <svg class="hazard-loading-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" opacity="0.25"/>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" opacity="0.75"/>
                </svg>
                Memuat layer bencana...
            </span>
        </div>
    </div>

    <div class="map-container map-admin-viewport rounded-lg overflow-hidden border border-gray-200 shadow-sm">
        <div id="map"></div>
        <div class="map-legend-inside" aria-label="Legenda peta">
            @include('map.partials.map-legend-content', ['legendLayout' => 'stack'])
        </div>
    </div>
</div>

@include('map.partials.map-scripts', ['mapUiVariant' => 'admin'])