@extends('layouts.admin')

@section('title', 'Detail Penerima Bantuan - Admin')

@section('page-title', 'Detail Penerima Bantuan')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.aid-recipients.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Penerima Bantuan</a>
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
                <h3 class="text-lg font-medium leading-6 text-gray-900">Detail Penerima Bantuan</h3>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <a href="{{ route('admin.aid-recipients.edit', $aidRecipient) }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            </div>
        </div>

        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">Nama Penerima</dt>
                <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $aidRecipient->recipient_name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Tanggal Penyaluran</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidRecipient->date->format('d F Y') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Jenis Bantuan</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">
                        {{ $aidRecipient->aid_type }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Jumlah Bantuan</dt>
                <dd class="mt-1 text-sm text-gray-900 font-bold text-indigo-600">{{ number_format($aidRecipient->amount, 0, ',', '.') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Bencana Terkait (Kecamatan)</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidRecipient->aidDisaster->district_name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Kecamatan</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidRecipient->village->district->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Desa/Kelurahan</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidRecipient->village->yard ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Dibuat Pada</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidRecipient->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Keterangan / Deskripsi</dt>
                <dd class="mt-1 text-sm text-gray-900 bg-gray-50 p-3 rounded-md border border-gray-100">
                    {{ $aidRecipient->description ?: 'Tidak ada keterangan tambahan.' }}
                </dd>
            </div>
        </dl>

        <div class="mt-6 flex items-center justify-end">
            <a href="{{ route('admin.aid-recipients.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection
