@extends('layouts.admin')

@section('title', 'Manajemen Staff - Admin')

@section('page-title', 'Manajemen Staff')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
        Dashboard
    </a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
    </svg>
    <span class="text-sm font-medium text-gray-500">Staff</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="sm:flex sm:items-center justify-between mb-4">
            <div class="sm:flex-auto">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Daftar Staff</h3>
                <p class="mt-2 text-sm text-gray-700">Daftar semua staff yang terdaftar dalam sistem.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-4 sm:flex-none flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                <a href="{{ route('admin.staff.create') }}" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto whitespace-nowrap">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Staff
                </a>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4 justify-between">
            <form action="{{ route('admin.staff.index') }}" method="GET" class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 w-full sm:w-auto">
                <div class="flex items-center gap-2 flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="flex w-full sm:w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Cari
                    </button>
                    @if(request()->filled('search'))
                    <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">NO</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Nama</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Telepon</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Organisasi</th>
                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($staffs as $staff)
                    <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $loop->iteration }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $staff->name }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $staff->email }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $staff->phone ?? '-' }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $staff->organization ?? '-' }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            @if($staff->is_active)
                            <span class="inline-flex rounded-full bg-green-100 px-2 py-1 leading-3 text-xs text-green-800">Aktif</span>
                            @else
                            <span class="inline-flex rounded-full bg-gray-100 px-2 py-1 leading-3 text-xs text-gray-800">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.staff.edit', $staff) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.staff.destroy', $staff) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus staff ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 text-center sm:pl-6">
                            Tidak ada data staff.
                        </td>
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
                    <select name="per_page" onchange="this.form.submit()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-1 pl-3 pr-8">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </form>
                <span class="text-sm text-gray-700">data</span>
            </div>
            
            <div class="w-full sm:w-auto">
                {{ $staffs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
