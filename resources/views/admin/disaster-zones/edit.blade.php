@extends('layouts.admin')

@section('title', 'Edit Zona Bencana - Admin')

@section('page-title', 'Edit Zona Bencana')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.disaster-zones.index') }}">Zona Bencana</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Zona Bencana</h3>
    </div>
    <form action="{{ route('admin.disaster-zones.update', $disasterZone) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nama Zona <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $disasterZone->name) }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="disaster_type">Jenis Bencana <span class="text-danger">*</span></label>
                <select class="form-control @error('disaster_type') is-invalid @enderror" id="disaster_type" name="disaster_type" required>
                    <option value="">Pilih Jenis Bencana</option>
                    <option value="tsunami" {{ old('disaster_type', $disasterZone->disaster_type) === 'tsunami' ? 'selected' : '' }}>Tsunami</option>
                    <option value="flood" {{ old('disaster_type', $disasterZone->disaster_type) === 'flood' ? 'selected' : '' }}>Banjir</option>
                    <option value="both" {{ old('disaster_type', $disasterZone->disaster_type) === 'both' ? 'selected' : '' }}>Keduanya</option>
                </select>
                @error('disaster_type')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="risk_level">Tingkat Risiko <span class="text-danger">*</span></label>
                <select class="form-control @error('risk_level') is-invalid @enderror" id="risk_level" name="risk_level" required>
                    <option value="">Pilih Tingkat Risiko</option>
                    <option value="low" {{ old('risk_level', $disasterZone->risk_level) === 'low' ? 'selected' : '' }}>Rendah</option>
                    <option value="medium" {{ old('risk_level', $disasterZone->risk_level) === 'medium' ? 'selected' : '' }}>Sedang</option>
                    <option value="high" {{ old('risk_level', $disasterZone->risk_level) === 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="very_high" {{ old('risk_level', $disasterZone->risk_level) === 'very_high' ? 'selected' : '' }}>Sangat Tinggi</option>
                </select>
                @error('risk_level')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $disasterZone->description) }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="area_hectares">Luas (Hektar)</label>
                        <input type="number" step="0.01" class="form-control @error('area_hectares') is-invalid @enderror" id="area_hectares" name="area_hectares" value="{{ old('area_hectares', $disasterZone->area_hectares) }}">
                        @error('area_hectares')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="affected_population">Populasi Terdampak</label>
                        <input type="number" class="form-control @error('affected_population') is-invalid @enderror" id="affected_population" name="affected_population" value="{{ old('affected_population', $disasterZone->affected_population) }}">
                        @error('affected_population')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="polygon_coordinates">Koordinat Polygon (GeoJSON) <span class="text-danger">*</span></label>
                <textarea class="form-control @error('polygon_coordinates') is-invalid @enderror" id="polygon_coordinates" name="polygon_coordinates" rows="5" required>{{ old('polygon_coordinates', json_encode($disasterZone->polygon_coordinates, JSON_PRETTY_PRINT)) }}</textarea>
                <small class="form-text text-muted">Format: JSON array of coordinates [[[lng, lat], [lng, lat], ...]]</small>
                @error('polygon_coordinates')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $disasterZone->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.disaster-zones.index') }}" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>
@endsection

