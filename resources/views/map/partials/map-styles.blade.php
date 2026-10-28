@php($mapUiVariant = $mapUiVariant ?? 'admin')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
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

    /* Animasi Kedip (Checkmark) */
    /* Efek Marker Berkedip untuk Fasilitas Evakuasi */
    .blinking-evac-marker {
        animation: blinker 1s linear infinite;
        filter: drop-shadow(0px 0px 8px rgba(255, 0, 0, 0.8)); /* Efek glow merah/kuning, bisa disesuaikan */
    }

    @keyframes blinker {
        0% { opacity: 1; }
        50% { opacity: 0.2; }
        100% { opacity: 1; }
    }
    // UPDATED FEATURE: Animasi Pulsing / Kedip Warna Hijau untuk Marker Titik Kumpul Tujuan (1 Menit)
    @keyframes facilityPulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.85);
        }
        70% {
            transform: scale(1.35);
            box-shadow: 0 0 0 16px rgba(16, 185, 129, 0);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    /* Class dinamis yang dipasang lewat JS pada marker tujuan */
    .evacuation-facility-marker.is-blinking div {
        background-color: #10b981 !important; /* Warna hijau menyala pembeda dari titik kumpul lain */
        animation: facilityPulse 1.2s infinite ease-in-out;
        border-color: #ffffff !important;
        box-shadow: 0 0 16px rgba(16, 185, 129, 0.9) !important;
        z-index: 999;
    }

    /* // NEW FEATURE: Styling Popup Khusus Titik Kumpul Tujuan & Popup Analisis Risiko User */
    .destination-marker-popup .leaflet-popup-content-wrapper {
        border-radius: 16px;
        padding: 0;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 15px 30px -10px rgba(16, 185, 129, 0.35), 0 0 0 1.5px rgba(16, 185, 129, 0.2);
    }
    .destination-marker-popup .leaflet-popup-content {
        margin: 0;
        line-height: 1.4;
        width: 240px !important;
        max-width: 85vw;
    }
    .user-risk-popup .leaflet-popup-content-wrapper {
        border-radius: 16px;
        padding: 0;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 18px 35px -10px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(0, 0, 0, 0.06);
    }
    .user-risk-popup .leaflet-popup-content {
        margin: 0;
        line-height: 1.4;
        width: 260px !important;
        max-width: 88vw;
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

    /* Sembunyikan default container LRM agar Tailwind Panel Navigasi yang tampil prima */
    .leaflet-routing-container {
        display: none !important;
    }

    /* User GPS Pulsing Marker */
    .user-gps-marker {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .user-gps-marker .pulse-ring {
        position: absolute;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: rgba(59, 130, 246, 0.4);
        animation: userGpsPulse 2s infinite ease-out;
    }
    .user-gps-marker .dot-center {
        position: relative;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #2563eb;
        border: 2.5px solid #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }
    @keyframes userGpsPulse {
        0% { transform: scale(0.6); opacity: 1; }
        100% { transform: scale(2.2); opacity: 0; }
    }

    /* Waypoint Titik Masuk Jalur Evakuasi */
    .junction-marker {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .junction-marker div {
        background-color: #7e22ce;
        color: white;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 2px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: bold;
        box-shadow: 0 3px 8px rgba(0,0,0,0.3);
    }

    /* Styling Kustom Tooltip Rute */
    .route-tooltip {
        background: rgba(15, 23, 42, 0.92);
        color: #f8fafc;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        padding: 6px 10px;
        font-size: 11px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4);
        line-height: 1.35;
        pointer-events: none;
    }
    .route-tooltip::before {
        border-top-color: rgba(15, 23, 42, 0.92) !important;
    }
    .clickable-route-hit {
        cursor: pointer !important;
    }

    /* Styling Elegan untuk Popup Instruksi Arah di Lokasi Marker */
    .instruction-popup .leaflet-popup-content-wrapper {
        border-radius: 18px;
        padding: 0;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.98);
        box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(0, 0, 0, 0.05);
    }
    .instruction-popup .leaflet-popup-content {
        margin: 0;
        line-height: 1.4;
        width: 320px !important;
        max-width: 88vw;
    }
    .instruction-popup .leaflet-popup-close-button {
        top: 8px;
        right: 8px;
        color: #94a3b8;
        padding: 4px;
        font-size: 16px;
    }
    .instruction-popup .leaflet-popup-close-button:hover {
        color: #ef4444;
    }
    .instruction-popup .leaflet-popup-tip {
        background: #ffffff;
    }
</style>
@endpush