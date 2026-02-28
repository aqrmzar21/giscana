@extends('layouts.admin')

@section('title', 'Edit Fasilitas Evakuasi - Admin')

@section('page-title', 'Edit Fasilitas Evakuasi')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.evacuation-facilities.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Fasilitas Evakuasi</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Edit</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Form Edit Fasilitas Evakuasi</h3>
        
        <form action="{{ route('admin.evacuation-facilities.update', $evacuationFacility) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Fasilitas <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <input type="text" name="name" id="name" value="{{ old('name', $evacuationFacility->name) }}" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>


                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <div class="mt-1">
                        <textarea id="description" name="description" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('description') border-red-300 @enderror">{{ old('description', $evacuationFacility->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <div class="mt-1">
                        <textarea id="address" name="address" rows="2" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('address') border-red-300 @enderror">{{ old('address', $evacuationFacility->address) }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="capacity" class="block text-sm font-medium text-gray-700">Kapasitas</label>
                        <div class="mt-1">
                            <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $evacuationFacility->capacity) }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('capacity') border-red-300 @enderror">
                            @error('capacity')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                        <div class="mt-1">
                            <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $evacuationFacility->contact_phone) }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('contact_phone') border-red-300 @enderror">
                            @error('contact_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="contact_person" class="block text-sm font-medium text-gray-700">Kontak Person</label>
                    <div class="mt-1">
                        <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $evacuationFacility->contact_person) }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('contact_person') border-red-300 @enderror">
                        @error('contact_person')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="point_coordinates" class="block text-sm font-medium text-gray-700">Koordinat Titik (GeoJSON) <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <textarea id="point_coordinates" name="point_coordinates" rows="3" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md font-mono text-xs @error('point_coordinates') border-red-300 @enderror">{{ old('point_coordinates', json_encode($evacuationFacility->point_coordinates, JSON_PRETTY_PRINT)) }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">Format: JSON array [lng, lat]</p>
                        @error('point_coordinates')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center">
                        <input id="has_medical_facility" name="has_medical_facility" type="checkbox" value="1" {{ old('has_medical_facility', $evacuationFacility->has_medical_facility) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="has_medical_facility" class="ml-2 block text-sm text-gray-900">Memiliki Fasilitas Medis</label>
                    </div>
                    <div class="flex items-center">
                        <input id="has_food_storage" name="has_food_storage" type="checkbox" value="1" {{ old('has_food_storage', $evacuationFacility->has_food_storage) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="has_food_storage" class="ml-2 block text-sm text-gray-900">Memiliki Penyimpanan Makanan</label>
                    </div>
                    <div class="flex items-center">
                        <input id="is_accessible" name="is_accessible" type="checkbox" value="1" {{ old('is_accessible', $evacuationFacility->is_accessible) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_accessible" class="ml-2 block text-sm text-gray-900">Aksesibel</label>
                    </div>
                    <div class="flex items-center">
                        <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $evacuationFacility->is_active) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif</label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.evacuation-facilities.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
