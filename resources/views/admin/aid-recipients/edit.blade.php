@extends('layouts.admin')

@section('title', 'Ubah Data Penerima Bantuan - Admin')

@section('page-title', 'Ubah Data Penerima Bantuan')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.aid-recipients.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Penerima Bantuan</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Ubah Data</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Form Ubah Data</h3>
        
        <form action="{{ route('admin.aid-recipients.update', $aidRecipient) }}" method="POST">
            @csrf

            <div class="space-y-6 sm:space-y-5">
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label for="date" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">Tanggal Penyaluran <span class="text-red-500">*</span></label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <input type="date" name="date" id="date" value="{{ old('date', isset($aidRecipient) ? $aidRecipient->date->format('Y-m-d') : date('Y-m-d')) }}" required class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md">
                        @error('date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label for="recipient_name" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">Nama Penerima <span class="text-red-500">*</span></label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <input type="text" name="recipient_name" id="recipient_name" value="{{ old('recipient_name', $aidRecipient->recipient_name ?? '') }}" required class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md">
                        @error('recipient_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="sm:grid sm:grid-cols-2 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label for="aid_type" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">Jenis Bantuan <span class="text-red-500">*</span></label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <select id="aid_type" name="aid_type" required class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md">
                            <option value="">Pilih Jenis Bantuan</option>
                            @foreach(['Sembako', 'Uang Tunai', 'Material Bangunan', 'Pakaian Layak Pakai', 'Obat-obatan'] as $type)
                                <option value="{{ $type }}" {{ old('aid_type', $aidRecipient->aid_type ?? '') == $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('aid_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="sm:grid sm:grid-cols-2 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label for="amount" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">Jumlah <span class="text-red-500">*</span></label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2 relative max-w-lg sm:max-w-xs">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        </div>
                        <input type="number" name="amount" id="amount" value="{{ old('amount', $aidRecipient->amount ?? '') }}" min="0" required class="pl-10 block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border-gray-300 rounded-md">
                        @error('amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label for="district_id" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">Kecamatan <span class="text-red-500">*</span></label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        @php
                            $selectedVillageId = old('village_id', $aidRecipient->village_id ?? '');
                            $selectedDistrictId = old('district_id', '');

                            if (!$selectedDistrictId && $selectedVillageId) {
                                foreach ($districts as $district) {
                                    if ($district->villages->contains('id', (int) $selectedVillageId)) {
                                        $selectedDistrictId = (string) $district->id;
                                        break;
                                    }
                                }
                            }
                        @endphp

                        <select id="district_id" name="district_id" required class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md">
                            <option value="">Pilih Kecamatan</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ (string) $selectedDistrictId === (string) $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('district_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label for="village_id" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">Desa/Kelurahan <span class="text-red-500">*</span></label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <select id="village_id" name="village_id" required class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md">
                            <option value="">Pilih Desa/Kelurahan</option>
                            @foreach($districts as $district)
                                @foreach($district->villages as $village)
                                    <option
                                        value="{{ $village->id }}"
                                        data-district-id="{{ $district->id }}"
                                        {{ (string) $selectedVillageId === (string) $village->id ? 'selected' : '' }}
                                    >
                                        {{ $village->yard }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                        @error('village_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start sm:border-t sm:border-gray-200 sm:pt-5">
                    <label for="description" class="block text-sm font-medium text-gray-700 sm:mt-px sm:pt-2">Deskripsi Keterangan</label>
                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                        <textarea id="description" name="description" rows="3" class="max-w-lg shadow-sm block w-full focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border border-gray-300 rounded-md">{{ old('description', $aidRecipient->description ?? '') }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">Tuliskan keterangan detail terkait penyaluran bantuan.</p>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="pt-5 mt-5 border-t border-gray-200">
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.aid-recipients.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Batal
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const districtSelect = document.getElementById('district_id');
    const villageSelect = document.getElementById('village_id');
    const villageOptions = Array.from(villageSelect.querySelectorAll('option[data-district-id]'));

    function filterVillages() {
        const selectedDistrictId = districtSelect.value;
        const selectedVillageOption = villageSelect.options[villageSelect.selectedIndex];

        villageOptions.forEach(option => {
            const matchesDistrict = selectedDistrictId && option.dataset.districtId === selectedDistrictId;
            option.hidden = !matchesDistrict;
            option.disabled = !matchesDistrict;
        });

        const selectedVillageMatchesDistrict =
            selectedVillageOption &&
            selectedVillageOption.dataset &&
            selectedVillageOption.dataset.districtId === selectedDistrictId;

        if (!selectedVillageMatchesDistrict) {
            villageSelect.value = '';
        }
    }

    districtSelect.addEventListener('change', filterVillages);
    filterVillages();
});
</script>
@endsection
