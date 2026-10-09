@extends('layouts.admin')

@section('title', 'Detail Distribusi Bantuan - Admin')
@section('page-title', 'Detail Distribusi Bantuan')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.aid-distributions.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Distribusi Bantuan</a>
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
                <h3 class="text-lg font-medium leading-6 text-gray-900">Detail Catatan Distribusi</h3>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none flex space-x-2">
                @can('update data')
                <a href="{{ route('admin.aid-distributions.edit', $aidDistribution) }}"
                   class="inline-flex items-center justify-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-yellow-700">
                    Edit
                </a>
                @endcan
                <a href="{{ route('admin.aid-distributions.index') }}"                
                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>

        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">Nama Penerima</dt>
                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $aidDistribution->beneficiary?->recipient_name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">NIK / No. KTP</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidDistribution->beneficiary?->identity_card_number ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Desa/Kelurahan</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidDistribution->beneficiary?->village?->full_name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Kecamatan</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidDistribution->beneficiary?->village?->district?->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Barang Logistik</dt>
                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $aidDistribution->aidInventory?->item_name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Kategori Bantuan</dt>
                <dd class="mt-1">
                    <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">
                        {{ $aidDistribution->aidInventory?->category ?? '-' }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Jumlah Diterima</dt>
                <dd class="mt-1 text-sm text-gray-900 font-bold text-indigo-600">{{ number_format($aidDistribution->quantity_received) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Tanggal Distribusi</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidDistribution->distribution_date?->format('d F Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Wilayah Bencana</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidDistribution->aidDisaster?->district_name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Status Penerima</dt>
                <dd class="mt-1">
                    @if($aidDistribution->beneficiary?->aid_status === 'received')
                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Sudah Diterima</span>
                    @else
                        <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">Pending</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Dicatat Oleh</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidDistribution->user?->name ?? 'System' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidDistribution->created_at?->format('d/m/Y H:i') }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Keterangan</dt>
                <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md border border-gray-100">
                    {{ $aidDistribution->description ?: 'Tidak ada keterangan tambahan.' }}
                </dd>
            </div>
        </dl>

                
    </div>
</div>
@endsection
