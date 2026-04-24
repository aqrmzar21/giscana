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

    const layers = {
        disasterZones: L.layerGroup().addTo(map),
        evacuationRoutes: L.layerGroup().addTo(map),
        evacuationFacilities: L.layerGroup().addTo(map),
        districtBoundaries: L.layerGroup().addTo(map),
        aidDistributionPoints: L.layerGroup().addTo(map)
    };

    const villageBoundariesLayer = L.layerGroup();
    let villageGeojsonLoaded = false;

    function invalidateMapSize() {
        map.invalidateSize({ animate: false });
    }

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
                    const marker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'disaster-zone-marker',
                            html: '<div style="background-color: #ef4444; width: 18px; height: 18px; border-radius: 50%; border: 2px solid white;"></div>',
                            iconSize: [18, 18]
                        })
                    }).addTo(layers.disasterZones);

                    marker.bindPopup(`
                        <strong>${feature.properties.name}</strong><br>
                        Jenis: ${feature.properties.disaster_type}<br>
                        Risiko: ${feature.properties.risk_level}<br>
                        Luas: ${feature.properties.area_hectares ?? '-'} ha<br>
                        Populasi Terdampak: ${feature.properties.affected_population ?? '-'}
                    `);
                });

                data.evacuation_routes.features.forEach(feature => {
                    const coordinates = feature.geometry.coordinates.map(coord => [coord[1], coord[0]]);
                    const polyline = L.polyline(coordinates, {
                        color: '#3b82f6',
                        weight: 4,
                        opacity: 0.8
                    }).addTo(layers.evacuationRoutes);

                    polyline.bindPopup(`
                        <strong>${feature.properties.name}</strong><br>
                        Jenis: ${feature.properties.route_type}<br>
                        Panjang: ${feature.properties.length_km} km<br>
                        Kapasitas: ${feature.properties.capacity_per_hour} orang/jam
                    `);
                });

                data.evacuation_facilities.features.forEach(feature => {
                    const [lng, lat] = feature.geometry.coordinates;
                    const marker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'evacuation-facility-marker',
                            html: '<div style="background-color: #10b981; width: 20px; height: 20px; border-radius: 50%; border: 2px solid white;"></div>',
                            iconSize: [20, 20]
                        })
                    }).addTo(layers.evacuationFacilities);

                    marker.bindPopup(`
                        <strong>${feature.properties.name}</strong><br>
                        Tipe: ${feature.properties.facility_type}<br>
                        Alamat: ${feature.properties.address || '-'}<br>
                        Kapasitas: ${feature.properties.capacity} orang<br>
                        ${feature.properties.has_medical_facility ? '✓ Fasilitas Medis' : ''}<br>
                        ${feature.properties.has_food_storage ? '✓ Penyimpanan Makanan' : ''}
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
                                <strong>${p.nama_kecamatan}</strong><br>
                                Penerima Bantuan: ${p.jumlah_penerima_bantuan ?? '-'}<br>
                                Terdistribusi: ${p.bantuan_terdistribusi ?? '-'}<br>
                                Persentase: ${p.persentase_distribusi ?? '-'}%
                            `);
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
                                        color: '#000000',
                                        weight: 1,
                                        fillColor: '#60a5fa',
                                        fillOpacity: 0.1,
                                        dashArray: '3 3'
                                    },
                                    onEachFeature: function(feature, layer) {
                                        if (feature.properties && feature.properties.NAMOBJ) {
                                            layer.bindTooltip(`<strong>Desa/Kel: ${feature.properties.NAMOBJ}</strong>`, {
                                                sticky: true,
                                                className: 'bg-white rounded shadow-sm text-sm'
                                            });
                                        }
                                    }
                                }).addTo(villageBoundariesLayer);
                            })
                            .catch(err => console.error('Error loading village boundaries:', err));
                    });
                }
            } else {
                map.removeLayer(villageBoundariesLayer);
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
