@extends('layouts.admin')

@section('title', 'Tambah Data Bantuan Bencana - Admin')

@section('page-title', 'Tambah Data Bantuan Bencana')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.aid-disasters.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Data Bantuan Bencana</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Tambah Baru</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Form Tambah Data Bantuan Bencana</h3>

        @if($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-4">
            <ul class="list-disc list-inside text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.aid-disasters.store') }}" method="POST">
            @csrf
            <div class="space-y-6">

                <div>
                    <label for="nama_kecamatan" class="block text-sm font-medium text-gray-700">
                        Nama Kecamatan <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" name="nama_kecamatan" id="nama_kecamatan"
                               value="{{ old('nama_kecamatan') }}" required
                               placeholder="Contoh: Kecamatan Sukajadi"
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('nama_kecamatan') border-red-300 @enderror">
                        @error('nama_kecamatan')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="jumlah_penerima_bantuan" class="block text-sm font-medium text-gray-700">
                            Jumlah Penerima Bantuan
                        </label>
                        <div class="mt-1">
                            <input type="number" name="jumlah_penerima_bantuan" id="jumlah_penerima_bantuan"
                                   value="{{ old('jumlah_penerima_bantuan') }}" min="0"
                                   placeholder="0"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('jumlah_penerima_bantuan') border-red-300 @enderror">
                            @error('jumlah_penerima_bantuan')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="bantuan_terdistribusi" class="block text-sm font-medium text-gray-700">
                            Bantuan Terdistribusi
                        </label>
                        <div class="mt-1">
                            <input type="number" name="bantuan_terdistribusi" id="bantuan_terdistribusi"
                                   value="{{ old('bantuan_terdistribusi') }}" min="0"
                                   placeholder="0"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('bantuan_terdistribusi') border-red-300 @enderror">
                            @error('bantuan_terdistribusi')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center">
                    <input id="is_active" name="is_active" type="checkbox" value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif</label>
                </div>

            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.aid-disasters.index') }}"
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
