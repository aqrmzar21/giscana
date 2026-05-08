@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@section('page-title', 'Dashboard')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
        Dashboard
    </a>
</li>
@endsection

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Zona Bencana</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ \App\Models\DisasterZone::count() }}</dd>
                    </dl>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.disaster-zones.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Lihat Detail <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Rute Evakuasi</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ \App\Models\EvacuationRoute::count() }}</dd>
                    </dl>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.evacuation-routes.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Lihat Detail <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Fasilitas Evakuasi</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ \App\Models\EvacuationFacility::count() }}</dd>
                    </dl>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.evacuation-facilities.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Lihat Detail <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Bantuan Bencana</dt>
                        <dd class="text-lg font-semibold text-gray-900">{{ \App\Models\AidDisaster::count() }}</dd>
                    </dl>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('admin.aid-disasters.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                    Lihat Detail <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div> -->
    
</div>

<!-- Welcome Card + Pie Chart Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

    {{-- Welcome Card --}}
    <div class="lg:col-span-1 bg-white shadow rounded-lg flex flex-col justify-center">
        <div class="px-4 py-6 sm:p-6 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-indigo-100 mb-4">
                <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Selamat Datang!</h3>
            <p class="text-gray-500 text-sm mb-4">
                Halo <strong class="text-gray-800">{{ Auth::user()->name }}</strong>, selamat datang di sistem informasi geografis untuk tanggap darurat bencana alam.
            </p>
            <a href="{{ route('admin.aid-disasters.index') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="mr-2 -ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                Data Bantuan Bencana
            </a>
        </div>

        {{-- Mini stats --}}
        <div class="border-t border-gray-100 px-4 py-4 sm:px-6 grid grid-cols-2 gap-3 text-center">
            <div>
                <p class="text-2xl font-bold text-indigo-600">{{ \App\Models\AidDisaster::sum('distributed_aid') }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Total Bantuan Tersalur</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-green-600">{{ \App\Models\AidDisaster::sum('total_recipients') }}</p>
                <p class="text-xs text-gray-500 mt-0.5">Total Penerima</p>
            </div>
        </div>
    </div>

    {{-- Pie Chart Card --}}
    <div class="lg:col-span-2 bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-gray-900">Distribusi Bantuan per Kecamatan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Top 5 kecamatan berdasarkan jumlah bantuan tersalur</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">
                Top 5
            </span>
        </div>

        @if($aidByDistrict->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-gray-400">
                <svg class="h-12 w-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                </svg>
                <p class="text-sm">Belum ada data distribusi bantuan.</p>
            </div>
        @else
            <div class="p-5 flex flex-col sm:flex-row items-center gap-6">
                {{-- Canvas --}}
                <div class="flex-shrink-0" style="width:220px; height:220px;">
                    <canvas id="aidPieChart"></canvas>
                </div>

                {{-- Legend + Detail --}}
                <div class="flex-1 w-full">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-xs text-gray-500 border-b border-gray-100">
                                <th class="pb-2 text-left font-medium">Kecamatan</th>
                                <th class="pb-2 text-right font-medium">Tersalur</th>
                                <th class="pb-2 text-right font-medium">Penerima</th>
                                <th class="pb-2 text-right font-medium">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @php
                                $colors = ['#6366f1','#22c55e','#f59e0b','#ef4444','#14b8a6'];
                                $totalDistributed = $aidByDistrict->sum('distributed_aid') ?: 1;
                            @endphp
                            @foreach($aidByDistrict as $i => $aid)
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 pr-3">
                                    <div class="flex items-center gap-2">
                                        <span class="inline-block w-3 h-3 rounded-full flex-shrink-0"
                                              style="background:{{ $colors[$i] ?? '#9ca3af' }}"></span>
                                        <span class="font-medium text-gray-800 truncate max-w-[130px]">{{ $aid->district_name }}</span>
                                    </div>
                                </td>
                                <td class="py-2 text-right text-gray-600">{{ number_format($aid->distributed_aid) }}</td>
                                <td class="py-2 text-right text-gray-600">{{ number_format($aid->total_recipients) }}</td>
                                <td class="py-2 text-right">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold"
                                          style="background:{{ $colors[$i] ?? '#9ca3af' }}22; color:{{ $colors[$i] ?? '#9ca3af' }}">
                                        {{ $totalDistributed > 0 ? round(($aid->distributed_aid / $totalDistributed) * 100, 1) : 0 }}%
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
</div>

{{-- Chart.js via CDN + Inisialisasi --}}
@if($aidByDistrict->isNotEmpty())
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
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
                borderColor: '#fff',
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '62%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const pct   = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return ` ${context.label}: ${context.parsed.toLocaleString()} (${pct}%)`;
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
});
</script>
@endif
@endsection

