@extends('layouts.admin')

@section('title', 'Statistik Bantuan Bencana - Admin')
@section('page-title', 'Statistik & Laporan Bantuan Bencana')


@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white">
        Dashboard
    </a>
    <svg class="w-5 h-5 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Statistik Bantuan</span>
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
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kecamatan terdaftar dalam sistem</p>
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
                    Logistik Terkirim
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
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unit logistik ({{ $selectedYear !== 'all' ? 'Tahun ' . $selectedYear : 'Semua Tahun' }})</p>
            </div>
        </div>

        {{-- Card 4: Tingkat Pemenuhan --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300">
                    Capaian Ratio
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
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Persentase pemenuhan logistik</p>
            </div>
        </div>
    </div>

    {{-- SECTION BARU: VISUALISASI CHARTS PIMPINAN (BAR CHART & DONUT CHART) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {{-- Chart 1: Bar Chart Perbandingan Penyaluran per Kecamatan --}}
        <div class="lg:col-span-8 bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span>📈</span> Grafik Perbandingan Penyaluran Logistik per Kecamatan
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbandingan Target KK vs Logistik Tersalurkan (Tahun {{ $selectedYear !== 'all' ? $selectedYear : 'Semua Tahun' }})</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">
                    Bar Chart
                </span>
            </div>
            <div class="relative h-64 sm:h-72 w-full">
                <canvas id="disasterBarChart"></canvas>
            </div>
        </div>

        {{-- Chart 2: Donut Chart Distribusi Kategori Bantuan --}}
        <div class="lg:col-span-4 bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100 dark:border-gray-700/60">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span>Kategori Logistik</span> 
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Proporsi jenis barang bantuan</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-purple-50 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300">
                    Donut Chart
                </span>
            </div>
            <div class="relative h-64 sm:h-72 w-full flex items-center justify-center">
                <canvas id="categoryDonutChart"></canvas>
            </div>
        </div>
    </div>

    {{-- MAIN STATISTICAL TABLE & YEAR FILTER CARD --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl overflow-hidden">
        
        {{-- HEADER & YEAR FILTER --}}
        <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-700/60 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span>📊</span>
                    <span>Rekapitulasi Bantuan Bencana per Kecamatan</span>
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Informasi terintegrasi penyaluran bantuan logistik untuk monitoring pimpinan BPBD
                </p>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                {{-- Form Filter Tahun --}}
                <form method="GET" action="{{ route('admin.aid-disasters.index') }}" class="flex items-center gap-2">
                    <label for="yearSelect" class="text-xs font-bold text-gray-700 dark:text-gray-300 whitespace-nowrap">
                        Filter Tahun:
                    </label>
                    <select id="yearSelect" name="year" onchange="this.form.submit()" 
                            class="py-2 pl-3 pr-8 text-xs font-bold bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white cursor-pointer shadow-xs">
                        <option value="all" {{ (string)$selectedYear === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ (string)$selectedYear === (string)$year ? 'selected' : '' }}>
                                Tahun {{ $year }}
                            </option>
                        @endforeach
                    </select>

                    @if($selectedYear !== 'all')
                        <a href="{{ route('admin.aid-disasters.index') }}" class="text-xs font-medium text-rose-600 dark:text-rose-400 hover:underline">
                            Reset Filter
                        </a>
                    @endif
                </form>

                @can('export data')
                <a href="{{ route('admin.aid-disasters.print', ['type' => 'district', 'year' => $selectedYear]) }}" target="_blank" data-no-pjax
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-red-600 hover:bg-red-700 text-white shadow-md transition-all hover:scale-[1.02] active:scale-[0.98] whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak PDF Rekap Kecamatan</span>
                </a>
                @endcan
            </div>
        </div>

        {{-- TABLE DATA KECAMATAN --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50/80 dark:bg-gray-700/50 text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="py-3.5 px-4 text-center w-12">No</th>
                        <th scope="col" class="py-3.5 px-4">Kecamatan</th>
                        <th scope="col" class="py-3.5 px-4 text-center">Bantuan Tersalur ({{ $selectedYear !== 'all' ? 'Tahun ' . $selectedYear : 'Total' }})</th>
                        <!-- <th scope="col" class="py-3.5 px-4 text-center">Jumlah Warga (KK)</th> -->
                        <th scope="col" class="py-3.5 px-4 text-center">Jumlah Penerima (KK)</th>
                        <th scope="col" class="py-3.5 px-4 text-center">Jumlah Warga Belum Menerima (KK)</th>
                        <th scope="col" class="py-3.5 px-4">Progres Penyaluran</th>
                        <th scope="col" class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 bg-white dark:bg-gray-800">
                    @forelse($aidDisasters as $aid)
                        @php
                            // 1. Ambil jumlah KK yang sudah menerima (mendukung filter tahun jika dipilih)
                            $receivedCount = isset($villageBreakdown[$aid->id]) ? $villageBreakdown[$aid->id]['year_recipients'] : $aid->total_received;

                            // 2. Ambil total unit logistik tersalurkan
                            $yearDistributed = isset($villageBreakdown[$aid->id]) ? $villageBreakdown[$aid->id]['year_received'] : $aid->distributed_aid;

                            // 3. Hitung persentase: Total KK Menerima / Total Target KK Warga
                            $percentage = $aid->total_recipients > 0 ? min(100, round(($receivedCount / $aid->total_recipients) * 100, 1)) : 0;

                            // 4. Warna Progress Bar & Badge
                            $barColor   = $percentage >= 80 ? 'bg-emerald-500' : ($percentage >= 40 ? 'bg-indigo-500' : 'bg-amber-500');
                            $badgeColor = $percentage >= 80 ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : ($percentage >= 40 ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300': 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300');
                        @endphp
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-4 px-4 text-center font-medium text-gray-500 dark:text-gray-400">
                                {{ $loop->iteration + ($aidDisasters->currentPage() - 1) * $aidDisasters->perPage() }}
                            </td>
                            <td class="py-4 px-4 font-bold text-gray-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                    <span>Kecamatan {{ $aid->district_name }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-center font-extrabold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($yearDistributed) }} Unit
                            </td>
                            <!-- <td class="py-4 px-4 text-center font-semibold text-gray-700 dark:text-gray-300">
                                {{ number_format($aid->total_recipients) }} KK
                            </td> -->
                            <td class="py-4 px-4 text-center font-semibold text-gray-700 dark:text-gray-300">
                                {{ number_format($aid->total_received) }} Jiwa
                            </td>
                            <td class="py-4 px-4 text-center font-semibold text-gray-700 dark:text-gray-300">
                                {{ number_format($aid->total_recipients - $aid->total_received) }} Jiwa
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
                                <a href="{{ route('admin.aid-disasters.show', $aid) }}" 
                                   title="Detail Bantuan Bencana"
                                   class="inline-flex items-center gap-1 p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/60 transition-colors text-xs font-semibold">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400 dark:text-gray-500">
                                <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <p class="text-sm font-medium">Tidak ada data bantuan bencana ditemukan untuk filter tahun ini.</p>
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

    {{-- EXECUTIVE BREAKDOWN PER DESA WITH DEDICATED YEAR FILTER & PRINT PDF --}}
    <div class="mt-8 space-y-5">
        <div class="bg-gradient-to-r from-indigo-900 via-slate-900 to-slate-900 text-white p-6 rounded-2xl shadow-lg border border-slate-800 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 text-xs font-semibold mb-2 border border-indigo-500/30">
                    <span>📌 Executive Dashboard Pimpinan</span>
                </div>
                <h3 class="text-xl font-extrabold text-white">Rincian Penyaluran Logistik per Desa</h3>
                <p class="text-xs text-slate-300 mt-1">
                    Informasi detail sebaran penerima dan kategori bantuan logistik di tingkat desa (Tahun Filter: <strong class="text-emerald-400">{{ $selectedYear !== 'all' ? $selectedYear : 'Semua Tahun' }}</strong>)
                </p>
            </div>
            
            <div class="flex items-center gap-3 flex-wrap">
                {{-- Form Filter Tahun Khusus Section Breakdown Desa --}}
                <form method="GET" action="{{ route('admin.aid-disasters.index') }}" class="flex items-center gap-2 bg-slate-800/90 px-3 py-2 rounded-xl border border-slate-700">
                    <label for="villageYearSelect" class="text-xs font-bold text-slate-300 whitespace-nowrap">Tahun:</label>
                    <select id="villageYearSelect" name="year" onchange="this.form.submit()" 
                            class="py-1 px-2.5 text-xs font-bold bg-slate-900 border border-slate-700 rounded-lg text-white cursor-pointer focus:ring-1 focus:ring-indigo-400">
                        <option value="all" {{ (string)$selectedYear === 'all' ? 'selected' : '' }}>Semua Tahun</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ (string)$selectedYear === (string)$year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </form>

                @can('export data')
                <a href="{{ route('admin.aid-disasters.print', ['type' => 'village', 'year' => $selectedYear]) }}" target="_blank" data-no-pjax
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md transition-all hover:scale-[1.02] active:scale-[0.98] whitespace-nowrap">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak PDF Semua Desa</span>
                </a>
                @endcan
            </div>
        </div>

        {{-- LIST KECAMATAN & RINCIAN DESA --}}
        <div class="space-y-4">
            @foreach($aidDisasters as $aid)
                @php
                    $villages = $villageBreakdown[$aid->id]['villages'] ?? [];
                    $totalVillagesCount = count($villages);
                    $totalYearUnits = $villageBreakdown[$aid->id]['year_received'] ?? 0;
                    $totalYearRecipients = $villageBreakdown[$aid->id]['year_recipients'] ?? 0;
                @endphp
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden transition-all" x-data="{ open: true }">
                    {{-- Header Accordion --}}
                    <div @click="open = !open" class="p-4 sm:p-5 flex items-center justify-between cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors select-none">
                        <div class="flex items-center gap-3">
                            <div class="p-3 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 font-bold text-lg">
                                🏙️
                            </div>
                            <div>
                                <h4 class="text-base font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
                                    <span>Kecamatan {{ $aid->district_name }}</span>
                                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">
                                        {{ $totalVillagesCount }} Desa Terdaftar
                                    </span>
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Total Terdistribusi: <strong class="text-emerald-600 dark:text-emerald-400">{{ number_format($totalYearUnits) }} Unit</strong> &bull; 
                                    Penerima Terdata: <strong class="text-blue-600 dark:text-blue-400">{{ number_format($totalYearRecipients) }} KK</strong>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            @can('export data')
                            <a href="{{ route('admin.aid-disasters.print', ['type' => 'village', 'year' => $selectedYear, 'disaster_id' => $aid->id]) }}" target="_blank" data-no-pjax @click.stop title="Cetak PDF khusus Laporan Desa Kecamatan {{ $aid->district_name }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-xs transition-all hover:scale-[1.02] active:scale-[0.98]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                <span>Cetak PDF Desa</span>
                            </a>
                            @endcan

                            <span class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                <span x-show="!open">Lihat Detail Desa &darr;</span>
                                <span x-show="open">Sembunyikan &uarr;</span>
                            </span>
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    {{-- Content Accordion --}}
                    <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-gray-700/60 p-4 sm:p-5 bg-gray-50/50 dark:bg-gray-800/50">
                        @if(empty($villages) || count($villages) === 0)
                            <div class="text-center py-6 text-gray-400 dark:text-gray-500">
                                <p class="text-xs font-medium">
                                    Belum ada transaksi penyaluran terdata untuk kecamatan ini 
                                    {{ $selectedYear !== 'all' ? 'pada tahun ' . $selectedYear : '' }}.
                                </p>
                            </div>
                        @else
                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                                {{-- Tabel Desa (2 kolom) --}}
                                <div class="xl:col-span-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                    <div class="overflow-x-auto overflow-y-auto max-h-[500px] rounded-xl">
                                        <table class="w-full text-xs sm:text-sm text-left bg-white dark:bg-gray-800">
                                            <thead class="bg-gray-100 dark:bg-gray-700/80 text-gray-600 dark:text-gray-300 uppercase tracking-wider font-bold">
                                                <tr>
                                                    <th class="py-3 px-2 sm:px-4 text-center w-12">No</th>
                                                    <th class="py-3 px-2 sm:px-4">Nama Desa / Kelurahan</th>
                                                    <th class="py-3 px-2 sm:px-4 text-center">Jumlah Penerima</th>
                                                    <th class="py-3 px-2 sm:px-4 text-center">Total Barang</th>
                                                    <th class="py-3 px-2 sm:px-4 hidden md:table-cell">Jenis Bantuan</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                                @foreach($villages as $villageName => $records)
                                                    @php
                                                        $recipientsInVillage = $records->pluck('beneficiary_id')->unique()->count();
                                                        $totalQtyInVillage = $records->sum('quantity_received');
                                                    @endphp
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                                        <td class="py-3 px-2 sm:px-4 text-center text-gray-500 dark:text-gray-400 font-bold">{{ $loop->iteration }}</td>
                                                        <td class="py-3 px-2 sm:px-4 font-bold text-gray-900 dark:text-white">
                                                            <div class="flex items-center gap-2">
                                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                                <span class="truncate max-w-[120px] sm:max-w-none">{{ $villageName }}</span>
                                                            </div>
                                                        </td>
                                                        <td class="py-3 px-2 sm:px-4 text-center font-bold text-gray-800 dark:text-gray-200">
                                                            {{ number_format($recipientsInVillage) }} KK
                                                        </td>
                                                        <td class="py-3 px-2 sm:px-4 text-center font-extrabold text-emerald-600 dark:text-emerald-400">
                                                            {{ number_format($totalQtyInVillage) }} Unit
                                                        </td>
                                                        <td class="py-3 px-2 sm:px-4 hidden md:table-cell">
                                                            <div class="flex flex-wrap gap-1.5">
                                                                @foreach($records->pluck('aidInventory.item_name')->filter()->unique() as $itemName)
                                                                    <span class="px-2 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 text-[11px] font-semibold border border-indigo-100 dark:border-indigo-800/40">
                                                                        📦 {{ $itemName }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- Chart Desa (kanan) --}}
                                <div class="flex flex-col gap-6">
                                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-2 relative h-64">
                                        <canvas id="villageRecipientsChart-{{ $aid->id }}"></canvas>
                                    </div>
                                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-2 relative h-64">
                                        <canvas id="villageItemsChart-{{ $aid->id }}"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94a3b8' : '#64748b';
        const gridColor = isDark ? 'rgba(51, 65, 85, 0.4)' : 'rgba(226, 232, 240, 0.8)';

        // Data dari Controller
        const chartLabels = @json($chartLabels ?? []);
        const chartTargets = @json($chartTargets ?? []);
        const chartDistributed = @json($chartDistributed ?? []);

        const categoryLabels = @json($categoryLabels ?? []);
        const categoryValues = @json($categoryValues ?? []);

        // 1. BAR CHART: Perbandingan Target KK vs Logistik Tersalurkan per Kecamatan
        const barCtx = document.getElementById('disasterBarChart');
        if (barCtx) {
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Target KK',
                            data: chartTargets,
                            backgroundColor: isDark ? 'rgba(99, 102, 241, 0.7)' : 'rgba(79, 70, 229, 0.85)',
                            borderColor: '#6366f1',
                            borderWidth: 1,
                            borderRadius: 8,
                        },
                        {
                            label: 'Logistik Tersalurkan (Unit)',
                            data: chartDistributed,
                            backgroundColor: isDark ? 'rgba(16, 185, 129, 0.7)' : 'rgba(16, 185, 129, 0.85)',
                            borderColor: '#10b981',
                            borderWidth: 1,
                            borderRadius: 8,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: { color: textColor, font: { family: 'Figtree', weight: 'bold' } }
                        }
                    },
                    scales: {
                        x: {
                            ticks: { color: textColor, font: { family: 'Figtree' } },
                            grid: { color: 'transparent' }
                        },
                        y: {
                            ticks: { color: textColor, font: { family: 'Figtree' } },
                            grid: { color: gridColor }
                        }
                    }
                }
            });
        }

        // 2. DONUT CHART: Proporsi Kategori Logistik Bantuan
        const donutCtx = document.getElementById('categoryDonutChart');
        if (donutCtx) {
            new Chart(donutCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryLabels,
                    datasets: [{
                        data: categoryValues,
                        backgroundColor: [
                            '#6366f1', // Indigo
                            '#10b981', // Emerald
                            '#f59e0b', // Amber
                            '#ec4899', // Pink
                            '#3b82f6', // Blue
                            '#8b5cf6'  // Purple
                        ],
                        borderWidth: 2,
                        borderColor: isDark ? '#1e293b' : '#ffffff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { color: textColor, font: { family: 'Figtree', size: 11 } }
                        }
                    },
                    cutout: '58%'
                }
            });
        }

        // 3. BAR CHARTS PER KECAMATAN (RINCIAN DESA)
        @foreach($aidDisasters as $aid)
            @php
                $vData = $villageBreakdown[$aid->id]['villages'] ?? collect();
                $vLabels = collect($vData)->keys()->values()->toArray();
                $vRecipients = collect($vData)->map(fn($records) => collect($records)->pluck('beneficiary_id')->unique()->count())->values()->toArray();
                $vQuantities = collect($vData)->map(fn($records) => collect($records)->sum('quantity_received'))->values()->toArray();
            @endphp

            const recipientsEl{{ $aid->id }} = document.getElementById('villageRecipientsChart-{{ $aid->id }}');
            if (recipientsEl{{ $aid->id }}) {
                new Chart(recipientsEl{{ $aid->id }}, {
                    type: 'bar',
                    data: {
                        labels: @json($vLabels),
                        datasets: [{
                            label: 'Jumlah Penerima (KK)',
                            data: @json($vRecipients),
                            backgroundColor: isDark ? 'rgba(56, 189, 248, 0.75)' : 'rgba(14, 165, 233, 0.85)',
                            borderColor: '#0284c7',
                            borderWidth: 1,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            title: {
                                display: true,
                                text: 'Jumlah Penerima (KK) per Desa',
                                color: textColor,
                                font: { family: 'Figtree', weight: 'bold', size: 12 }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor, font: { family: 'Figtree', size: 11 } },
                                grid: { color: 'transparent' }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: textColor, font: { family: 'Figtree', size: 11 } },
                                grid: { color: gridColor }
                            }
                        }
                    }
                });
            }

            const itemsEl{{ $aid->id }} = document.getElementById('villageItemsChart-{{ $aid->id }}');
            if (itemsEl{{ $aid->id }}) {
                new Chart(itemsEl{{ $aid->id }}, {
                    type: 'bar',
                    data: {
                        labels: @json($vLabels),
                        datasets: [{
                            label: 'Total Barang Tersalur (Unit)',
                            data: @json($vQuantities),
                            backgroundColor: isDark ? 'rgba(244, 63, 94, 0.75)' : 'rgba(225, 29, 72, 0.85)',
                            borderColor: '#be123c',
                            borderWidth: 1,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            title: {
                                display: true,
                                text: 'Total Barang Tersalur per Desa',
                                color: textColor,
                                font: { family: 'Figtree', weight: 'bold', size: 12 }
                            }
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor, font: { family: 'Figtree', size: 11 } },
                                grid: { color: 'transparent' }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: textColor, font: { family: 'Figtree', size: 11 } },
                                grid: { color: gridColor }
                            }
                        }
                    }
                });
            }
        @endforeach
    });
</script>
@endpush

