{{-- Peta dashboard admin: filter di atas peta, checkbox di bawah filter, legenda di dalam peta --}}
@include('map.partials.map-styles', ['mapUiVariant' => 'admin'])

<div class="map-page map-ui--admin space-y-3">
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-4 sm:p-5">
        <h3 class="text-base font-semibold text-gray-900 mb-1">Filter Peta</h3>
        <p class="text-xs text-gray-500 mb-4">Wilayah: <span class="font-medium text-gray-700">Kabupaten Bone Bolango</span>, Prov. Gorontalo</p>
        @include('map.partials.map-filters-fields')
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm px-4 py-3 sm:px-5">
        @include('map.partials.map-district-checkbox')
    </div>

    <div class="map-container map-admin-viewport rounded-lg overflow-hidden border border-gray-200 shadow-sm">
        <div id="map"></div>
        <div class="map-legend-inside" aria-label="Legenda peta">
            @include('map.partials.map-legend-content', ['legendLayout' => 'stack'])
        </div>
    </div>
</div>

@include('map.partials.map-scripts', ['mapUiVariant' => 'admin'])
