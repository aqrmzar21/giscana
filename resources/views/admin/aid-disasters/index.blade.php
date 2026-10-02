@extends('layouts.admin')

@section('title', 'Data Bantuan Bencana - Admin')

@section('page-title', 'Manajemen Data Bantuan Bencana')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Data Bantuan Bencana</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">

        @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
        @endif

        <div class="sm:flex sm:items-center mb-4">
            <div class="sm:flex-auto">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Daftar Bantuan Bencana</h3>
                <p class="mt-2 text-sm text-gray-700">Data jumlah penerima dan distribusi bantuan per kecamatan.</p>
            </div>
            <!-- <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <a href="{{ route('admin.aid-disasters.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Data Baru
                </a>
            </div> -->
        </div>

        {{-- Bantuan Tersalurkan --}}
        <!-- <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-5">
            @foreach($aidDisasters as $aid)
            <div class="bg-white shadow rounded-lg p-5">
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-medium text-gray-500">{{ $aid->district_name }}</dt>
                    <button 
                        class="toggle-btn text-xs text-indigo-600 hover:text-indigo-900">
                        Lihat Desa
                    </button>
                </div>
                <dd class="mt-2 text-lg font-semibold text-indigo-900">{{ number_format($aid->distributed_aid ?? 0) }}
                    <span class="text-xs ">Bantuan Tersalurkan</span>
                </dd>
            </div>
            @endforeach
        </div> -->

        <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold textrt-gray-900 sm:pl-6">NO</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kecamatan</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Jumlah KK</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">KK Menerima</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                        <!-- <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Bantuan Tersalur</th> -->
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($aidDisasters as $aid)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $loop->iteration }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $aid->district_name }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ number_format($aid->total_recipients) }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-green-600 font-semibold">
                            {{ number_format($aid->total_received) }} orang
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                    <div class="bg-green-400 h-2.5 rounded-full" style="width: {{ $aid->received_percentage }}%"></div>
                                </div>
                                <span>{{ $aid->received_percentage }}%</span>
                            </div>
                        </td>                        
                        <!-- <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            @if($aid->is_active)
                            <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Aktif</span>
                            @else
                            <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">Selesai</span>
                            @endif
                        </td> -->
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-center text-sm font-medium sm:pr-6">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.aid-disasters.show', $aid) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @can('update data')
                                <a href="{{ route('admin.aid-disasters.edit', $aid) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    
                    @empty
                    <tr>
                        <td colspan="7" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 text-center sm:pl-6">
                            Tidak ada data bantuan bencana.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Breakdown per desa --}}
        {{-- Tabel desa per kecamatan --}}
       <div class="space-y-6 mt-6">
            @foreach($aidDisasters as $aid)
                <div class="border rounded-lg shadow bg-white p-4 my-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-700">
                            {{ $aid->district_name }}
                        </h3>
                        <p class="mt-2 text-lg font-semibold text-indigo-900">
                            {{ number_format($aid->distributed_aid ?? 0) }} Bantuan Tersalurkan
                            <span class="text-sm text-gray-500">
                                | {{ $villageBreakdown[$aid->id]['total_villages'] ?? 0  }} Desa
                            </span>
                        </p>
                        <button class="toggle-btn text-xs text-indigo-600 hover:text-indigo-900">
                            Lihat Desa
                        </button>
                    </div>

                    {{-- Breakdown desa --}}
                    <div class="village-table hidden mt-3">
                        <table class="min-w-full divide-y divide-gray-200 bg-white shadow rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">No</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Desa</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Total Penerima</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Total Barang</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Jenis Bantuan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach(($villageBreakdown[$aid->id]['villages'] ?? []) as $villageName => $records)
                                    <tr>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $loop->iteration }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $villageName }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $records->count() }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-900">{{ $records->sum('quantity_received') }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-500">
                                            {{ implode(', ', $records->pluck('aidInventory.item_name')->unique()->toArray()) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

        @if($aidDisasters->hasPages())
        <div class="mt-4">
            {{ $aidDisasters->links() }}
        </div>
        @endif
    </div>
</div>

<script>
document.querySelectorAll('.toggle-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const container = this.closest('.bg-white'); // ambil card terdekat
        const table = container.querySelector('.village-table'); // cari tabel di dalam card
        table.classList.toggle('hidden');
    });
});
</script>

@endsection
@push('script')
@endpush
