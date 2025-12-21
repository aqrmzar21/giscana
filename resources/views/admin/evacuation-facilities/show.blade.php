@extends('layouts.admin')

@section('title', 'Detail Fasilitas Evakuasi - Admin')

@section('page-title', 'Detail Fasilitas Evakuasi')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('admin.evacuation-facilities.index') }}">Fasilitas Evakuasi</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detail Fasilitas Evakuasi</h3>
        <div class="card-tools">
            <a href="{{ route('admin.evacuation-facilities.edit', $evacuationFacility) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th width="200">ID</th>
                <td>{{ $evacuationFacility->id }}</td>
            </tr>
            <tr>
                <th>Nama</th>
                <td>{{ $evacuationFacility->name }}</td>
            </tr>
            <tr>
                <th>Tipe Fasilitas</th>
                <td>
                    @if($evacuationFacility->facility_type === 'shelter')
                        <span class="badge badge-info">Shelter</span>
                    @elseif($evacuationFacility->facility_type === 'evacuation_center')
                        <span class="badge badge-primary">Pusat Evakuasi</span>
                    @else
                        <span class="badge badge-secondary">Titik Kumpul</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Deskripsi</th>
                <td>{{ $evacuationFacility->description ?? '-' }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td>{{ $evacuationFacility->address ?? '-' }}</td>
            </tr>
            <tr>
                <th>Kapasitas</th>
                <td>{{ number_format($evacuationFacility->capacity ?? 0) }} orang</td>
            </tr>
            <tr>
                <th>Kontak Person</th>
                <td>{{ $evacuationFacility->contact_person ?? '-' }}</td>
            </tr>
            <tr>
                <th>No. Telepon</th>
                <td>{{ $evacuationFacility->contact_phone ?? '-' }}</td>
            </tr>
            <tr>
                <th>Fasilitas</th>
                <td>
                    @if($evacuationFacility->has_medical_facility)
                        <span class="badge badge-success">Fasilitas Medis</span>
                    @endif
                    @if($evacuationFacility->has_food_storage)
                        <span class="badge badge-warning">Penyimpanan Makanan</span>
                    @endif
                    @if(!$evacuationFacility->has_medical_facility && !$evacuationFacility->has_food_storage)
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    @if($evacuationFacility->is_active)
                        <span class="badge badge-success">Aktif</span>
                    @else
                        <span class="badge badge-secondary">Tidak Aktif</span>
                    @endif
                    @if($evacuationFacility->is_accessible)
                        <span class="badge badge-info">Aksesibel</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Koordinat Titik</th>
                <td>
                    <pre class="bg-light p-2 rounded">{{ json_encode($evacuationFacility->point_coordinates, JSON_PRETTY_PRINT) }}</pre>
                </td>
            </tr>
            <tr>
                <th>Dibuat</th>
                <td>{{ $evacuationFacility->created_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            <tr>
                <th>Diperbarui</th>
                <td>{{ $evacuationFacility->updated_at->format('d/m/Y H:i:s') }}</td>
            </tr>
        </table>
    </div>
    <div class="card-footer">
        <a href="{{ route('admin.evacuation-facilities.index') }}" class="btn btn-default">Kembali</a>
    </div>
</div>
@endsection

