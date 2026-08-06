@extends('layouts.admin')

@section('title', 'Tambah Titik Kumpul - Admin')

@section('page-title', 'Tambah Titik Kumpul Baru')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.evacuation-facilities.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Titik Kumpul</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Tambah Baru</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Form Tambah Titik Kumpul</h3>
        
        <form action="{{ route('admin.evacuation-facilities.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label for="aid_disaster_id" class="block text-sm font-medium text-gray-700">Kecamatan (Bantuan Bencana)</label>
                    <div class="mt-1">
                        <select name="aid_disaster_id" id="aid_disaster_id" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('aid_disaster_id') border-red-300 @enderror">
                            <option value="">-- Pilih Kecamatan --</option>
                            @foreach($aidDisasters as $ad)
                                <option value="{{ $ad->id }}" {{ old('aid_disaster_id') == $ad->id ? 'selected' : '' }}>{{ $ad->district_name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-sm text-gray-500">Nama kecamatan diambil dari data Bantuan Bencana (aid_disasters).</p>
                        @error('aid_disaster_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Fasilitas <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <div class="mt-1">
                        <textarea id="address" name="address" rows="2" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('address') border-red-300 @enderror">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700">Kontak Person</label>
                        <div class="mt-1">
                            <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('contact_person') border-red-300 @enderror">
                            @error('contact_person')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                        <div class="mt-1">
                            <input type="number" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('contact_phone') border-red-300 @enderror">
                            @error('contact_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700">Kapasitas</label>
                    <div class="mt-1">
                        <input type="text" name="capacity" id="capacity" value="{{ old('capacity') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('capacity') border-red-300 @enderror">
                        @error('capacity')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div> -->

                <div>
                    <label class="block text-sm font-medium text-gray-700">Titik Koordinat Fasilitas <span class="text-red-500">*</span></label>
                    <p class="mt-1 text-sm text-gray-500 mb-2">Klik pada peta untuk menentukan lokasi fasilitas evakuasi.</p>
                    <div id="map" class="mb-4 border border-gray-300"></div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div>
                            <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                            <input type="text" id="latitude" readonly class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md bg-gray-50">
                        </div>
                        <div>
                            <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                            <input type="text" id="longitude" readonly class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md bg-gray-50">
                        </div>
                    </div>
                    
                    <input type="hidden" id="point_coordinates" name="point_coordinates" value="{{ old('point_coordinates') }}">
                    @error('point_coordinates')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

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
                <a href="{{ route('admin.evacuation-facilities.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    (function () {
        if (typeof L === 'undefined') {
            console.error('Leaflet is not loaded');
            return;
        }

        // Initialize map centered at default location (Gorontalo area)
        var map = L.map('map').setView([0.545, 123.06], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marker;

        // Check if there is old data from validation errors
        var oldCoordinates = document.getElementById('point_coordinates').value;
        if (oldCoordinates) {
            try {
                var coords = JSON.parse(oldCoordinates);
                if (Array.isArray(coords) && coords.length >= 2) {
                    var lng = coords[0];
                    var lat = coords[1];
                    marker = L.marker([lat, lng]).addTo(map);
                    map.setView([lat, lng], 14);
                    
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                }
            } catch (e) {
                console.error("Invalid coordinates format.");
            }
        }

        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            if (marker) {
                map.removeLayer(marker);
            }

            marker = L.marker([lat, lng]).addTo(map);
            
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            
            // Format expected by backend: JSON array [lng, lat]
            document.getElementById('point_coordinates').value = JSON.stringify([lng, lat]);
        });
    })();
</script>
@endsection
