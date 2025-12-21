@extends('layouts.admin')

@section('title', 'Detail Zona Bencana - Admin')

@section('page-title', 'Detail Zona Bencana')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.disaster-zones.index') }}">Zona Bencana</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Zona Bencana</h3>
        <div class="card-tools">
            <a href="{{ route('admin.disaster-zones.edit', $disasterZone) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th width="200">ID</th>
                <td>{{ $disasterZone->id }}</td>
            </tr>
            <tr>
                <th>Nama</th>
                <td>{{ $disasterZone->name }}</td>
            </tr>
            <tr>
                <th>Jenis Bencana</th>
                <td>
                    @if($disasterZone->disaster_type === 'tsunami')
                        <span class="badge badge-info">Tsunami</span>
                    @elseif($disasterZone->disaster_type === 'flood')
                        <span class="badge badge-primary">Banjir</span>
                    @else
                        <span class="badge badge-secondary">Keduanya</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Tingkat Risiko</th>
                <td>
                    @if($disasterZone->risk_level === 'low')
                        <span class="badge badge-success">Rendah</span>
                    @elseif($disasterZone->risk_level === 'medium')
                        <span class="badge badge-warning">Sedang</span>
                    @elseif($disasterZone->risk_level === 'high')
                        <span class="badge badge-danger">Tinggi</span>
                    @else
                        <span class="badge badge-dark">Sangat Tinggi</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Deskripsi</th>
                <td>{{ $disasterZone->description ?? '-' }}</td>
            </tr>
            <tr>
                <th>Luas (Hektar)</th>
                <td>{{ number_format($disasterZone->area_hectares ?? 0, 2) }}</td>
            </tr>
            <tr>
                <th>Populasi Terdampak</th>
                <td>{{ number_format($disasterZone->affected_population ?? 0) }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($disasterZone->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">Tidak Aktif</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Koordinat Polygon</th>
                <td>
                    <pre class="bg-light p-2 rounded">{{ json_encode($disasterZone->polygon_coordinates, JSON_PRETTY_PRINT) }}</pre>
                </td>
            </tr>
            <tr>
                <th>Dibuat</th>
                <td>{{ $disasterZone->created_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <th>Diperbarui</th>
                <td>{{ $disasterZone->updated_at->format('d/m/Y H:i:s') }}</td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <a href="{{ route('admin.disaster-zones.index') }}" class="btn btn-default">Kembali</a>
    </div>
</div>
@endsection

