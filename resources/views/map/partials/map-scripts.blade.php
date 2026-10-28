@php($mapUiVariant = $mapUiVariant ?? 'admin')
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
<script>
(function () {
    const mapUiVariant = @json($mapUiVariant);
    const map = L.map('map').setView([0.45, 123.2], 10);

    let mapMaxBoundsApplied = false;
    let mapInitialExtentDone = false;

    const villageBoundariesLayer = L.layerGroup();
    let villageGeojsonLoaded = false;
    let villageAidsData = {};

    function invalidateMapSize() {
        map.invalidateSize({ animate: false });
    }

    const layers = {
        disasterZones: L.layerGroup().addTo(map),
        evacuationRoutes: L.layerGroup().addTo(map),
        evacuationFacilities: L.layerGroup().addTo(map),
        districtBoundaries: L.layerGroup().addTo(map),
    };

    // Base Maps
    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    const topo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenTopoMap contributors',
        maxZoom: 17
    });

    const esriSat = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles © Esri'
    });

    const baseMaps = {
        "Peta Jalan": osm,
        "Topografi": topo,
        "Satelit": esriSat
    };

    const overlayMaps = {
        "Zona Bencana": layers.disasterZones,
        "Rute Evakuasi": layers.evacuationRoutes,
        "Fasilitas Evakuasi": layers.evacuationFacilities,
        "Batas Administrasi": layers.districtBoundaries
    };

    // Control Layer Leaflet
    L.control.layers(baseMaps, null, {
        collapsed: true,
        position: 'topright'
    }).addTo(map);

    L.control.layers(null, overlayMaps, {
        collapsed: true,
        position: 'bottomright'
    }).addTo(map);

    // =====================================================================
    // MANAJEMEN MODAL & KONTROL LOKASI USER (HYBRID EVACUATION SYSTEM)
    // =====================================================================
    // STATE MANAGEMENT: NAVIGASI & MULTI-HAZARD ANALYSIS
    // =====================================================================
    let userMarker = null;
    let nearestMarker = null;
    let userRouteLayerGroup = L.layerGroup().addTo(map); // Layer khusus rute user
    let userRoutingControl = null;
    let blinkingTimer = null;
    let currentNavigationBounds = null;
    let currentRoutingData = null;
    let currentRoutePolylines = {
        public: { casing: null, main: null, hit: null },
        bpbd: { casing: null, main: null, hit: null }
    };
    let activeRouteKey = 'bpbd';
    let currentUserLat = null;
    let currentUserLng = null;
    let isUserPositionActive = false;
    let hasPlayedAlert = false;
    let lastUserRegion = null;
    let lastUserHazards = null;
    let isAnalyzingHazards = false;

    // Fungsi Buka Modal Input Koordinat Manual
    function openLocationModal() {
        const modal = document.getElementById('locationModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    // Fungsi Tutup Modal Input Koordinat Manual
    function closeLocationModal() {
        const modal = document.getElementById('locationModal');
        if (modal) {
            modal.classList.add('hidden');
        }
        invalidateMapSize();
    }

    // =====================================================================
    // MANAJEMEN MODAL UJI COBA INPUT KOORDINAT CEK KAWASAN (TESTING SIMULASI)
    // =====================================================================
    function openUserHazardModal() {
        const modal = document.getElementById('userHazardModal');
        if (modal) {
            modal.classList.remove('hidden');
            const inputLat = document.getElementById('hazardLat');
            const inputLng = document.getElementById('hazardLng');
            if (currentUserLat && inputLat) inputLat.value = currentUserLat.toFixed(6);
            if (currentUserLng && inputLng) inputLng.value = currentUserLng.toFixed(6);
        }
    }

    function closeUserHazardModal() {
        const modal = document.getElementById('userHazardModal');
        if (modal) {
            modal.classList.add('hidden');
        }
        invalidateMapSize();
    }

    // Fungsi Pengujian & Analisis Lokasi Simulasi (Cek Kawasan Aman / Rawan Bencana)
    async function executeCheckHazardStatus(lat, lng) {
        closeUserHazardModal();

        currentUserLat = lat;
        currentUserLng = lng;
        isUserPositionActive = true;

        // Sinkronisasi input form pada modal rute manual
        const inputRouteLat = document.getElementById('lat');
        const inputRouteLng = document.getElementById('lng');
        if (inputRouteLat) inputRouteLat.value = lat.toFixed(6);
        if (inputRouteLng) inputRouteLng.value = lng.toFixed(6);

        // Buat Marker Lokasi User (Radar Pulsing)
        if (userMarker) map.removeLayer(userMarker);
        const userIcon = L.divIcon({
            className: 'user-gps-marker',
            html: '<div class="pulse-ring"></div><div class="dot-center"></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(map);

        // Pasang popup loading sementara di peta
        userMarker.bindPopup(`
            <div style="padding:10px 14px;font-size:11px;font-weight:600;color:#334155;display:flex;align-items:center;gap:6px;">
                <svg class="animate-spin h-3.5 w-3.5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>Menganalisis status kawasan aman/rawan...</span>
            </div>
        `, { className: 'user-risk-popup' }).openPopup();

        map.panTo([lat, lng]);

        // Update tombol panel kontrol kanan agar tombol Cari Titik Kumpul aktif
        const btnToggle = document.getElementById('btnToggleUserPosition');
        const textElem = document.getElementById('btnToggleUserPositionText');
        const btnFind = document.getElementById('btnFindNearestFacility');
        if (btnToggle) {
            btnToggle.className = 'w-9 h-9 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white font-bold py-2.5 px-3 rounded-xl shadow-xs transition-all text-xs flex items-center justify-center gap-2 cursor-pointer';
        }
        if (textElem) textElem.textContent = 'Posisi Aktif (Klik utk Matikan)';
        if (btnFind) {
            btnFind.disabled = false;
            btnFind.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        // Jalankan Analisis Multi-Hazard & Identifikasi Wilayah
        try {
            const [region, matchedHazards] = await Promise.all([
                identifyUserRegion(lat, lng),
                collectAllHazardMatches(lat, lng)
            ]);

            lastUserRegion = region;
            lastUserHazards = matchedHazards;

            // Buka Popup Status Aman / Rawan Bencana langsung di titik marker user
            const popupHtml = buildUserRiskPopupHtml(region, matchedHazards);
            userMarker.bindPopup(popupHtml, { className: 'user-risk-popup', maxWidth: 280 }).openPopup();
        } catch (e) {
            console.error('Error saat analisis multi hazard:', e);
            userMarker.bindPopup(`
                <div style="padding:10px 12px;font-size:11px;color:#0f172a;">
                    <b>📍 POSISI SAYA</b><br>
                    Koordinat: ${lat.toFixed(5)}, ${lng.toFixed(5)}
                </div>
            `, { className: 'user-risk-popup' }).openPopup();
        }
    }

    // =====================================================================
    // // NEW FEATURE: ENGINE ANALISIS RISIKO SPASIAL MULTI-HAZARD
    // Menggunakan Ray-Casting Point-in-Polygon dengan In-Memory Cache
    // Menerapkan Prinsip "Collect All Matches" (Membaca SEMUA layer hazard tanpa break/first-match)
    // =====================================================================
    window.giscanaGeojsonCache = window.giscanaGeojsonCache || {};

    async function fetchGeojsonWithCache(url) {
        if (window.giscanaGeojsonCache[url]) {
            return window.giscanaGeojsonCache[url];
        }
        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error(`HTTP ${res.status} saat memuat ${url}`);
            const data = await res.json();
            window.giscanaGeojsonCache[url] = data;
            return data;
        } catch (err) {
            console.error('Gagal memuat GeoJSON untuk analisis spasial:', url, err);
            return null;
        }
    }

    // Algoritma Ray-Casting Point in Ring
    function isPointInRing(point, ring) {
        const x = point[0], y = point[1];
        let inside = false;
        for (let i = 0, j = ring.length - 1; i < ring.length; j = i++) {
            const xi = ring[i][0], yi = ring[i][1];
            const xj = ring[j][0], yj = ring[j][1];
            const intersect = ((yi > y) !== (yj > y)) && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
            if (intersect) inside = !inside;
        }
        return inside;
    }

    // Pengecekan Geometri Polygon / MultiPolygon (mendukung holes)
    function isPointInGeoJsonGeometry(lng, lat, geometry) {
        if (!geometry || !geometry.coordinates) return false;
        const pt = [lng, lat];
        if (geometry.type === 'Polygon') {
            const rings = geometry.coordinates;
            if (!rings || rings.length === 0) return false;
            // Harus berada di dalam exterior ring
            if (!isPointInRing(pt, rings[0])) return false;
            // Dan tidak boleh berada di dalam lubang (interior ring / holes)
            for (let i = 1; i < rings.length; i++) {
                if (isPointInRing(pt, rings[i])) return false;
            }
            return true;
        } else if (geometry.type === 'MultiPolygon') {
            for (let p = 0; p < geometry.coordinates.length; p++) {
                const rings = geometry.coordinates[p];
                if (rings && rings.length > 0 && isPointInRing(pt, rings[0])) {
                    let inHole = false;
                    for (let h = 1; h < rings.length; h++) {
                        if (isPointInRing(pt, rings[h])) {
                            inHole = true;
                            break;
                        }
                    }
                    if (!inHole) return true;
                }
            }
        }
        return false;
    }

    // // NEW FEATURE: Identifikasi Wilayah (Desa & Kecamatan) dari Batas Administrasi Resmi
    async function identifyUserRegion(lat, lng) {
        const districtUrls = [
            '/geojson/Kecamatan Kabila Bone-KEL_DESA.geojson',
            '/geojson/Kecamatan Bulawa-KEL_DESA.geojson',
            '/geojson/Kecamatan Bonepantai-KEL_DESA.geojson',
            '/geojson/Kecamatan Bone-KEL_DESA.geojson',
            '/geojson/Kecamatan Bone Raya-KEL_DESA.geojson'
        ];
        for (const url of districtUrls) {
            const data = await fetchGeojsonWithCache(url);
            if (data && data.features) {
                for (const feat of data.features) {
                    if (isPointInGeoJsonGeometry(lng, lat, feat.geometry)) {
                        const p = feat.properties || {};
                        const namaDesa = p.nama || (p.kel_desa ? `Desa ${p.kel_desa}` : (p.ori_name ? `Desa ${p.ori_name}` : 'Kawasan Pesisir'));
                        const namaKec = p.kecamatan ? `Kecamatan ${p.kecamatan}` : 'Kabupaten Bone Bolango';
                        return { desa: namaDesa, kecamatan: namaKec };
                    }
                }
            }
        }
        return { desa: 'Kawasan Pesisir Bone Bolango', kecamatan: 'Kabupaten Bone Bolango' };
    }

    // // NEW FEATURE: Analisis Multi Hazard ("Collect All Matches")
    // Membaca SEMUA 4 layer rawan bencana: Banjir, Longsor, Gempa, Gelombang
    async function collectAllHazardMatches(lat, lng) {
        const hazardDefs = [
            { name: 'Banjir',    field: 'Bhy_Bjr',   url: '/geojson/rawan_banjir.geojson' },
            { name: 'Longsor',   field: 'Bhy_Lsr',   url: '/geojson/rawan_longsor.geojson' },
            { name: 'Gempa',     field: 'Bhy_GmpBm', url: '/geojson/rawan_gempa.geojson' },
            { name: 'Gelombang', field: 'Bhy_GEA',   url: '/geojson/rawan_gelombang.geojson' }
        ];

        const matches = [];

        // Evaluasi setiap layer hazard secara independen (Collect All Matches)
        for (const h of hazardDefs) {
            const geojson = await fetchGeojsonWithCache(h.url);
            if (geojson && geojson.features) {
                for (const feat of geojson.features) {
                    if (isPointInGeoJsonGeometry(lng, lat, feat.geometry)) {
                        const level = feat.properties ? (feat.properties[h.field] || 'Tinggi') : 'Tinggi';
                        matches.push({
                            name: h.name,
                            level: level
                        });
                        break; // Ditemukan ancaman pada layer ini, lanjut ke jenis layer hazard lainnya
                    }
                }
            }
        }

        return matches;
    }

    // // NEW FEATURE: Builder Konten Popup Lokasi User (Khusus Analisis Risiko Multi-Hazard)
    function buildUserRiskPopupHtml(region, hazards) {
        const count = hazards.length;

        // Kondisi 0 Hazard: 🟢 AMAN
        if (count === 0) {
            // <div style="margin-bottom:8px;">
            //     <div style="font-weight:700;font-size:12px;color:#0f172a;">${region.desa}</div>
            //     <div style="font-size:11px;color:#64748b;">${region.kecamatan}</div>
            // </div>
            return `
                <div style="padding:12px 14px;font-family:system-ui,-apple-system,sans-serif;color:#1e293b;min-width:220px;">
                    <div style="font-weight:800;font-size:12px;color:#0f172a;display:flex;align-items:center;gap:6px;border-bottom:1px solid #e2e8f0;padding-bottom:6px;margin-bottom:8px;">
                        <span>📍</span> POSISI SAYA
                    </div>
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:8px 10px;">
                        <div style="font-size:10px;font-weight:700;color:#15803d;margin-top:6px;display:flex;align-items:center;gap:4px;">
                            <span>Status:</span>
                            <span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:999px;font-weight:800;">🟢 AMAN</span>
                        </div>
                    </div>
                </div>
            `;
        }

        // Kategori Risiko berdasarkan Jumlah Hazard
        let catBadge = '🟡 WASPADA';
        let catColor = '#854d0e';
        let catBg = '#fef9c3';

        if (count === 2) {
            catBadge = '🟠 RISIKO TINGGI';
            catColor = '#c2410c';
            catBg = '#ffedd5';
        } else if (count >= 3) {
            catBadge = '🔴 RISIKO SANGAT TINGGI';
            catColor = '#b91c1c';
            catBg = '#fee2e2';
        }

        let hazardRows = '';
        hazards.forEach(hz => {
            hazardRows += `
                <div style="display:flex;align-items:center;justify-content:space-between;padding:3px 0;font-size:11px;border-bottom:1px dashed #f1f5f9;">
                    <span style="font-weight:700;color:#1e293b;">• ${hz.name}</span>
                    <span style="font-weight:600;font-size:10px;color:#dc2626;">Risiko ${hz.level}</span>
                </div>
            `;
        });

        return `
            <div style="padding:12px 14px;font-family:system-ui,-apple-system,sans-serif;color:#1e293b;min-width:240px;">
                <div style="font-weight:800;font-size:12px;color:#0f172a;display:flex;align-items:center;gap:6px;border-bottom:1px solid #e2e8f0;padding-bottom:6px;margin-bottom:8px;">
                    <span>📍</span> POSISI SAYA
                </div>
                <div style="margin-bottom:8px;">
                    <div style="font-weight:700;font-size:12px;color:#0f172a;">${region.desa}</div>
                    <div style="font-size:11px;color:#64748b;">${region.kecamatan}</div>
                </div>
                <div style="border-top:1px solid #f1f5f9;border-bottom:1px solid #f1f5f9;padding:6px 0;margin-bottom:8px;">
                    <div style="font-weight:800;font-size:10px;text-transform:uppercase;color:#dc2626;margin-bottom:4px;display:flex;align-items:center;gap:4px;">
                        <span>⚠</span> Ancaman Terdeteksi
                    </div>
                    ${hazardRows}
                </div>
                <div style="margin-bottom:8px;font-size:11px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <span style="color:#64748b;font-size:10px;font-weight:600;">Jumlah Ancaman:</span>
                        <span style="font-weight:800;color:#0f172a;">${count}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <span style="color:#64748b;font-size:10px;font-weight:600;">Kategori Risiko:</span>
                        <span style="font-weight:800;font-size:10px;padding:2px 8px;border-radius:999px;background:${catBg};color:${catColor};">${catBadge}</span>
                    </div>
                </div>
                <div style="background:#fff1f2;border:1px solid #fecdd3;border-radius:8px;padding:6px 8px;font-size:10px;color:#9f1239;">
                    <span style="font-weight:700;display:block;margin-bottom:1px;">Saran:</span>
                    Segera menuju titik kumpul terdekat.
                </div>
            </div>
        `;
    }

    // // NEW FEATURE: Builder Konten Popup Titik Kumpul Tujuan (Menempel pada Marker Tujuan)
    // Tidak menampilkan langkah navigasi, estimasi waktu, atau informasi lokasi user (karena sudah di Panel Navigasi)
    function buildDestinationMarkerPopupHtml(facilityName) {
        return `
            <div style="padding:10px 12px;font-family:system-ui,-apple-system,sans-serif;color:#1e293b;min-width:200px;">
                <div style="font-weight:800;font-size:12px;color:#059669;display:flex;align-items:center;gap:5px;margin-bottom:6px;border-bottom:1px solid #e2e8f0;padding-bottom:5px;">
                    <span>🏫</span> TITIK KUMPUL TUJUAN
                </div>
                <div style="font-size:10px;color:#64748b;text-transform:uppercase;font-weight:700;margin-bottom:1px;">Nama Lokasi:</div>
                <div style="font-weight:800;font-size:12px;color:#0f172a;margin-bottom:6px;">${facilityName || 'Titik Kumpul'}</div>
            </div>
        `;
    }

    // // UPDATED FEATURE: Fungsi Reset Rute Evakuasi
    // Menghapus: routing, polyline, panel navigasi aktif, audio, blinking marker.
    // PENTING: JANGAN menghapus marker user!
    function resetEvacuationRoute() {
        // 1. Hapus Garis Polyline Rute Khusus User & LRM
        userRouteLayerGroup.clearLayers();
        if (userRoutingControl) {
            try {
                map.removeControl(userRoutingControl);
            } catch (e) {
                console.log('LRM control remove safe catch');
            }
            userRoutingControl = null;
        }
        currentRoutingData = null;
        currentRoutePolylines = {
            public: { casing: null, main: null, hit: null },
            bpbd: { casing: null, main: null, hit: null }
        };
        activeRouteKey = 'bpbd';

        // 2. Matikan Efek Kedip Hijau pada Marker Fasilitas & Tutup Popup Tujuan
        if (blinkingTimer) {
            clearTimeout(blinkingTimer);
            blinkingTimer = null;
        }

        if (nearestMarker) {
            const el = nearestMarker.getElement();
            if (el) el.classList.remove('is-blinking');
            nearestMarker.closePopup();
            nearestMarker = null;
        }

        layers.evacuationFacilities.eachLayer(marker => {
            const el = marker.getElement();
            if (el) el.classList.remove('is-blinking');
        });

        // 3. Hentikan dan Reset Audio Alarm Sirine
        const audio = document.getElementById('alert-sound');
        if (audio) {
            audio.pause();
            audio.currentTime = 0;
        }
        hasPlayedAlert = false;

        // 4. Sembunyikan Panel Navigasi Aktif
        const navPanel = document.getElementById('evacuationNavigationPanel');
        if (navPanel) {
            navPanel.classList.add('hidden');
        }

        // 5. Nonaktifkan Tombol Reset Rute
        const resetRouteBtn = document.getElementById('btnResetRoute');
        if (resetRouteBtn) {
            resetRouteBtn.disabled = true;
            resetRouteBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }

        currentNavigationBounds = null;
        invalidateMapSize();
    }

    // // UPDATED FEATURE: Fungsi Toggle Posisi Saya (Toggle ON/OFF)
    // Klik pertama: aktifkan GPS, tampilkan marker user, lakukan analisis multi-hazard
    // Klik kedua: hapus marker user, hapus popup analisis risiko (tidak menghapus rute, titik kumpul, atau layer lain)
    async function toggleUserPosition() {
        const btnToggle = document.getElementById('btnToggleUserPosition');
        const textElem = document.getElementById('btnToggleUserPositionText');
        const btnFind = document.getElementById('btnFindNearestFacility');

        // KASUS 1: Klik Kedua -> Toggle OFF (Matikan posisi user)
        if (isUserPositionActive) {
            if (userMarker) {
                map.removeLayer(userMarker);
                userMarker = null;
            }
            isUserPositionActive = false;
            currentUserLat = null;
            currentUserLng = null;
            lastUserRegion = null;
            lastUserHazards = null;

            if (textElem) textElem.textContent = 'Posisi Saya';
            if (btnToggle) {
                btnToggle.className = 'w-full bg-blue-50 hover:bg-blue-100 active:scale-[0.98] text-blue-700 font-bold py-2.5 px-3 rounded-xl border border-blue-200 shadow-xs transition-all text-xs flex items-center justify-center gap-2 cursor-pointer';
            }

            // Nonaktifkan tombol Cari Titik Kumpul
            if (btnFind) {
                btnFind.disabled = true;
                btnFind.classList.add('opacity-50', 'cursor-not-allowed');
            }
            return;
        }

        // KASUS 2: Klik Pertama -> Toggle ON (Aktifkan GPS & Analisis Multi-Hazard)
        if (!navigator.geolocation) {
            alert('Fitur Geolocation/GPS tidak didukung oleh browser Anda.');
            return;
        }

        if (textElem) textElem.textContent = 'Mendeteksi GPS...';
        if (btnToggle) btnToggle.disabled = true;

        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                currentUserLat = lat;
                currentUserLng = lng;
                isUserPositionActive = true;

                // Isi koordinat form jika modal manual dibuka
                const inputLat = document.getElementById('lat');
                const inputLng = document.getElementById('lng');
                if (inputLat) inputLat.value = lat.toFixed(6);
                if (inputLng) inputLng.value = lng.toFixed(6);

                // Buat Marker Lokasi User (Radar Pulsing)
                if (userMarker) map.removeLayer(userMarker);
                const userIcon = L.divIcon({
                    className: 'user-gps-marker',
                    html: '<div class="pulse-ring"></div><div class="dot-center"></div>',
                    iconSize: [32, 32],
                    iconAnchor: [16, 16]
                });

                userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(map);

                // Pasang popup sementara selagi proses analisis spasial berlangsung
                userMarker.bindPopup(`
                    <div style="padding:10px 14px;font-size:11px;font-weight:600;color:#334155;display:flex;align-items:center;gap:6px;">
                        <svg class="animate-spin h-3.5 w-3.5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Menganalisis ancaman multi-hazard...</span>
                    </div>
                `, { className: 'user-risk-popup' }).openPopup();

                map.panTo([lat, lng]);

                // Jalankan Analisis Multi-Hazard & Identifikasi Wilayah
                try {
                    const [region, matchedHazards] = await Promise.all([
                        identifyUserRegion(lat, lng),
                        collectAllHazardMatches(lat, lng)
                    ]);

                    lastUserRegion = region;
                    lastUserHazards = matchedHazards;

                    // Update Popup Lokasi User dengan Hasil Analisis Multi Hazard
                    const popupHtml = buildUserRiskPopupHtml(region, matchedHazards);
                    userMarker.bindPopup(popupHtml, { className: 'user-risk-popup', maxWidth: 280 }).openPopup();
                } catch (e) {
                    console.error('Error saat analisis multi hazard:', e);
                    userMarker.bindPopup(`
                        <div style="padding:10px 12px;font-size:11px;color:#0f172a;">
                            <b>📍 POSISI SAYA</b><br>
                            Koordinat: ${lat.toFixed(5)}, ${lng.toFixed(5)}
                        </div>
                    `, { className: 'user-risk-popup' }).openPopup();
                }

                // Update UI Tombol Toggle ON
                if (btnToggle) {
                    btnToggle.disabled = false;
                    btnToggle.className = 'w-full bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold py-2.5 px-3 rounded-xl shadow-xs transition-all text-xs flex items-center justify-center gap-2 cursor-pointer';
                }
                if (textElem) textElem.textContent = 'Posisi Aktif (Klik utk Matikan)';

                // Aktifkan Tombol Cari Titik Kumpul
                if (btnFind) {
                    btnFind.disabled = false;
                    btnFind.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            },
            (error) => {
                if (btnToggle) {
                    btnToggle.disabled = false;
                    btnToggle.className = 'w-full bg-emerald-50 hover:bg-emerald-100 active:scale-[0.98] text-emerald-700 font-bold py-2.5 px-3 rounded-xl border border-emerald-200 shadow-xs transition-all text-xs flex items-center justify-center gap-2 cursor-pointer';
                }
                if (textElem) textElem.textContent = 'Posisi Saya';

                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        alert("Izin akses GPS ditolak. Silakan izinkan akses lokasi pada browser Anda.");
                        break;
                    case error.POSITION_UNAVAILABLE:
                        alert("Informasi lokasi GPS tidak tersedia.");
                        break;
                    case error.TIMEOUT:
                        alert("Waktu permintaan lokasi GPS habis (Timeout).");
                        break;
                    default:
                        alert("Gagal mendeteksi lokasi GPS.");
                        break;
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    // Event Listener Tombol Panel Kontrol Peta
    document.getElementById('btnToggleUserPosition')?.addEventListener('click', toggleUserPosition);
    document.getElementById('btnResetRoute')?.addEventListener('click', resetEvacuationRoute);

    // Event Listener Modal Cadangan
    document.getElementById('btnMapLocationTool')?.addEventListener('click', openLocationModal);
    document.getElementById('btnOpenLocationModal')?.addEventListener('click', openLocationModal);
    document.getElementById('closeLocationModal')?.addEventListener('click', closeLocationModal);
    document.getElementById('locationModal')?.addEventListener('click', function (e) {
        if (e.target.id === 'locationModal') closeLocationModal();
    });

    // Event Listener Modal Informasi Status Kawasan (Aman / Rawan Bencana)
    document.getElementById('btnOpenUserHazardModal')?.addEventListener('click', openUserHazardModal);
    document.getElementById('closeUserHazardModal')?.addEventListener('click', closeUserHazardModal);
    document.getElementById('userHazardModal')?.addEventListener('click', function (e) {
        if (e.target.id === 'userHazardModal') closeUserHazardModal();
    });

    // Event Listener Tombol Cek Status Kawasan (Uji Coba Input Manual)
    document.getElementById('btnCheckHazardStatus')?.addEventListener('click', function() {
        const lat = parseFloat(document.getElementById('hazardLat').value);
        const lng = parseFloat(document.getElementById('hazardLng').value);

        if (isNaN(lat) || isNaN(lng)) {
            alert('Silakan masukkan nilai Latitude dan Longitude yang valid untuk uji coba.');
            return;
        }

        executeCheckHazardStatus(lat, lng);
    });

    // Helper render icon SVG instruksi navigasi
    function getInstructionIconSvg(type) {
        switch (type) {
            case 'left':
                return `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>`;
            case 'right':
                return `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>`;
            case 'arrive':
                return `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>`;
            case 'enter_official':
                return `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>`;
            case 'uturn':
                return `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h10a5 5 0 015 5v2m-15-7l4-4m-4 4l4 4"/></svg>`;
            default:
                return `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>`;
        }
    }

    // // UPDATED FEATURE: Helper update tampilan Panel Navigasi Modern (Tailwind)
    // Sesuai prinsip "One Information = One Place", nama titik kumpul tidak ditampilkan di panel kiri karena sudah ada pada popup marker tujuan
    function renderNavigationPanel(data, userLat, userLng, activeKey = null) {
        const panel = document.getElementById('evacuationNavigationPanel');
        if (!panel) return;

        if (activeKey) {
            activeRouteKey = activeKey;
        } else {
            activeKey = activeRouteKey || data.selected_route_key || 'bpbd';
        }

        const facility = data.facility || {};
        const options = data.route_options || {};
        const optPublic = options.public;
        const optBpbd = options.bpbd;
        const currentOpt = (options && options[activeKey]) ? options[activeKey] : (optBpbd || optPublic || data.summary || {});

        const isPublic = activeKey === 'public';
        const isBpbd = activeKey === 'bpbd';

        // 1. // UPDATED FEATURE: Update Metrik Ringkasan (Jarak & Estimasi Durasi)
        const distElem = document.getElementById('navDistanceValue');
        const durElem = document.getElementById('navDurationValue');
        if (distElem) distElem.textContent = currentOpt.distance_formatted || '–';
        if (durElem) durElem.textContent = currentOpt.duration_formatted || '–';

        // 2. Subtitle Status di Header Panel
        const subtitleElem = document.getElementById('navSubtitleStatus');
        if (subtitleElem) {
            subtitleElem.textContent = isBpbd 
                ? 'Jalur Resmi BPBD (Terverifikasi)' 
                : 'Jalur Umum (Google Maps Style)';
        }

        // 3. Tampilkan kartu opsi rute dan metriknya jika opsi rute tersedia
        if (optPublic) {
            const cardDist = document.getElementById('cardPublicDist');
            const cardDur = document.getElementById('cardPublicDur');
            if (cardDist) cardDist.textContent = optPublic.distance_formatted || '–';
            if (cardDur) cardDur.textContent = optPublic.duration_formatted || '–';
        }
        if (optBpbd) {
            const cardDist = document.getElementById('cardBpbdDist');
            const cardDur = document.getElementById('cardBpbdDur');
            if (cardDist) cardDist.textContent = optBpbd.distance_formatted || '–';
            if (cardDur) cardDur.textContent = optBpbd.duration_formatted || '–';
        }

        // Highlight kartu yang aktif vs nonaktif
        const btnPublic = document.getElementById('btnSelectRoutePublic');
        const badgePublic = document.getElementById('badgeActivePublic');

        // 4. Update Badge Jumlah Langkah Instruksi
        const instructions = currentOpt.instructions || data.instructions || [];
        const stepsBadge = document.getElementById('navStepsCount');
        if (stepsBadge) {
            stepsBadge.textContent = `${instructions.length} Langkah`;
        }

        // 5. Render Daftar Instruksi Arah ke dalam Panel Body
        const instructionsContainer = document.getElementById('navInstructionsList');
        if (instructionsContainer) {
            if (instructions.length === 0) {
                instructionsContainer.innerHTML = `
                    <div class="p-3 text-center text-slate-500 text-xs bg-slate-50 dark:bg-slate-800/50 rounded-xl">
                        Tidak ada panduan arah terinci. Silakan ikuti garis rute evakuasi pada peta.
                    </div>
                `;
            } else {
                let html = '';
                instructions.forEach(ins => {
                    const isArrival = ins.type === 'arrive';
                    const isEnterOfficial = ins.type === 'enter_official';

                    let cardClass = 'bg-slate-50 dark:bg-slate-800/60 border-slate-200/70 dark:border-slate-700/60 text-slate-800 dark:text-slate-200';
                    let iconBgClass = 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200';

                    if (isArrival) {
                        cardClass = 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-950 dark:text-emerald-100 font-semibold';
                        iconBgClass = 'bg-emerald-500 text-white';
                    } else if (isEnterOfficial) {
                        cardClass = 'bg-purple-50 dark:bg-purple-950/40 border-purple-300 dark:border-purple-800 text-purple-950 dark:text-purple-100 font-semibold';
                        iconBgClass = 'bg-purple-600 text-white';
                    } else if (isPublic) {
                        cardClass = 'bg-blue-50/40 dark:bg-blue-950/30 border-blue-200/70 dark:border-blue-800/50 text-slate-800 dark:text-slate-200';
                        iconBgClass = 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200';
                    }

                    html += `
                        <div class="flex items-start gap-2.5 p-2.5 rounded-xl border ${cardClass} transition-all hover:shadow-xs">
                            <div class="w-6 h-6 rounded-lg ${iconBgClass} flex items-center justify-center shrink-0 mt-0.5 text-xs shadow-xs">
                                ${getInstructionIconSvg(ins.type)}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs leading-snug font-medium">
                                    <span class="font-bold opacity-80">${ins.step}.</span> ${ins.text}
                                </p>
                                ${ins.distance_meters > 0 ? `
                                    <div class="text-[10px] font-semibold opacity-70 mt-1 flex items-center gap-1">
                                        <span>📏</span>
                                        <span>${ins.distance_formatted}</span>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                });
                instructionsContainer.innerHTML = html;
            }
        }

        // Tampilkan panel
        panel.classList.remove('hidden');
    }

    // Helper update visual polyline rute pada peta
    function updateRoutePolylinesVisual(activeKey) {
        if (!currentRoutingData || !currentRoutePolylines) return;
        const opts = currentRoutingData.route_options || {};
        const bpbd = currentRoutePolylines.bpbd;
        const pub = currentRoutePolylines.public;
        const optBpbd = opts.bpbd;
        const optPublic = opts.public;

        if (activeKey === 'bpbd') {
            // BPBD Aktif: Tegas, solid, satu warna ungu
            if (bpbd && bpbd.main && bpbd.casing) {
                bpbd.casing.setStyle({
                    color: '#581c87',
                    weight: 9,
                    opacity: 0.95
                });
                bpbd.main.setStyle({
                    color: 'rgb(153, 0, 255)',
                    weight: 6,
                    opacity: 1.0,
                    dashArray: null
                });
                bpbd.casing.bringToFront();
                bpbd.main.bringToFront();
                if (bpbd.hit) bpbd.hit.bringToFront();

                const textDist = optBpbd ? optBpbd.distance_formatted : '';
                const textDur = optBpbd ? optBpbd.duration_formatted : '';
                bpbd.main.unbindTooltip();
                bpbd.main.bindTooltip(`<b>🛡️ Jalur Resmi BPBD (Rute Aktif)</b><br>Jarak: ${textDist} • Waktu: ${textDur}`, {
                    sticky: true,
                    direction: 'top',
                    className: 'route-tooltip'
                });
            }

            // Public Alternatif: Muted Google Blue
            if (pub && pub.main && pub.casing) {
                pub.casing.setStyle({
                    color: '#1e3a8a',
                    weight: 5,
                    opacity: 0.35
                });
                pub.main.setStyle({
                    color: '#60a5fa',
                    weight: 4,
                    opacity: 0.7,
                    dashArray: '6, 6'
                });
                const textDist = optPublic ? optPublic.distance_formatted : '';
                const textDur = optPublic ? optPublic.duration_formatted : '';
                pub.main.unbindTooltip();
                pub.main.bindTooltip(`<b>🛣️ Jalur Umum (Google Maps)</b><br>Jarak: ${textDist} • Waktu: ${textDur}<br><span style="color:#2563eb;font-weight:700;">👉 Klik untuk pilih Jalur Umum</span>`, {
                    sticky: true,
                    direction: 'top',
                    className: 'route-tooltip'
                });
            }
        } else {
            // Public Aktif: Gaya Google Maps Biru Vibrant
            if (pub && pub.main && pub.casing) {
                pub.casing.setStyle({
                    color: '#174ea6',
                    weight: 9,
                    opacity: 0.95
                });
                pub.main.setStyle({
                    color: '#4285F4',
                    weight: 6,
                    opacity: 1.0,
                    dashArray: null
                });
                pub.casing.bringToFront();
                pub.main.bringToFront();
                if (pub.hit) pub.hit.bringToFront();

                const textDist = optPublic ? optPublic.distance_formatted : '';
                const textDur = optPublic ? optPublic.duration_formatted : '';
                pub.main.unbindTooltip();
                pub.main.bindTooltip(`<b>🛣️ Jalur Umum (Rute Aktif - Google Maps)</b><br>Jarak: ${textDist} • Waktu: ${textDur}`, {
                    sticky: true,
                    direction: 'top',
                    className: 'route-tooltip'
                });
            }

            // BPBD Alternatif: Muted Purple
            if (bpbd && bpbd.main && bpbd.casing) {
                bpbd.casing.setStyle({
                    color: '#4c1d95',
                    weight: 5,
                    opacity: 0.35
                });
                bpbd.main.setStyle({
                    color: '#c084fc',
                    weight: 4,
                    opacity: 0.7,
                    dashArray: '6, 6'
                });
                const textDist = optBpbd ? optBpbd.distance_formatted : '';
                const textDur = optBpbd ? optBpbd.duration_formatted : '';
                bpbd.main.unbindTooltip();
                bpbd.main.bindTooltip(`<b>🛡️ Jalur Resmi BPBD</b><br>Jarak: ${textDist} • Waktu: ${textDur}<br><span style="color:#9333ea;font-weight:700;">👉 Klik untuk pilih Jalur BPBD</span>`, {
                    sticky: true,
                    direction: 'top',
                    className: 'route-tooltip'
                });
            }
        }
    }

    // // UPDATED FEATURE: Fungsi global pemilihan rute evakuasi (Jalur Umum vs Jalur Resmi BPBD)
    window.selectEvacuationRoute = function(selectedKey) {
        if (!currentRoutingData) return;
        activeRouteKey = selectedKey;

        // 1. Update style visual garis rute pada peta
        updateRoutePolylinesVisual(selectedKey);

        // 2. Update panel navigasi langkah demi langkah di panel kiri
        renderNavigationPanel(currentRoutingData, currentRoutingData.summary.user_location.lat, currentRoutingData.summary.user_location.lng, selectedKey);

        // // REMOVED DUPLICATE UI: Tidak lagi memperbarui popup tengah atau status teks panel kanan
        // karena informasi rute sudah dipusatkan secara eksklusif di Panel Navigasi (kiri)
    };

    // // NEW FEATURE: Eksekusi Pencarian Titik Kumpul & Rute Hybrid Evakuasi
    function executeFindNearestEvacuation(lat, lng) {
        // 1. Bersihkan rute evakuasi sebelumnya (jika ada) tanpa menghapus marker user
        resetEvacuationRoute();
        closeLocationModal();

        // Indikator loading pada tombol cari titik kumpul
        const btnFind = document.getElementById('btnFindNearestFacility');
        let origBtnFindHtml = '';
        if (btnFind) {
            origBtnFindHtml = btnFind.innerHTML;
            btnFind.disabled = true;
            btnFind.innerHTML = `
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>Mencari Titik Kumpul...</span>
            `;
        }

        // 2. Panggil API Hybrid Evacuation Routing
        fetch(`/api/nearest-evacuation-with-route?lat=${lat}&lng=${lng}`)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP Error ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (btnFind) {
                    btnFind.disabled = false;
                    btnFind.innerHTML = origBtnFindHtml;
                }

                if (!data.success || !data.facility) {
                    alert(data.message || "Gagal menemukan titik kumpul terdekat.");
                    return;
                }

                const facility = data.facility;
                let fLat, fLng;

                if (data.summary && data.summary.facility_location) {
                    fLat = data.summary.facility_location.lat;
                    fLng = data.summary.facility_location.lng;
                } else if (Array.isArray(facility.point_coordinates)) {
                    fLng = parseFloat(facility.point_coordinates[0]);
                    fLat = parseFloat(facility.point_coordinates[1]);
                } else if (typeof facility.point_coordinates === 'string') {
                    const coords = JSON.parse(facility.point_coordinates);
                    fLng = parseFloat(coords[0]);
                    fLat = parseFloat(coords[1]);
                }

                const facilityLatLng = L.latLng(fLat, fLng);

                // --- A. // UPDATED FEATURE: BLINKING MARKER HIJAU (1 MENIT / 60s) & AUDIO ALARM ---
                nearestMarker = null;
                let minTargetDist = Infinity;

                layers.evacuationFacilities.eachLayer(marker => {
                    const dist = facilityLatLng.distanceTo(marker.getLatLng());
                    if (dist < minTargetDist) {
                        minTargetDist = dist;
                        nearestMarker = marker;
                    }
                });

                if (nearestMarker) {
                    const el = nearestMarker.getElement();
                    if (el) {
                        // Class is-blinking beranimasi pulsing warna hijau cerah
                        el.classList.add('is-blinking');

                        // Putar audio alarm
                        const audio = document.getElementById('alert-sound');
                        if (audio) {
                            audio.currentTime = 0;
                            audio.play().catch(err => console.log('Audio autoplay policy catch:', err));
                            hasPlayedAlert = true;
                        }

                        // Durasi kedip otomatis tepat 1 menit (60 detik)
                        if (blinkingTimer) clearTimeout(blinkingTimer);
                        blinkingTimer = setTimeout(() => {
                            if (el) el.classList.remove('is-blinking');
                        }, 60000);
                    }

                    // --- // UPDATED FEATURE: POPUP TITIK KUMPUL TUJUAN MENEMPEL PADA MARKER TUJUAN ---
                    // Sesuai arsitektur "One Information = One Place", popup ini hanya menjelaskan tujuan
                    const destPopupHtml = buildDestinationMarkerPopupHtml(facility.name);
                    nearestMarker.bindPopup(destPopupHtml, {
                        className: 'destination-marker-popup',
                        autoPan: false
                    }).openPopup();
                }

                // --- B. GAMBAR DUAL RUTE HYBRID (SATU WARNA UNGU BPBD VS BIRU GOOGLE MAPS) ---
                userRouteLayerGroup.clearLayers();
                currentRoutingData = data;
                activeRouteKey = data.selected_route_key || 'bpbd';

                const routeOpts = data.route_options || {};
                const optBpbd = routeOpts.bpbd || {
                    coordinates: data.full_route_latlngs || [[lat, lng], [fLat, fLng]],
                    distance_formatted: data.summary?.distance_formatted || '',
                    duration_formatted: data.summary?.duration_formatted || '',
                    instructions: data.instructions || []
                };
                const optPublic = routeOpts.public || {
                    coordinates: [[lat, lng], [fLat, fLng]],
                    distance_formatted: data.summary?.distance_formatted || '',
                    duration_formatted: data.summary?.duration_formatted || '',
                    instructions: []
                };

                let allLatLngs = [];
                if (optBpbd.coordinates && optBpbd.coordinates.length > 0) {
                    allLatLngs = allLatLngs.concat(optBpbd.coordinates);
                }
                if (optPublic.coordinates && optPublic.coordinates.length > 0) {
                    allLatLngs = allLatLngs.concat(optPublic.coordinates);
                }
                if (allLatLngs.length === 0) {
                    allLatLngs = [[lat, lng], [fLat, fLng]];
                }

                currentRoutePolylines = {
                    public: { casing: null, main: null, hit: null },
                    bpbd: { casing: null, main: null, hit: null }
                };

                // 1. Gambar Jalur Resmi BPBD (Warna Ungu Terunifikasi)
                if (optBpbd.coordinates && optBpbd.coordinates.length > 1) {
                    const bpbdCasing = L.polyline(optBpbd.coordinates, {
                        color: '#581c87',
                        weight: 9,
                        opacity: 0.95,
                        lineCap: 'round',
                        lineJoin: 'round'
                    }).addTo(userRouteLayerGroup);

                    const bpbdMain = L.polyline(optBpbd.coordinates, {
                        color: 'rgb(153, 0, 255)',
                        weight: 6,
                        opacity: 1.0,
                        lineCap: 'round',
                        lineJoin: 'round'
                    }).addTo(userRouteLayerGroup);

                    const bpbdHit = L.polyline(optBpbd.coordinates, {
                        weight: 22,
                        opacity: 0,
                        interactive: true,
                        className: 'clickable-route-hit',
                        bubblingMouseEvents: false
                    }).addTo(userRouteLayerGroup);

                    currentRoutePolylines.bpbd = {
                        casing: bpbdCasing,
                        main: bpbdMain,
                        hit: bpbdHit
                    };

                    const handleBpbdClick = function(e) {
                        if (e && e.originalEvent) L.DomEvent.stopPropagation(e);
                        window.selectEvacuationRoute('bpbd');
                    };

                    bpbdHit.on('click', handleBpbdClick);
                    bpbdMain.on('click', handleBpbdClick);
                    bpbdCasing.on('click', handleBpbdClick);

                    bpbdHit.on('mouseover', function() {
                        if (activeRouteKey !== 'bpbd') {
                            bpbdMain.setStyle({ opacity: 0.9, weight: 6 });
                        }
                    });
                    bpbdHit.on('mouseout', function() {
                        if (activeRouteKey !== 'bpbd') {
                            updateRoutePolylinesVisual(activeRouteKey);
                        }
                    });

                    // Titik Masuk Jalur Evakuasi Resmi
                    const entryPoint = optBpbd.entry_point || (data.summary && data.summary.entry_point);
                    if (entryPoint && entryPoint.lat && entryPoint.lng) {
                        const junctionIcon = L.divIcon({
                            className: 'junction-marker',
                            html: '<div title="Titik Masuk Jalur Evakuasi BPBD (Klik untuk pilih)">🛡️</div>',
                            iconSize: [26, 26],
                            iconAnchor: [13, 13]
                        });
                        const jMarker = L.marker([entryPoint.lat, entryPoint.lng], { icon: junctionIcon })
                            .addTo(userRouteLayerGroup)
                            .bindPopup(`<b>Titik Masuk Jalur Evakuasi Resmi BPBD</b><br>${optBpbd.route_name || 'Jalur Evakuasi BPBD'}<br><button type="button" onclick="window.selectEvacuationRoute('bpbd')" style="margin-top:6px;padding:4px 8px;background:#7e22ce;color:white;border:none;border-radius:6px;font-size:10px;font-weight:bold;cursor:pointer;">Pilih Jalur Resmi BPBD</button>`);
                        jMarker.on('click', handleBpbdClick);
                    }
                }

                // 2. Gambar Jalur Umum (Gaya Google Maps - Biru)
                if (optPublic.coordinates && optPublic.coordinates.length > 1) {
                    const publicCasing = L.polyline(optPublic.coordinates, {
                        color: '#174ea6',
                        weight: 8,
                        opacity: 0.8,
                        lineCap: 'round',
                        lineJoin: 'round'
                    }).addTo(userRouteLayerGroup);

                    const publicMain = L.polyline(optPublic.coordinates, {
                        color: '#4285F4',
                        weight: 5,
                        opacity: 1.0,
                        lineCap: 'round',
                        lineJoin: 'round'
                    }).addTo(userRouteLayerGroup);

                    const publicHit = L.polyline(optPublic.coordinates, {
                        weight: 22,
                        opacity: 0,
                        interactive: true,
                        className: 'clickable-route-hit',
                        bubblingMouseEvents: false
                    }).addTo(userRouteLayerGroup);

                    currentRoutePolylines.public = {
                        casing: publicCasing,
                        main: publicMain,
                        hit: publicHit
                    };

                    const handlePublicClick = function(e) {
                        if (e && e.originalEvent) L.DomEvent.stopPropagation(e);
                        window.selectEvacuationRoute('public');
                    };

                    publicHit.on('click', handlePublicClick);
                    publicMain.on('click', handlePublicClick);
                    publicCasing.on('click', handlePublicClick);

                    publicHit.on('mouseover', function() {
                        if (activeRouteKey !== 'public') {
                            publicMain.setStyle({ opacity: 0.9, weight: 6 });
                        }
                    });
                    publicHit.on('mouseout', function() {
                        if (activeRouteKey !== 'public') {
                            updateRoutePolylinesVisual(activeRouteKey);
                        }
                    });
                }

                // Terapkan visual style rute aktif & alternatif
                updateRoutePolylinesVisual(activeRouteKey);

                // C. Zoom & Fit Bounds Otomatis mencakup seluruh rute
                currentNavigationBounds = L.latLngBounds(allLatLngs);
                map.fitBounds(currentNavigationBounds, { padding: [60, 60], maxZoom: 17 });

                // // REMOVED DUPLICATE UI: Menghapus popup tengah pada lokasi user saat rute aktif
                // Informasi arah dan metrik evakuasi sudah tersaji di panel navigasi (kiri)
                // dan popup tujuan menempel pada marker tujuan (nearestMarker).

                // D. Buka Panel Navigasi Modern (Tailwind) dengan Panduan Arah
                renderNavigationPanel(data, lat, lng, activeRouteKey);

                // E. Aktifkan Tombol Reset Rute
                const btnReset = document.getElementById('btnResetRoute');
                if (btnReset) {
                    btnReset.disabled = false;
                    btnReset.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            })
            .catch(err => {
                if (btnFind) {
                    btnFind.disabled = false;
                    btnFind.innerHTML = origBtnFindHtml;
                }
                console.error('Error fetching hybrid evacuation route:', err);
                alert("Gagal memuat rute navigasi evakuasi. Silakan periksa koneksi internet.");
            });
    }

    // Tombol Cari Titik Kumpul (Panel Kontrol Kanan)
    document.getElementById('btnFindNearestFacility')?.addEventListener('click', function() {
        if (!currentUserLat || !currentUserLng) {
            alert('Silakan aktifkan Posisi Saya terlebih dahulu.');
            return;
        }
        executeFindNearestEvacuation(currentUserLat, currentUserLng);
    });

    // Proses "Set Lokasi" dari Modal Input Koordinat Manual (Jika Digunakan)
    document.getElementById('setLocation')?.addEventListener('click', function() {
        const lat = parseFloat(document.getElementById('lat').value);
        const lng = parseFloat(document.getElementById('lng').value);

        if (isNaN(lat) || isNaN(lng)) {
            alert('Silakan masukkan nilai Latitude dan Longitude yang valid.');
            return;
        }

        currentUserLat = lat;
        currentUserLng = lng;
        isUserPositionActive = true;

        if (userMarker) map.removeLayer(userMarker);
        const userIcon = L.divIcon({
            className: 'user-gps-marker',
            html: '<div class="pulse-ring"></div><div class="dot-center"></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });
        userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(map);

        // Update tombol panel
        const btnToggle = document.getElementById('btnToggleUserPosition');
        const textElem = document.getElementById('btnToggleUserPositionText');
        const btnFind = document.getElementById('btnFindNearestFacility');
        if (btnToggle) {
            btnToggle.className = 'w-full bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold py-2.5 px-3 rounded-xl shadow-xs transition-all text-xs flex items-center justify-center gap-2 cursor-pointer';
        }
        if (textElem) textElem.textContent = 'Posisi Aktif (Klik utk Matikan)';
        if (btnFind) {
            btnFind.disabled = false;
            btnFind.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        // Jalankan Analisis Multi-Hazard & Identifikasi Wilayah untuk koordinat manual
        Promise.all([
            identifyUserRegion(lat, lng),
            collectAllHazardMatches(lat, lng)
        ]).then(([region, matchedHazards]) => {
            lastUserRegion = region;
            lastUserHazards = matchedHazards;
            if (userMarker) {
                const popupHtml = buildUserRiskPopupHtml(region, matchedHazards);
                userMarker.bindPopup(popupHtml, { className: 'user-risk-popup', maxWidth: 280 });
            }
        }).catch(err => {
            console.error('Error analisis multi hazard manual:', err);
        });

        // Jalankan pencarian rute
        executeFindNearestEvacuation(lat, lng);
    });

    // Event Listener Interaktif untuk Panel Navigasi
    document.getElementById('btnOpenInstructionPopup')?.addEventListener('click', function() {
        if (nearestMarker) {
            map.panTo(nearestMarker.getLatLng());
            nearestMarker.openPopup();
        }
    });

    document.getElementById('btnFocusNavigationRoute')?.addEventListener('click', function() {
        if (currentNavigationBounds) {
            map.fitBounds(currentNavigationBounds, { padding: [60, 60], maxZoom: 17 });
        }
    });

    document.getElementById('btnResetNavFromPanel')?.addEventListener('click', function() {
        resetEvacuationRoute();
    });

    document.getElementById('btnCloseNavPanel')?.addEventListener('click', function() {
        const panel = document.getElementById('evacuationNavigationPanel');
        if (panel) panel.classList.add('hidden');
    });

    document.getElementById('btnToggleNavCollapse')?.addEventListener('click', function() {
        const body = document.getElementById('navPanelBody');
        const icon = document.getElementById('iconNavCollapse');
        if (body) {
            body.classList.toggle('hidden');
            if (icon) {
                icon.classList.toggle('rotate-180');
            }
        }
    });
    // =====================================================================
    // =====================================================================
    

    // =====================================================================
    // HAZARD LAYERS (GeoJSON dari public/geojson/)
    // Perilaku: EXCLUSIVE — hanya satu layer aktif sekaligus.
    // =====================================================================
    const hazardLayers = {};
    let activeHazardType = null;

    function buildHazardLayerUI(layerConfigs) {
        const container = document.getElementById('hazard_layer_checkboxes');
        if (!container) return;
        container.innerHTML = '';

        const shadowMap = {
            'banjir':    'rgba(59,130,246,0.25)',
            'gempa':     'rgba(249,115,22,0.25)',
            'gelombang': 'rgba(6,182,212,0.25)',
            'longsor':   'rgba(245, 205, 236, 0.25)',
        };
        const bgMap = {
            'banjir':    '#eff6ff',
            'gempa':     '#fff7ed',
            'gelombang': '#ecfeff',
            'longsor':   '#fee7faff',
        };


        layerConfigs.forEach(cfg => {
            hazardLayers[cfg.disaster_type] = {
                config: cfg,
                leafletLayer: L.layerGroup(),
                loaded: false,
                visible: false,
            };

            const card = document.createElement('div');
            card.className = 'hazard-card inactive';
            card.id = `hazard_badge_${cfg.disaster_type}`;
            card.title = cfg.description || cfg.label;
            card.style.setProperty('--hc-color', cfg.border_color);
            card.style.setProperty('--hc-bg', bgMap[cfg.disaster_type] || '#f8fafc');
            card.style.setProperty('--hc-shadow', shadowMap[cfg.disaster_type] || 'rgba(0,0,0,0.1)');

            card.innerHTML = `
                <span class="hazard-card-check" aria-hidden="true">
                    <svg viewBox="0 0 10 10"><polyline points="1.5,5.5 4,8 8.5,2.5"/></svg>
                </span>
                <span class="hazard-card-emoji">${cfg.icon_emoji || '🗺️'}</span>
                <div class="hazard-card-label">${cfg.label}</div>
                <div class="hazard-card-status" id="hazard_status_${cfg.disaster_type}">Nonaktif</div>
            `;
            container.appendChild(card);

            card.addEventListener('click', () => {
                const isCurrentlyActive = (activeHazardType === cfg.disaster_type);
                Object.keys(hazardLayers).forEach(type => _setHazardVisible(type, false));
                if (!isCurrentlyActive) {
                    _setHazardVisible(cfg.disaster_type, true);
                    activeHazardType = cfg.disaster_type;
                } else {
                    activeHazardType = null;
                }
            });
        });
    }

    function _setHazardVisible(disasterType, show) {
        const state = hazardLayers[disasterType];
        if (!state) return;

        state.visible = show;
        const card   = document.getElementById(`hazard_badge_${disasterType}`);
        const status = document.getElementById(`hazard_status_${disasterType}`);

        if (card) {
            card.classList.toggle('active', show);
            card.classList.toggle('inactive', !show);
        }
        if (status) status.textContent = show ? 'Ditampilkan ✓' : 'Nonaktif';

        if (show) {
            if (!state.loaded) {
                state.loaded = true;
                loadHazardGeoJSON(disasterType);
            } else {
                state.leafletLayer.addTo(map);
            }
        } else {
            map.removeLayer(state.leafletLayer);
        }
    }

    function loadHazardGeoJSON(disasterType) {
        // Peta nama field hazard per jenis bencana
        const hazardFieldMap = {
            'longsor':   'Bhy_Lsr',
            'gempa':     'Bhy_GmpBm',
            'gelombang': 'Bhy_GEA',
            'banjir':    'Bhy_Bjr',
        };
        
        const state = hazardLayers[disasterType];
        if (!state) return;

        const cfg  = state.config;
        const card = document.getElementById(`hazard_badge_${disasterType}`);

        if (card) card.style.opacity = '0.65';

        fetch(cfg.geojson_path)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status} – ${cfg.geojson_path}`);
                return res.json();
            })
            .then(geojsonData => {
                const geoLayer = L.geoJSON(geojsonData, {
                    style: () => ({
                        color:       cfg.border_color,
                        weight:      cfg.border_weight,
                        fillColor:   cfg.fill_color,
                        fillOpacity: cfg.fill_opacity,
                        opacity:     0.85,
                    }),
                    onEachFeature: (feature, layer) => {
                        const p = feature.properties || {};
                        const namaArea = p.WADMPR || p.NAMOBJ || p.nama || '–';
                        const luas = p.Luas_Ha ? `${p.Luas_Ha.toFixed(2)} Ha` : '–';
                        // ambil field hazard sesuai jenis bencana
                        const hazardField = hazardFieldMap[disasterType];
                        const tingkat = p[hazardField] || 'Tidak diketahui';

                        const popupHtml = `
                            <div style="min-width:200px;font-size:13px;">
                                <div style="font-weight:700;font-size:14px;margin-bottom:8px;color:${cfg.border_color};">
                                    ${cfg.icon_emoji || ''} ${cfg.label}
                                </div>
                                <div><b>Tingkat Bahaya:</b> ${tingkat}</div>
                                <div><b>Luas:</b> ${luas}</div>
                            </div>`;
                        layer.bindPopup(popupHtml, { maxWidth: 300 });

                        layer.on({
                            mouseover(e) {
                                e.target.setStyle({ weight: 3, fillOpacity: Math.min(cfg.fill_opacity + 0.2, 0.75) });
                            },
                            mouseout(e) {
                                geoLayer.resetStyle(e.target);
                            },
                        });
                    },
                });

                geoLayer.addTo(state.leafletLayer);

                if (state.visible) {
                    state.leafletLayer.addTo(map);
                }
                if (card) card.style.opacity = '';
            })
            .catch(err => {
                console.error(`Gagal memuat GeoJSON (${disasterType}):`, err);
                state.loaded = false;
                if (card) {
                    card.style.opacity = '';
                    card.title = `⚠ Gagal memuat: ${cfg.geojson_path}`;
                }
            });
    }

    function initHazardLayers() {
        fetch('{{ route("map.hazard-layers") }}')
            .then(res => res.json())
            .then(data => {
                if (data.layers && data.layers.length > 0) {
                    buildHazardLayerUI(data.layers);
                } else {
                    const container = document.getElementById('hazard_layer_checkboxes');
                    if (container) container.innerHTML =
                        '<span style="grid-column:1/-1;font-size:12px;color:#ef4444;">Tidak ada layer tersedia.</span>';
                }
            })
            .catch(err => {
                console.error('Gagal memuat metadata hazard layers:', err);
                const container = document.getElementById('hazard_layer_checkboxes');
                if (container) container.innerHTML =
                    '<span style="grid-column:1/-1;font-size:12px;color:#ef4444;">Gagal memuat layer bencana.</span>';
            });
    }

    // =====================================================================
    // MAP DATA (data DB: disaster zones, routes, facilities)
    // =====================================================================

    function loadMapData() {
        const disasterTypeElem = document.getElementById('disaster_type');
        const riskLevelElem = document.getElementById('risk_level');

        const disasterType = disasterTypeElem ? disasterTypeElem.value : 'all';
        const riskLevel = riskLevelElem ? riskLevelElem.value : 'all';

        const url = new URL('{{ route("map.data") }}', window.location.origin);
        if (disasterType !== 'all') url.searchParams.append('disaster_type', disasterType);
        if (riskLevel !== 'all') url.searchParams.append('risk_level', riskLevel);

        Object.values(layers).forEach(layer => layer.clearLayers());

        fetch(url)
            .then(response => response.json())
            .then(data => {
                villageAidsData = data.village_aids || {};
                if (data.map_extent) {
                    const sw = data.map_extent.southwest;
                    const ne = data.map_extent.northeast;
                    const regencyBounds = L.latLngBounds(L.latLng(sw[0], sw[1]), L.latLng(ne[0], ne[1]));
                    if (!mapMaxBoundsApplied) {
                        map.setMaxBounds(regencyBounds.pad(0.18));
                        mapMaxBoundsApplied = true;
                    }
                    if (!mapInitialExtentDone) {
                        map.fitBounds(regencyBounds.pad(0.04));
                        mapInitialExtentDone = true;
                    }
                }

                data.disaster_zones.features.forEach(feature => {
                    if (!feature.geometry || !feature.geometry.coordinates) return;
                    const [lng, lat] = feature.geometry.coordinates;
                    let color = '#ef4444';
                    if (feature.properties.disaster_type === 'longsor') {
                        color = '#ef4444';
                    }

                    const marker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'disaster-zone-marker',
                            html: `<div style="background-color: ${color}; 
                                        width: 18px; height: 18px; 
                                        border-radius: 50%; 
                                        border: 2px solid white;"></div>`,
                            iconSize: [18, 18]
                        })
                    }).addTo(layers.disasterZones);

                    marker.bindPopup(`
                        <strong>${feature.properties.name}</strong><br>
                        Jenis: ${feature.properties.disaster_type}<br>
                        Risiko: ${feature.properties.risk_level}<br>
                        Populasi Terdampak: ${feature.properties.affected_population ?? '-'}
                    `);
                });

                data.evacuation_routes.features.forEach(feature => {
                    if (!feature.geometry || !feature.geometry.coordinates) return;
                    const coordinates = feature.geometry.coordinates.map(coord => [coord[1], coord[0]]);
                    const polyline = L.polyline(coordinates, {
                        color: 'rgb(153, 0, 255)',
                        weight: 10,
                        opacity: 0.8
                    }).addTo(layers.evacuationRoutes);

                    polyline.bindPopup(`
                        <strong>${feature.properties.name}</strong><br>
                        Jenis: ${feature.properties.route_type}<br>
                    `);
                });

                data.evacuation_facilities.features.forEach(feature => {
                    if (!feature.geometry || !feature.geometry.coordinates) return;
                    const [lng, lat] = feature.geometry.coordinates;
                    
                    const marker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'evacuation-facility-marker',
                            html: '<div style="background-color: #3b82f6; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white; transition: all 0.3s ease;"></div>',
                            iconSize: [20, 20]
                        })
                    }).addTo(layers.evacuationFacilities);

                    // 🔹 SIMPAN ID FASIUTAS KE DALAM MARKER (Penting untuk pencarian nanti)
                    marker.facilityId = feature.id || feature.properties.id || feature.properties.name;

                    marker.bindPopup(`
                        <strong>${feature.properties.name}</strong><br>
                        Alamat: ${feature.properties.address || '-'}
                    `);
                });

                // Helper Fungsi Penentu Warna Hangat Berdasarkan ID/Nama Kecamatan
                function getDistrictStyle(props) {
                    const name = String(props.district_name || props.nama || props.name || '').toLowerCase().trim();
                    const id = String(props.id || props.district_id || '');

                    // Border Tetap Hitam untuk SEMUA Kecamatan
                    const borderColor = '#000000ff'; 

                    // Pembeda Warna Isi (Fill Color) Warm & Kental
                    if (name.includes('bone raya') || id === '1') {
                        return { color: borderColor, fillColor: '#f59e0b' }; // Amber / Emas Pekat
                    }
                    if (name.includes('bulawa') || id === '2') {
                        return { color: borderColor, fillColor: '#f95e16ff' }; // Oranye Jingga
                    }
                    if (name.includes('bonepantai') || name.includes('bone pantai') || id === '4') {
                        return { color: borderColor, fillColor: '#f36e08ff' }; // Cokelat Terakota
                    }
                    if (name.includes('kabila bone') || id === '5') {
                        return { color: borderColor, fillColor: '#f43f5e' }; // Rose / Merah Muda Pekat
                    }
                    if (name.includes('bone') || id === '3') {
                        return { color: borderColor, fillColor: '#58ef44ff' }; // Merah
                    }

                    return { color: borderColor, fillColor: '#eab308' };
                }

                // Render Batas Administrasi Kecamatan dengan Warna Berbeda
                if (data.district_boundaries && data.district_boundaries.features.length > 0) {
                    data.district_boundaries.features.forEach(feature => {
                        const geom = feature.geometry;
                        if (!geom || !geom.coordinates) return;

                        const polys = [];
                        if (geom.type === 'MultiPolygon') {
                            geom.coordinates.forEach(poly => {
                                const coords = poly[0].map(coord => [coord[1], coord[0]]);
                                polys.push(coords);
                            });
                        } else if (geom.type === 'Polygon') {
                            const coords = geom.coordinates[0].map(coord => [coord[1], coord[0]]);
                            polys.push(coords);
                        }

                        const p = feature.properties || {};
                        const dStyle = getDistrictStyle(p); // Ambil gaya warna sesuai kecamatan

                        polys.forEach(coords => {
                            const polygon = L.polygon(coords, {
                                color: dStyle.color,
                                fillColor: dStyle.fillColor,
                                fillOpacity: 0.35, // Transparansi isi warna
                                weight: 2.5,
                                dashArray: '4 2'
                            }).addTo(layers.districtBoundaries);

                            polygon.bindPopup(`
                                <strong>${p.district_name || p.nama || 'Kecamatan'}</strong><br>
                                Penerima Bantuan: ${p.total_recipients ?? '-'} orang<br>
                                Terdistribusi: ${p.distributed_aid ?? '-'} bantuan<br>
                                `);
                                // Persentase: ${p.distribution_percentage ?? '-'}%

                            const vToggle = document.getElementById('toggle_village_boundaries');
                            if (vToggle && vToggle.checked) {
                                polygon._storedPopup = polygon.getPopup();
                                polygon.unbindPopup();
                            }
                        });
                    });
                }

                const districtToggle = document.getElementById('toggle_district_boundaries');
                // Default: boundaries tidak ditampilkan
                map.removeLayer(layers.districtBoundaries);
                if (districtToggle) {
                    districtToggle.addEventListener('change', function() {
                        if (this.checked) {
                            layers.districtBoundaries.addTo(map);
                        } else {
                            map.removeLayer(layers.districtBoundaries);
                        }
                    });
                }


                if (!data.map_extent && !mapInitialExtentDone &&
                    (data.disaster_zones.features.length > 0 ||
                    data.evacuation_routes.features.length > 0 ||
                    data.evacuation_facilities.features.length > 0)) {
                    const group = new L.featureGroup(Object.values(layers).flatMap(l => Array.from(l.getLayers())));
                    map.fitBounds(group.getBounds().pad(0.1));
                    mapInitialExtentDone = true;
                }

                if (mapUiVariant === 'landing-fs') {
                    requestAnimationFrame(invalidateMapSize);
                }
            })
            .catch(error => {
                console.error('Error loading map data:', error);
            });
    }

    // Inisialisasi Event Listener setelah DOM Siap
    document.addEventListener('DOMContentLoaded', () => {
        loadMapData();
        initHazardLayers();

        const disasterTypeElem = document.getElementById('disaster_type');
        if (disasterTypeElem) disasterTypeElem.addEventListener('change', loadMapData);

        const riskLevelElem = document.getElementById('risk_level');
        if (riskLevelElem) riskLevelElem.addEventListener('change', loadMapData);

        const districtToggle = document.getElementById('toggle_district_boundaries');
        if (districtToggle) {
            districtToggle.addEventListener('change', () => {
                if (districtToggle.checked) {
                    layers.districtBoundaries.addTo(map);
                } else {
                    map.removeLayer(layers.districtBoundaries);
                }
            });
        }

        const villageToggle = document.getElementById('toggle_village_boundaries');
        if (villageToggle) {
            villageToggle.addEventListener('change', () => {
                if (villageToggle.checked) {
                    villageBoundariesLayer.addTo(map);

                    layers.districtBoundaries.eachLayer(layer => {
                        if (layer.getPopup()) {
                            layer._storedPopup = layer.getPopup();
                            layer.unbindPopup();
                        }
                    });

                    if (!villageGeojsonLoaded) {
                        villageGeojsonLoaded = true;
                        const files = [
                            '/geojson/Kecamatan Bone Raya-KEL_DESA.geojson',
                            '/geojson/Kecamatan Bone-KEL_DESA.geojson',
                            '/geojson/Kecamatan Bonepantai-KEL_DESA.geojson',
                            '/geojson/Kecamatan Bulawa-KEL_DESA.geojson',
                            '/geojson/Kecamatan Kabila Bone-KEL_DESA.geojson'
                        ];
                        files.forEach(file => {
                            fetch(file)
                                .then(res => res.json())
                                .then(data => {
                                    L.geoJSON(data, {
                                        style: {
                                            color: '#000000ff',
                                            weight: 1,
                                            fillColor: '#facc15',
                                            fillOpacity: 0.1,
                                            dashArray: '3 3'
                                        },
                                        onEachFeature: function(feature, layer) {
                                            if (feature.properties) {
                                                const name = feature.properties.nama || feature.properties.kel_desa || feature.properties.NAMOBJ || 'Tidak diketahui';
                                                const key = name.toLowerCase().replace(/desa |kelurahan /g, '').trim();
                                                // const aidInfo =  [key];
                                                const aidInfo = villageAidsData[key];

                                                
                                                let aidHtml = '';
                                                if (aidInfo) {
                                                    const types = aidInfo.aid_items.length > 0 ? aidInfo.aid_items.join(', ') : '-';
                                                    aidHtml = `
                                                        Total Penerima: ${aidInfo.total_beneficiaries}<br>
                                                        Total Barang Tersalur: ${aidInfo.total_quantity}<br>
                                                        Jenis Bantuan: ${types}
                                                    `;
                                                } else {
                                                    aidHtml = `<em>Belum ada data distribusi</em>`;
                                                }

                                                
                                                layer.bindPopup(`
                                                    <strong>${name}</strong><br>
                                                    ${aidHtml}
                                                `);
                                            }
                                        }
                                    }).addTo(villageBoundariesLayer);
                                })
                                .catch(err => console.error('Error loading village boundaries:', err));
                        });
                    }
                } else {
                    map.removeLayer(villageBoundariesLayer);

                    layers.districtBoundaries.eachLayer(layer => {
                        if (layer._storedPopup && !layer.getPopup()) {
                            layer.bindPopup(layer._storedPopup);
                        }
                    });
                }
            });

            if (villageToggle.checked) {
                villageToggle.dispatchEvent(new Event('change'));
            }
        }

    });
    

    if (mapUiVariant === 'landing-fs') {
        window.addEventListener('resize', invalidateMapSize);
        requestAnimationFrame(() => {
            invalidateMapSize();
            setTimeout(invalidateMapSize, 250);
        });
    }
})();
</script>
@endpush