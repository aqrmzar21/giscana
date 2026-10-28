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

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Kecamatan/Bencana --}}
                    <div>
                        <label for="aid_disaster_id" class="block text-sm font-medium text-gray-700">Kecamatan / Wilayah Bencana <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <select id="aid_disaster_id" name="aid_disaster_id" required
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('aid_disaster_id') border-red-300 @enderror">
                                <option value="" hidden>-- Pilih Kecamatan --</option>
                                @foreach($aidDisasters as $disaster)
                                    <option value="{{ $disaster->id }}"
                                        data-district-id="{{ $disaster->district_id ?? $disaster->district?->id ?? '' }}"
                                        data-district-name="{{ strtolower(trim($disaster->district_name)) }}"
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

                    {{-- Desa/Bencana --}}
                    <div>
                        <label for="village_id" class="block text-sm font-medium text-gray-700">Desa / Wilayah Bencana <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            @php
                                $selectedVillageId = old('village_id', $aidDistribution->village_id ?? $aidDistribution->beneficiary?->village_id);
                            @endphp
                            <select id="village_id" name="village_id" required
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('village_id') border-red-300 @enderror">
                                <option value="" hidden>-- Pilih Desa --</option>
                                @foreach($villages as $village)
                                    <option value="{{ $village->id }}"
                                        data-district-id="{{ $village->district_id }}"
                                        data-district-name="{{ strtolower(trim($village->district?->name ?? '')) }}"
                                        {{ (string) $selectedVillageId === (string) $village->id ? 'selected' : '' }}>
                                        {{ $village->full_name ?? $village->yard }}
                                    </option>
                                @endforeach
                            </select>
                            @error('village_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    {{-- Warga Penerima --}}
                    <div>
                        <label for="beneficiary_id" class="block text-sm font-medium text-gray-700">Warga Penerima Bantuan <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <select id="beneficiary_id" name="beneficiary_id" required
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('beneficiary_id') border-red-300 @enderror">
                                <option value="" hidden>-- Pilih Warga --</option>
                                @foreach($beneficiaries as $beneficiary)
                                    <option value="{{ $beneficiary->id }}"
                                        data-village-id="{{ $beneficiary->village_id }}"
                                        data-district-id="{{ $beneficiary->district_id ?? $beneficiary->village?->district_id ?? '' }}"
                                        data-status="{{ $beneficiary->aid_status }}"
                                        data-village="{{ $beneficiary->village?->yard ?? '-' }}"
                                        data-district="{{ $beneficiary->district?->name ?? '-' }}"
                                        {{ old('beneficiary_id', $aidDistribution->beneficiary_id) == $beneficiary->id ? 'selected' : '' }}>
                                        {{ $beneficiary->recipient_name }}
                                    </option>
                                @endforeach
                            </select>
                            <p id="beneficiary-info" class="mt-1 text-sm text-gray-600 font-medium"></p>
                            @error('beneficiary_id')
                                <p class="text-xs text-red-600">{{ $message }}</p>
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
                                    @php
                                        $isCurrentItem = $inventory->id == $aidDistribution->aid_inventory_id;
                                        $effectiveStock = $isCurrentItem ? ($inventory->remaining_stock + $aidDistribution->quantity_received) : $inventory->remaining_stock;
                                    @endphp
                                    <option value="{{ $inventory->id }}"
                                            data-stock="{{ $effectiveStock }}"
                                            data-category="{{ $inventory->category }}"
                                            {{ old('aid_inventory_id', $aidDistribution->aid_inventory_id) == $inventory->id ? 'selected' : '' }}>
                                        {{ $inventory->item_name }}
                                    </option>
                                @endforeach
                            </select>
                            <p id="stock-info" class="text-xs text-gray-500"></p>
                            @error('aid_inventory_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Jumlah & Tanggal --}}
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
        const category = option.dataset.category;

        let text = `<span class="text-black">Kategori: ${category}</span>`;
        if (stock > 0) {
            text += ` | <span class="text-green-600 font-medium">Sisa stok: ${stock.toLocaleString('id-ID')} unit</span>`;
        } else {
            text += ` | <span class="text-red-600 font-medium">Sisa stok: ${stock.toLocaleString('id-ID')} unit</span>`;
        }

        stockInfo.innerHTML = text;
        stockInfo.className = 'text-xs';
    } else {
        stockInfo.textContent = '';
    }
}

function updateBeneficiaryInfo(select) {
    const option = select.options[select.selectedIndex];
    const info = document.getElementById('beneficiary-info');
    if (option && option.value && option.dataset.status !== undefined) {
        const status = option.dataset.status;
        const village = option.dataset.village;
        const district = option.dataset.district;
        let text = `Desa: ${village}, Kecamatan: ${district}`;
        if (status === 'received') {
            text += ` | <span class="text-green-600 font-medium">Sudah menerima bantuan</span>`;
        }
        info.innerHTML = text;
        info.className = 'text-xs text-gray-600 font-medium';
    } else {
        info.textContent = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const disasterSelect = document.getElementById('aid_disaster_id');
    const villageSelect = document.getElementById('village_id');
    const beneficiarySelect = document.getElementById('beneficiary_id');
    const stockSelect = document.getElementById('aid_inventory_id');

    const villageOptions = villageSelect ? Array.from(villageSelect.querySelectorAll('option[data-district-id]')) : [];
    const beneficiaryOptions = beneficiarySelect ? Array.from(beneficiarySelect.querySelectorAll('option[data-village-id]')) : [];

    function filterVillages(keepSelection = false) {
        if (!disasterSelect || !villageSelect) return;

        const selOption = disasterSelect.options[disasterSelect.selectedIndex];
        const districtId = selOption ? (selOption.dataset.districtId || '') : '';
        const districtName = selOption ? (selOption.dataset.districtName || '').toLowerCase().trim() : '';

        let validSelected = false;

        villageOptions.forEach(opt => {
            const matches = Boolean(
                (districtId && opt.dataset.districtId === districtId) ||
                (!districtId && districtName && opt.dataset.districtName && opt.dataset.districtName === districtName)
            );

            opt.hidden = !matches;
            opt.disabled = !matches;
            opt.style.display = matches ? '' : 'none';

            if (matches && opt.value === villageSelect.value) {
                validSelected = true;
            }
        });

        if (!districtId && !districtName) {
            villageSelect.options[0].textContent = '-- Pilih Kecamatan Terlebih Dahulu --';
        } else {
            villageSelect.options[0].textContent = '-- Pilih Desa --';
        }

        if (!keepSelection || !validSelected) {
            if (!validSelected) {
                villageSelect.value = '';
            }
        }

        filterBeneficiaries(keepSelection);
    }

    function filterBeneficiaries(keepSelection = false) {
        if (!villageSelect || !beneficiarySelect) return;

        const selectedVillageId = villageSelect.value;
        let validSelected = false;

        beneficiaryOptions.forEach(opt => {
            const matches = Boolean(selectedVillageId && opt.dataset.villageId === selectedVillageId);

            opt.hidden = !matches;
            opt.disabled = !matches;
            opt.style.display = matches ? '' : 'none';

            if (matches && opt.value === beneficiarySelect.value) {
                validSelected = true;
            }
        });

        if (!selectedVillageId) {
            beneficiarySelect.options[0].textContent = '-- Pilih Desa Terlebih Dahulu --';
        } else {
            beneficiarySelect.options[0].textContent = '-- Pilih Warga --';
        }

        if (!keepSelection || !validSelected) {
            if (!validSelected) {
                beneficiarySelect.value = '';
            }
        }

        updateBeneficiaryInfo(beneficiarySelect);
    }

    if (disasterSelect) {
        disasterSelect.addEventListener('change', function() {
            filterVillages(false);
        });
    }

    if (villageSelect) {
        villageSelect.addEventListener('change', function() {
            filterBeneficiaries(false);
        });
    }

    if (beneficiarySelect) {
        beneficiarySelect.addEventListener('change', function() {
            updateBeneficiaryInfo(beneficiarySelect);
        });
    }

    if (stockSelect) {
        stockSelect.addEventListener('change', function() {
            updateStockInfo(stockSelect);
        });
        updateStockInfo(stockSelect);
    }

    // Inisialisasi awal dengan mempertahankan data yang sudah terpilih
    filterVillages(true);
});
</script>
@endpush
@endsection
