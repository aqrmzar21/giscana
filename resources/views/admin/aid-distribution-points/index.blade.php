@extends('layouts.admin')

@section('title', 'Titik Distribusi Bantuan - Admin')

@section('page-title', 'Manajemen Titik Distribusi Bantuan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Titik Distribusi Bantuan</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Titik Distribusi Bantuan</h3>
        <div class="card-tools">
            <a href="{{ route('admin.aid-distribution-points.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Titik Baru
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Tipe Bantuan</th>
                    <th>Alamat</th>
                    <th>Kapasitas/hari</th>
                    <th>Kontak</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($points as $point)
                <tr>
                    <td>{{ $point->id }}</td>
                    <td>{{ $point->name }}</td>
                    <td>
                        @if($point->aid_type === 'food')
                            <span class="badge badge-warning">Makanan</span>
                        @elseif($point->aid_type === 'water')
                            <span class="badge badge-info">Air</span>
                        @elseif($point->aid_type === 'medical')
                            <span class="badge badge-danger">Medis</span>
                        @elseif($point->aid_type === 'shelter')
                            <span class="badge badge-primary">Shelter</span>
                        @elseif($point->aid_type === 'clothing')
                            <span class="badge badge-secondary">Pakaian</span>
                        @else
                            <span class="badge badge-dark">Lainnya</span>
                        @endif
                    </td>
                    <td>{{ Str::limit($point->address ?? '-', 30) }}</td>
                    <td>{{ number_format($point->capacity_per_day ?? 0) }}</td>
                    <td>{{ $point->contact_person ?? '-' }}</td>
                    <td>
                        @if($point->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Tidak Aktif</span>
                        @endif
                        @if($point->is_accessible)
                            <span class="badge badge-info">Aksesibel</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.aid-distribution-points.show', $point) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.aid-distribution-points.edit', $point) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.aid-distribution-points.destroy', $point) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus titik ini?');">
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
                    <td colspan="8" class="text-center">Tidak ada data titik distribusi bantuan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($points->hasPages())
    <div class="card-footer">
        {{ $points->links() }}
    </div>
    @endif
</div>
@endsection

