@extends('layouts.admin')

@section('title', 'Edit Rute Evakuasi - Admin')

@section('page-title', 'Edit Rute Evakuasi')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.evacuation-routes.index') }}">Rute Evakuasi</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Edit Rute Evakuasi</h3>
    </div>
    <form action="{{ route('admin.evacuation-routes.update', $evacuationRoute) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card-body">
            <div class="form-group">
                <label for="name">Nama Rute <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $evacuationRoute->name) }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="disaster_type">Jenis Bencana <span class="text-danger">*</span></label>
                <select class="form-control @error('disaster_type') is-invalid @enderror" id="disaster_type" name="disaster_type" required>
                    <option value="">Pilih Jenis Bencana</option>
                    <option value="tsunami" {{ old('disaster_type', $evacuationRoute->disaster_type) === 'tsunami' ? 'selected' : '' }}>Tsunami</option>
                    <option value="flood" {{ old('disaster_type', $evacuationRoute->disaster_type) === 'flood' ? 'selected' : '' }}>Banjir</option>
                    <option value="both" {{ old('disaster_type', $evacuationRoute->disaster_type) === 'both' ? 'selected' : '' }}>Keduanya</option>
                </select>
                @error('disaster_type')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="route_type">Tipe Rute <span class="text-danger">*</span></label>
                <select class="form-control @error('route_type') is-invalid @enderror" id="route_type" name="route_type" required>
                    <option value="">Pilih Tipe Rute</option>
                    <option value="road" {{ old('route_type', $evacuationRoute->route_type) === 'road' ? 'selected' : '' }}>Jalan</option>
                    <option value="path" {{ old('route_type', $evacuationRoute->route_type) === 'path' ? 'selected' : '' }}>Jalan Setapak</option>
                    <option value="waterway" {{ old('route_type', $evacuationRoute->route_type) === 'waterway' ? 'selected' : '' }}>Jalur Air</option>
                </select>
                @error('route_type')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $evacuationRoute->description) }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="length_km">Panjang (km)</label>
                        <input type="number" step="0.01" class="form-control @error('length_km') is-invalid @enderror" id="length_km" name="length_km" value="{{ old('length_km', $evacuationRoute->length_km) }}">
                        @error('length_km')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="capacity_per_hour">Kapasitas per Jam</label>
                        <input type="number" class="form-control @error('capacity_per_hour') is-invalid @enderror" id="capacity_per_hour" name="capacity_per_hour" value="{{ old('capacity_per_hour', $evacuationRoute->capacity_per_hour) }}">
                        @error('capacity_per_hour')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="line_coordinates">Koordinat Garis (GeoJSON) <span class="text-danger">*</span></label>
                <textarea class="form-control @error('line_coordinates') is-invalid @enderror" id="line_coordinates" name="line_coordinates" rows="5" required>{{ old('line_coordinates', json_encode($evacuationRoute->line_coordinates, JSON_PRETTY_PRINT)) }}</textarea>
                <small class="form-text text-muted">Format: JSON array of coordinates [[lng, lat], [lng, lat], ...]</small>
                @error('line_coordinates')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_accessible" name="is_accessible" value="1" {{ old('is_accessible', $evacuationRoute->is_accessible) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_accessible">Aksesibel</label>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $evacuationRoute->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.evacuation-routes.index') }}" class="btn btn-default">Batal</a>
        </div>
    </form>
</div>
@endsection

