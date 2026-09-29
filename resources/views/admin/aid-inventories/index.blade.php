@extends('layouts.admin')

@section('title', 'Logistik Bantuan - Admin')
@section('page-title', 'Manajemen Logistik Bantuan')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Logistik Bantuan</span>
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
        <div class="sm:flex sm:items-center justify-between mb-4">
            <div class="sm:flex-auto">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Daftar Logistik Bantuan</h3>
                <p class="mt-2 text-sm text-gray-700">Master stok barang bantuan: jumlah masuk, sisa stok, dan sumber donasi.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-4 sm:flex-none">
                <a href="{{ route('admin.aid-inventories.create') }}"
                   class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 whitespace-nowrap">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Logistik Baru
                </a>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
            <form action="{{ route('admin.aid-inventories.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang, kategori, sumber..."
                    class="block w-full sm:w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'active_only']))
                    <a href="{{ route('admin.aid-inventories.index') }}"
                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">NO</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama Barang</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kategori</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Sumber</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Stok Masuk</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Sisa Stok</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($inventories as $inventory)
                    @php
                        $stockPercent = $inventory->initial_stock > 0
                            ? min(100, round(($inventory->remaining_stock / $inventory->initial_stock) * 100, 1))
                            : 0;
                    @endphp
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            {{ $loop->iteration + ($inventories->currentPage() - 1) * $inventories->perPage() }}
                        </td>
                        <td class="px-3 py-4 text-sm font-medium text-gray-900">{{ $inventory->item_name }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">
                                {{ $categoryLabels[$inventory->category] ?? $inventory->category }}
                            </span>
                        </td>
                        <td class="px-3 py-4 text-sm text-gray-500">{{ $inventory->source ?: '-' }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ number_format($inventory->initial_stock) }}</td>
                        <td class="px-3 py-4 text-sm text-gray-500 min-w-[140px]">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                    <div class="{{ $stockPercent <= 20 ? 'bg-red-400' : ($stockPercent <= 50 ? 'bg-yellow-400' : 'bg-green-400') }} h-2.5 rounded-full" style="width: {{ $stockPercent }}%"></div>
                                </div>
                                <span class="whitespace-nowrap font-medium text-gray-900">{{ number_format($inventory->remaining_stock) }}</span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            @if($inventory->is_active)
                                <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Aktif</span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">Nonaktif</span>
                            @endif
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.aid-inventories.show', $inventory) }}" class="text-indigo-600 hover:text-indigo-900" title="Detail">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @can('update data')
                                <a href="{{ route('admin.aid-inventories.edit', $inventory) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @endcan
                                @can('delete data')
                                <form action="{{ route('admin.aid-inventories.destroy', $inventory) }}" method="POST" class="inline form-delete">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-sm text-gray-500">Belum ada data logistik bantuan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-700">Tampilkan</span>
                <form action="{{ request()->url() }}" method="GET" class="inline-block">
                    @foreach(request()->except('per_page', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="per_page" onchange="this.form.submit()"
                            class="block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-1 pl-3 pr-8">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>                        
                    </select>
                </form>
                <span class="text-sm text-gray-700">data</span>
            </div>
            <div>{{ $inventories->links() }}</div>
        </div>
    </div>
</div>
@endsection
