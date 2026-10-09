@extends('layouts.admin')

@section('title', 'Dashboard | Giscana')

@section('page-title', 'Dashboard')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white">
        Dashboard
    </a>
</li>
@endsection

@section('content')

{{-- 1. HERO & WELCOME BANNER --}}
<div class="relative overflow-hidden bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 text-white rounded-2xl shadow-xl mb-8">
    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute right-1/3 -top-10 w-48 h-48 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

    <div class="px-6 py-8 sm:px-8 sm:py-10 relative z-10 flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Sistem Tanggap Darurat Aktif
                </span>
                <span class="text-xs text-indigo-200/80">| Bone Bolango GIS</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">
                Selamat Datang, {{ Auth::user()?->name }}!
            </h1>
            <p class="mt-2 text-sm sm:text-base text-indigo-100/90 max-w-2xl leading-relaxed">
                Pusat Komando dan Pengawasan Geografis Bencana Alam. Pantau rute evakuasi, fasilitas pengungsian, zona rawan, dan efisiensi penyaluran bantuan logistik secara *real-time*.
            </p>
        </div>

        {{-- Quick Action Buttons --}}
        <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto">
            <a href="{{ route('dashboard.map') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/30 transition-all duration-200 transform hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
                Peta SIG Interaktif
            </a>
            @if(Auth::user()?->isAdmin())
            <a href="{{ route('admin.aid-distributions.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-white/10 hover:bg-white/20 text-white backdrop-blur-md border border-white/20 transition-all duration-200">
                <svg class="w-5 h-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Catat Distribusi
            </a>
            @endif
        </div>
    </div>
</div>

{{-- 2. OPERATIONAL SUMMARY STAT CARDS (GRID 6 CARDS) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 sm:gap-5 mb-8">
    
    {{-- 1. Zona Bencana --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div class="p-3 rounded-xl bg-red-500/10 text-red-600 dark:text-red-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-300">
                {{ $stats['disaster_zones_high_risk'] }} Rawan
            </span>
        </div>
        <div class="mt-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Zona Bencana</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['disaster_zones_count']) }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                {{ number_format($stats['total_affected_population']) }} jiwa terdampak
            </p>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/50">
            <a href="{{ route('admin.disaster-zones.index') }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 inline-flex items-center gap-1">
                Detail Lokasi &rarr;
            </a>
        </div>
    </div>

    {{-- 2. Rute Evakuasi --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div class="p-3 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
            </div>
            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-300">
                {{ $stats['evacuation_routes_accessible'] }} Aksesibel
            </span>
        </div>
        <div class="mt-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rute Evakuasi</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['evacuation_routes_count']) }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Jalur darurat terhubung
            </p>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/50">
            <a href="{{ route('admin.evacuation-routes.index') }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 inline-flex items-center gap-1">
                Daftar Rute &rarr;
            </a>
        </div>
    </div>

    {{-- 3. Fasilitas Pengungsian --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div class="p-3 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300">
                {{ $stats['medical_facilities_count'] }} Faskes Medis
            </span>
        </div>
        <div class="mt-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fasilitas</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['evacuation_facilities_count']) }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Terkonfirmasi dan tercatat
            </p>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/50">
            <a href="{{ route('admin.evacuation-facilities.index') }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 inline-flex items-center gap-1">
                Titik Kumpul &rarr;
            </a>
        </div>
    </div>

    {{-- 4. Penerima Bantuan --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div class="p-3 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-300">
                Warga Terdata
            </span>
        </div>
        <div class="mt-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Penerima Bantuan</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['aid_beneficiaries_count']) }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Kepala Keluarga (KK)
            </p>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/50">
            <a href="{{ route('admin.aid-beneficiaries.index') }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 inline-flex items-center gap-1">
                Daftar Penerima &rarr;
            </a>
        </div>
    </div>

    {{-- 5. Stok Logistik Bantuan --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div class="p-3 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300">
                {{ $stats['aid_inventories_count'] }} Jenis Barang
            </span>
        </div>
        <div class="mt-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stok Logistik</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['aid_inventory_total_stock']) }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Unit stok tersisa
            </p>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/50">
            <a href="{{ route('admin.aid-inventories.index') }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 inline-flex items-center gap-1">
                Gudang Logistik &rarr;
            </a>
        </div>
    </div>

    {{-- 6. Total Bantuan Tersalur --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700/60 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between">
            <div class="p-3 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-300">
                Tersalurkan
            </span>
        </div>
        <div class="mt-4">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bantuan Tersalur</p>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($stats['total_distributed_aid']) }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Paket logistik terkirim
            </p>
        </div>
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700/50">
            <a href="{{ route('admin.aid-distributions.index') }}" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 inline-flex items-center gap-1">
                Riwayat Distribusi &rarr;
            </a>
        </div>
    </div>

</div>

{{-- 3. CHARTS & PROGRESS ROW (2 COLUMNS) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    {{-- Pie / Doughnut Chart Card --}}
<<<<<<< HEAD
    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl overflow-hidden flex flex-col justify-between">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Distribusi Bantuan per Kecamatan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Top 5 kecamatan berdasarkan akumulasi bantuan tersalur</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-indigo-50 dark:bg-indigo-900/40 px-3 py-1 text-xs font-semibold text-indigo-700 dark:text-indigo-300">
=======
    <div class="lg:col-span-1 bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Distribusi Logistik</h3>
                <p class="text-xs text-gray-500 mt-0.5">Top 5 barang berdasarkan stok tersisa</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
>>>>>>> main
                Top 5
            </span>
        </div>

<<<<<<< HEAD
        @if($aidByDistrict->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500">
                <svg class="h-12 w-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                <p class="text-sm">Belum ada data distribusi bantuan.</p>
            </div>
        @else
            <div class="p-6 flex flex-col sm:flex-row items-center gap-6">
                {{-- Canvas Chart --}}
                <div class="flex-shrink-0" style="width:210px; height:210px;">
                    <canvas id="aidPieChart"></canvas>
                </div>

                {{-- Table Legend --}}
                <div class="flex-1 w-full overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-400 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700/60">
                                <th class="pb-2 text-left font-semibold">Kecamatan</th>
                                <th class="pb-2 text-right font-semibold">Tersalur</th>
                                <th class="pb-2 text-right font-semibold">Penerima</th>
                                <th class="pb-2 text-right font-semibold">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/40">
                            @php
                                $colors = ['#6366f1','#22c55e','#f59e0b','#ef4444','#14b8a6'];
                                $totalDistributed = $aidByDistrict->sum('distributed_aid') ?: 1;
                            @endphp
                            @foreach($aidByDistrict as $i => $aid)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="py-2.5 pr-3">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block w-3 h-3 rounded-full flex-shrink-0" style="background:{{ $colors[$i] ?? '#9ca3af' }}"></span>
                                        <span class="font-semibold text-gray-800 dark:text-gray-200 truncate max-w-[120px]">{{ $aid->district_name }}</span>
                                    </div>
                                </td>
                                <td class="py-2.5 text-right font-medium text-gray-700 dark:text-gray-300">{{ number_format($aid->distributed_aid) }}</td>
                                <td class="py-2.5 text-right font-medium text-gray-500 dark:text-gray-400">{{ number_format($aid->total_recipients) }}</td>
                                <td class="py-2.5 text-right">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-bold" style="background:{{ $colors[$i] ?? '#9ca3af' }}22; color:{{ $colors[$i] ?? '#9ca3af' }}">
                                        {{ $totalDistributed > 0 ? round(($aid->distributed_aid / $totalDistributed) * 100, 1) : 0 }}%
=======
        @if($inventories->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg class="h-12 w-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                <p class="text-sm">Belum ada data logistik.</p>
            </div>
        @else
            <div class="p-5 flex flex-col sm:flex-row items-center gap-6">
                {{-- Canvas --}}
                <div class="flex-shrink-0" style="width:220px; height:220px;">
                    <canvas id="logisticPieChart"></canvas>
                </div>

                {{-- Legend + Detail --}}
                <div class="flex-1 w-full">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-500 border-b border-gray-100">
                                <th class="pb-2 text-left font-medium">Barang</th>
                                <th class="pb-2 text-right font-medium">Stok Masuk</th>
                                <th class="pb-2 text-right font-medium">Sisa Stok</th>
                                <th class="pb-2 text-right font-medium">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @php
                                $colors = ['#6366f1','#22c55e','#f59e0b','#ef4444','#14b8a6'];
                                $totalStock = $inventories->sum('initial_stock') ?: 1;
                            @endphp
                            @foreach($inventories->take(5) as $i => $inventory)
                            @php
                                $stockPercent = $inventory->initial_stock > 0
                                    ? round(($inventory->remaining_stock / $inventory->initial_stock) * 100, 1)
                                    : 0;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 pr-3">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block w-3 h-3 rounded-full flex-shrink-0"
                                            style="background:{{ $colors[$i] ?? '#9ca3af' }}"></span>
                                        <span class="font-medium text-gray-800 truncate max-w-[130px]">{{ $inventory->item_name }}</span>
                                    </div>
                                </td>
                                <td class="py-2 text-right text-gray-600">{{ number_format($inventory->initial_stock) }}</td>
                                <td class="py-2 text-right text-gray-600">{{ number_format($inventory->remaining_stock) }}</td>
                                <td class="py-2 text-right">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                        style="background:{{ $colors[$i] ?? '#9ca3af' }}22; color:{{ $colors[$i] ?? '#9ca3af' }}">
                                        {{ $stockPercent }}%
>>>>>>> main
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- Progress Table Card --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl overflow-hidden flex flex-col justify-between">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Progres Pemenuhan Bantuan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Persentase bantuan tersalur vs target penerima per kecamatan</p>
            </div>
            <a href="{{ route('admin.aid-beneficiaries.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                Kelola Target &rarr;
            </a>
        </div>

        <div class="p-6 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-400 dark:text-gray-400 border-b border-gray-100 dark:border-gray-700/60">
                        <th class="pb-3 text-left font-semibold">Kecamatan</th>
<<<<<<< HEAD
                        <th class="pb-3 text-center font-semibold">Target / Tersalur</th>
=======
                        <th class="pb-3 text-center font-semibold">Tersalur / Target</th>
>>>>>>> main
                        <th class="pb-3 text-left font-semibold">Progres Penyaluran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/40">
                    @forelse($aidDisasters as $aid)
                        @php
<<<<<<< HEAD
                            $percentage = $aid->total_recipients > 0 ? min(100, round(($aid->distributed_aid / $aid->total_recipients) * 100, 1)) : 0;
=======
                            $percentage = $aid->total_recipients > 0 ? min(100, round(($aid->total_received / $aid->total_recipients) * 100, 1)) : 0;
>>>>>>> main
                            $barColor = $percentage >= 80 ? 'bg-emerald-500' : ($percentage >= 40 ? 'bg-indigo-500' : 'bg-amber-500');
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="py-3 font-semibold text-gray-800 dark:text-gray-200 whitespace-nowrap">
                                {{ $aid->district_name }}
                            </td>
                            <td class="py-3 text-center text-xs text-gray-600 dark:text-gray-300 font-medium">
<<<<<<< HEAD
                                <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ number_format($aid->distributed_aid) }}</span> / {{ number_format($aid->total_recipients) }}
=======
                                <span class="text-indigo-600 dark:text-indigo-400 font-bold">{{ number_format($aid->total_received) }}</span> / {{ number_format($aid->total_recipients) }}
>>>>>>> main
                            </td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                                        <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-700 dark:text-gray-300 w-11 text-right">{{ $percentage }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                                Belum ada data kecamatan terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- 4. SECONDARY DATA ROW (LOGISTICS STATUS & RECENT DISTRIBUTIONS) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    {{-- Inventory Alert & Stock Status Card --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl p-6">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100 dark:border-gray-700/60">
            <div>
                <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Status Logistik & Alert Stok
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pemantauan stok barang bantuan di gudang logistik</p>
            </div>
            <a href="{{ route('admin.aid-inventories.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="space-y-4">
            @forelse($aidInventories as $item)
                @php
                    $ratio = $item->initial_stock > 0 ? round(($item->remaining_stock / $item->initial_stock) * 100) : 0;
                    $statusColor = $ratio <= 20 ? 'text-red-600 bg-red-50 dark:bg-red-900/30 dark:text-red-300 border-red-200' : ($ratio <= 50 ? 'text-amber-600 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-300 border-amber-200' : 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-200');
                    $badgeLabel = $ratio <= 20 ? 'Kritis' : ($ratio <= 50 ? 'Menipis' : 'Aman');
                    $barBg = $ratio <= 20 ? 'bg-red-500' : ($ratio <= 50 ? 'bg-amber-500' : 'bg-emerald-500');
                @endphp
                <div class="p-3.5 rounded-xl bg-gray-50/70 dark:bg-gray-700/30 border border-gray-100 dark:border-gray-700/40">
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-sm text-gray-800 dark:text-gray-200">{{ $item->item_name }}</span>
                            <span class="text-[10px] uppercase font-semibold px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300">
                                {{ $item->category ?? 'Logistik' }}
                            </span>
                        </div>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full border {{ $statusColor }}">
                            {{ $badgeLabel }} ({{ $ratio }}%)
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-2">
                        <span>Sumber: <strong class="text-gray-700 dark:text-gray-300">{{ $item->source ?? '-' }}</strong></span>
                        <span>Stok Sisa: <strong class="text-gray-900 dark:text-white font-bold">{{ number_format($item->remaining_stock) }}</strong> / {{ number_format($item->initial_stock) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 overflow-hidden">
                        <div class="{{ $barBg }} h-2 rounded-full transition-all duration-500" style="width: {{ $ratio }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-center py-6 text-sm text-gray-400 dark:text-gray-500">Belum ada data barang logistik.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Aid Distribution History Card --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl p-6 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between pb-4 mb-4 border-b border-gray-100 dark:border-gray-700/60">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Riwayat Penyaluran Terbaru
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Catatan pengiriman logistik bantuan terkini ke warga</p>
                </div>
                <a href="{{ route('admin.aid-distributions.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Semua Catatan &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentDistributions as $dist)
                    <div class="flex items-start justify-between p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors border border-transparent hover:border-gray-100 dark:hover:border-gray-700/40">
                        <div class="flex items-start gap-3">
                            <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 mt-0.5 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $dist->beneficiary?->recipient_name ?? 'Warga Penerima' }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Desa {{ $dist->village?->name ?? '-' }} &bull; Barang: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $dist->aidInventory?->item_name ?? 'Logistik' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="text-right shrink-0 ml-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">
                                +{{ number_format($dist->quantity_received) }} unit
                            </span>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">
                                {{ $dist->distribution_date ? \Carbon\Carbon::parse($dist->distribution_date)->format('d M Y') : '-' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-center py-6 text-sm text-gray-400 dark:text-gray-500">Belum ada riwayat penyaluran bantuan.</p>
                @endforelse
            </div>
        </div>

        @if(Auth::user()?->isAdmin())
        <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60">
            <a href="{{ route('admin.aid-distributions.create') }}" class="w-full py-2 px-4 rounded-xl border border-dashed border-indigo-300 dark:border-indigo-600 text-indigo-600 dark:text-indigo-400 text-xs font-semibold hover:bg-indigo-50 dark:hover:bg-indigo-900/30 flex items-center justify-center gap-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Input Catatan Distribusi Baru
            </a>
        </div>
        @endif
    </div>

</div>

{{-- 5. KESIAPSIAGAAN INFRASTRUKTUR & TIPE BENCANA (3 CARDS GRID) --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    
    {{-- Card 1: Fasilitas Pengungsian & Faskes --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Kesiapsiagaan Posko</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Fasilitas penampungan pengungsi</p>
            </div>
        </div>
        <div class="space-y-2 text-xs">
            <div class="flex justify-between py-1.5 border-b border-gray-50 dark:border-gray-700/40">
                <span class="text-gray-600 dark:text-gray-400">Total Daya Tampung:</span>
                <strong class="text-gray-900 dark:text-white font-bold">{{ number_format($stats['total_facility_capacity']) }} Jiwa</strong>
            </div>
            <div class="flex justify-between py-1.5 border-b border-gray-50 dark:border-gray-700/40">
                <span class="text-gray-600 dark:text-gray-400">Memiliki Posko Kesehatan:</span>
                <strong class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $stats['medical_facilities_count'] }} Posko</strong>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-gray-600 dark:text-gray-400">Memiliki Gudang Pangan:</span>
                <strong class="text-blue-600 dark:text-blue-400 font-bold">{{ $stats['food_facilities_count'] }} Posko</strong>
            </div>
        </div>
    </div>

    {{-- Card 2: Status Aksesibilitas Rute --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Akses Rute Evakuasi</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Kondisi jalan darurat ke posko</p>
            </div>
        </div>
        <div class="space-y-2 text-xs">
            <div class="flex justify-between py-1.5 border-b border-gray-50 dark:border-gray-700/40">
                <span class="text-gray-600 dark:text-gray-400">Total Jalur Terdata:</span>
                <strong class="text-gray-900 dark:text-white font-bold">{{ $stats['evacuation_routes_count'] }} Jalur</strong>
            </div>
            <div class="flex justify-between py-1.5 border-b border-gray-50 dark:border-gray-700/40">
                <span class="text-gray-600 dark:text-gray-400">Rute Aman / Aksesibel:</span>
                <strong class="text-emerald-600 dark:text-emerald-400 font-bold">{{ $stats['evacuation_routes_accessible'] }} Jalur</strong>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-gray-600 dark:text-gray-400">Rute Terhambat / Tertutup:</span>
                <strong class="text-red-500 font-bold">{{ max(0, $stats['evacuation_routes_count'] - $stats['evacuation_routes_accessible']) }} Jalur</strong>
            </div>
        </div>
    </div>

    {{-- Card 3: Tipe Bencana Terdaftar --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl p-5">
        <div class="flex items-center gap-3 mb-3">
            <div class="p-2.5 rounded-xl bg-red-500/10 text-red-600 dark:text-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-white">Jenis Bencana Rawan</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400">Kategori potensi bencana lokal</p>
            </div>
        </div>
        <div class="space-y-2 text-xs">
            @forelse($disasterTypes as $dt)
                <div class="flex justify-between py-1.5 border-b border-gray-50 dark:border-gray-700/40 last:border-0">
                    <span class="capitalize text-gray-700 dark:text-gray-300 font-medium">{{ $dt->disaster_type }}</span>
                    <strong class="text-gray-900 dark:text-white font-bold">{{ $dt->count }} Lokasi ({{ number_format($dt->total_affected) }} Jiwa)</strong>
                </div>
            @empty
                <p class="text-gray-400 dark:text-gray-500 py-2">Belum ada jenis bencana teridentifikasi.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- Chart.js CDN & Doughnut Initialization --}}
@if($aidByDistrict->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const ctx = document.getElementById('aidPieChart');
        if (!ctx) return;

        const labels  = @json($aidByDistrict->pluck('district_name'));
        const data    = @json($aidByDistrict->pluck('distributed_aid'));
        const colors  = ['#6366f1','#22c55e','#f59e0b','#ef4444','#14b8a6'];
        const hovers  = ['#4f46e5','#16a34a','#d97706','#dc2626','#0d9488'];

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    hoverBackgroundColor: hovers,
                    borderWidth: 2,
                    borderColor: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '64%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const pct   = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${context.parsed.toLocaleString()} Unit (${pct}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 800,
                    easing: 'easeInOutQuart',
                }
            }
        });
    })();
</script>
@endif
<<<<<<< HEAD
=======
@if($inventories->isNotEmpty())
<script>
(function () {
    const ctx = document.getElementById('logisticPieChart');
    if (!ctx) return;

    // Ambil label dan data dari koleksi inventories
    const labels  = @json($inventories->take(5)->pluck('item_name'));
    const data    = @json($inventories->take(5)->pluck('remaining_stock'));
    const colors  = ['#6366f1','#22c55e','#f59e0b','#ef4444','#14b8a6'];
    const hovers  = ['#4f46e5','#16a34a','#d97706','#dc2626','#0d9488'];

    new Chart(ctx, {
        type: 'pie', // Gunakan 'doughnut' jika ingin menggunakan opsi cutout
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                hoverBackgroundColor: hovers,
                borderWidth: 2,
                borderColor: document.documentElement.classList.contains('dark') ? '#1f2937' : '#ffffff',
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        // Menggunakan callback 'formattedValue' atau menyesuaikan baris 'label'
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const pct   = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            // Format ini mencegah label diulang oleh bawaan Chart.js
                            return ` Stok: ${context.parsed.toLocaleString()} (${pct}%)`;
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 800,
                easing: 'easeInOutQuart',
            }
        }
    });
})();
</script>
@endif
>>>>>>> main
@endsection
