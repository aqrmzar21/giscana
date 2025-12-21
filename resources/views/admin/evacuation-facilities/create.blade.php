@extends('layouts.admin')

@section('title', 'Tambah Fasilitas Evakuasi - Admin')

@section('page-title', 'Tambah Fasilitas Evakuasi Baru')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.evacuation-facilities.index') }}">Fasilitas Evakuasi</a></li>
<li class="breadcrumb-item active">Tambah Baru</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah Fasilitas Evakuasi</h3>
    </div>
    <form action="{{ route('admin.evacuation-facilities.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nama Fasilitas <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="facility_type">Tipe Fasilitas <span class="text-danger">*</span></label>
                <select class="form-control @error('facility_type') is-invalid @enderror" id="facility_type" name="facility_type" required>
                    <option value="">Pilih Tipe Fasilitas</option>
                    <option value="shelter" {{ old('facility_type') === 'shelter' ? 'selected' : '' }}>Shelter</option>
                    <option value="evacuation_center" {{ old('facility_type') === 'evacuation_center' ? 'selected' : '' }}>Pusat Evakuasi</option>
                    <option value="assembly_point" {{ old('facility_type') === 'assembly_point' ? 'selected' : '' }}>Titik Kumpul</option>
                </select>
                @error('facility_type')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="address">Alamat</label>
                <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2">{{ old('address') }}</textarea>
                @error('address')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="capacity">Kapasitas</label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" id="capacity" name="capacity" value="{{ old('capacity') }}">
                        @error('capacity')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="contact_phone">No. Telepon</label>
                        <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}">
                        @error('contact_phone')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="contact_person">Kontak Person</label>
                <input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" value="{{ old('contact_person') }}">
                @error('contact_person')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="point_coordinates">Koordinat Titik (GeoJSON) <span class="text-danger">*</span></label>
                <textarea class="form-control @error('point_coordinates') is-invalid @enderror" id="point_coordinates" name="point_coordinates" rows="3" required>{{ old('point_coordinates') }}</textarea>
                <small class="form-text text-muted">Format: JSON array [lng, lat]</small>
                @error('point_coordinates')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="has_medical_facility" name="has_medical_facility" value="1" {{ old('has_medical_facility') ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_medical_facility">Memiliki Fasilitas Medis</label>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="has_food_storage" name="has_food_storage" value="1" {{ old('has_food_storage') ? 'checked' : '' }}>
                    <label class="form-check-label" for="has_food_storage">Memiliki Penyimpanan Makanan</label>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_accessible" name="is_accessible" value="1" {{ old('is_accessible', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_accessible">Aksesibel</label>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.evacuation-facilities.index') }}" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>
@endsection

