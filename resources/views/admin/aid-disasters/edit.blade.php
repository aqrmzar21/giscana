@extends('layouts.admin')

@section('title', 'Edit Data Bantuan Bencana - Admin')

@section('page-title', 'Edit Data Bantuan Bencana Kecamatan')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white">Dashboard</a>
    <svg class="w-5 h-5 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.aid-disasters.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white">Bantuan Bencana</a>
    <svg class="w-5 h-5 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Edit {{ $aidDisaster->district_name }}</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl p-6">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-100 dark:border-gray-700/60">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Data Bantuan Kecamatan {{ $aidDisaster->district_name }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui nama kecamatan atau status keaktifan</p>
            </div>
            <a href="{{ route('admin.aid-disasters.index') }}" class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                &larr; Kembali
            </a>
        </div>

        @if($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 p-4">
            <ul class="list-disc list-inside text-xs text-red-700 dark:text-red-300 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($aidDisaster->last_synced_at)
        <div class="mb-6 p-3 rounded-xl bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 flex items-center gap-2 text-xs text-blue-700 dark:text-blue-300">
            <svg class="h-4 w-4 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Terakhir disinkronkan dari sistem: <strong>{{ $aidDisaster->last_synced_at->format('d M Y H:i') }} WITA</strong></span>
        </div>
        @endif

        <form action="{{ route('admin.aid-disasters.update', $aidDisaster) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="district_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                    Nama Kecamatan <span class="text-red-500">*</span>
                </label>
                <input type="text" name="district_name" id="district_name"
                       value="{{ old('district_name', $aidDisaster->district_name) }}" required
                       class="w-full text-sm bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white p-3">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                        Total Target Penerima (KK)
                    </label>
                    <input type="number" value="{{ $aidDisaster->total_recipients }}" disabled
                           class="w-full text-sm bg-gray-100 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-500 dark:text-gray-400 p-3 cursor-not-allowed">
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Dihitung otomatis dari data penerima bantuan terdaftar</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                        Bantuan Terdistribusi
                    </label>
                    <input type="number" value="{{ $aidDisaster->distributed_aid }}" disabled
                           class="w-full text-sm bg-gray-100 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-500 dark:text-gray-400 p-3 cursor-not-allowed">
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Dihitung otomatis dari akumulasi transaksi penyaluran</p>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $aidDisaster->is_active) ? 'checked' : '' }}
                       class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="is_active" class="text-xs font-semibold text-gray-700 dark:text-gray-300">Status Aktif Tanggap Bencana</label>
            </div>

            <div class="pt-6 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-end gap-3">
                <a href="{{ route('admin.aid-disasters.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-sm font-semibold text-white shadow-sm transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
