@extends('layouts.admin')

@section('title', 'Edit Warga Penerima - Admin')
@section('page-title', 'Edit Warga Penerima Bantuan')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.aid-beneficiaries.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Data Warga Penerima</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Edit</span>
</li>
@endsection

@section('content')
@php
    $selectedVillageId = old('village_id', $aidBeneficiary->village_id);
    $selectedDistrictId = old('district_id', $aidBeneficiary->district_id);
@endphp
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Form Edit Warga Penerima</h3>

        <form action="{{ route('admin.aid-beneficiaries.update', $aidBeneficiary) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="recipient_name" class="block text-sm font-medium text-gray-700">Nama Kepala Keluarga <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="text" name="recipient_name" id="recipient_name" value="{{ old('recipient_name', $aidBeneficiary->recipient_name) }}" required
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('recipient_name') border-red-300 @enderror">
                            @error('recipient_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="identity_card_number" class="block text-sm font-medium text-gray-700">NIK / No. KTP</label>
                        <div class="mt-1">
                            <input type="text" name="identity_card_number" id="identity_card_number" value="{{ old('identity_card_number', $aidBeneficiary->identity_card_number) }}" maxlength="16"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('identity_card_number') border-red-300 @enderror">
                            @error('identity_card_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="district_id" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <select id="district_id" name="district_id" required
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('district_id') border-red-300 @enderror">
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
                    <div>
                        <label for="village_id" class="block text-sm font-medium text-gray-700">Desa/Kelurahan <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <select id="village_id" name="village_id" required
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('village_id') border-red-300 @enderror">
                                <option value="">Pilih Desa/Kelurahan</option>
                                @foreach($districts as $district)
                                    @foreach($district->villages as $village)
                                        <option value="{{ $village->id }}" data-district-id="{{ $district->id }}"
                                            {{ (string) $selectedVillageId === (string) $village->id ? 'selected' : '' }}>
                                            {{ $village->full_name ?? $village->yard }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('village_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div>
                    <label for="address_detail" class="block text-sm font-medium text-gray-700">Alamat Detail</label>
                    <div class="mt-1">
                        <textarea id="address_detail" name="address_detail" rows="3"
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md @error('address_detail') border-red-300 @enderror">{{ old('address_detail', $aidBeneficiary->address_detail) }}</textarea>
                        @error('address_detail')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="aid_status" class="block text-sm font-medium text-gray-700">Status Bantuan</label>
                    <div class="mt-1">
                        <select id="aid_status" name="aid_status" disabled
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            <option value="pending" {{ old('aid_status', $aidBeneficiary->aid_status) === 'pending' ? 'selected' : '' }}>Belum menerima</option>
                            <option value="received" {{ old('aid_status', $aidBeneficiary->aid_status) === 'received' ? 'selected' : '' }}>Sudah menerima</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.aid-beneficiaries.index') }}"
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const districtSelect = document.getElementById('district_id');
        const villageSelect = document.getElementById('village_id');
        if (!districtSelect || !villageSelect) return;

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
