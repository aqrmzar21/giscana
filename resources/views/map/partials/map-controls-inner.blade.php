@php($toolbarContext = $toolbarContext ?? 'overlay')
@if($toolbarContext === 'footer')
    @if(!isset($hideToolbarHeading) || !$hideToolbarHeading)
        <h3 class="text-lg font-semibold mb-1">Filter Peta</h3>
        <p class="text-xs text-gray-600 mb-4 leading-snug">Wilayah: <strong>Kabupaten Bone Bolango</strong>, Prov. Gorontalo</p>
    @endif

    @include('map.partials.map-filters-fields')

    @if(!isset($hideLegend) || !$hideLegend)
    <div class="legend mt-3 pt-3 border-t border-gray-100">
        @include('map.partials.map-legend-content', ['legendLayout' => 'inline'])
    </div>
    @endif

    <div class="mt-3">
        @include('map.partials.map-district-checkbox')
    </div>
@else
    {{-- Legacy overlay tunggal — dipakai jika ada; dashboard memakai interactive.blade.php terpisah --}}
    <h3 class="text-lg font-semibold mb-1">Filter Peta</h3>
    <p class="text-xs text-gray-600 mb-4 leading-snug">Wilayah: <strong>Kabupaten Bone Bolango</strong>, Prov. Gorontalo</p>
    @include('map.partials.map-filters-fields')
    <div class="legend mt-3">
        @include('map.partials.map-legend-content')
    </div>
    <div class="mt-3">
        @include('map.partials.map-district-checkbox')
    </div>
@endif
