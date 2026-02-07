@extends('layouts.admin')

@section('title', 'Detail Fasilitas Evakuasi - Admin')

@section('page-title', 'Detail Fasilitas Evakuasi')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.evacuation-facilities.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Fasilitas Evakuasi</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Detail</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="sm:flex sm:items-center mb-6">
            <div class="sm:flex-auto">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Detail Fasilitas Evakuasi</h3>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <a href="{{ route('admin.evacuation-facilities.edit', $evacuationFacility) }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            </div>
        </div>

        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">ID</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $evacuationFacility->id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Nama</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $evacuationFacility->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Tipe Fasilitas</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($evacuationFacility->facility_type === 'shelter')
                        <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">Shelter</span>
                    @elseif($evacuationFacility->facility_type === 'evacuation_center')
                        <span class="inline-flex rounded-full bg-indigo-100 px-2 text-xs font-semibold leading-5 text-indigo-800">Pusat Evakuasi</span>
                    @elseif($evacuationFacility->facility_type === 'assembly_point')
                        <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">Titik Kumpul</span>
                    @else
                        <span class="inline-flex rounded-full bg-purple-100 px-2 text-xs font-semibold leading-5 text-purple-800">Lainnya</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $evacuationFacility->description ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $evacuationFacility->address ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Kapasitas</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ number_format($evacuationFacility->capacity ?? 0) }} orang</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Kontak Person</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $evacuationFacility->contact_person ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">No. Telepon</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $evacuationFacility->contact_phone ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Fasilitas</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($evacuationFacility->has_medical_facility)
                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Fasilitas Medis</span>
                    @endif
                    @if($evacuationFacility->has_food_storage)
                        <span class="ml-1 inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">Penyimpanan Makanan</span>
                    @endif
                    @if(!$evacuationFacility->has_medical_facility && !$evacuationFacility->has_food_storage)
                        -
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($evacuationFacility->is_active)
                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Aktif</span>
                    @else
                        <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">Tidak Aktif</span>
                    @endif
                    @if($evacuationFacility->is_accessible)
                        <span class="ml-1 inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">Aksesibel</span>
                    @endif
                </dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Koordinat Titik</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    <pre class="bg-gray-50 p-4 rounded-md text-xs overflow-x-auto">{{ json_encode($evacuationFacility->point_coordinates, JSON_PRETTY_PRINT) }}</pre>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $evacuationFacility->created_at->format('d/m/Y H:i:s') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Diperbarui</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $evacuationFacility->updated_at->format('d/m/Y H:i:s') }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex items-center justify-end">
            <a href="{{ route('admin.evacuation-facilities.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection
