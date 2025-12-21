@extends('layouts.admin')

@section('title', 'Rute Evakuasi - Admin')

@section('page-title', 'Manajemen Rute Evakuasi')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Rute Evakuasi</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Rute Evakuasi</h3>
        <div class="card-tools">
            <a href="{{ route('admin.evacuation-routes.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Rute Baru
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
                    <th>Tipe Rute</th>
                    <th>Panjang (km)</th>
                    <th>Kapasitas/jam</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($routes as $route)
                <tr>
                    <td>{{ $route->id }}</td>
                    <td>{{ $route->name }}</td>
                    <td>
                        @if($route->disaster_type === 'tsunami')
                            <span class="badge badge-info">Tsunami</span>
                        @elseif($route->disaster_type === 'flood')
                            <span class="badge badge-primary">Banjir</span>
                        @else
                            <span class="badge badge-secondary">Keduanya</span>
                        @endif
                    </td>
                    <td>
                        @if($route->route_type === 'road')
                            Jalan
                        @elseif($route->route_type === 'path')
                            Jalan Setapak
                        @else
                            Jalur Air
                        @endif
                    </td>
                    <td>{{ number_format($route->length_km ?? 0, 2) }}</td>
                    <td>{{ number_format($route->capacity_per_hour ?? 0) }}</td>
                    <td>
                        @if($route->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Tidak Aktif</span>
                        @endif
                        @if($route->is_accessible)
                            <span class="badge badge-info">Aksesibel</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.evacuation-routes.show', $route) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.evacuation-routes.edit', $route) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.evacuation-routes.destroy', $route) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rute ini?');">
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
                    <td colspan="8" class="text-center">Tidak ada data rute evakuasi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($routes->hasPages())
    <div class="card-footer">
        {{ $routes->links() }}
    </div>
    @endif
</div>
@endsection

