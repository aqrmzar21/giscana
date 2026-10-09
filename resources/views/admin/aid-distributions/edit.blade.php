@extends('layouts.admin')

@section('title', 'Edit Distribusi Bantuan - Admin')
@section('page-title', 'Edit Distribusi Bantuan')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.aid-distributions.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Distribusi Bantuan</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Edit</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Form Edit Distribusi Bantuan</h3>

        <form action="{{ route('admin.aid-distributions.update', $aidDistribution) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-6">

                {{-- Kecamatan/Bencana --}}
                <div>
                    <label for="aid_disaster_id" class="block text-sm font-medium text-gray-700">Kecamatan / Wilayah Bencana <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <select id="aid_disaster_id" name="aid_disaster_id" required
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('aid_disaster_id') border-red-300 @enderror">
                            <option value="" hidden>-- Pilih Kecamatan --</option>
                            @foreach($aidDisasters as $disaster)
                                <option value="{{ $disaster->id }}"
                                    {{ old('aid_disaster_id', $aidDistribution->aid_disaster_id) == $disaster->id ? 'selected' : '' }}>
                                    {{ $disaster->district_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('aid_disaster_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Warga Penerima --}}
                <div>
                    <label for="beneficiary_id" class="block text-sm font-medium text-gray-700">Warga Penerima Bantuan <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <select id="beneficiary_id" name="beneficiary_id" required
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('beneficiary_id') border-red-300 @enderror">
                            <option value="" hidden>-- Pilih Warga --</option>
                            @foreach($beneficiaries as $beneficiary)
                                <option value="{{ $beneficiary->id }}"
                                    {{ old('beneficiary_id', $aidDistribution->beneficiary_id) == $beneficiary->id ? 'selected' : '' }}>
                                    {{ $beneficiary->recipient_name }}
                                    ({{ $beneficiary->village?->full_name ?? '-' }}, {{ $beneficiary->district?->name ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('beneficiary_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Barang Logistik --}}
                <div>
                    <label for="aid_inventory_id" class="block text-sm font-medium text-gray-700">Barang Logistik <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <select id="aid_inventory_id" name="aid_inventory_id" required
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('aid_inventory_id') border-red-300 @enderror"
                                onchange="updateStockInfo(this)">
                            <option value="" hidden>-- Pilih Barang --</option>
                            @foreach($inventories as $inventory)
                                <option value="{{ $inventory->id }}"
                                        data-stock="{{ $inventory->remaining_stock }}"
                                        {{ old('aid_inventory_id', $aidDistribution->aid_inventory_id) == $inventory->id ? 'selected' : '' }}>
                                    {{ $inventory->item_name }} ({{ $inventory->category }}) — Sisa: {{ number_format($inventory->remaining_stock) }}
                                </option>
                            @endforeach
                        </select>
                        <p id="stock-info" class="mt-1 text-sm text-gray-500"></p>
                        @error('aid_inventory_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Jumlah & Tanggal --}}
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="quantity_received" class="block text-sm font-medium text-gray-700">Jumlah Diterima <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="number" name="quantity_received" id="quantity_received"
                                   value="{{ old('quantity_received', $aidDistribution->quantity_received) }}" min="1" required
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('quantity_received') border-red-300 @enderror">
                            @error('quantity_received')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="distribution_date" class="block text-sm font-medium text-gray-700">Tanggal Distribusi <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="date" name="distribution_date" id="distribution_date"
                                   value="{{ old('distribution_date', $aidDistribution->distribution_date?->format('Y-m-d')) }}" required
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('distribution_date') border-red-300 @enderror">
                            @error('distribution_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <div class="mt-1">
                        <textarea id="description" name="description" rows="3"
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md">{{ old('description', $aidDistribution->description) }}</textarea>
                    </div>
                </div>

            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.aid-distributions.index') }}"
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function updateStockInfo(select) {
    const option = select.options[select.selectedIndex];
    const stockInfo = document.getElementById('stock-info');
    if (option && option.dataset.stock !== undefined) {
        const stock = parseInt(option.dataset.stock);
        stockInfo.textContent = 'Sisa stok tersedia: ' + stock.toLocaleString('id-ID') + ' unit';
        stockInfo.className = stock > 0 ? 'mt-1 text-sm text-green-600 font-medium' : 'mt-1 text-sm text-red-600 font-medium';
    } else {
        stockInfo.textContent = '';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('aid_inventory_id');
    if (sel) updateStockInfo(sel);
});
</script>
@endpush
@endsection
