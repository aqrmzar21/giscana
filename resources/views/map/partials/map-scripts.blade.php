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
    let userMarker = null;
    let nearestMarker = null;
    let userRouteLayerGroup = L.layerGroup().addTo(map); // Layer khusus rute user
    let userRoutingControl = null;
    let blinkingTimer = null;
    let currentNavigationBounds = null;

    // Fungsi Buka Modal
    function openLocationModal() {
        const modal = document.getElementById('locationModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    // Fungsi Tutup Modal
    function closeLocationModal() {
        const modal = document.getElementById('locationModal');
        if (modal) {
            modal.classList.add('hidden');
        }
        invalidateMapSize();
    }

    // Fungsi Reset Lokasi User (Kembalikan Peta ke Tampilan Awal)
    function resetUserLocation() {
        // 1. Hapus Marker User
        if (userMarker) {
            map.removeLayer(userMarker);
            userMarker = null;
        }

        // 2. Hapus Garis Rute Khusus User & LRM
        userRouteLayerGroup.clearLayers();
        if (userRoutingControl) {
            try {
                map.removeControl(userRoutingControl);
            } catch (e) {
                console.log('LRM control remove safe catch');
            }
            userRoutingControl = null;
        }

        // 3. Matikan Efek Kedip pada Marker Fasilitas
        if (blinkingTimer) {
            clearTimeout(blinkingTimer);
            blinkingTimer = null;
        }

        if (nearestMarker) {
            const el = nearestMarker.getElement();
            if (el) el.classList.remove('is-blinking');
            nearestMarker = null;
        }

        layers.evacuationFacilities.eachLayer(marker => {
            const el = marker.getElement();
            if (el) el.classList.remove('is-blinking');
        });

        // 4. Hentikan dan Reset Audio Alarm
        const audio = document.getElementById('alert-sound');
        if (audio) {
            audio.pause();
            audio.currentTime = 0;
        }
        hasPlayedAlert = false;

        // 5. Sembunyikan Panel Navigasi Modern
        const navPanel = document.getElementById('evacuationNavigationPanel');
        if (navPanel) {
            navPanel.classList.add('hidden');
        }

        // 6. Update UI Status Kontrol
        const statusElem = document.getElementById('userLocationStatus');
        const resetBtn = document.getElementById('btnResetLocation');

        if (statusElem) {
            statusElem.textContent = 'Belum Diatur';
            statusElem.className = 'text-[10px] font-semibold text-slate-500 bg-slate-200/80 px-2 py-0.5 rounded-full';
        }

        if (resetBtn) {
            resetBtn.disabled = true;
            resetBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }

        currentNavigationBounds = null;
        invalidateMapSize();
    }

    // Event Listener Modal & Reset
    document.getElementById('btnMapLocationTool')?.addEventListener('click', openLocationModal);
    document.getElementById('btnOpenLocationModal')?.addEventListener('click', openLocationModal);
    document.getElementById('closeLocationModal')?.addEventListener('click', closeLocationModal);
    document.getElementById('btnResetLocation')?.addEventListener('click', resetUserLocation);

    document.getElementById('locationModal')?.addEventListener('click', function (e) {
        if (e.target.id === 'locationModal') closeLocationModal();
    });

    // =====================================================================
    // DETEKSI LOKASI OTOMATIS VIA GPS BROWSER
    // =====================================================================
    document.getElementById('btnGetCurrentLocation')?.addEventListener('click', function() {
        if (!navigator.geolocation) {
            alert('Fitur Geolocation/GPS tidak didukung oleh browser Anda.');
            return;
        }

        const btn = this;
        const originalContent = btn.innerHTML;

        // Ubah tampilan tombol jadi loading
        btn.disabled = true;
        btn.innerHTML = `
            <svg class="animate-spin h-4 w-4 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Mendeteksi Lokasi GPS...</span>
        `;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Isi otomatis ke input form
                document.getElementById('lat').value = lat.toFixed(6);
                document.getElementById('lng').value = lng.toFixed(6);

                // Kembalikan tombol ke kondisi normal
                btn.innerHTML = originalContent;
                btn.disabled = false;

                // Langsung jalankan proses Set Lokasi
                document.getElementById('setLocation').click();
            },
            (error) => {
                btn.innerHTML = originalContent;
                btn.disabled = false;

                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        alert("Izin akses GPS ditolak. Silakan izinkan akses lokasi pada browser Anda.");
                        break;
                    case error.POSITION_UNAVAILABLE:
                        alert("Informasi lokasi GPS tidak tersedia.");
                        break;
                    case error.TIMEOUT:
                        alert("Waktu permintaan lokasi habis (Timeout).");
                        break;
                    default:
                        alert("Gagal mendeteksi lokasi GPS.");
                        break;
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });

    // 🚫 Flag status audio alarm
    let hasPlayedAlert = false; 

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

    // Helper bangun konten HTML popup instruksi arah untuk Marker Lokasi User
    function buildInstructionPopupHtml(data, userLat, userLng) {
        const facility = data.facility;
        const summary = data.summary || {};
        const instructions = data.instructions || [];
        const isMode1 = data.mode === 'jalur_resmi';

        const modeBadge = isMode1
            ? `<span style="font-size:10px;font-weight:700;color:#065f46;background:#d1fae5;padding:2px 8px;border-radius:9999px;border:1px solid #a7f3d0;">🛡️ Jalur Resmi BPBD</span>`
            : `<span style="font-size:10px;font-weight:700;color:#3730a3;background:#e0e7ff;padding:2px 8px;border-radius:9999px;border:1px solid #c7d2fe;">🛣 Routing Jalan Umum</span>`;

        let rowsHtml = '';
        instructions.forEach(ins => {
            const isArrival = ins.type === 'arrive';
            const isEnterOfficial = ins.type === 'enter_official';

            let rowBg = '#f8fafc';
            let iconBg = '#e2e8f0';
            let iconColor = '#334155';

            if (isArrival) {
                rowBg = '#ecfdf5';
                iconBg = '#10b981';
                iconColor = '#ffffff';
            } else if (isEnterOfficial) {
                rowBg = '#eef2ff';
                iconBg = '#6366f1';
                iconColor = '#ffffff';
            }

            rowsHtml += `
                <div style="display:flex;align-items:flex-start;gap:8px;padding:6px 9px;border-radius:10px;background:${rowBg};margin-bottom:6px;border:1px solid rgba(0,0,0,0.04);">
                    <div style="width:22px;height:22px;border-radius:6px;background:${iconBg};color:${iconColor};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;">
                        ${getInstructionIconSvg(ins.type)}
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;font-size:11px;color:#1e293b;line-height:1.35;">
                            ${ins.step}. ${ins.text}
                        </div>
                        ${ins.distance_meters > 0 ? `<div style="font-size:10px;color:#64748b;font-weight:500;margin-top:2px;">${ins.distance_formatted}</div>` : ''}
                    </div>
                </div>
            `;
        });

        return `
            <div style="padding:14px;font-family:system-ui,-apple-system,sans-serif;color:#1e293b;">
                <!-- Header Popup -->
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;padding-bottom:10px;margin-bottom:10px;border-bottom:1px solid #f1f5f9;">
                    <div style="flex:1;">
                        <div style="font-size:12px;font-weight:800;color:#0f172a;display:flex;align-items:center;gap:4px;">
                            <span>🧭</span> Instruksi Arah Evakuasi
                        </div>
                        <div style="margin-top:4px;">
                            ${modeBadge}
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:13px;font-weight:900;color:#4f46e5;">${summary.distance_formatted || '–'}</div>
                        <div style="font-size:10px;color:#64748b;font-weight:600;">⏱ ${summary.duration_formatted || '–'}</div>
                    </div>
                </div>

                <!-- Info Titik Kumpul Tujuan -->
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:7px 10px;margin-bottom:10px;font-size:11px;">
                    <div style="font-size:9px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:0.5px;">🏫 Titik Kumpul Tujuan</div>
                    <div style="font-weight:700;color:#14532d;margin-top:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                        ${facility.name}
                    </div>
                </div>

                <!-- Daftar Instruksi -->
                <div style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;display:flex;justify-content:space-between;">
                    <span>Panduan Evakuasi</span>
                    <span style="color:#94a3b8;font-weight:600;">${instructions.length} Langkah</span>
                </div>
                <div style="max-height:210px;overflow-y:auto;padding-right:4px;">
                    ${rowsHtml}
                </div>
            </div>
        `;
    }

    // Helper update tampilan Panel Navigasi Modern (Tailwind)
    function renderNavigationPanel(data, userLat, userLng) {
        const panel = document.getElementById('evacuationNavigationPanel');
        if (!panel) return;

        const isMode1 = data.mode === 'jalur_resmi';
        const facility = data.facility;
        const summary = data.summary || {};
        const instructions = data.instructions || [];

        // 1. Badge Mode
        const modeBadgeContainer = document.getElementById('navModeBadgeContainer');
        const modePrefix = document.getElementById('navModePrefix');
        const modeTitle = document.getElementById('navModeTitle');
        const modeLabel = document.getElementById('navModeLabel');
        const modeIcon = document.getElementById('navModeIcon');
        const modeTag = document.getElementById('navModeTag');
        const modeDesc = document.getElementById('navModeDescription');

        if (isMode1) {
            modeBadgeContainer.className = 'p-3 rounded-xl border transition-all bg-emerald-50 dark:bg-emerald-950/40 border-emerald-300 dark:border-emerald-800 text-emerald-950 dark:text-emerald-100';
            if (modePrefix) modePrefix.textContent = 'MODE 1 (DEKAT < 100M)';
            if (modeIcon) modeIcon.textContent = '🛡️';
            if (modeLabel) modeLabel.textContent = 'Jalur Evakuasi Resmi';
            if (modeTag) {
                modeTag.textContent = 'JALUR RESMI';
                modeTag.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full uppercase bg-emerald-200 dark:bg-emerald-800 text-emerald-800 dark:text-emerald-200';
            }
            if (modeDesc) modeDesc.textContent = 'Posisi Anda berada di/dekat jalur resmi. Mengikuti rute evakuasi resmi terverifikasi BPBD langsung ke titik kumpul.';
        } else {
            modeBadgeContainer.className = 'p-3 rounded-xl border transition-all bg-indigo-50 dark:bg-indigo-950/40 border-indigo-300 dark:border-indigo-800 text-indigo-950 dark:text-indigo-100';
            if (modePrefix) modePrefix.textContent = 'MODE 2 (JAUH > 100M)';
            if (modeIcon) modeIcon.textContent = '🛣';
            if (modeLabel) modeLabel.textContent = 'Routing Jalan Umum (Hybrid)';
            if (modeTag) {
                modeTag.textContent = 'HYBRID OSRM';
                modeTag.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full uppercase bg-indigo-200 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-200';
            }
            if (modeDesc) modeDesc.textContent = 'Menavigasikan rute via jalan umum menuju titik masuk jalur evakuasi resmi, kemudian diarahkan ke titik kumpul.';
        }

        // 2. Lokasi User & Fasilitas
        const userCoordElem = document.getElementById('navUserCoord');
        if (userCoordElem) {
            userCoordElem.textContent = `${userLat.toFixed(5)}, ${userLng.toFixed(5)}`;
        }

        const facilityNameElem = document.getElementById('navFacilityName');
        if (facilityNameElem) {
            facilityNameElem.textContent = facility.name || 'Titik Kumpul Evakuasi';
        }

        const facilityDetailElem = document.getElementById('navFacilityDetail');
        if (facilityDetailElem) {
            const district = facility.district_name || 'Bone Bolango';
            const capacity = facility.capacity ? ` • Kapasitas: ${facility.capacity} Jiwa` : '';
            facilityDetailElem.textContent = `${facility.address || 'Kecamatan ' + district}${capacity}`;
        }

        // 3. Jarak & Estimasi
        const distElem = document.getElementById('navDistanceValue');
        if (distElem) {
            distElem.textContent = summary.distance_formatted || '–';
        }

        const durElem = document.getElementById('navDurationValue');
        if (durElem) {
            durElem.textContent = summary.duration_formatted || '–';
        }

        // 4. Update Badge Jumlah Langkah Instruksi di Tombol Panel
        const stepsBadge = document.getElementById('navStepsCountBadge');
        if (stepsBadge) {
            stepsBadge.textContent = `${instructions.length} Langkah`;
        }

        // Tampilkan panel
        panel.classList.remove('hidden');
    }

    // Proses "Set Lokasi" (Hybrid Routing Pipeline)
    document.getElementById('setLocation')?.addEventListener('click', function() {
        const lat = parseFloat(document.getElementById('lat').value);
        const lng = parseFloat(document.getElementById('lng').value);

        if (isNaN(lat) || isNaN(lng)) {
            alert('Silakan masukkan nilai Latitude dan Longitude yang valid.');
            return;
        }

        // 1. Bersihkan state lokasi lama & tutup modal
        resetUserLocation();
        closeLocationModal();

        // 2. Tambahkan Marker Lokasi User Berdesain Modern (Radar Pulsing)
        const userIcon = L.divIcon({
            className: 'user-gps-marker',
            html: '<div class="pulse-ring"></div><div class="dot-center"></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });
        userMarker = L.marker([lat, lng], { icon: userIcon })
            .addTo(map)
            .bindPopup(`<div style="padding:10px 14px;font-size:12px;font-weight:600;color:#334155;display:flex;align-items:center;gap:6px;"><span>🧭</span><span>Menghitung instruksi arah ke titik kumpul...</span></div>`, { className: 'instruction-popup' })
            .openPopup();

        // Update status kontrol panel
        const statusElem = document.getElementById('userLocationStatus');
        const resetBtn = document.getElementById('btnResetLocation');

        if (statusElem) {
            statusElem.textContent = 'Mencari Rute...';
            statusElem.className = 'text-[10px] font-semibold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full';
        }

        // 3. Panggil API Hybrid Evacuation Routing
        fetch(`/api/nearest-evacuation-with-route?lat=${lat}&lng=${lng}`)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP Error ${res.status}`);
                return res.json();
            })
            .then(data => {
                if (!data.success || !data.facility) {
                    alert(data.message || "Gagal menemukan titik kumpul terdekat.");
                    if (statusElem) {
                        statusElem.textContent = 'Gagal';
                        statusElem.className = 'text-[10px] font-semibold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-full';
                    }
                    return;
                }

                // Update Status Kontrol Aktif
                if (statusElem) {
                    statusElem.textContent = data.mode === 'jalur_resmi' ? 'Jalur Resmi ✓' : 'Jalan Umum ✓';
                    statusElem.className = data.mode === 'jalur_resmi' 
                        ? 'text-[10px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full'
                        : 'text-[10px] font-semibold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded-full';
                }
                if (resetBtn) {
                    resetBtn.disabled = false;
                    resetBtn.classList.remove('opacity-50', 'cursor-not-allowed');
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

                // --- A. EFEK KEDIP (BLINKING) & AUDIO SIRINE ALARM ---
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
                        el.classList.add('is-blinking');
                        // Popup titik kumpul tetap tersedia jika marker diklik,
                        // namun tidak dipanggil openPopup() otomatis agar popup instruksi arah pada lokasi user tetap aktif di layar
                        
                        // Mainkan suara alarm audio
                        const audio = document.getElementById('alert-sound');
                        if (audio) {
                            audio.currentTime = 0;
                            audio.play().catch(err => console.log('Audio autoplay blocked by browser policy:', err));
                            hasPlayedAlert = true;
                        }

                        // Durasi kedip otomatis 30 detik
                        if (blinkingTimer) clearTimeout(blinkingTimer);
                        blinkingTimer = setTimeout(() => {
                            if (el) el.classList.remove('is-blinking');
                        }, 30000);
                    }
                }

                // --- B. GAMBAR RUTE HYBRID PADA userRouteLayerGroup ---
                userRouteLayerGroup.clearLayers();
                const fullRouteLatLngs = data.full_route_latlngs || [[lat, lng], [fLat, fLng]];

                if (data.mode === 'jalur_resmi') {
                    // MODE 1: Jalur Evakuasi Resmi (Hijau Emerald Tebal dengan Casing Kontras Tinggi)
                    L.polyline(fullRouteLatLngs, {
                        color: '#064e3b',
                        weight: 9,
                        opacity: 0.85
                    }).addTo(userRouteLayerGroup);

                    const officialLine = L.polyline(fullRouteLatLngs, {
                        color: '#10b981',
                        weight: 6,
                        opacity: 1.0,
                        lineCap: 'round',
                        lineJoin: 'round'
                    }).addTo(userRouteLayerGroup);

                    officialLine.bindPopup(`<b>🛣 Jalur Evakuasi Resmi BPBD</b><br>Menuju: ${facility.name}<br>Jarak: ${data.summary?.distance_formatted || ''}`);

                } else {
                    // MODE 2: Hybrid (Segmen 1: Jalan Umum via OSRM/ORS + Segmen 2: Jalur Evakuasi Resmi)
                    const segs = data.segments || {};

                    // 1. Rute Jalan Umum (Indigo beraksen jalan raya modern)
                    if (segs.public_road && segs.public_road.coordinates && segs.public_road.coordinates.length > 0) {
                        L.polyline(segs.public_road.coordinates, {
                            color: '#312e81',
                            weight: 8,
                            opacity: 0.8
                        }).addTo(userRouteLayerGroup);

                        L.polyline(segs.public_road.coordinates, {
                            color: '#6366f1',
                            weight: 5,
                            dashArray: '8, 8',
                            opacity: 1.0
                        }).addTo(userRouteLayerGroup).bindPopup(`<b>🛣 Jaringan Jalan Umum (${segs.public_road.source || 'OSRM'})</b><br>Jarak: ${segs.public_road.distance_formatted}`);
                    }

                    // 2. Waypoint Titik Masuk Jalur Evakuasi Resmi
                    if (data.summary && data.summary.entry_point) {
                        const junctionIcon = L.divIcon({
                            className: 'junction-marker',
                            html: '<div>🛡️</div>',
                            iconSize: [26, 26],
                            iconAnchor: [13, 13]
                        });
                        L.marker([data.summary.entry_point.lat, data.summary.entry_point.lng], { icon: junctionIcon })
                            .addTo(userRouteLayerGroup)
                            .bindPopup(`<b>Titik Masuk Jalur Evakuasi Resmi</b><br>${segs.official_evacuation?.route_name || 'Jalur Evakuasi BPBD'}`);
                    }

                    // 3. Rute Resmi BPBD Lanjutan ke Titik Kumpul
                    if (segs.official_evacuation && segs.official_evacuation.coordinates && segs.official_evacuation.coordinates.length > 0) {
                        L.polyline(segs.official_evacuation.coordinates, {
                            color: '#064e3b',
                            weight: 8,
                            opacity: 0.85
                        }).addTo(userRouteLayerGroup);

                        L.polyline(segs.official_evacuation.coordinates, {
                            color: '#10b981',
                            weight: 5,
                            opacity: 1.0
                        }).addTo(userRouteLayerGroup).bindPopup(`<b>🛡️ Jalur Evakuasi Resmi BPBD</b><br>${segs.official_evacuation.route_name || ''}<br>Menuju: ${facility.name}`);
                    }
                }

                // C. Zoom & Fit Bounds Otomatis
                currentNavigationBounds = L.latLngBounds(fullRouteLatLngs);
                map.fitBounds(currentNavigationBounds, { padding: [60, 60], maxZoom: 17 });

                // D. Inisialisasi Integrasi Leaflet Routing Machine
                try {
                    if (typeof L.Routing !== 'undefined') {
                        if (userRoutingControl) {
                            map.removeControl(userRoutingControl);
                            userRoutingControl = null;
                        }
                        userRoutingControl = L.Routing.control({
                            waypoints: [
                                L.latLng(lat, lng),
                                L.latLng(fLat, fLng)
                            ],
                            show: false,
                            addWaypoints: false,
                            routeWhileDragging: false,
                            createMarker: () => null // Marker diatur oleh userMarker & nearestMarker
                        }).addTo(map);
                    }
                } catch (e) {
                    console.log("LRM fallback: ", e);
                }

                // E. Pasang Pop-up Instruksi Arah pada Marker Lokasi User (Bukan lagi hanya titik koordinat)
                const instructionPopupHtml = buildInstructionPopupHtml(data, lat, lng);
                userMarker.bindPopup(instructionPopupHtml, {
                    maxWidth: 350,
                    minWidth: 290,
                    className: 'instruction-popup',
                    autoPan: false
                }).openPopup();

                // F. Render Panel Navigasi Modern (Tailwind)
                renderNavigationPanel(data, lat, lng);
            })
            .catch(err => {
                console.error('Error fetching hybrid evacuation route:', err);
                alert("Gagal memuat rute navigasi evakuasi. Silakan periksa koneksi internet atau koordinat.");
                if (statusElem) {
                    statusElem.textContent = 'Gagal';
                    statusElem.className = 'text-[10px] font-semibold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-full';
                }
            });
    });

    // Event Listener Interaktif untuk Panel Navigasi
    document.getElementById('btnOpenInstructionPopup')?.addEventListener('click', function() {
        if (userMarker) {
            map.panTo(userMarker.getLatLng());
            userMarker.openPopup();
        }
    });

    document.getElementById('btnFocusNavigationRoute')?.addEventListener('click', function() {
        if (currentNavigationBounds) {
            map.fitBounds(currentNavigationBounds, { padding: [60, 60], maxZoom: 17 });
        }
    });

    document.getElementById('btnResetNavFromPanel')?.addEventListener('click', function() {
        resetUserLocation();
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