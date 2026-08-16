@php($mapUiVariant = $mapUiVariant ?? 'admin')
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const mapUiVariant = @json($mapUiVariant);
    const map = L.map('map').setView([0.45, 123.2], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

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

    // OpenStreetMap default
    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    });

    // OpenTopoMap
    const topo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenTopoMap contributors',
        maxZoom: 17
    });

    // Esri World Imagery (satellite)
    const esriSat = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles © Esri'
    });

    osm.addTo(map);

    const overlayMaps = {
        "Zona Bencana": layers.disasterZones,
        "Rute Evakuasi": layers.evacuationRoutes,
        "Fasilitas Evakuasi": layers.evacuationFacilities,
        "Batas Kecamatan": layers.districtBoundaries
    };

    const baseMaps = {
        "Peta Jalan": osm,
        "Topografi": topo,
        "Satelit": esriSat
    };

    L.control.layers(baseMaps, overlayMaps).addTo(map);

    // =====================================================================
    // HAZARD LAYERS (GeoJSON dari public/geojson/)
    // =====================================================================

    /** Map: disaster_type -> { config, leafletLayer, loaded } */
    const hazardLayers = {};

    /**
     * Inisialisasi panel checkbox badge setelah metadata diambil dari API.
     */
    function buildHazardLayerUI(layerConfigs) {
        const container = document.getElementById('hazard_layer_checkboxes');
        if (!container) return;
        container.innerHTML = '';

        layerConfigs.forEach(cfg => {
            // Buat layer Leaflet kosong, belum di-add ke map
            hazardLayers[cfg.disaster_type] = {
                config: cfg,
                leafletLayer: L.layerGroup(),
                loaded: false,
                visible: false,
            };

            // Badge elemen
            const badge = document.createElement('label');
            badge.className = 'hazard-layer-badge inactive';
            badge.id = `hazard_badge_${cfg.disaster_type}`;
            badge.title = cfg.description || cfg.label;
            badge.style.cssText = `
                background-color: ${cfg.fill_color}22;
                border-color: ${cfg.border_color};
                color: ${cfg.border_color};
            `;
            badge.innerHTML = `
                <input type="checkbox" id="hazard_toggle_${cfg.disaster_type}" data-type="${cfg.disaster_type}">
                <span>${cfg.icon_emoji || ''} ${cfg.label}</span>
            `;
            container.appendChild(badge);

            // Event: klik badge -> toggle layer
            badge.addEventListener('click', () => {
                const state = hazardLayers[cfg.disaster_type];
                toggleHazardLayer(cfg.disaster_type, !state.visible);
            });
        });

        // Tombol "Semua Aktif / Semua Nonaktif"
        const toggleAllBtn = document.getElementById('toggle_all_hazard_layers');
        if (toggleAllBtn) {
            toggleAllBtn.addEventListener('click', () => {
                const anyActive = Object.values(hazardLayers).some(s => s.visible);
                const targetState = !anyActive;
                toggleAllBtn.textContent = targetState ? 'Semua Nonaktif' : 'Semua Aktif';
                Object.keys(hazardLayers).forEach(type => toggleHazardLayer(type, targetState));
            });
        }
    }

    /**
     * Toggle visibilitas layer bencana tertentu.
     * Jika belum di-load, fetch GeoJSON-nya terlebih dahulu.
     */
    function toggleHazardLayer(disasterType, show) {
        const state = hazardLayers[disasterType];
        if (!state) return;

        state.visible = show;

        // Update tampilan badge
        const badge = document.getElementById(`hazard_badge_${disasterType}`);
        if (badge) {
            badge.classList.toggle('active', show);
            badge.classList.toggle('inactive', !show);
        }

        if (show) {
            // Muat GeoJSON jika belum
            if (!state.loaded) {
                state.loaded = true; // flag awal agar tidak double-fetch
                loadHazardGeoJSON(disasterType);
            } else {
                state.leafletLayer.addTo(map);
            }
        } else {
            map.removeLayer(state.leafletLayer);
        }
    }

    /**
     * Fetch dan render GeoJSON untuk satu jenis bencana.
     */
    function loadHazardGeoJSON(disasterType) {
        const state = hazardLayers[disasterType];
        if (!state) return;

        const cfg = state.config;

        // Tampilkan loading indicator pada badge
        const badge = document.getElementById(`hazard_badge_${disasterType}`);
        if (badge) badge.style.opacity = '0.6';

        fetch(cfg.geojson_path)
            .then(res => {
                if (!res.ok) throw new Error(`HTTP ${res.status} saat memuat ${cfg.geojson_path}`);
                return res.json();
            })
            .then(geojsonData => {
                const geoLayer = L.geoJSON(geojsonData, {
                    style: feature => ({
                        color: cfg.border_color,
                        weight: cfg.border_weight,
                        fillColor: cfg.fill_color,
                        fillOpacity: cfg.fill_opacity,
                        opacity: 0.85,
                    }),
                    onEachFeature: (feature, layer) => {
                        const p = feature.properties || {};

                        // Bangun konten popup dari properti GeoJSON yang umum
                        const namaArea  = p.NAMOBJ || p.nama || p.name || p.NAMA || p.desa || p.kecamatan || '–';
                        const tingkat   = p.KELAS || p.kelas || p.tingkat || p.risk_level || p.RAWAN || '–';
                        const luas      = p.SHAPE_Area ? (parseFloat(p.SHAPE_Area) / 10000).toFixed(2) + ' ha' : (p.luas || '–');
                        const keterangan = p.KETERANGAN || p.keterangan || p.description || '';

                        let popupHtml = `
                            <div style="min-width:180px; font-size:13px;">
                                <div style="font-weight:700; font-size:14px; margin-bottom:6px; color:${cfg.border_color};">
                                    ${cfg.icon_emoji || ''} ${cfg.label}
                                </div>
                                <table style="border-collapse:collapse; width:100%;">
                                    <tr>
                                        <td style="color:#6b7280; padding:2px 6px 2px 0; white-space:nowrap;">Nama Area</td>
                                        <td style="font-weight:600;">${namaArea}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#6b7280; padding:2px 6px 2px 0; white-space:nowrap;">Tingkat Rawan</td>
                                        <td style="font-weight:600;">${tingkat}</td>
                                    </tr>
                                    <tr>
                                        <td style="color:#6b7280; padding:2px 6px 2px 0; white-space:nowrap;">Luas</td>
                                        <td>${luas}</td>
                                    </tr>
                                    ${keterangan ? `<tr><td colspan="2" style="color:#374151; padding-top:4px; font-style:italic;">${keterangan}</td></tr>` : ''}
                                </table>
                            </div>
                        `;

                        layer.bindPopup(popupHtml, { maxWidth: 280 });

                        // Hover highlight
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

                // Hanya tambah ke peta jika masih dikehendaki visible
                if (state.visible) {
                    state.leafletLayer.addTo(map);
                }

                if (badge) badge.style.opacity = '';
            })
            .catch(err => {
                console.error(`Gagal memuat GeoJSON (${disasterType}):`, err);
                state.loaded = false; // izinkan retry
                if (badge) {
                    badge.style.opacity = '';
                    badge.title = `Gagal memuat: ${cfg.geojson_path}`;
                }
            });
    }

    /**
     * Fetch metadata layer dari endpoint backend, lalu bangun UI.
     */
    function initHazardLayers() {
        fetch('{{ route("map.hazard-layers") }}')
            .then(res => res.json())
            .then(data => {
                if (data.layers && data.layers.length > 0) {
                    buildHazardLayerUI(data.layers);
                } else {
                    const container = document.getElementById('hazard_layer_checkboxes');
                    if (container) container.innerHTML = '<span class="text-xs text-red-400">Tidak ada layer tersedia.</span>';
                }
            })
            .catch(err => {
                console.error('Gagal memuat metadata hazard layers:', err);
                const container = document.getElementById('hazard_layer_checkboxes');
                if (container) container.innerHTML = '<span class="text-xs text-red-400">Gagal memuat layer.</span>';
            });
    }

    // =====================================================================
    // MAP DATA (data DB: disaster zones, routes, facilities)
    // =====================================================================

    function loadMapData() {
        const disasterType = document.getElementById('disaster_type').value;
        const riskLevel = document.getElementById('risk_level').value;
        const districtToggle = document.getElementById('toggle_district_boundaries');

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
                            html: '<div style="background-color: #3b82f6; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white;"></div>',
                            iconSize: [20, 20]
                        })
                    }).addTo(layers.evacuationFacilities);

                    marker.bindPopup(`
                        <strong>${feature.properties.name}</strong><br>
                        Alamat: ${feature.properties.address || '-'}
                    `);
                });

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

                        polys.forEach(coords => {
                            const polygon = L.polygon(coords, {
                                color: '#facc15',
                                fillColor: '#fde68a',
                                fillOpacity: 0.15,
                                weight: 2,
                                dashArray: '4 2'
                            }).addTo(layers.districtBoundaries);

                            const p = feature.properties;
                            polygon.bindPopup(`
                                <strong>${p.district_name}</strong><br>
                                Penerima Bantuan: ${p.total_recipients ?? '-'}<br>
                                Terdistribusi: ${p.distributed_aid ?? '-'}<br>
                                Persentase: ${p.distribution_percentage ?? '-'}%
                            `);

                            const vToggle = document.getElementById('toggle_village_boundaries');
                            if (vToggle && vToggle.checked) {
                                polygon._storedPopup = polygon.getPopup();
                                polygon.unbindPopup();
                            }
                        });
                    });
                }

                if (districtToggle && !districtToggle.checked) {
                    map.removeLayer(layers.districtBoundaries);
                } else {
                    layers.districtBoundaries.addTo(map);
                }

                if (data.aid_disasters && data.aid_disasters.features.length > 0) {
                    console.log('Data bantuan bencana:', data.aid_disasters.features.length, 'kecamatan');
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

    loadMapData();
    initHazardLayers();

    document.getElementById('disaster_type').addEventListener('change', loadMapData);
    document.getElementById('risk_level').addEventListener('change', loadMapData);

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

                // Sembunyikan popup kecamatan
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
                                        color: '#ffffffff',
                                        weight: 1,
                                        fillColor: '#e3fa60ff',
                                        fillOpacity: 0.1,
                                        dashArray: '3 3'
                                    },
                                    onEachFeature: function(feature, layer) {
                                        if (feature.properties) {
                                            const name = feature.properties.nama || feature.properties.kel_desa || feature.properties.NAMOBJ || 'Tidak diketahui';
                                            const key = name.toLowerCase().replace(/desa |kelurahan /g, '').trim();
                                            const aidInfo = villageAidsData[key];
                                            
                                            let aidHtml = '';
                                            if (aidInfo && aidInfo.total_amount > 0) {
                                                const types = aidInfo.aid_types && aidInfo.aid_types.length > 0 ? aidInfo.aid_types.join(', ') : '-';
                                                aidHtml = `
                                                    Jenis Bantuan: ${types}<br>
                                                    Total Disalurkan: ${aidInfo.total_amount}
                                                `;
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

                // Tampilkan kembali popup kecamatan
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
