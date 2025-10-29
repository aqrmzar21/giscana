<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Giscana') }} - @yield('title', 'Geographic Information System for Natural Disaster Mitigation')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- TailwindCSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="font-sans antialiased">
    {{-- <div class="min-h-screen bg-gray-50"> --}}
{{-- <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">

    <div class="app-brand demo">
        <a href="{{url('/')}}" class="app-brand-link">
            <span class="app-brand-text demo menu-text fw-bold ms-2">{{config('variables.templateName')}}</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="icon-base bx bx-chevron-left icon-sm d-flex align-items-center justify-content-center"></i>
        </a>
    </div>

    <div class="menu-divider mt-0"></div>
    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">MenuHeader</span>
        </li>

    </ul>

</aside> --}}


    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-80 bg-white shadow-lg overflow-y-auto">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    <svg class="w-8 h-8 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-gray-700">
                        Giscana
                    </a>
                </h2>
                
                <!-- Search -->
                {{-- <div class="mb-6">
                    <label for="search-input" class="block text-sm font-medium text-gray-700 mb-2">Search Location</label>
                    <div class="relative">
                        <input type="text" id="search-input" placeholder="Search for locations..." 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <div id="search-results" class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg hidden"></div>
                    </div>
                </div> --}}

                <!-- Layer Controls -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Map Layers</h3>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" id="layer-disaster-zones" checked class="mr-2">
                            <span class="text-sm text-gray-700">Disaster Zones</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="layer-evacuation-routes" checked class="mr-2">
                            <span class="text-sm text-gray-700">Evacuation Routes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="layer-evacuation-facilities" checked class="mr-2">
                            <span class="text-sm text-gray-700">Evacuation Facilities</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" id="layer-aid-points" checked class="mr-2">
                            <span class="text-sm text-gray-700">Aid Distribution Points</span>
                        </label>
                    </div>
                </div>

                <!-- Legend -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Legend</h3>
                    
                    <!-- Disaster Zones -->
                    <div class="mb-4">
                        <h4 class="text-xs font-medium text-gray-600 mb-2">Disaster Risk Levels</h4>
                        <div class="space-y-1">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-green-500 rounded mr-2"></div>
                                <span class="text-xs text-gray-600">Low Risk</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                                <span class="text-xs text-gray-600">Medium Risk</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-orange-500 rounded mr-2"></div>
                                <span class="text-xs text-gray-600">High Risk</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                                <span class="text-xs text-gray-600">Critical Risk</span>
                            </div>
                        </div>
                    </div>

                    <!-- Evacuation Routes -->
                    <div class="mb-4">
                        <h4 class="text-xs font-medium text-gray-600 mb-2">Evacuation Routes</h4>
                        <div class="space-y-1">
                            <div class="flex items-center">
                                <div class="w-4 h-1 bg-blue-600 mr-2"></div>
                                <span class="text-xs text-gray-600">Primary Route</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-1 bg-blue-400 mr-2"></div>
                                <span class="text-xs text-gray-600">Secondary Route</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-1 bg-red-600 mr-2"></div>
                                <span class="text-xs text-gray-600">Emergency Route</span>
                            </div>
                        </div>
                    </div>

                    <!-- Facilities -->
                    <div class="mb-4">
                        <h4 class="text-xs font-medium text-gray-600 mb-2">Facilities</h4>
                        <div class="space-y-1">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-600 rounded-full mr-2"></div>
                                <span class="text-xs text-gray-600">Evacuation Facility</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-purple-600 rounded-full mr-2"></div>
                                <span class="text-xs text-gray-600">Aid Distribution</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Statistics</h3>
                    <div class="space-y-2 text-xs text-gray-600">
                        <div class="flex justify-between">
                            <span>Disaster Zones:</span>
                            <span id="stats-disaster-zones">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Evacuation Routes:</span>
                            <span id="stats-evacuation-routes">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Evacuation Facilities:</span>
                            <span id="stats-evacuation-facilities">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Aid Points:</span>
                            <span id="stats-aid-points">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div class="flex-1 relative">
            <!-- Filter Controls -->
            <div class="absolute top-4 left-4 z-50 bg-white bg-opacity-90 rounded-lg shadow-lg p-4 flex space-x-4">
                <div>
                    <label for="disaster-type-filter" class="block text-xs font-medium text-gray-700 mb-1">Disaster Type</label>
                    <select id="disaster-type-filter" class="block w-32 px-2 py-1 border border-gray-300 rounded-md text-xs">
                        <option value="all">All Types</option>
                        <option value="flood">Flood</option>
                        <option value="earthquake">Earthquake</option>
                        <option value="landslide">Landslide</option>
                        <option value="tsunami">Tsunami</option>
                        <!-- Tambahkan jenis lain sesuai kebutuhan -->
                    </select>
                </div>
                <div>
                    <label for="risk-level-filter" class="block text-xs font-medium text-gray-700 mb-1">Risk Level</label>
                    <select id="risk-level-filter" class="block w-32 px-2 py-1 border border-gray-300 rounded-md text-xs">
                        <option value="all">All Levels</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
            </div>
            <div id="map" class="w-full h-full"></div>
            <!-- Map Loading Indicator -->
            <div id="map-loading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600 mx-auto mb-2"></div>
                    <p class="text-sm text-gray-600">Loading map data...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Info Modal -->
    <div id="feature-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900" id="modal-title">Feature Information</h3>
                    <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="modal-content" class="text-sm text-gray-600">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

 {{-- </div> --}}
</body>
</html>

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
