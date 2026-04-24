@php($legendLayout = $legendLayout ?? 'stack')
<div class="map-legend-content text-xs">
    <div class="font-semibold mb-2 text-gray-800 @if($legendLayout === 'inline') text-sm @endif">Legenda</div>
    <div class="@if($legendLayout === 'inline') flex flex-wrap gap-x-4 gap-y-1 @endif">
        <div class="legend-item">
            <div class="legend-color" style="background-color: #ef4444;"></div>
            <span class="text-gray-700">Titik Rawan</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #10b981;"></div>
            <span class="text-gray-700">Titik Kumpul</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #3b82f6;"></div>
            <span class="text-gray-700">Rute Evakuasi</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background-color: #fde68a; border: 1px dashed #facc15;"></div>
            <span class="text-gray-700">Batas Kecamatan</span>
        </div>
    </div>
</div>
