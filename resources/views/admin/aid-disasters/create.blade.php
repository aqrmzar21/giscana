@extends('layouts.admin')

@section('title', 'Tambah Data Bantuan Bencana - Admin')

@section('page-title', 'Tambah Data Bantuan Bencana Kecamatan')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white">Dashboard</a>
    <svg class="w-5 h-5 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.aid-disasters.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white">Bantuan Bencana</a>
    <svg class="w-5 h-5 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Tambah Baru</span>
</li>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700/60 rounded-2xl p-6">
        <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-100 dark:border-gray-700/60">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Form Tambah Bantuan Bencana Kecamatan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftarkan target penerima bantuan logistik per kecamatan</p>
            </div>
            <a href="{{ route('admin.aid-disasters.index') }}" class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                &larr; Kembali
            </a>
        </div>

        @if($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 p-4">
            <ul class="list-disc list-inside text-xs text-red-700 dark:text-red-300 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.aid-disasters.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Select District --}}
            <div>
                <label for="district_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                    Pilih Kecamatan <span class="text-red-500">*</span>
                </label>
                <select id="district_id" name="district_id" required onchange="syncDistrictName(this)"
                        class="w-full text-sm bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white p-3">
                    <option value="">-- Pilih Wilayah Kecamatan --</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" data-name="{{ $district->name }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                            Kecamatan {{ $district->name }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="district_name" id="district_name" value="{{ old('district_name') }}">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Target Recipients --}}
                <div>
                    <label for="total_recipients" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                        Total Target Penerima (KK)
                    </label>
                    <input type="number" name="total_recipients" id="total_recipients" value="{{ old('total_recipients') }}" min="0" placeholder="0"
                           class="w-full text-sm bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white p-3">
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Estimasi jumlah Kepala Keluarga penerima di kecamatan ini</p>
                </div>

                {{-- Distributed Aid Initial --}}
                <div>
                    <label for="distributed_aid" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                        Bantuan Terdistribusi Awal
                    </label>
                    <input type="number" name="distributed_aid" id="distributed_aid" value="{{ old('distributed_aid', 0) }}" min="0" placeholder="0"
                           class="w-full text-sm bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 text-gray-900 dark:text-white p-3">
                    <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Jumlah barang logistik tersalurkan awal (jika ada)</p>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                       class="w-4 h-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="is_active" class="text-xs font-semibold text-gray-700 dark:text-gray-300">Status Aktif Tanggap Bencana</label>
            </div>

            <div class="pt-6 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-end gap-3">
                <a href="{{ route('admin.aid-disasters.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-sm font-semibold text-white shadow-sm transition-colors">
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function syncDistrictName(select) {
    const selectedOption = select.options[select.selectedIndex];
    const districtNameInput = document.getElementById('district_name');
    if (selectedOption && selectedOption.dataset.name) {
        districtNameInput.value = selectedOption.dataset.name;
    }
}
</script>
@endsection


{{-- INTERACTIVE BREAKDOWN PER DESA (ACCORDION CARDS) --}}
<div class="mt-8 space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Breakdown Penyaluran Bantuan per Desa</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">Rincian desa penerima logistik di setiap kecamatan</p>
        </div>
        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300">
            Data Rinci Desa
        </span>
    </div>

    @foreach($aidDisasters as $aid)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700/60 overflow-hidden transition-all" x-data="{ open: false }">
            {{-- Accordion Header --}}
            <div @click="open = !open" class="p-4 sm:p-5 flex items-center justify-between cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors select-none">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-gray-900 dark:text-white">
                            Kecamatan {{ $aid->district_name }}
                        </h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ number_format($aid->total_received ?? 0) }} Unit Bantuan Tersalurkan &bull; 
                            <span class="font-semibold text-indigo-600 dark:text-indigo-400">
                                {{ $villageBreakdown[$aid->id]['total_villages'] ?? 0 }} Desa Terjangkau
                            </span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold px-3 py-1 rounded-xl bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <span x-show="!open">Buka Desa &darr;</span>
                        <span x-show="open">Tutup &uarr;</span>
                    </span>
                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>

            {{-- Accordion Content Table --}}
            <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-gray-700/60 p-4 sm:p-5 bg-gray-50/50 dark:bg-gray-800/60">
                @php
                    $villages = $villageBreakdown[$aid->id]['villages'] ?? [];
                @endphp

                @if(empty($villages) || count($villages) === 0)
                    <p class="text-xs text-gray-400 dark:text-gray-500 text-center py-4">Belum ada rincian transaksi penyaluran per desa di kecamatan ini.</p>
                @else
                    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        <table class="w-full text-xs text-left bg-white dark:bg-gray-800">
                            <thead class="bg-gray-100 dark:bg-gray-700/80 text-gray-600 dark:text-gray-300 uppercase tracking-wider font-semibold">
                                <tr>
                                    <th class="py-2.5 px-3 text-center w-10">No</th>
                                    <th class="py-2.5 px-3">Nama Desa</th>
                                    <th class="py-2.5 px-3 text-center">Jumlah Penerima (KK)</th>
                                    <th class="py-2.5 px-3 text-center">Total Barang Tersalur</th>
                                    <th class="py-2.5 px-3">Jenis Bantuan Diterima</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                                @foreach($villages as $villageName => $records)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="py-2.5 px-3 text-center text-gray-500 dark:text-gray-400 font-medium">{{ $loop->iteration }}</td>
                                        <td class="py-2.5 px-3 font-bold text-gray-800 dark:text-gray-200">{{ $villageName }}</td>
                                        <td class="py-2.5 px-3 text-center font-semibold text-gray-700 dark:text-gray-300">{{ $records->count() }} KK</td>
                                        <td class="py-2.5 px-3 text-center font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($records->sum('quantity_received')) }} Unit</td>
                                        <td class="py-2.5 px-3 text-gray-600 dark:text-gray-400">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($records->pluck('aidInventory.item_name')->filter()->unique() as $itemName)
                                                    <span class="px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[11px] font-medium">
                                                        {{ $itemName }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
</div>