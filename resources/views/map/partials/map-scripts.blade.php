@php($mapUiVariant = $mapUiVariant ?? 'admin')
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
    // MANAJEMEN MODAL & KONTROL LOKASI USER
    // =====================================================================
    let userMarker = null;
    let userRouteLayerGroup = L.layerGroup().addTo(map); // Layer khusus rute user
    let userRoutingControl = null;
    let blinkingTimer = null;

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

        // 2. Hapus Garis Rute Khusus User
        userRouteLayerGroup.clearLayers();
        if (userRoutingControl) {
            map.removeControl(userRoutingControl);
            userRoutingControl = null;
        }

        // 3. Matikan Efek Kedip pada Marker Fasilitas
        if (blinkingTimer) {
            clearTimeout(blinkingTimer);
            blinkingTimer = null;
        }

        layers.evacuationFacilities.eachLayer(marker => {
            const el = marker.getElement();
            if (el) el.classList.remove('is-blinking');
        });

        // 4. Update UI Status
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

    // 🚫 default: belum pernah bunyi
    let hasPlayedAlert = false; 

    // Proses "Set Lokasi"
    document.getElementById('setLocation')?.addEventListener('click', function() {
        const lat = parseFloat(document.getElementById('lat').value);
        const lng = parseFloat(document.getElementById('lng').value);

        if (isNaN(lat) || isNaN(lng)) {
            alert('Silakan masukkan nilai Latitude dan Longitude yang valid.');
            return;
        }

        // 1. Bersihkan state lokasi lama
        resetUserLocation();
        closeLocationModal();

        // 2. Tambahkan Marker Lokasi User
        userMarker = L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Saya").openPopup();
        map.setView([lat, lng], 14);

        const statusElem = document.getElementById('userLocationStatus');
        const resetBtn = document.getElementById('btnResetLocation');

        if (statusElem) {
            statusElem.textContent = 'Aktif ✓';
            statusElem.className = 'text-[10px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full';
        }
        if (resetBtn) {
            resetBtn.disabled = false;
            resetBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }

        // 3. Panggil API Rute Evakuasi
        fetch(`/api/nearest-evacuation-with-route?lat=${lat}&lng=${lng}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success || !data.facility) {
                    alert("Gagal menemukan titik kumpul terdekat.");
                    return;
                }

                const facility = data.facility;
                let fLat, fLng;

                // --- PERBAIKAN BACA DATA DATABASE (ARRAY [LNG, LAT]) ---
                if (Array.isArray(facility.point_coordinates)) {
                    fLng = parseFloat(facility.point_coordinates[0]); // Indeks 0 = Longitude
                    fLat = parseFloat(facility.point_coordinates[1]); // Indeks 1 = Latitude
                } else if (typeof facility.point_coordinates === 'string') {
                    // Jaga-jaga jika dari API terbaca sebagai string "[123.xxx, 0.xxx]"
                    const coords = JSON.parse(facility.point_coordinates);
                    fLng = parseFloat(coords[0]);
                    fLat = parseFloat(coords[1]);
                }

                if (isNaN(fLat) || isNaN(fLng)) {
                    console.error("Format koordinat tidak valid", facility.point_coordinates);
                    return; 
                }

                const facilityLatLng = L.latLng(fLat, fLng);

                // --- A. EFEK KEDIP & SUARA PADA TITIK KUMPUL ---
                let targetMarker = null;
                let minTargetDist = Infinity;

                layers.evacuationFacilities.eachLayer(marker => {
                    const dist = facilityLatLng.distanceTo(marker.getLatLng());
                    if (dist < minTargetDist) {
                        minTargetDist = dist;
                        targetMarker = marker;
                    }
                });

                if (targetMarker) {
                    const el = targetMarker.getElement();
                    if (el) {
                        el.classList.add('is-blinking');
                        targetMarker.openPopup();

                        // // Mainkan suara sirine/alert
                        // const audio = document.getElementById('alert-sound');
                        // if (audio) {
                        //     audio.currentTime = 0;
                        //     audio.play().catch(err => console.log('Audio play blocked:', err));
                        // }
                        
                        // Mainkan suara sirine/alert hanya sekali
                        const audio = document.getElementById('alert-sound');
                        if (audio && !hasPlayedAlert) {
                            audio.currentTime = 0;
                            audio.play().catch(err => console.log('Audio play blocked:', err));
                            hasPlayedAlert = true; // ✅ tandai sudah bunyi
                        }

                        blinkingTimer = setTimeout(() => {
                            if (el) el.classList.remove('is-blinking');
                        }, 30000);
                    }
                }

                // --- B. BUAT RUTE BARU DARI USER KE TITIK KUMPUL ---
                const routeCoords = [[lat, lng], [fLat, fLng]];
                
                // 1. Gambar Garis Lurus (Biru Putus-putus)
                const dashLine = L.polyline(routeCoords, {
                    color: '#3b82f6', // Warna biru 
                    weight: 4,
                    dashArray: '8, 8' 
                }).addTo(userRouteLayerGroup).bindPopup('Jarak Lurus ke Fasilitas');
                
                // Garis Lurus (Biru Putus-putus)
                // const dashLine = L.polyline(routeCoords, {
                //     color: '#41f63bff',
                //     color: '#3b82f6',
                //     weight: 4,
                //     dashArray: '8, 8'
                // }).addTo(userRouteLayerGroup).bindPopup('Jarak Lurus ke Fasilitas');

                // // Tambahkan panah arah di sepanjang garis
                // const arrowHead = L.polylineDecorator(dashLine, {
                //     patterns: [
                //         {
                //             offset: 25, // jarak awal panah
                //             repeat: 50, // jarak antar panah
                //             symbol: L.Symbol.arrowHead({
                //                 pixelSize: 12,
                //                 polygon: false,
                //                 pathOptions: { stroke: true, color: '#41f63bff', weight: 2 }
                //             })
                //         }
                //     ]
                // }).addTo(userRouteLayerGroup);

                // Zoom peta otomatis agar lokasi user dan titik kumpul terlihat sekaligus
                map.fitBounds(dashLine.getBounds(), { padding: [50, 50] });

                // 2. (Opsional) Coba gambar rute mengikuti jalan jika L.Routing tersedia
                try {
                    if (typeof L.Routing !== 'undefined') {
                        userRoutingControl = L.Routing.control({
                            waypoints: [
                                L.latLng(lat, lng),
                                L.latLng(fLat, fLng)
                            ],
                            lineOptions: {
                                styles: [{color: '#ef4444', weight: 4, dashArray: '5, 10'}] // Warna merah untuk rute jalan
                            },
                            show: false,
                            addWaypoints: false,
                            routeWhileDragging: false
                        }).addTo(map);
                    }
                } catch (e) {
                    console.log("Leaflet Routing Machine tidak dijalankan.");
                }
            })
            .catch(err => console.error('Error fetching route:', err));
    });
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