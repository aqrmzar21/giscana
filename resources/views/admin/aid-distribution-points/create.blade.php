@extends('layouts.admin')

@section('title', 'Tambah Titik Distribusi Bantuan - Admin')

@section('page-title', 'Tambah Titik Distribusi Bantuan Baru')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.aid-distribution-points.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Titik Distribusi Bantuan</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Tambah Baru</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Form Tambah Titik Distribusi Bantuan</h3>
        
        <form action="{{ route('admin.aid-distribution-points.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nama Titik <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('name') border-red-300 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="aid_type" class="block text-sm font-medium text-gray-700">Tipe Bantuan <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <select id="aid_type" name="aid_type" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('aid_type') border-red-300 @enderror">
                            <option value="">Pilih Tipe Bantuan</option>
                            <option value="food" {{ old('aid_type') === 'food' ? 'selected' : '' }}>Makanan</option>
                            <option value="water" {{ old('aid_type') === 'water' ? 'selected' : '' }}>Air</option>
                            <option value="medical" {{ old('aid_type') === 'medical' ? 'selected' : '' }}>Medis</option>
                            <option value="shelter" {{ old('aid_type') === 'shelter' ? 'selected' : '' }}>Shelter</option>
                            <option value="clothing" {{ old('aid_type') === 'clothing' ? 'selected' : '' }}>Pakaian</option>
                            <option value="other" {{ old('aid_type') === 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('aid_type')
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
                            <input type="text" name="contact_phone" id="contact_phone" value="{{ old('contact_phone') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('contact_phone') border-red-300 @enderror">
                            @error('contact_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="capacity_per_day" class="block text-sm font-medium text-gray-700">Kapasitas per Hari</label>
                    <div class="mt-1">
                        <input type="number" name="capacity_per_day" id="capacity_per_day" value="{{ old('capacity_per_day') }}" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('capacity_per_day') border-red-300 @enderror">
                        @error('capacity_per_day')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="point_coordinates" class="block text-sm font-medium text-gray-700">Koordinat Titik (GeoJSON) <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <textarea id="point_coordinates" name="point_coordinates" rows="3" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md font-mono text-xs @error('point_coordinates') border-red-300 @enderror">{{ old('point_coordinates') }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">Format: JSON array [lng, lat]</p>
                        @error('point_coordinates')
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
                <a href="{{ route('admin.aid-distribution-points.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
