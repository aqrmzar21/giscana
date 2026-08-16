@php($legendLayout = $legendLayout ?? 'stack')
<div class="map-legend-content text-xs">
    <div class="font-semibold mb-2 text-gray-800 @if($legendLayout === 'inline') text-sm @endif">Legenda</div>
    <div class="@if($legendLayout === 'inline') flex flex-wrap gap-x-4 gap-y-1 @endif">
        {{-- Data dari DB --}}
        <div class="legend-item">
            <div class="legend-color" style="background-color: #ef4444;"></div>
            <span class="text-gray-700">Titik Bencana</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #3b82f6;"></div>
            <span class="text-gray-700">Titik Kumpul</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: rgb(153, 0, 255);"></div>
            <span class="text-gray-700">Rute Evakuasi</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #fde68a; border: 1px dashed #facc15;"></div>
            <span class="text-gray-700">Batas Wilayah</span>
        </div>

        {{-- Divider --}}
        <div class="legend-divider my-1"></div>
        <div class="text-gray-500 font-semibold text-xs mb-1 w-full">Sebaran Rawan Bencana</div>

        {{-- Layer GeoJSON Rawan Bencana --}}
        <div class="legend-item">
            <div class="legend-color" style="background-color: #3b82f6; opacity: 0.7;"></div>
            <span class="text-gray-700">Rawan Banjir</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #f97316; opacity: 0.7;"></div>
            <span class="text-gray-700">Rawan Gempa</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #06b6d4; opacity: 0.7;"></div>
            <span class="text-gray-700">Rawan Tsunami</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #84cc16; opacity: 0.7;"></div>
            <span class="text-gray-700">Rawan Longsor</span>
        </div>
    </div>
</div>
