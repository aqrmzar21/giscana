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

    // --- existing layers and tile layers (keep your definitions) ---
    const layers = {
        disasterZones: L.layerGroup().addTo(map),
        evacuationRoutes: L.layerGroup().addTo(map),
        evacuationFacilities: L.layerGroup().addTo(map),
        districtBoundaries: L.layerGroup().addTo(map),
    };

    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    });
    const topo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenTopoMap contributors',
        maxZoom: 17
    });
    const esriSat = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles © Esri'
    });
    osm.addTo(map);

    // mapping keys to objects for UI
    const baseMaps = { osm, topo, esriSat };
    const overlayMaps = {
        "Zona Bencana": layers.disasterZones,
        "Rute Evakuasi": layers.evacuationRoutes,
        "Fasilitas Evakuasi": layers.evacuationFacilities,
        "Batas Administrasi": layers.districtBoundaries
    };

    // --- helper to create overlay item node ---
    function createOverlayItem(label, key, layerObj) {
        const wrapper = document.createElement('div');
        wrapper.className = 'flex items-center justify-between gap-3';

        const left = document.createElement('div');
        left.className = 'flex items-center gap-3';

        // small icon placeholder
        const icon = document.createElement('div');
        icon.className = 'h-8 w-8 rounded-md flex items-center justify-center text-sm';
        icon.style.background = '#f3f4f6';
        icon.innerText = label.charAt(0);

        const title = document.createElement('div');
        title.className = 'text-sm text-gray-800';
        title.innerText = label;

        left.appendChild(icon);
        left.appendChild(title);

        const toggle = document.createElement('button');
        toggle.className = 'overlay-toggle inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs bg-white/60 hover:bg-white/80 border border-transparent';
        toggle.innerText = 'Nonaktif';
        toggle.dataset.key = key;

        // click handler: exclusive for disasterZones group, others toggle independently
        toggle.addEventListener('click', () => {
            // UX: show loading state
            toggle.disabled = true;
            toggle.classList.add('opacity-70');

            // exclusive behavior for disasterZones group (if key contains 'disaster' or label matches)
            const isDisaster = /zona|bencana/i.test(label);
            if (isDisaster) {
                // turn off all disaster-related layers first
                Object.keys(overlayMaps).forEach(k => {
                    if (/zona|bencana/i.test(k)) {
                        _setOverlayVisible(k, false);
                    }
                });
                // toggle this one
                const currentlyOn = toggle.classList.contains('active');
                if (!currentlyOn) {
                    _setOverlayVisible(label, true);
                } else {
                    _setOverlayVisible(label, false);
                }
            } else {
                // independent toggle
                const currentlyOn = toggle.classList.contains('active');
                _setOverlayVisible(label, !currentlyOn);
            }

            // small delay to simulate loading UX; real loading handled in _setOverlayVisible
            setTimeout(() => {
                toggle.disabled = false;
                toggle.classList.remove('opacity-70');
            }, 300);
        });

        wrapper.appendChild(left);
        wrapper.appendChild(toggle);
        return wrapper;
    }

    // --- function to set overlay visible by label key ---
    function _setOverlayVisible(label, show) {
        const layer = overlayMaps[label];
        if (!layer) return;
        const overlayList = document.getElementById('overlay-list');
        const btn = overlayList.querySelector(`button[data-key="${label}"]`);
        if (show) {
            layer.addTo(map);
            if (btn) { btn.classList.add('active'); btn.innerText = 'Aktif ✓'; }
        } else {
            map.removeLayer(layer);
            if (btn) { btn.classList.remove('active'); btn.innerText = 'Nonaktif'; }
        }
    }

    // --- populate overlay list in UI ---
    function initLayerControlUI() {
        const overlayList = document.getElementById('overlay-list');
        overlayList.innerHTML = '';
        Object.keys(overlayMaps).forEach(label => {
            const item = createOverlayItem(label, label, overlayMaps[label]);
            overlayList.appendChild(item);
        });

        // base map buttons
        const baseBtns = document.querySelectorAll('.basemap-btn');
        baseBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const key = btn.dataset.basemap;
                // visual active state
                baseBtns.forEach(b => b.classList.remove('ring-2','ring-indigo-300','bg-indigo-50'));
                btn.classList.add('ring-2','ring-indigo-300','bg-indigo-50');
                // switch tile layer
                Object.values(baseMaps).forEach(t => map.removeLayer(t));
                if (key === 'osm') osm.addTo(map);
                if (key === 'topo') topo.addTo(map);
                if (key === 'esriSat') esriSat.addTo(map);
            });
        });

        // set default base active visual
        const defaultBtn = document.querySelector('.basemap-btn[data-basemap="osm"]');
        if (defaultBtn) defaultBtn.classList.add('ring-2','ring-indigo-300','bg-indigo-50');
    }

    // --- mobile collapse behavior ---
    function initLayerControlMobile() {
        const fab = document.getElementById('layer-control-fab');
        const card = document.getElementById('layer-control-card');
        const toggle = document.getElementById('layer-control-toggle');

        function openCard() { card.classList.remove('hidden'); }
        function closeCard() { card.classList.add('hidden'); }

        if (fab) {
            fab.addEventListener('click', () => {
                if (card.classList.contains('hidden')) openCard(); else closeCard();
            });
        }
        if (toggle) {
            toggle.addEventListener('click', () => {
                if (card.classList.contains('hidden')) openCard(); else closeCard();
            });
        }

        // responsive: show card on md+, hide on small
        function handleResize() {
            if (window.matchMedia('(min-width: 768px)').matches) {
                card.classList.remove('hidden');
                fab.classList.add('hidden');
            } else {
                card.classList.add('hidden');
                fab.classList.remove('hidden');
            }
        }
        window.addEventListener('resize', handleResize);
        handleResize();
    }

    // --- init all ---
    initLayerControlUI();
    initLayerControlMobile();

    // L.control.layers(baseMaps, overlayMaps).addTo(map);

    // =====================================================================
    // HAZARD LAYERS (GeoJSON dari public/geojson/)
    // Perilaku: EXCLUSIVE — hanya satu layer aktif sekaligus.
    // Klik card → aktifkan/nonaktifkan layer.
    // =====================================================================

    /** Map: disaster_type -> { config, leafletLayer, loaded, visible } */
    const hazardLayers = {};

    /** disaster_type yang sedang aktif (atau null jika tidak ada) */
    let activeHazardType = null;

    /**
     * Bangun UI kartu badge per layer setelah metadata API diambil.
     */
    function buildHazardLayerUI(layerConfigs) {
        const container = document.getElementById('hazard_layer_checkboxes');
        if (!container) return;
        container.innerHTML = '';

        // Warna CSS-variable per card
        const shadowMap = {
            'banjir':    'rgba(59,130,246,0.25)',
            'gempa':     'rgba(249,115,22,0.25)',
            'gelombang': 'rgba(6,182,212,0.25)',
            'longsor':   'rgba(132,204,22,0.25)',
        };
        const bgMap = {
            'banjir':    '#eff6ff',
            'gempa':     '#fff7ed',
            'gelombang': '#ecfeff',
            'longsor':   '#f7fee7',
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

            // Klik card → exclusive toggle
            card.addEventListener('click', () => {
                const isCurrentlyActive = (activeHazardType === cfg.disaster_type);
                // Nonaktifkan semua dulu
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

    /**
     * Set visibilitas satu layer (tanpa mengubah state card lain).
     */
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

    /**
     * Fetch dan render GeoJSON untuk satu jenis bencana.
     */
    function loadHazardGeoJSON(disasterType) {
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
                        const namaArea   = p.NAMOBJ || p.nama || p.name || p.NAMA || p.desa || p.kecamatan || '–';

                        const popupHtml = `
                            <div style="min-width:190px;font-size:13px;">
                                <div style="font-weight:700;font-size:14px;margin-bottom:6px;color:${cfg.border_color};">
                                    ${cfg.icon_emoji || ''} ${cfg.label}
                                </div>
                                <div>${namaArea}</div>
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
                state.loaded = false; // izinkan retry
                if (card) {
                    card.style.opacity = '';
                    card.title = `⚠ Gagal memuat: ${cfg.geojson_path}`;
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
                                        color: '#00ff37ff',
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
