@extends('layouts.admin')

@section('title', 'Detail Logistik Bantuan - Admin')
@section('page-title', 'Detail Logistik Bantuan')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.aid-inventories.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Logistik Bantuan</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Detail</span>
</li>
@endsection

@section('content')
@php
    $categoryLabels = [
        'sembako'  => 'Sembako',
        'obat'     => 'Obat',
        'pakaian'  => 'Pakaian',
        'material' => 'Material',
        'lainnya'  => 'Lainnya',
    ];
    $stockPercent = $aidInventory->initial_stock > 0
        ? min(100, round(($aidInventory->remaining_stock / $aidInventory->initial_stock) * 100, 1))
        : 0;
        $distributed = $aidInventory->initial_stock - $aidInventory->remaining_stock;
        @endphp
        
<div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <dt class="text-sm font-medium text-gray-500 truncate">Stok Masuk</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($aidInventory->initial_stock) }}</dd>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <dt class="text-sm font-medium text-gray-500 truncate">Sisa Stok</dt>
            <dd class="mt-1 text-2xl font-semibold text-indigo-600">{{ number_format($aidInventory->remaining_stock) }}</dd>
        </div>
    </div>
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <dt class="text-sm font-medium text-gray-500 truncate">Telah Disalurkan</dt>
            <dd class="mt-1 text-2xl font-semibold text-green-600">{{ number_format(max(0, $distributed)) }}</dd>
        </div>
    </div>
</div>

<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="sm:flex sm:items-center mb-6">
            <div class="sm:flex-auto">
                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $aidInventory->item_name }}</h3>
                <p class="mt-1 text-sm text-gray-500">Detail master stok logistik dan riwayat penyaluran.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none flex space-x-2 justify-end">
                @can('update data')
                <a href="{{ route('admin.aid-inventories.edit', $aidInventory) }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-yellow-700">
                    Edit
                </a>
                @endcan
                <a href="{{ route('admin.aid-inventories.index') }}"
                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>

        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                <dd class="mt-1">
                    <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">
                        {{ $categoryLabels[$aidInventory->category] ?? $aidInventory->category }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                    @if($aidInventory->is_active)
                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Aktif</span>
                    @else
                        <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">Nonaktif</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Sumber Bantuan</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidInventory->source ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Persentase Sisa</dt>
                <dd class="mt-2">
                    <div class="flex items-center">
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                            <div class="{{ $stockPercent <= 20 ? 'bg-red-400' : ($stockPercent <= 50 ? 'bg-yellow-400' : 'bg-green-400') }} h-2.5 rounded-full" style="width: {{ $stockPercent }}%"></div>
                        </div>
                        <span class="text-sm text-gray-700">{{ $stockPercent }}%</span>
                    </div>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidInventory->created_at?->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Diperbarui</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidInventory->updated_at?->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>
    </div>
</div>


<div class="bg-white shadow rounded-lg">
    <div class="mt-8 p-6 border-t border-gray-200">
        <h4 class="text-base font-bold text-gray-900 mb-4">Riwayat Penyaluran</h4>
    
        <div class="overflow-hidden rounded-lg border border-gray-200 shadow-sm">
            <table id="distributions-table" class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-4 py-3.5">Tanggal</th>
                        <th scope="col" class="px-4 py-3.5">Penerima</th>
                        <th scope="col" class="px-4 py-3.5 text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($aidInventory->distributions as $distribution)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="whitespace-nowrap px-4 py-3 text-gray-700">
                            {{ $distribution->distribution_date?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $distribution->beneficiary?->recipient_name ?? '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-gray-900">
                            {{ number_format($distribution->quantity_received) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-sm text-gray-500">
                            Belum ada penyaluran dari stok ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('style')
<!-- Container Riwayat Penyaluran -->

<!-- Styling Tambahan untuk Merapikan Layout DataTables -->
<style>
    /* Merapikan kontrol DataTables agar tidak bentrok dengan Tailwind */
    .dataTables_wrapper {
        padding: 0.5rem 0;
    }
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: #374151;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem;
        margin-left: 0.5rem;
        outline: none;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem;
        margin: 0 0.25rem;
    }
    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        margin-top: 1rem;
        font-size: 0.875rem;
        color: #4b5563;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 0.375rem !important;
        border: 1px solid #e5e7eb !important;
        margin: 0 0.125rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #2563eb !important;
        color: white !important;
        border-color: #2563eb !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#distributions-table').DataTable({
        pageLength: 5, // jumlah data per halaman
        lengthMenu: [5, 10, 25, 50], // opsi jumlah per halaman
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' // bahasa Indonesia
        }
    });
});
</script>
@endpush
