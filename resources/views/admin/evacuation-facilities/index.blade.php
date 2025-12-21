@extends('layouts.admin')

@section('title', 'Fasilitas Evakuasi - Admin')

@section('page-title', 'Manajemen Fasilitas Evakuasi')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item active">Fasilitas Evakuasi</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Fasilitas Evakuasi</h3>
        <div class="card-tools">
            <a href="{{ route('admin.evacuation-facilities.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Fasilitas Baru
            </a>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Alamat</th>
                    <th>Kapasitas</th>
                    <th>Fasilitas</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($facilities as $facility)
                <tr>
                    <td>{{ $facility->id }}</td>
                    <td>{{ $facility->name }}</td>
                    <td>
                        @if($facility->facility_type === 'shelter')
                            <span class="badge badge-info">Shelter</span>
                        @elseif($facility->facility_type === 'evacuation_center')
                            <span class="badge badge-primary">Pusat Evakuasi</span>
                        @else
                            <span class="badge badge-secondary">Titik Kumpul</span>
                        @endif
                    </td>
                    <td>{{ Str::limit($facility->address ?? '-', 30) }}</td>
                    <td>{{ number_format($facility->capacity ?? 0) }}</td>
                    <td>
                        @if($facility->has_medical_facility)
                            <span class="badge badge-success">Medis</span>
                        @endif
                        @if($facility->has_food_storage)
                            <span class="badge badge-warning">Makanan</span>
                        @endif
                    </td>
                    <td>
                        @if($facility->is_active)
                            <span class="badge badge-success">Aktif</span>
                        @else
                            <span class="badge badge-secondary">Tidak Aktif</span>
                        @endif
                        @if($facility->is_accessible)
                            <span class="badge badge-info">Aksesibel</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.evacuation-facilities.show', $facility) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.evacuation-facilities.edit', $facility) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.evacuation-facilities.destroy', $facility) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini?');">
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
                    <td colspan="8" class="text-center">Tidak ada data fasilitas evakuasi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($facilities->hasPages())
    <div class="card-footer">
        {{ $facilities->links() }}
    </div>
    @endif
</div>
@endsection

