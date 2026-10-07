@extends('layouts.admin')

@section('title', 'Detail Bantuan Bencana - Admin')

@section('page-title', 'Detail Bantuan Bencana Kecamatan')

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
    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Detail {{ $aidDisaster->district_name }}</span>
</li>
@endsection

@section('content')
<div class="space-y-6">

    {{-- HEADER CARD WITH NAVIGATION & ACTIONS --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                        Kecamatan Focus
                    </span>
                    @if($aidDisaster->is_active)
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            Aktif
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                            Nonaktif
                        </span>
                    @endif
                </div>
                <h1 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-2">
                    Kecamatan {{ $aidDisaster->district_name }}
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Detail rekapitulasi bantuan bencana logistik & progres penyaluran warga
                </p>
            </div>

            <div class="flex items-center gap-3">
                @can('update data')
                <a href="{{ route('admin.aid-disasters.edit', $aidDisaster) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Data
                </a>
                @endcan
                <a href="{{ route('admin.aid-disasters.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 transition-colors">
                    &larr; Kembali
                </a>
            </div>
        </div>

        {{-- PROGRESS BANNER --}}
        @php
            // Persentase dihitung dari KK yang sudah menerima (total_received) dibanding Target KK (total_recipients)
            $percentage = $aidDisaster->total_recipients > 0 ? min(100, round(($aidDisaster->total_received / $aidDisaster->total_recipients) * 100, 1)) : 0;
                
            $barColor = $percentage >= 80 ? 'bg-emerald-500' : ($percentage >= 40 ? 'bg-indigo-500' : 'bg-amber-500');
        @endphp
        <div class="mt-6 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between text-xs font-bold mb-2 text-gray-700 dark:text-gray-300">
                <span>Progres Penyaluran Bantuan</span>
                <span class="text-sm text-indigo-600 dark:text-indigo-400 font-extrabold">{{ $percentage }}% Terpenuhi</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3 overflow-hidden">
                <div class="{{ $barColor }} h-3 rounded-full transition-all duration-700" style="width: {{ $percentage }}%"></div>
            </div>
        </div>
    </div>

    {{-- STAT CARDS GRID --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- Total Target Penerima --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between text-blue-600 dark:text-blue-400 mb-2">
                <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Target Penerima</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">
                {{ number_format($aidDisaster->total_recipients ?? 0) }} KK
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Target Kepala Keluarga di {{ $aidDisaster->district_name }}</p>
        </div>

        {{-- Bantuan Tersalurkan --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between text-emerald-600 dark:text-emerald-400 mb-2">
                <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Bantuan Tersalur</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                {{ number_format($aidDisaster->distributed_aid ?? 0) }} Unit
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total barang logistik terkirim</p>
        </div>

        {{-- Belum Menerima / Sisa --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between text-amber-500 mb-2">
                <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Sisa Target</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">
                {{ number_format($aidDisaster->remaining_aid ?? 0) }} KK
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">KK yang belum terpenuhi</p>
        </div>

        {{-- Desa Terjangkau --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between text-purple-600 dark:text-purple-400 mb-2">
                <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Desa Terjangkau</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white">
                {{ count($villageStats ?? []) }} Desa
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Wilayah desa penerima bantuan</p>
        </div>
    </div>

    {{-- VILLAGE BREAKDOWN TABLE & RECENT LOGS GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Left: Desa Breakdown --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Breakdown Distribusi per Desa</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Akumulasi penyaluran barang logistik di tiap desa</p>

            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 uppercase font-semibold">
                        <tr>
                            <th class="py-2.5 px-3">Nama Desa</th>
                            <th class="py-2.5 px-3 text-center">Penerima (KK)</th>
                            <th class="py-2.5 px-3 text-center">Total Logistik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                        @forelse($villageStats as $villageName => $records)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                <td class="py-3 px-3 font-bold text-gray-800 dark:text-gray-200">{{ $villageName }}</td>
                                <td class="py-3 px-3 text-center font-semibold text-gray-700 dark:text-gray-300">{{ $records->count() }} KK</td>
                                <td class="py-3 px-3 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                    {{ number_format($records->sum('quantity_received')) }} Unit
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-gray-400 dark:text-gray-500">Belum ada rincian data per desa.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Right: Recent Distribution Logs --}}
        <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl p-6">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1">Catatan Transaksi Distribusi Terakhir</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Log pengiriman bantuan logistik ke warga di kecamatan ini</p>

            <div class="space-y-3">
                @forelse(($distributions ?? [])->take(5) as $dist)
                    <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/40 flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white">
                                {{ $dist->beneficiary?->recipient_name ?? 'Warga Penerima' }}
                            </h4>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                                Desa {{ $dist->village?->yard ?? '-' }} &bull; Item: <strong class="text-gray-700 dark:text-gray-300">{{ $dist->aidInventory?->item_name ?? 'Logistik' }}</strong>
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">+{{ number_format($dist->quantity_received) }} unit</span>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500">
                                {{ $dist->distribution_date ? \Carbon\Carbon::parse($dist->distribution_date)->format('d M Y') : '-' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-6 text-xs text-gray-400 dark:text-gray-500">Belum ada catatan transaksi penyaluran.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection