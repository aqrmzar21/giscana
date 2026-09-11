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

    /* Common Form & Filter Controls */
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

    /* Common Legend Elements */
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
    .legend-divider {
        width: 100%;
        height: 1px;
        background: #e5e7eb;
        margin: 5px 0;
    }

    .map-card {
        background-color: rgba(255, 255, 255, 0.75);
        -webkit-backdrop-filter: blur(6px);
        backdrop-filter: blur(6px);
    }

    /* =========================================================
       HAZARD LAYER PANEL
    ========================================================= */
    .hazard-layer-panel {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #f9fafb;
        overflow: hidden;
    }

    /* Header */
    .hazard-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        background: #fff;
        border-bottom: 1px solid #f3f4f6;
    }
    .hazard-panel-title {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .hazard-panel-icon {
        width: 14px;
        height: 14px;
        color: #6366f1;
        flex-shrink: 0;
    }
    .hazard-panel-hint {
        font-size: 10px;
        color: #9ca3af;
        padding: 5px 2px 0;
        font-style: italic;
    }

    /* Master Toggle Switch */
    .hazard-master-toggle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        user-select: none;
    }
    .hazard-master-toggle input[type="checkbox"] {
        display: none;
    }
    .hazard-toggle-track {
        position: relative;
        width: 34px;
        height: 18px;
        background: #d1d5db;
        border-radius: 9px;
        transition: background 0.2s ease;
        flex-shrink: 0;
    }
    .hazard-master-toggle input:checked + .hazard-toggle-track {
        background: #6366f1;
    }
    .hazard-toggle-thumb {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 14px;
        height: 14px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        transition: transform 0.2s ease;
    }
    .hazard-master-toggle input:checked + .hazard-toggle-track .hazard-toggle-thumb {
        transform: translateX(16px);
    }
    .hazard-toggle-text {
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        min-width: 36px;
    }
    .hazard-master-toggle input:checked ~ .hazard-toggle-text {
        color: #6366f1;
    }

    /* Badge Grid */
    .hazard-badges-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        width: 100%;
    }

    @media (max-width: 640px) {
        .hazard-badges-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Alpine.js Guard */
    [x-cloak] { display: none !important; }

    /* Style Kartu Bencana Dinamis */
    .hazard-card {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        padding: 0.65rem;
        border-radius: 0.75rem;
        border: 1.5px solid #e2e8f0;
        background-color: var(--hc-bg, #f8fafc);
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        position: relative;
        user-select: none;
    }

    .hazard-card:hover {
        transform: translateY(-1px);
    }

    /* Status Aktif */
    .hazard-card.active {
        border-color: var(--hc-color, #3b82f6);
        box-shadow: 0 4px 12px var(--hc-shadow, rgba(0,0,0,0.1));
        background-color: var(--hc-bg, #ffffff);
    }

    .hazard-card.active .hazard-card-check {
        display: flex;
    }

    /* Status Nonaktif */
    .hazard-card.inactive {
        opacity: 0.7;
        border-color: #cbd5e1;
        background-color: #f8fafc;
    }

    /* Elemen Internal Kartu */
    .hazard-card-emoji {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }

    .hazard-card-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }

    .hazard-card-status {
        font-size: 0.65rem;
        color: #64748b;
        margin-top: 0.2rem;
    }

    .hazard-card.active .hazard-card-status {
        color: var(--hc-color, #2563eb);
        font-weight: 600;
    }

    /* Tanda Centang (Checkmark) */
    .hazard-card-check {
        display: none;
        position: absolute;
        top: 0.4rem;
        right: 0.4rem;
        width: 14px;
        height: 14px;
        background-color: var(--hc-color, #3b82f6);
        border-radius: 50%;
        align-items: center;
        justify-content: center;
    }

    .hazard-card-check svg {
        stroke: #ffffff;
        stroke-width: 2;
        fill: none;
        width: 8px;
        height: 8px;
    }
</style>
@endpush