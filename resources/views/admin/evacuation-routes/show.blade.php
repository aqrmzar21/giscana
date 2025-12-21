@extends('layouts.admin')

@section('title', 'Detail Rute Evakuasi - Admin')

@section('page-title', 'Detail Rute Evakuasi')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.evacuation-routes.index') }}">Rute Evakuasi</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Rute Evakuasi</h3>
        <div class="card-tools">
            <a href="{{ route('admin.evacuation-routes.edit', $evacuationRoute) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th width="200">ID</th>
                <td>{{ $evacuationRoute->id }}</td>
            </tr>
            <tr>
                <th>Nama</th>
                <td>{{ $evacuationRoute->name }}</td>
            </tr>
            <tr>
                <th>Jenis Bencana</th>
                <td>
                    @if($evacuationRoute->disaster_type === 'tsunami')
                        <span class="badge badge-info">Tsunami</span>
                    @elseif($evacuationRoute->disaster_type === 'flood')
                        <span class="badge badge-primary">Banjir</span>
                    @else
                        <span class="badge badge-secondary">Keduanya</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Tipe Rute</th>
                <td>
                    @if($evacuationRoute->route_type === 'road')
                        Jalan
                    @elseif($evacuationRoute->route_type === 'path')
                        Jalan Setapak
                    @else
                        Jalur Air
                    @endif
                </td>
            </tr>
            <tr>
                <th>Deskripsi</th>
                <td>{{ $evacuationRoute->description ?? '-' }}</td>
            </tr>
            <tr>
                <th>Panjang (km)</th>
                <td>{{ number_format($evacuationRoute->length_km ?? 0, 2) }}</td>
            </tr>
            <tr>
                <th>Kapasitas per Jam</th>
                <td>{{ number_format($evacuationRoute->capacity_per_hour ?? 0) }} orang</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($evacuationRoute->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">Tidak Aktif</span>
                    @endif
                    @if($evacuationRoute->is_accessible)
                        <span class="badge badge-info">Aksesibel</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Koordinat Garis</th>
                <td>
                    <pre class="bg-light p-2 rounded">{{ json_encode($evacuationRoute->line_coordinates, JSON_PRETTY_PRINT) }}</pre>
                </td>
            </tr>
            <tr>
                <th>Dibuat</th>
                <td>{{ $evacuationRoute->created_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <th>Diperbarui</th>
                <td>{{ $evacuationRoute->updated_at->format('d/m/Y H:i:s') }}</td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <a href="{{ route('admin.evacuation-routes.index') }}" class="btn btn-default">Kembali</a>
    </div>
</div>
@endsection

