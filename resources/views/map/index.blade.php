@extends('layouts.app') {{-- Make sure this layout uses AdminLTE 3 base structure --}}

@section('title', 'Interactive Disaster Risk Map')

@section('content')
<div class="row">
    <!-- Sidebar -->
    <div class="col-md-3">
        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title">Map Controls</h3>
            </div>
            <div class="card-body">
                <!-- Search -->
                <div class="form-group">
                    <label for="search-input">Search Location</label>
                    <input type="text" id="search-input" class="form-control" placeholder="Search for locations...">
                    <div id="search-results" class="position-absolute w-100 mt-1 bg-white border rounded shadow d-none"></div>
                </div>

                <!-- Layer Controls -->
                <div class="mt-4">
                    <label class="font-weight-bold">Map Layers</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="layer-disaster-zones" checked>
                        <label class="form-check-label" for="layer-disaster-zones">Disaster Zones</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="layer-evacuation-routes" checked>
                        <label class="form-check-label" for="layer-evacuation-routes">Evacuation Routes</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="layer-evacuation-facilities" checked>
                        <label class="form-check-label" for="layer-evacuation-facilities">Evacuation Facilities</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="layer-aid-points" checked>
                        <label class="form-check-label" for="layer-aid-points">Aid Distribution Points</label>
                    </div>
                </div>

                <!-- Legend -->
                <div class="mt-4">
                    <label class="font-weight-bold">Legend</label>
                    <small class="d-block text-muted mb-2">Disaster Risk Levels</small>
                    <div class="d-flex align-items-center mb-1"><div class="bg-success rounded mr-2" style="width:16px; height:16px;"></div>Low Risk</div>
                    <div class="d-flex align-items-center mb-1"><div class="bg-warning rounded mr-2" style="width:16px; height:16px;"></div>Medium Risk</div>
                    <div class="d-flex align-items-center mb-1"><div class="bg-orange rounded mr-2" style="width:16px; height:16px;"></div>High Risk</div>
                    <div class="d-flex align-items-center mb-1"><div class="bg-danger rounded mr-2" style="width:16px; height:16px;"></div>Critical Risk</div>

                    <small class="d-block text-muted mt-3 mb-2">Evacuation Routes</small>
                    <div class="d-flex align-items-center mb-1"><div class="bg-primary mr-2" style="width:24px; height:4px;"></div>Primary Route</div>
                    <div class="d-flex align-items-center mb-1"><div class="bg-info mr-2" style="width:24px; height:4px;"></div>Secondary Route</div>
                    <div class="d-flex align-items-center mb-1"><div class="bg-danger mr-2" style="width:24px; height:4px;"></div>Emergency Route</div>

                    <small class="d-block text-muted mt-3 mb-2">Facilities</small>
                    <div class="d-flex align-items-center mb-1"><div class="bg-success rounded-circle mr-2" style="width:12px; height:12px;"></div>Evacuation Facility</div>
                    <div class="d-flex align-items-center mb-1"><div class="bg-purple rounded-circle mr-2" style="width:12px; height:12px;"></div>Aid Distribution</div>
                </div>

                <!-- Statistics -->
                <div class="mt-4">
                    <label class="font-weight-bold">Statistics</label>
                    <ul class="list-unstyled text-muted small">
                        <li class="d-flex justify-content-between"><span>Disaster Zones:</span><span id="stats-disaster-zones">0</span></li>
                        <li class="d-flex justify-content-between"><span>Evacuation Routes:</span><span id="stats-evacuation-routes">0</span></li>
                        <li class="d-flex justify-content-between"><span>Evacuation Facilities:</span><span id="stats-evacuation-facilities">0</span></li>
                        <li class="d-flex justify-content-between"><span>Aid Points:</span><span id="stats-aid-points">0</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="col-md-9 position-relative">
        <div id="map" class="w-100" style="height: calc(100vh - 100px);"></div>

        <!-- Map Loading Indicator -->
        <div id="map-loading" class="position-absolute w-100 h-100 bg-white bg-opacity-75 d-flex align-items-center justify-content-center" style="top:0; left:0; z-index:999;">
            <div class="text-center">
                <div class="spinner-border text-primary mb-2" role="status"></div>
                <p class="text-muted">Loading map data...</p>
            </div>
        </div>
    </div>
</div>

<!-- Feature Info Modal -->
<div id="feature-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Feature Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-content">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize map when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeMap();
        setupEventListeners();
        loadMapData();
    });

    let map;
    let mapLayers = {
        disasterZones: null,
        evacuationRoutes: null,
        evacuationFacilities: null,
        aidPoints: null
    };

    function initializeMap() {
        // Initialize map centered on Bone Bolango Regency, Gorontalo
        map = L.map('map').setView([0.5, 123.2], 10);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Initialize layer groups
        mapLayers.disasterZones = L.layerGroup().addTo(map);
        mapLayers.evacuationRoutes = L.layerGroup().addTo(map);
        mapLayers.evacuationFacilities = L.layerGroup().addTo(map);
        mapLayers.aidPoints = L.layerGroup().addTo(map);
    }

    function setupEventListeners() {
        // Filter controls
        document.getElementById('disaster-type-filter').addEventListener('change', function() {
            loadMapData();
        });

        document.getElementById('risk-level-filter').addEventListener('change', function() {
            loadMapData();
        });

        // Layer controls
        document.getElementById('layer-disaster-zones').addEventListener('change', function() {
            toggleLayer('disasterZones', this.checked);
        });

        document.getElementById('layer-evacuation-routes').addEventListener('change', function() {
            toggleLayer('evacuationRoutes', this.checked);
        });

        document.getElementById('layer-evacuation-facilities').addEventListener('change', function() {
            toggleLayer('evacuationFacilities', this.checked);
        });

        document.getElementById('layer-aid-points').addEventListener('change', function() {
            toggleLayer('aidPoints', this.checked);
        });

        // Search functionality
        let searchTimeout;
        document.getElementById('search-input').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(this.value);
            }, 300);
        });

        // Modal controls
        document.getElementById('close-modal').addEventListener('click', function() {
            document.getElementById('feature-modal').classList.add('hidden');
        });

        // Close modal when clicking outside
        document.getElementById('feature-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    }

    function loadMapData() {
        const disasterType = document.getElementById('disaster-type-filter').value;
        const riskLevel = document.getElementById('risk-level-filter').value;
        
        const params = new URLSearchParams();
        if (disasterType !== 'all') params.append('disaster_type', disasterType);
        if (riskLevel !== 'all') params.append('risk_level', riskLevel);

        fetch(`/map/data?${params}`)
            .then(response => response.json())
            .then(data => {
                clearMapLayers();
                addDisasterZones(data.disaster_zones);
                addEvacuationRoutes(data.evacuation_routes);
                addEvacuationFacilities(data.evacuation_facilities);
                addAidPoints(data.aid_distribution_points);
                updateStatistics(data);
                hideLoadingIndicator();
            })
            .catch(error => {
                console.error('Error loading map data:', error);
                hideLoadingIndicator();
            });
    }

    function clearMapLayers() {
        Object.values(mapLayers).forEach(layer => {
            layer.clearLayers();
        });
    }

    function addDisasterZones(geoJsonData) {
        if (!geoJsonData.features) return;

        geoJsonData.features.forEach(feature => {
            const properties = feature.properties;
            const coordinates = feature.geometry.coordinates[0];
            
            // Create polygon
            const polygon = L.polygon(coordinates.map(coord => [coord[1], coord[0]]), {
                color: getRiskLevelColor(properties.risk_level),
                fillColor: getRiskLevelColor(properties.risk_level),
                fillOpacity: 0.3,
                weight: 2
            });

            polygon.bindPopup(`
                <div class="p-2">
                    <h3 class="font-semibold text-lg">${properties.name}</h3>
                    <p class="text-sm text-gray-600">${properties.disaster_type}</p>
                    <p class="text-sm">Risk Level: <span class="font-medium">${properties.risk_level}</span></p>
                    ${properties.area_hectares ? `<p class="text-sm">Area: ${properties.area_hectares} hectares</p>` : ''}
                    ${properties.affected_population ? `<p class="text-sm">Affected Population: ${properties.affected_population}</p>` : ''}
                </div>
            `);

            polygon.on('click', () => showFeatureModal('Disaster Zone', properties));
            mapLayers.disasterZones.addLayer(polygon);
        });
    }

    function addEvacuationRoutes(geoJsonData) {
        if (!geoJsonData.features) return;

        geoJsonData.features.forEach(feature => {
            const properties = feature.properties;
            const coordinates = feature.geometry.coordinates;
            
            // Create polyline
            const polyline = L.polyline(coordinates.map(coord => [coord[1], coord[0]]), {
                color: getRouteTypeColor(properties.route_type),
                weight: 4,
                opacity: 0.8
            });

            polyline.bindPopup(`
                <div class="p-2">
                    <h3 class="font-semibold text-lg">${properties.name}</h3>
                    <p class="text-sm text-gray-600">${properties.route_type} route</p>
                    <p class="text-sm">Disaster Type: ${properties.disaster_type}</p>
                    ${properties.length_km ? `<p class="text-sm">Length: ${properties.length_km} km</p>` : ''}
                    ${properties.capacity_per_hour ? `<p class="text-sm">Capacity: ${properties.capacity_per_hour} people/hour</p>` : ''}
                </div>
            `);

            polyline.on('click', () => showFeatureModal('Evacuation Route', properties));
            mapLayers.evacuationRoutes.addLayer(polyline);
        });
    }

    function addEvacuationFacilities(geoJsonData) {
        if (!geoJsonData.features) return;

        geoJsonData.features.forEach(feature => {
            const properties = feature.properties;
            const coordinates = feature.geometry.coordinates;
            
            // Create marker
            const marker = L.marker([coordinates[1], coordinates[0]], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div class="w-4 h-4 bg-green-600 rounded-full border-2 border-white"></div>',
                    iconSize: [16, 16]
                })
            });

            marker.bindPopup(`
                <div class="p-2">
                    <h3 class="font-semibold text-lg">${properties.name}</h3>
                    <p class="text-sm text-gray-600">${properties.facility_type}</p>
                    ${properties.address ? `<p class="text-sm">Address: ${properties.address}</p>` : ''}
                    ${properties.capacity ? `<p class="text-sm">Capacity: ${properties.capacity} people</p>` : ''}
                    ${properties.contact_person ? `<p class="text-sm">Contact: ${properties.contact_person}</p>` : ''}
                    ${properties.contact_phone ? `<p class="text-sm">Phone: ${properties.contact_phone}</p>` : ''}
                </div>
            `);

            marker.on('click', () => showFeatureModal('Evacuation Facility', properties));
            mapLayers.evacuationFacilities.addLayer(marker);
        });
    }

    function addAidPoints(geoJsonData) {
        if (!geoJsonData.features) return;

        geoJsonData.features.forEach(feature => {
            const properties = feature.properties;
            const coordinates = feature.geometry.coordinates;
            
            // Create marker
            const marker = L.marker([coordinates[1], coordinates[0]], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div class="w-4 h-4 bg-purple-600 rounded-full border-2 border-white"></div>',
                    iconSize: [16, 16]
                })
            });

            marker.bindPopup(`
                <div class="p-2">
                    <h3 class="font-semibold text-lg">${properties.name}</h3>
                    <p class="text-sm text-gray-600">${properties.aid_type} aid</p>
                    ${properties.address ? `<p class="text-sm">Address: ${properties.address}</p>` : ''}
                    ${properties.capacity_per_day ? `<p class="text-sm">Daily Capacity: ${properties.capacity_per_day}</p>` : ''}
                    ${properties.contact_person ? `<p class="text-sm">Contact: ${properties.contact_person}</p>` : ''}
                    ${properties.contact_phone ? `<p class="text-sm">Phone: ${properties.contact_phone}</p>` : ''}
                </div>
            `);

            marker.on('click', () => showFeatureModal('Aid Distribution Point', properties));
            mapLayers.aidPoints.addLayer(marker);
        });
    }

    function getRiskLevelColor(riskLevel) {
        const colors = {
            'low': '#10B981',
            'medium': '#F59E0B',
            'high': '#F97316',
            'critical': '#EF4444'
        };
        return colors[riskLevel] || '#6B7280';
    }

    function getRouteTypeColor(routeType) {
        const colors = {
            'primary': '#2563EB',
            'secondary': '#60A5FA',
            'emergency': '#DC2626'
        };
        return colors[routeType] || '#6B7280';
    }

    function toggleLayer(layerName, visible) {
        if (visible) {
            map.addLayer(mapLayers[layerName]);
        } else {
            map.removeLayer(mapLayers[layerName]);
        }
    }

    function performSearch(query) {
        if (query.length < 2) {
            document.getElementById('search-results').classList.add('hidden');
            return;
        }

        fetch(`/map/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                displaySearchResults(data.results);
            })
            .catch(error => {
                console.error('Search error:', error);
            });
    }

    function displaySearchResults(results) {
        const resultsContainer = document.getElementById('search-results');
        
        if (results.length === 0) {
            resultsContainer.innerHTML = '<div class="p-2 text-sm text-gray-500">No results found</div>';
        } else {
            resultsContainer.innerHTML = results.map(result => `
                <div class="p-2 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" 
                     onclick="goToLocation(${JSON.stringify(result.coordinates).replace(/"/g, '&quot;')})">
                    <div class="font-medium text-sm">${result.name}</div>
                    <div class="text-xs text-gray-500">${result.type.replace('_', ' ')}</div>
                </div>
            `).join('');
        }
        
        resultsContainer.classList.remove('hidden');
    }

    function goToLocation(coordinates) {
        if (coordinates && coordinates.length === 2) {
            map.setView([coordinates[1], coordinates[0]], 15);
            document.getElementById('search-results').classList.add('hidden');
        }
    }

    function showFeatureModal(title, properties) {
        document.getElementById('modal-title').textContent = title;
        
        const content = Object.entries(properties)
            .filter(([key, value]) => value !== null && value !== '')
            .map(([key, value]) => `
                <div class="mb-2">
                    <span class="font-medium text-gray-700">${key.replace(/_/g, ' ')}:</span>
                    <span class="text-gray-600">${value}</span>
                </div>
            `).join('');
        
        document.getElementById('modal-content').innerHTML = content;
        document.getElementById('feature-modal').classList.remove('hidden');
    }

    function updateStatistics(data) {
        document.getElementById('stats-disaster-zones').textContent = data.disaster_zones.features.length;
        document.getElementById('stats-evacuation-routes').textContent = data.evacuation_routes.features.length;
        document.getElementById('stats-evacuation-facilities').textContent = data.evacuation_facilities.features.length;
        document.getElementById('stats-aid-points').textContent = data.aid_distribution_points.features.length;
    }

    function hideLoadingIndicator() {
        document.getElementById('map-loading').style.display = 'none';
    }
</script>
@endpush
