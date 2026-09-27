@extends('layouts.admin')

@section('title', 'Data Warga Penerima - Admin')
@section('page-title', 'Manajemen Warga Penerima Bantuan')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Data Warga Penerima</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="sm:flex sm:items-center justify-between mb-4">
            <div class="sm:flex-auto">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Daftar Warga Penerima Bantuan</h3>
                <p class="mt-2 text-sm text-gray-700">Master target KK/warga terdampak. Status diperbarui otomatis saat distribusi dicatat.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-4 sm:flex-none">
                <a href="{{ route('admin.aid-beneficiaries.create') }}"
                   class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 whitespace-nowrap">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Warga
                </a>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
            <form action="{{ route('admin.aid-beneficiaries.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, desa..."
                       class="block w-full sm:w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <select name="aid_status"
                        class="block rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Semua status</option>
                    <option value="pending" {{ request('aid_status') === 'pending' ? 'selected' : '' }}>Belum menerima</option>
                    <option value="received" {{ request('aid_status') === 'received' ? 'selected' : '' }}>Sudah menerima</option>
                </select>
                <button type="submit"
                        class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'aid_status']))
                    <a href="{{ route('admin.aid-beneficiaries.index') }}"
                       class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
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
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama KK</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">NIK</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Desa / Kecamatan</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($beneficiaries as $beneficiary)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                            {{ $loop->iteration + ($beneficiaries->currentPage() - 1) * $beneficiaries->perPage() }}
                        </td>
                        <td class="px-3 py-4 text-sm">
                            <div class="font-medium text-gray-900">{{ $beneficiary->recipient_name }}</div>
                            @if($beneficiary->address_detail)
                                <div class="text-xs text-gray-500 truncate max-w-xs">{{ $beneficiary->address_detail }}</div>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $beneficiary->identity_card_number ?: '-' }}</td>
                        <td class="px-3 py-4 text-sm text-gray-500">
                            <div>{{ $beneficiary->village?->full_name ?? $beneficiary->village?->yard ?? '-' }}</div>
                            <div class="text-xs">{{ $beneficiary->district?->name ?? '' }}</div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            @if($beneficiary->aid_status === 'received')
                                <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Sudah menerima</span>
                            @else
                                <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">Belum menerima</span>
                            @endif
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.aid-beneficiaries.show', $beneficiary) }}" class="text-indigo-600 hover:text-indigo-900" title="Detail">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                @can('update data')
                                <a href="{{ route('admin.aid-beneficiaries.edit', $beneficiary) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                @endcan
                                @can('delete data')
                                <form action="{{ route('admin.aid-beneficiaries.destroy', $beneficiary) }}" method="POST" class="inline form-delete">
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
                        <td colspan="6" class="py-8 text-center text-sm text-gray-500">Belum ada data warga penerima.</td>
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
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="250" {{ request('per_page') == 250 ? 'selected' : '' }}>250</option>
                        <option value="500" {{ request('per_page') == 500 ? 'selected' : '' }}>500</option>
                    </select>
                </form>
                <span class="text-sm text-gray-700">data</span>
            </div>
            <div>{{ $beneficiaries->links() }}</div>
        </div>
    </div>
</div>
@endsection
