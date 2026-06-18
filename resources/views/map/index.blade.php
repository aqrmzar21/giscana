@extends('layouts.landing-map')

@section('title', 'Peta Interaktif - Giscana')

@include('map.partials.map-styles', ['mapUiVariant' => 'landing-fs'])

@section('content')
<div class="map-landing-fs" id="map-landing-wrapper">
    <div id="map"></div>
</div>
@endsection
@section('map-toolbar')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
    <div class="flex flex-col lg:grid lg:grid-cols-12 lg:gap-x-0 lg:items-start">
        <!-- Text: Mobile Bottom (order 3), Desktop Top-Left -->
        <div class="order-3 lg:order-none lg:col-span-4 lg:col-start-1 lg:row-start-1 lg:pr-6 border-t border-gray-200 pt-5 mt-5 lg:border-t-0 lg:pt-0 lg:mt-0">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kontrol peta</p>
        </div>

        <!-- Legend: Mobile Top (order 1), Desktop Bottom-Left -->
        <div class="order-1 lg:order-none lg:col-span-4 lg:col-start-1 lg:row-start-2 lg:pr-6 lg:mt-4">
            @include('map.partials.map-legend-content')
        </div>

        <!-- Controls: Mobile Middle (order 2), Desktop Right (spans 2 rows) -->
        <div class="order-2 lg:order-none lg:col-span-8 lg:col-start-5 lg:row-start-1 lg:row-span-2 min-w-0 space-y-3 lg:border-l border-gray-200 lg:pl-6 border-t border-gray-200 pt-5 mt-5 lg:border-t-0 lg:pt-0 lg:mt-0">
            @include('map.partials.map-controls-inner', ['toolbarContext' => 'footer', 'hideToolbarHeading' => false, 'hideLegend' => true])
        </div>
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
        districtBoundaries: L.layerGroup().addTo(map),
        aidDistributionPoints: L.layerGroup().addTo(map)
        // aidDistributionPoints: L.layerGroup().addTo(map)
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

@include('map.partials.map-scripts', ['mapUiVariant' => 'landing-fs'])
