@php($mapUiVariant = $mapUiVariant ?? 'admin')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    @if($mapUiVariant === 'admin')
    .map-ui--admin .map-admin-viewport {
        position: relative;
        margin: 0;
    }
    .map-ui--admin .map-admin-viewport #map {
        height: calc(100vh - 380px);
        min-height: 360px;
        width: 100%;
        z-index: 1;
    }
    .map-ui--admin .map-legend-inside {
        position: absolute;
        left: 12px;
        bottom: 12px;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.96);
        padding: 10px 12px;
        border-radius: 8px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.12);
        max-width: min(240px, calc(100% - 24px));
        pointer-events: auto;
        border: 1px solid rgba(229, 231, 235, 0.9);
    }
    .map-ui--admin .map-legend-inside .legend-item {
        margin-bottom: 4px;
    }
    .map-ui--admin .map-legend-inside .legend-item:last-child {
        margin-bottom: 0;
    }
    @elseif($mapUiVariant === 'landing-fs')
    .map-landing-fs {
        position: relative;
        flex: 1 1 0;
        min-height: 0;
        width: 100%;
        background: #e2e8f0;
    }
    .map-landing-fs #map {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }
    .map-landing-fs .leaflet-bottom.leaflet-right {
        margin-right: 8px;
        margin-bottom: 8px;
    }
    @endif

    .map-ui--admin .filter-group label,
    footer .filter-group label {
        display: block;
        margin-bottom: 4px;
        font-weight: 600;
        font-size: 12px;
        color: #4b5563;
    }
    .map-ui--admin .filter-group select,
    footer .filter-group select {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        background: #fff;
    }
    footer .filter-group {
        margin-bottom: 0;
    }
    .map-legend-content .legend-item,
    .legend .legend-item {
        display: flex;
        align-items: center;
    }
    .legend-item {
        margin-bottom: 5px;
        font-size: 12px;
    }
    .legend-color {
        width: 18px;
        height: 18px;
        margin-right: 8px;
        border-radius: 3px;
        flex-shrink: 0;
    }
</style>
@endpush
