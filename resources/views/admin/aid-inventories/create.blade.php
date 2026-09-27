@extends('layouts.admin')

@section('title', 'Tambah Logistik Bantuan - Admin')
@section('page-title', 'Tambah Logistik Bantuan')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.aid-inventories.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Logistik Bantuan</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Tambah Baru</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Form Tambah Logistik Bantuan</h3>

        <form action="{{ route('admin.aid-inventories.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="item_name" class="block text-sm font-medium text-gray-700">Nama Barang <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}" required
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('item_name') border-red-300 @enderror"
                                   placeholder="Contoh: Sembako Paket A">
                            @error('item_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <select id="category" name="category" required
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('category') border-red-300 @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach(['sembako' => 'Sembako', 'obat' => 'Obat', 'pakaian' => 'Pakaian', 'material' => 'Material', 'lainnya' => 'Lainnya'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('category') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="source" class="block text-sm font-medium text-gray-700">Sumber Bantuan <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <input type="text" name="source" id="source" value="{{ old('source') }}" required
                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('source') border-red-300 @enderror"
                               placeholder="Contoh: BPBD Provinsi, Donasi PT. X">
                        @error('source')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="initial_stock" class="block text-sm font-medium text-gray-700">Jumlah Masuk <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="number" name="initial_stock" id="initial_stock" value="{{ old('initial_stock') }}" min="0" required
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('initial_stock') border-red-300 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Sisa stok awal akan diset sama dengan jumlah masuk.</p>
                            @error('initial_stock')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="date_stock" class="block text-sm font-medium text-gray-700">Tanggal Masuk</label>
                        <div class="mt-1">
                            <input type="date" name="date_stock" id="date_stock" value="{{ old('date_stock') }}" min="0"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('date_stock') border-red-300 @enderror">
                            @error('date_stock')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex items-end">
                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 pb-2">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            Stok aktif (dapat disalurkan)
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.aid-inventories.index') }}"class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
