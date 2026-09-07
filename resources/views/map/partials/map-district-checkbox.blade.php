
<!-- <div class="flex flex-col gap-3">
    <div class="flex flex-wrap gap-x-5 gap-y-2">
        <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer select-none">
            <input type="checkbox" id="toggle_district_boundaries" class="rounded border-gray-300 text-yellow-300 focus:ring-yellow-900" checked>
            <span>Tampilkan Batas Kecamatan</span>
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer select-none">
            <input type="checkbox" id="toggle_village_boundaries" class="rounded border-gray-300 text-green-600 focus:ring-indigo-500">
            <span>Tampilkan Batas Desa</span>
        </label>
    </div>
</div> -->

{{-- Toggle batas wilayah --}}
<div class="flex flex-wrap items-center gap-x-6 gap-y-3">
    <label class="flex items-center cursor-pointer">
        <input type="checkbox" id="toggle_district_boundaries" class="sr-only peer" checked>
        <div class="w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-yellow-400 relative transition">
            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-5"></div>
        </div>
        <span class="ml-3 text-sm text-gray-700">Batas Kecamatan</span>
    </label>

    <label class="flex items-center cursor-pointer">
        <input type="checkbox" id="toggle_village_boundaries" class="sr-only peer">
        <div class="w-11 h-6 bg-gray-200 rounded-full peer-checked:bg-green-500 relative transition">
            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-5"></div>
        </div>
        <span class="ml-3 text-sm text-gray-700">Batas Desa</span>
    </label>
</div>