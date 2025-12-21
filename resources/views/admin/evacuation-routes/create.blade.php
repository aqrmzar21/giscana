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
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Rute <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="disaster_type" class="block text-sm font-medium text-gray-700">Jenis Bencana <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <select id="disaster_type" name="disaster_type" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('disaster_type') border-red-300 @enderror">
                                <option value="">Pilih Jenis Bencana</option>
                                <option value="tsunami" {{ old('disaster_type') === 'tsunami' ? 'selected' : '' }}>Tsunami</option>
                                <option value="flood" {{ old('disaster_type') === 'flood' ? 'selected' : '' }}>Banjir</option>
                                <option value="both" {{ old('disaster_type') === 'both' ? 'selected' : '' }}>Keduanya</option>
                            </select>
                            @error('disaster_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="route_type" class="block text-sm font-medium text-gray-700">Tipe Rute <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <select id="route_type" name="route_type" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('route_type') border-red-300 @enderror">
                                <option value="">Pilih Tipe Rute</option>
                                <option value="road" {{ old('route_type') === 'road' ? 'selected' : '' }}>Jalan</option>
                                <option value="path" {{ old('route_type') === 'path' ? 'selected' : '' }}>Jalan Setapak</option>
                                <option value="waterway" {{ old('route_type') === 'waterway' ? 'selected' : '' }}>Jalur Air</option>
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

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="length_km" class="block text-sm font-medium text-gray-700">Panjang (km)</label>
                        <div class="mt-1">
                            <input type="number" step="0.01" name="length_km" id="length_km" value="{{ old('length_km') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('length_km') border-red-300 @enderror">
                            @error('length_km')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="capacity_per_hour" class="block text-sm font-medium text-gray-700">Kapasitas per Jam</label>
                        <div class="mt-1">
                            <input type="number" name="capacity_per_hour" id="capacity_per_hour" value="{{ old('capacity_per_hour') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('capacity_per_hour') border-red-300 @enderror">
                            @error('capacity_per_hour')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="line_coordinates" class="block text-sm font-medium text-gray-700">Koordinat Garis (GeoJSON) <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <textarea id="line_coordinates" name="line_coordinates" rows="5" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md font-mono text-xs @error('line_coordinates') border-red-300 @enderror">{{ old('line_coordinates') }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">Format: JSON array of coordinates [[lng, lat], [lng, lat], ...]</p>
                        @error('line_coordinates')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
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
