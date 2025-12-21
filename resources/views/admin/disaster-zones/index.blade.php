@extends('layouts.admin')

@section('title', 'Zona Bencana - Admin')

@section('page-title', 'Manajemen Zona Bencana')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Zona Bencana</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Zona Bencana</h3>
        <div class="card-tools">
            <a href="{{ route('admin.disaster-zones.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Zona Baru
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Jenis Bencana</th>
                    <th>Tingkat Risiko</th>
                    <th>Luas (ha)</th>
                    <th>Populasi Terdampak</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($zones as $zone)
                <tr>
                    <td>{{ $zone->id }}</td>
                    <td>{{ $zone->name }}</td>
                    <td>
                        @if($zone->disaster_type === 'tsunami')
                            <span class="badge badge-info">Tsunami</span>
                        @elseif($zone->disaster_type === 'flood')
                            <span class="badge badge-primary">Banjir</span>
                        @else
                            <span class="badge badge-secondary">Keduanya</span>
                        @endif
                    </td>
                    <td>
                        @if($zone->risk_level === 'low')
                            <span class="badge badge-success">Rendah</span>
                        @elseif($zone->risk_level === 'medium')
                            <span class="badge badge-warning">Sedang</span>
                        @elseif($zone->risk_level === 'high')
                            <span class="badge badge-danger">Tinggi</span>
                        @else
                            <span class="badge badge-dark">Sangat Tinggi</span>
                        @endif
                    </td>
                    <td>{{ number_format($zone->area_hectares ?? 0, 2) }}</td>
                    <td>{{ number_format($zone->affected_population ?? 0) }}</td>
                    <td>
                        @if($zone->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.disaster-zones.show', $zone) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.disaster-zones.edit', $zone) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.disaster-zones.destroy', $zone) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus zona ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data zona bencana.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($zones->hasPages())
    <div class="card-footer">
        {{ $zones->links() }}
    </div>
    @endif
</div>
@endsection

