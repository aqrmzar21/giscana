@extends('layouts.admin')

@section('title', 'Tambah Rute Evakuasi - Admin')

@section('page-title', 'Tambah Rute Evakuasi Baru')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.evacuation-routes.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Rute Evakuasi</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Tambah Baru</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Form Tambah Rute Evakuasi</h3>
        
        <form action="{{ route('admin.evacuation-routes.store') }}" method="POST">
            @csrf
            <div class="space-y-6">

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                    <div>
                        <label for="evacuation_facility_id" class="block text-sm font-medium text-gray-700">Fasilitas Tujuan</label>
                        <div class="mt-1">
                            <select name="evacuation_facility_id" id="evacuation_facility_id"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('evacuation_facility_id') border-red-300 @enderror">
                                <option value="">-- Pilih Fasilitas Evakuasi --</option>
                                @foreach($facilities as $f)
                                    <option value="{{ $f->id }}"
                                        data-coords='@json($f->point_coordinates)'
                                        {{ old('evacuation_facility_id', $evacuationRoute->evacuation_facility_id ?? '') == $f->id ? 'selected' : '' }}>
                                        {{ $f->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-sm text-gray-500">Nama fasilitas diambil dari Titik Kumpul</p>
                            @error('evacuation_facility_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Rute <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 @enderror">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="route_type" class="block text-sm font-medium text-gray-700">Tipe Rute <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <select id="route_type" name="route_type" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('route_type') border-red-300 @enderror">
                                <option value="">Pilih Tipe Rute</option>
                                <option value="primary" {{ old('route_type') === 'primary' ? 'selected' : '' }}>Utama</option>
                                <option value="secondary" {{ old('route_type') === 'secondary' ? 'selected' : '' }}>Sekunder</option>
                                <option value="emergency" {{ old('route_type') === 'emergency' ? 'selected' : '' }}>Darurat</option>
                            </select>
                            @error('route_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <div class="mt-1">
                        <textarea id="description" name="description" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- <div>
                    <label for="line_coordinates" class="block text-sm font-medium text-gray-700">Koordinat Garis (GeoJSON) <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <textarea id="line_coordinates" name="line_coordinates" rows="5" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md font-mono text-xs @error('line_coordinates') border-red-300 @enderror">{{ old('line_coordinates') }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">Format: JSON array of coordinates [[lng, lat], [lng, lat], ...]</p>
                        @error('line_coordinates')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div> -->
                
                <div>
                    <label for="line_coordinates" class="block text-sm font-medium text-gray-700">
                        Koordinat Garis (GeoJSON) <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <textarea id="line_coordinates" name="line_coordinates" rows="5" required
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md font-mono text-xs @error('line_coordinates') border-red-300 @enderror">
                            {{ old('line_coordinates', $route->line_coordinates ?? '') }}
                        </textarea>
                        <p class="mt-2 text-sm text-gray-500">
                            Format: JSON FeatureCollection dengan LineString
                        </p>
                        @error('line_coordinates')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div id="map" style="height: 400px;" class="mt-4 rounded-md border"></div>
                <button type="button" id="resetRoute"
                    class="mt-3 bg-red-500 hover:bg-red-600 text-white text-sm px-3 py-1 rounded">
                    Reset Jalur
                </button>

                <script>
                    var map = L.map('map').setView([0.4681485, 123.126115], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);

                    var points = [];
                    var markers = [];
                    var polyline;
                    var facilityMarker; // marker khusus fasilitas tujuan

                    // Jika ada data lama, tampilkan jalur
                    var existingCoords = {!! json_encode($route->line_coordinates ?? []) !!};
                    if (existingCoords.length > 0) {
                        points = existingCoords;

                        points.forEach(function(coord, idx) {
                            var marker = L.marker([coord[1], coord[0]], {draggable:true}).addTo(map);
                            marker.on('dragend', function(e) {
                                var latlng = e.target.getLatLng();
                                points[idx] = [latlng.lng, latlng.lat];
                                updatePolyline();
                            });
                            markers.push(marker);
                        });
                        updatePolyline();
                        map.fitBounds(polyline.getBounds());
                    }

                    // Klik peta untuk tambah titik jalur
                    map.on('click', function(e) {
                        var lat = e.latlng.lat;
                        var lng = e.latlng.lng;

                        points.push([lng, lat]);

                        var marker = L.marker([lat, lng], {draggable:true}).addTo(map);
                        marker.on('dragend', function(ev) {
                            var latlng = ev.target.getLatLng();
                            var idx = markers.indexOf(marker);
                            points[idx] = [latlng.lng, latlng.lat];
                            updatePolyline();
                        });
                        markers.push(marker);

                        updatePolyline();
                    });

                    // Update polyline & textarea
                    function updatePolyline() {
                        if (polyline) {
                            map.removeLayer(polyline);
                        }
                        polyline = L.polyline(points.map(p => [p[1], p[0]]), {color: 'blue'}).addTo(map);

                        document.getElementById('line_coordinates').value = JSON.stringify(points, null, 2);
                    }

                    // Reset jalur
                    document.getElementById('resetRoute').addEventListener('click', function() {
                        points = [];
                        markers.forEach(m => map.removeLayer(m));
                        markers = [];
                        if (polyline) {
                            map.removeLayer(polyline);
                            polyline = null;
                        }
                        document.getElementById('line_coordinates').value = '';
                    });

                    // Tambahkan marker fasilitas tujuan saat dipilih
                    document.getElementById('evacuation_facility_id').addEventListener('change', function() {
                        var selected = this.options[this.selectedIndex];
                        var coords = selected.getAttribute('data-coords');
                        if (coords) {
                            var point = JSON.parse(coords); // [lng, lat]

                            // Hapus marker fasilitas lama
                            if (facilityMarker) {
                                map.removeLayer(facilityMarker);
                            }

                            // Tambahkan marker fasilitas (tidak draggable)
                            facilityMarker = L.marker([point[1], point[0]], {
                                icon: L.icon({
                                    iconUrl: '/images/facility.png', // ganti sesuai ikon
                                    iconSize: [25, 25]
                                })
                            }).addTo(map).bindPopup("Titik Kumpul: " + selected.text);

                            map.setView([point[1], point[0]], 15);
                        }
                    });

                    // Trigger sekali saat load jika ada fasilitas terpilih
                    var selectedOption = document.querySelector('#evacuation_facility_id option:checked');
                    if (selectedOption && selectedOption.value) {
                        var coords = selectedOption.getAttribute('data-coords');
                        if (coords) {
                            var point = JSON.parse(coords);
                            facilityMarker = L.marker([point[1], point[0]]).addTo(map).bindPopup("Titik Kumpul: " + selectedOption.text);
                            map.setView([point[1], point[0]], 15);
                        }
                    }
                </script>


                <div class="space-y-3">
                    <div class="flex items-center">
                        <input id="is_accessible" name="is_accessible" type="checkbox" value="1" {{ old('is_accessible', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_accessible" class="ml-2 block text-sm text-gray-900">Aksesibel</label>
                    </div>
                    <div class="flex items-center">
                        <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif</label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.evacuation-routes.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

