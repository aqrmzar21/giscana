@extends('layouts.landing')

@section('title', 'Peta Interaktif - Giscana')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 600px;
        width: 100%;
        z-index: 1;
    }
    .map-controls {
        position: absolute;
        /* top: 10px;
        right: 10px; */
        top: 180px;
        bottom: 10px;
        left: 10px;
        z-index: 1000;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        min-width: 250px;
    }
    .map-container {
        position: relative;
        margin: 20px 0;
    }
    .filter-group {
        margin-bottom: 15px;
    }
    .filter-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        font-size: 14px;
        color: #374151;
    }
    .filter-group select {
        width: 100%;
        padding: 8px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 14px;
    }
    .legend {
        background: white;
        padding: 10px;
        border-radius: 4px;
        margin-top: 10px;
        font-size: 12px;
    }
    .legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
    }
    .legend-color {
        width: 20px;
        height: 20px;
        margin-right: 8px;
        border-radius: 3px;
    }
</style>
@endpush

@section('content')
<!-- <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"> -->
<div class="max-f mx-auto px-4 sm:px-6 lg:px-8 py-8 bg-blue-600">
    <div class="mb-6 text-center text-white">
        <!-- <h1 class="text-3xl font-bold  mb-2">Peta Interaktif</h1> -->
        <!-- <p class="text-sm">Visualisasi zona risiko bencana, rute evakuasi, fasilitas evakuasi, dan titik distribusi bantuan</p> -->
    </div>

    <div class="map-container">
        <div class="map-controls">
            <h3 class="text-lg font-semibold mb-4">Filter Peta</h3>
            
            <div class="filter-group">
                <label for="disaster_type">Jenis Bencana</label>
                <select id="disaster_type" name="disaster_type">
                    <option value="all" {{ $disasterType === 'all' ? 'selected' : '' }}>Semua Jenis</option>
                    <option value="longsor" {{ $disasterType === 'longsor' ? 'selected' : '' }}>Longsor</option>
                    <option value="banjir" {{ $disasterType === 'banjir' ? 'selected' : '' }}>Banjir</option>
                    <!-- <option value="other" {{ $disasterType === 'other' ? 'selected' : '' }}>Lainnya</option> -->
                </select>
            </div>

            <div class="filter-group">
                <label for="risk_level">Tingkat Risiko</label>
                <select id="risk_level" name="risk_level">
                    <option value="all" {{ $riskLevel === 'all' ? 'selected' : '' }}>Semua Tingkat</option>
                    <option value="low" {{ $riskLevel === 'low' ? 'selected' : '' }}>Rendah</option>
                    <option value="medium" {{ $riskLevel === 'medium' ? 'selected' : '' }}>Sedang</option>
                    <option value="high" {{ $riskLevel === 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="very_high" {{ $riskLevel === 'very_high' ? 'selected' : '' }}>Sangat Tinggi</option>
                </select>
            </div>

            <div class="legend">
                <div class="font-semibold mb-2">Legenda</div>
                <div class="legend-item">
<<<<<<< HEAD
                    <div class="legend-color" style="background-color: #ef4444;"></div>
                    <span>Area Bencana</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #fde68a; border: 1px dashed #facc15;"></div>
                    <span>Batas Kecamatan</span>
=======
                    <div class="legend-color" style="background-color: #e91717ff;"></div>
                    <span>Zona Bencana</span>
>>>>>>> main
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #3b82f6;"></div>
                    <span>Rute Evakuasi</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background-color: #10b981;"></div>
                    <span>Titik Kumpul</span>
                </div>
<<<<<<< HEAD
                
                <div class="mt-3">
                    <label class="inline-flex items-center text-xs text-gray-700">
                        <input type="checkbox" id="toggle_district_boundaries" class="mr-2" checked>
                        Tampilkan Batas Kecamatan
                    </label>
                </div>
=======
                <!-- <div class="legend-item">
                    <div class="w-5 h-5 mr-2 flex items-center justify-center"></div>
                    <span>Data Bantuan Bencana</span>
                </div> -->
>>>>>>> main
            </div>
        </div>

        <div id="map"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Initialize map centered on Bone Bolango, Gorontalo
    const map = L.map('map').setView([0.5, 123.2], 11);

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Store layers
    const layers = {
        disasterZones: L.layerGroup().addTo(map),
        evacuationRoutes: L.layerGroup().addTo(map),
        evacuationFacilities: L.layerGroup().addTo(map),
<<<<<<< HEAD
        districtBoundaries: L.layerGroup().addTo(map),
        aidDistributionPoints: L.layerGroup().addTo(map)
=======
        // aidDistributionPoints: L.layerGroup().addTo(map)
>>>>>>> main
    };

    // Function to load map data
    function loadMapData() {
        const disasterType = document.getElementById('disaster_type').value;
        const riskLevel = document.getElementById('risk_level').value;
        const districtToggle = document.getElementById('toggle_district_boundaries');
        
        const url = new URL('{{ route("map.data") }}', window.location.origin);
        if (disasterType !== 'all') url.searchParams.append('disaster_type', disasterType);
        if (riskLevel !== 'all') url.searchParams.append('risk_level', riskLevel);

        // Clear existing layers
        Object.values(layers).forEach(layer => layer.clearLayers());

        fetch(url)
            .then(response => response.json())
            .then(data => {
                // Add disaster zones (points / marker merah)
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

                // Add evacuation routes (lines)
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

                // Add evacuation facilities (points)
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

                // Add district boundaries (batas administrasi kecamatan dari GeoJSON + DB aid_disasters)
                if (data.district_boundaries && data.district_boundaries.features.length > 0) {
                    data.district_boundaries.features.forEach(feature => {
                        // MultiPolygon -> array tingkat paling dalam adalah [lng, lat, (z?)]
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

                // Atur visibilitas layer batas kecamatan sesuai toggle
                if (districtToggle && !districtToggle.checked) {
                    map.removeLayer(layers.districtBoundaries);
                } else {
                    layers.districtBoundaries.addTo(map);
                }

                // Add aid disasters data (statistik per kecamatan — tanpa koordinat)
                // Data ini ditampilkan sebagai info panel, bukan marker di peta
                if (data.aid_disasters && data.aid_disasters.features.length > 0) {
                    console.log('Data bantuan bencana:', data.aid_disasters.features.length, 'kecamatan');
                }

                // Fit map to bounds if there are features
                if (data.disaster_zones.features.length > 0 || 
                    data.evacuation_routes.features.length > 0 ||
                    data.evacuation_facilities.features.length > 0) {
                    const allBounds = [];
                    Object.values(layers).forEach(layer => {
                        layer.eachLayer(l => {
                            if (l.getBounds) {
                                allBounds.push(l.getBounds());
                            } else if (l.getLatLng) {
                                allBounds.push([[l.getLatLng().lat, l.getLatLng().lng], [l.getLatLng().lat, l.getLatLng().lng]]);
                            }
                        });
                    });
                    if (allBounds.length > 0) {
                        const group = new L.featureGroup(Object.values(layers).flatMap(l => Array.from(l.getLayers())));
                        map.fitBounds(group.getBounds().pad(0.1));
                    }
                }
            })
            .catch(error => {
                console.error('Error loading map data:', error);
            });
    }

    // Load initial data
    loadMapData();

    // Add event listeners to filters
    document.getElementById('disaster_type').addEventListener('change', loadMapData);
    document.getElementById('risk_level').addEventListener('change', loadMapData);

    // Toggle batas kecamatan on/off
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
</script>
@endpush

