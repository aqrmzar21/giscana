<!-- {{-- Toggle Batas Administrasi (Kecamatan & Desa) --}} -->
<!-- <div class="space-y-2"> -->
<div class="grid grid-cols-2 md:grid-cols-1 gap-2">

    <!-- Baris 1: Batas Kecamatan -->
    <div class="p-2.5 rounded-xl hover:bg-slate-100/50 transition-colors dark:bg-dark-100">
        <label class="flex items-center w-full cursor-pointer select-none">
            <div class="relative inline-flex items-center">
                <input type="checkbox" id="toggle_district_boundaries" class="sr-only peer">
                <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
            </div>
            <span class="text-sm font-semibold dark:text-white-900 pl-2">Batas Kecamatan</span>
        </label>
    </div>

    <!-- Baris 2: Batas Desa -->
    <div class="p-2.5 rounded-xl hover:bg-slate-100/50 transition-colors dark:bg-dark-100">
        <label class="flex items-center w-full cursor-pointer select-none">
            <div class="relative inline-flex items-center">
                <input type="checkbox" id="toggle_village_boundaries" class="sr-only peer">
                <div class="w-10 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-500"></div>
            </div>
            <span class="text-sm font-semibold dark:text-white-900 pl-2">Batas Desa</span>
        </label>
    </div>

</div>