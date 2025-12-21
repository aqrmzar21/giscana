@extends('layouts.admin')

@section('title', 'Detail Titik Distribusi Bantuan - Admin')

@section('page-title', 'Detail Titik Distribusi Bantuan')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.aid-distribution-points.index') }}">Titik Distribusi Bantuan</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Titik Distribusi Bantuan</h3>
        <div class="card-tools">
            <a href="{{ route('admin.aid-distribution-points.edit', $aidDistributionPoint) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th width="200">ID</th>
                <td>{{ $aidDistributionPoint->id }}</td>
            </tr>
            <tr>
                <th>Nama</th>
                <td>{{ $aidDistributionPoint->name }}</td>
            </tr>
            <tr>
                <th>Tipe Bantuan</th>
                <td>
                    @if($aidDistributionPoint->aid_type === 'food')
                        <span class="badge badge-warning">Makanan</span>
                    @elseif($aidDistributionPoint->aid_type === 'water')
                        <span class="badge badge-info">Air</span>
                    @elseif($aidDistributionPoint->aid_type === 'medical')
                        <span class="badge badge-danger">Medis</span>
                    @elseif($aidDistributionPoint->aid_type === 'shelter')
                        <span class="badge badge-primary">Shelter</span>
                    @elseif($aidDistributionPoint->aid_type === 'clothing')
                        <span class="badge badge-secondary">Pakaian</span>
                    @else
                        <span class="badge badge-dark">Lainnya</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Deskripsi</th>
                <td>{{ $aidDistributionPoint->description ?? '-' }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td>{{ $aidDistributionPoint->address ?? '-' }}</td>
            </tr>
            <tr>
                <th>Kontak Person</th>
                <td>{{ $aidDistributionPoint->contact_person ?? '-' }}</td>
            </tr>
            <tr>
                <th>No. Telepon</th>
                <td>{{ $aidDistributionPoint->contact_phone ?? '-' }}</td>
            </tr>
            <tr>
                <th>Kapasitas per Hari</th>
                <td>{{ number_format($aidDistributionPoint->capacity_per_day ?? 0) }} orang</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($aidDistributionPoint->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">Tidak Aktif</span>
                    @endif
                    @if($aidDistributionPoint->is_accessible)
                        <span class="badge badge-info">Aksesibel</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Koordinat Titik</th>
                <td>
                    <pre class="bg-light p-2 rounded">{{ json_encode($aidDistributionPoint->point_coordinates, JSON_PRETTY_PRINT) }}</pre>
                </td>
            </tr>
            <tr>
                <th>Dibuat</th>
                <td>{{ $aidDistributionPoint->created_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <th>Diperbarui</th>
                <td>{{ $aidDistributionPoint->updated_at->format('d/m/Y H:i:s') }}</td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <a href="{{ route('admin.aid-distribution-points.index') }}" class="btn btn-default">Kembali</a>
    </div>
</div>
@endsection

