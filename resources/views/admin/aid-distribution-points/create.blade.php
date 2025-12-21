@extends('layouts.admin')

@section('title', 'Tambah Titik Distribusi Bantuan - Admin')

@section('page-title', 'Tambah Titik Distribusi Bantuan Baru')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.aid-distribution-points.index') }}">Titik Distribusi Bantuan</a></li>
<li class="breadcrumb-item active">Tambah Baru</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Tambah Titik Distribusi Bantuan</h3>
    </div>
    <form action="{{ route('admin.aid-distribution-points.store') }}" method="POST">
        @csrf
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nama Titik <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="aid_type">Tipe Bantuan <span class="text-danger">*</span></label>
                <select class="form-control @error('aid_type') is-invalid @enderror" id="aid_type" name="aid_type" required>
                    <option value="">Pilih Tipe Bantuan</option>
                    <option value="food" {{ old('aid_type') === 'food' ? 'selected' : '' }}>Makanan</option>
                    <option value="water" {{ old('aid_type') === 'water' ? 'selected' : '' }}>Air</option>
                    <option value="medical" {{ old('aid_type') === 'medical' ? 'selected' : '' }}>Medis</option>
                    <option value="shelter" {{ old('aid_type') === 'shelter' ? 'selected' : '' }}>Shelter</option>
                    <option value="clothing" {{ old('aid_type') === 'clothing' ? 'selected' : '' }}>Pakaian</option>
                    <option value="other" {{ old('aid_type') === 'other' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('aid_type')
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
                        <label for="contact_person">Kontak Person</label>
                        <input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" value="{{ old('contact_person') }}">
                        @error('contact_person')
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
                <label for="capacity_per_day">Kapasitas per Hari</label>
                <input type="number" class="form-control @error('capacity_per_day') is-invalid @enderror" id="capacity_per_day" name="capacity_per_day" value="{{ old('capacity_per_day') }}">
                @error('capacity_per_day')
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
            <a href="{{ route('admin.aid-distribution-points.index') }}" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>
@endsection

