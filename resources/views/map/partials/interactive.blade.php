{{-- Peta dashboard admin: filter di atas peta, checkbox di bawah filter, legenda di dalam peta --}}
@include('map.partials.map-styles', ['mapUiVariant' => 'admin'])

<div class="map-page map-ui--admin space-y-3">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 sm:p-5">
            <p class="text-md">Klik disini untuk <button class="text-sm text-blue-500 px-2" onclick="location.reload()">Load peta </button>jika peta tidak muncul</p>
            <h3 class="text-base font-semibold text-gray-900 mb-1">Peta Interaktif Admin</h3>
            <p class="text-xs text-gray-500 mb-4">Wilayah: <span class="font-medium text-gray-700">Kabupaten Bone Bolango</span>, Prov. Gorontalo</p>
            <!-- {{-- Toggle batas wilayah --}} -->
            <div class="flex flex-wrap items-end gap-4 mt-2 mb-4">
                <div class="filter-group mb-0 min-w-[160px]">
                    <label for="disaster_type" class="block text-xs font-medium text-gray-600">Jenis Bencana</label>
                    <select id="disaster_type" name="disaster_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="all" {{ $disasterType === 'all' ? 'selected' : '' }}>Semua Jenis</option>
                        <option value="longsor" {{ $disasterType === 'longsor' ? 'selected' : '' }}>Longsor</option>
                        <option value="banjir" {{ $disasterType === 'banjir' ? 'selected' : '' }}>Banjir</option>
                    </select>
                </div>

                <div class="filter-group mb-0 min-w-[160px]">
                    <label for="risk_level" class="block text-xs font-medium text-gray-600">Tingkat Risiko</label>
                    <select id="risk_level" name="risk_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="all" {{ $riskLevel === 'all' ? 'selected' : '' }}>Semua Tingkat</option>
                        <option value="low" {{ $riskLevel === 'low' ? 'selected' : '' }}>Rendah</option>
                        <option value="medium" {{ $riskLevel === 'medium' ? 'selected' : '' }}>Sedang</option>
                        <option value="high" {{ $riskLevel === 'high' ? 'selected' : '' }}>Tinggi</option>
                        <option value="very_high" {{ $riskLevel === 'very_high' ? 'selected' : '' }}>Sangat Tinggi</option>
                    </select>
                </div>
            </div>

            <!-- {{-- Toggle batas wilayah --}} -->
            <div class="flex flex-wrap items-center gap-x-6 gap-y-3 mt-2">
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
    
    </div>
    
    <div class="map-container map-admin-viewport rounded-lg overflow-hidden border border-gray-200 shadow-sm relative">
        <div id="map"></div>
        @include('map.partials.navigation-panel')
        <div class="map-legend-inside" aria-label="Legenda peta">
            @include('map.partials.map-legend-content', ['legendLayout' => 'stack'])
        </div>
    </div>
</div>

@include('map.partials.map-scripts', ['mapUiVariant' => 'admin'])