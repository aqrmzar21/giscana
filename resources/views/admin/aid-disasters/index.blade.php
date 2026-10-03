@extends('layouts.admin')

@section('title', 'Data Bantuan Bencana Kecamatan - Admin')

@section('page-title', 'Statistik Bantuan Bencana per Kecamatan')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white">
        Dashboard
    </a>
    <svg class="w-5 h-5 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Bantuan Bencana</span>
</li>
@endsection

@section('content')
<div class="space-y-6">

    {{-- TOP SUMMARY STATS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- Card 1: Total Kecamatan --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">
                    Wilayah Fokus
                </span>
                <div class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Kecamatan</p>
                <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1">{{ number_format($aidDisasters->total()) }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kecamatan terdaftar dalam tanggap bencana</p>
            </div>
        </div>

        {{-- Card 2: Total Target Penerima (KK) --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300">
                    Target Warga
                </span>
                <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Target Penerima</p>
                <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1">{{ number_format($totalRecipientsSum ?? 0) }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kepala Keluarga (KK) terdaftar</p>
            </div>
        </div>

        {{-- Card 3: Total Bantuan Tersalur --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">
                    Logistik Terikirim
                </span>
                <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bantuan Tersalurkan</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($totalDistributedSum ?? 0) }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unit / paket logistik terdistribusi</p>
            </div>
        </div>

        {{-- Card 4: Persentase Capaian Rata-rata --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">
                    Tingkat Pemenuhan
                </span>
                <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Capaian Rata-Rata</p>
                <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1">{{ $overallPercentage ?? 0 }}%</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Rasio pemenuhan bantuan total</p>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE & SEARCH CARD --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl overflow-hidden">
        
        {{-- HEADER & SEARCH BAR --}}
        <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-700/60 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Rekapitulasi Bantuan Bencana per Kecamatan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Monitoring progres penyaluran bantuan logistik dan target penerima per kecamatan</p>
            </div>

            <div class="flex items-center gap-3">
                {{-- Form Pencarian --}}
                <form method="GET" action="{{ route('admin.aid-disasters.index') }}" class="relative flex-1 sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Cari kecamatan..." 
                           class="w-full pl-9 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 text-gray-900 dark:text-white placeholder-gray-400 transition-all">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    @if(request('search'))
                        <a href="{{ route('admin.aid-disasters.index') }}" class="absolute right-3 top-2.5 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            ✕ Reset
                        </a>
                    @endif
                </form>

                @can('export data')
                <a href="{{ route('admin.aid-disasters.print', request()->query()) }}" target="_blank" data-no-pjax
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold bg-red-600 hover:bg-red-700 text-white shadow-sm transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Laporan PDF
                </a>
                @endcan

                <!-- 
                @can('create data')
                <a href="{{ route('admin.aid-disasters.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Data
                </a>
                @endcan
                 -->
            </div>
        </div>

        {{-- TABLE DATA --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50/80 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="py-3.5 px-4 text-center w-12">No</th>
                        <th scope="col" class="py-3.5 px-4">Kecamatan</th>
                        <th scope="col" class="py-3.5 px-4 text-center">Target Penerima (KK)</th>
                        <th scope="col" class="py-3.5 px-4 text-center">Bantuan Tersalur</th>
                        <th scope="col" class="py-3.5 px-4">Progres Penyaluran</th>
                        <th scope="col" class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 bg-white dark:bg-gray-800">
                    @forelse($aidDisasters as $aid)
                        @php
                            $percentage = $aid->total_recipients > 0 ? min(100, round(($aid->total_received / $aid->total_recipients) * 100, 1)) : 0;
                            $barColor   = $percentage >= 80 ? 'bg-emerald-500' : ($percentage >= 40 ? 'bg-indigo-500' : 'bg-amber-500');
                            $badgeColor = $percentage >= 80 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : ($percentage >= 40 ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300');
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-4 px-4 text-center font-medium text-gray-500 dark:text-gray-400">
                                {{ $loop->iteration + ($aidDisasters->currentPage() - 1) * $aidDisasters->perPage() }}
                            </td>
                            <td class="py-4 px-4 font-bold text-gray-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                    <span>{{ $aid->district_name }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center font-semibold text-gray-700 dark:text-gray-300">
                                {{ number_format($aid->total_recipients) }} KK
                            </td>
                            <td class="py-4 px-4 text-center font-extrabold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($aid->total_received) }} Unit
                            </td>
                            <td class="py-4 px-4 min-w-[200px]">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                        <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-bold {{ $badgeColor }}">
                                        {{ $percentage }}%
                                    </span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.aid-disasters.show', $aid) }}" 
                                       title="Detail Bantuan Bencana"
                                       class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-colors">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <!-- 
                                    @can('update data')
                                    <a href="{{ route('admin.aid-disasters.edit', $aid) }}" 
                                       title="Edit Data"
                                       class="p-2 rounded-lg bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300 hover:bg-amber-100 dark:hover:bg-amber-900/60 transition-colors">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a> 
                                    @endcan
                                    -->
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400 dark:text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-sm font-medium">Tidak ada data bantuan bencana ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($aidDisasters->hasPages())
        <div class="p-4 border-t border-gray-100 dark:border-gray-700/60">
            {{ $aidDisasters->links() }}
        </div>
        @endif
    </div>

    {{-- INTERACTIVE BREAKDOWN PER DESA (ACCORDION CARDS) --}}
    <div class="mt-8 space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Breakdown Penyaluran Bantuan per Desa</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Rincian desa penerima logistik di setiap kecamatan</p>
            </div>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">
                Data Rinci Desa
            </span>
        </div>

        @foreach($aidDisasters as $aid)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden transition-all" x-data="{ open: false }">
                {{-- Accordion Header --}}
                <div @click="open = !open" class="p-4 sm:p-5 flex items-center justify-between cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors select-none">
                    <div class="flex items-center gap-3">
                        <div class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-base font-bold text-gray-900 dark:text-white">
                                Kecamatan {{ $aid->district_name }}
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ number_format($aid->total_received ?? 0) }} Unit Bantuan Tersalurkan &bull; 
                                <span class="font-semibold text-indigo-600 dark:text-indigo-400">
                                    {{ $villageBreakdown[$aid->id]['total_villages'] ?? 0 }} Desa Terjangkau
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="text-xs font-semibold px-3 py-1 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            <span x-show="!open">Buka Desa &darr;</span>
                            <span x-show="open">Tutup &uarr;</span>
                        </span>
                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                {{-- Accordion Content Table --}}
                <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-gray-700/60 p-4 sm:p-5 bg-gray-50/50 dark:bg-gray-800/60">
                    @php
                        $villages = $villageBreakdown[$aid->id]['villages'] ?? [];
                    @endphp

                    @if(empty($villages) || count($villages) === 0)
                        <p class="text-xs text-gray-400 dark:text-gray-500 text-center py-4">Belum ada rincian transaksi penyaluran per desa di kecamatan ini.</p>
                    @else
                        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                            <table class="w-full text-xs text-left bg-white dark:bg-gray-800">
                                <thead class="bg-gray-100 dark:bg-gray-700/80 text-gray-600 dark:text-gray-300 uppercase tracking-wider font-semibold">
                                    <tr>
                                        <th class="py-2.5 px-3 text-center w-10">No</th>
                                        <th class="py-2.5 px-3">Nama Desa</th>
                                        <th class="py-2.5 px-3 text-center">Jumlah Penerima (KK)</th>
                                        <th class="py-2.5 px-3 text-center">Total Barang Tersalur</th>
                                        <th class="py-2.5 px-3">Jenis Bantuan Diterima</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                    @foreach($villages as $villageName => $records)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                            <td class="py-2.5 px-3 text-center text-gray-500 dark:text-gray-400 font-medium">{{ $loop->iteration }}</td>
                                            <td class="py-2.5 px-3 font-bold text-gray-800 dark:text-gray-200">{{ $villageName }}</td>
                                            <td class="py-2.5 px-3 text-center font-semibold text-gray-700 dark:text-gray-300">{{ $records->count() }} KK</td>
                                            <td class="py-2.5 px-3 text-center font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($records->sum('quantity_received')) }} Unit</td>
                                            <td class="py-2.5 px-3 text-gray-600 dark:text-gray-400">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($records->pluck('aidInventory.item_name')->filter()->unique() as $itemName)
                                                        <span class="px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[11px] font-medium">
                                                            {{ $itemName }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection
