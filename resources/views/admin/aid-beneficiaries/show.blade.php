@extends('layouts.admin')

@section('title', 'Detail Warga Penerima - Admin')
@section('page-title', 'Detail Warga Penerima Bantuan')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.aid-beneficiaries.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Data Warga Penerima</a>
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
@endphp

<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="sm:flex sm:items-center mb-6">
            <div class="sm:flex-auto">
                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $aidBeneficiary->recipient_name }}</h3>
                <p class="mt-1 text-sm text-gray-500">Detail data warga target penerima dan riwayat bantuan yang diterima.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none flex space-x-2">
                @can('update data')
                <a href="{{ route('admin.aid-beneficiaries.edit', $aidBeneficiary) }}"
                   class="inline-flex items-center justify-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-yellow-700">
                    Edit
                </a>
                @endcan
                <a href="{{ route('admin.aid-beneficiaries.index') }}"
                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>

        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">NIK / No. KTP</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidBeneficiary->identity_card_number ?: '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Status Bantuan</dt>
                <dd class="mt-1">
                    @if($aidBeneficiary->aid_status === 'received')
                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Sudah menerima</span>
                    @else
                        <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">Belum menerima</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Kecamatan</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidBeneficiary->district?->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Desa/Kelurahan</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidBeneficiary->village?->full_name ?? $aidBeneficiary->village?->yard ?? '-' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Alamat Detail</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidBeneficiary->address_detail ?: 'Tidak ada alamat detail.' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidBeneficiary->created_at?->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Diperbarui</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidBeneficiary->updated_at?->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>

        <div class="mt-8">
            <h4 class="text-base font-semibold text-gray-900 mb-3">Riwayat Bantuan Diterima</h4>
            <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Tanggal</th>
                            <th class="px-3 py-3 text-left text-sm font-semibold text-gray-900">Barang Logistik</th>
                            <th class="px-3 py-3 text-left text-sm font-semibold text-gray-900">Kategori</th>
                            <th class="px-3 py-3 text-left text-sm font-semibold text-gray-900">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse($aidBeneficiary->distributions as $distribution)
                        <tr>
                            <td class="whitespace-nowrap py-3 pl-4 pr-3 text-sm text-gray-500 sm:pl-6">
                                {{ $distribution->distribution_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-3 py-3 text-sm text-gray-900">{{ $distribution->aidInventory?->item_name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-3 py-3 text-sm">
                                <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">
                                    {{ $categoryLabels[$distribution->aidInventory?->category] ?? ($distribution->aidInventory?->category ?? '-') }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-3 text-sm font-semibold text-gray-900">{{ number_format($distribution->quantity_received) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-sm text-gray-500">Warga ini belum memiliki catatan distribusi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
