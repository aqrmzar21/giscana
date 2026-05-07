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
                <select id="district_id" name="district_id" required class="max-w-lg block w-full shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:max-w-xs sm:text-sm border-gray-300 rounded-md">
                    <option value="">Pilih Kecamatan</option>
                    @foreach($districts as $district)                        
                        @foreach($district->villages as $village)
                            <!-- <option value="{{ $district->id }}" {{ old('district_id', $aidRecipient->district_id ?? '') == $district->id ? 'selected' : '' }}>{{ $district->zone }}</option> -->
                            <option value="{{ $village->id }}" {{ old('village_id', $aidRecipient->village_id ?? '') == $village->id ? 'selected' : '' }}>{{ $village->zone }}</option>
                        @endforeach                        
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
                            <option value="{{ $village->id }}" {{ old('village_id', $aidRecipient->village_id ?? '') == $village->id ? 'selected' : '' }}>{{ $village->yard }}</option>
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
      
<script>
document.getElementById('district_id').addEventListener('change', function() {
    let districtId = this.value;
    let villageSelect = document.getElementById('village_id');
    villageSelect.innerHTML = '<option value="">Loading...</option>';

    if (districtId) {
        fetch('/villages/' + districtId)
            .then(response => response.json())
            .then(data => {
                villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
                data.forEach(function(village) {
                    villageSelect.innerHTML += `<option value="${village.id}">${village.name}</option>`;
                });
            });
    } else {
        villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan</option>';
    }
});
</script>
