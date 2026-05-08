@extends('layouts.admin')

@section('title', 'Tambah Zona Bencana - Admin')

@section('page-title', 'Tambah Zona Bencana Baru')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.disaster-zones.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Zona Bencana</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Tambah Baru</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Form Tambah Zona Bencana</h3>
        
        <form action="{{ route('admin.disaster-zones.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Zona <span class="text-red-500">*</span></label>
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
                                <option value="longsor" {{ old('disaster_type') === 'longsor' ? 'selected' : '' }}>Longsor</option>
                                <option value="banjir" {{ old('disaster_type') === 'banjir' ? 'selected' : '' }}>Banjir</option>
                                <option value="other" {{ old('disaster_type') === 'other' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('disaster_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="risk_level" class="block text-sm font-medium text-gray-700">Tingkat Risiko <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <select id="risk_level" name="risk_level" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('risk_level') border-red-300 @enderror">
                                <option value="">Pilih Tingkat Risiko</option>
                                <option value="low" {{ old('risk_level') === 'low' ? 'selected' : '' }}>Rendah</option>
                                <option value="medium" {{ old('risk_level') === 'medium' ? 'selected' : '' }}>Sedang</option>
                                <option value="high" {{ old('risk_level') === 'high' ? 'selected' : '' }}>Tinggi</option>
                                <option value="critical" {{ old('risk_level') === 'critical' ? 'selected' : '' }}>Sangat Tinggi / Kritis</option>
                            </select>
                            @error('risk_level')
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
                    <!-- <div>
                        <label for="area_hectares" class="block text-sm font-medium text-gray-700">Luas (Hektar)</label>
                        <div class="mt-1">
                            <input type="number" step="0.01" name="area_hectares" id="area_hectares" value="{{ old('area_hectares') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('area_hectares') border-red-300 @enderror">
                            @error('area_hectares')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div> -->
                    <div>
                        <label for="affected_population" class="block text-sm font-medium text-gray-700">Terdampak (Korban)</label>
                        <div class="mt-1">
                        <input type="number" name="affected_population" id="affected_population" value="{{ old('affected_population') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('affected_population') border-red-300 @enderror">
                        @error('affected_population')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="point_coordinates" class="block text-sm font-medium text-gray-700">Koordinat Point (GeoJSON) <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <textarea id="point_coordinates" name="point_coordinates" rows="5" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md font-mono text-xs @error('point_coordinates') border-red-300 @enderror">{{ old('point_coordinates') }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">Format: JSON array of coordinates [[[lng, lat], [lng, lat], ...]]</p>
                        @error('point_coordinates')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif</label>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.disaster-zones.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
