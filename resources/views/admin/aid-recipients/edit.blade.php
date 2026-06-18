@extends('layouts.admin')

@section('title', 'Edit Penerima Bantuan - Admin')

@section('page-title', 'Edit Penerima Bantuan')

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
    <span class="text-sm font-medium text-gray-500">Edit</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">Form Edit Penerima Bantuan</h3>
        
        <form action="{{ route('admin.aid-recipients.update', $aidRecipient) }}" method="POST">
            @csrf
            @method('PUT')

            @php
                $selectedVillageId = old('village_id', $aidRecipient->village_id);
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

            <div class="space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Tanggal Penyaluran <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="date" name="date" id="date" value="{{ old('date', $aidRecipient->date->format('Y-m-d')) }}" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('date') border-red-300 @enderror">
                            @error('date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="recipient_name" class="block text-sm font-medium text-gray-700">Nama Penerima <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="text" name="recipient_name" id="recipient_name" value="{{ old('recipient_name', $aidRecipient->recipient_name) }}" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('recipient_name') border-red-300 @enderror">
                            @error('recipient_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="aid_type" class="block text-sm font-medium text-gray-700">Jenis Bantuan <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <select id="aid_type" name="aid_type" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('aid_type') border-red-300 @enderror">
                                <option value="">Pilih Jenis Bantuan</option>
                                @foreach(['Sembako', 'Material Bangunan', 'Pakaian Layak Pakai', 'Obat-obatan'] as $type)
                                    <option value="{{ $type }}" {{ old('aid_type', $aidRecipient->aid_type) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('aid_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Jumlah <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <input type="number" name="amount" id="amount" value="{{ old('amount', $aidRecipient->amount) }}" min="0" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('amount') border-red-300 @enderror">
                            @error('amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="district_id" class="block text-sm font-medium text-gray-700">Kecamatan <span class="text-red-500">*</span></label>
                        <div class="mt-1">
                            <select id="district_id" name="district_id" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('district_id') border-red-300 @enderror">
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
                            <select id="village_id" name="village_id" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('village_id') border-red-300 @enderror">
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
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi Keterangan</label>
                    <div class="mt-1">
                        <textarea id="description" name="description" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border border-gray-300 rounded-md @error('description') border-red-300 @enderror">{{ old('description', $aidRecipient->description) }}</textarea>
                        <p class="mt-2 text-sm text-gray-500">Tuliskan keterangan detail terkait penyaluran bantuan.</p>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.aid-recipients.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Batal
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
        if (!districtSelect || !villageSelect) {
            return;
        }

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
