@extends('layouts.admin')

@section('title', 'Detail Data Bantuan Bencana - Admin')
@section('page-title', 'Detail Data Bantuan Bencana')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Dashboard</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <a href="{{ route('admin.aid-disasters.index') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">Data Bantuan Bencana</a>
    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="inline-flex items-center">
    <span class="text-sm font-medium text-gray-500">Detail</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <div class="sm:flex sm:items-center mb-6">
            <div class="sm:flex-auto">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Detail Bantuan Bencana</h3>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none flex gap-2">
                @can('update data')
                <a href="{{ route('admin.aid-disasters.edit', $aidDisaster) }}"
                   class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                @endcan
                <a href="{{ route('admin.aid-disasters.index') }}"
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Kembali
                </a>
            </div>
        </div>

        {{-- Progress distribusi --}}
        @if(!is_null($aidDisaster->received_percentage))
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm font-medium text-gray-700 mb-2">Progress Distribusi KK</p>
                <div class="flex items-center gap-3">
                    <div class="flex-1 bg-gray-200 rounded-full h-4">
                        <div class="h-4 rounded-full transition-all duration-500 
                            {{ $aidDisaster->received_percentage >= 100 ? 'bg-green-500' : ($aidDisaster->received_percentage >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                            style="width: {{ min($aidDisaster->received_percentage, 100) }}%">
                        </div>
                    </div>
                    <span class="text-sm font-bold text-gray-700 w-12 text-right">{{ $aidDisaster->received_percentage }}%</span>
                </div>
            </div>
        @endif
        
        {{-- Card Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Kecamatan --}}
            <div class="bg-white shadow rounded-lg p-5">
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-medium text-gray-500">Kecamatan</dt>
                    @if($aidDisaster->is_active)
                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-semibold text-green-800">Aktif</span>
                    @else
                        <span class="inline-flex rounded-full bg-gray-100 px-3 py-1 text-sm font-semibold text-gray-800">Nonaktif</span>
                    @endif
                </div>
                <dd class="mt-2 text-lg font-semibold text-gray-900">{{ $aidDisaster->district_name }}</dd>
            </div>

            {{-- Total Penerima --}}
            <div class="bg-white shadow rounded-lg p-5">
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-medium text-gray-500">Total Penerima</dt>
                    <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5V4H2v16h5" />
                    </svg>
                </div>
                <dd class="mt-2 text-lg font-semibold text-gray-900">{{ number_format($aidDisaster->total_recipients ?? 0) }} orang</dd>
            </div>

            {{-- KK Menerima --}}
            <div class="bg-white shadow rounded-lg p-5">
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-medium text-gray-500">KK Menerima</dt>
                    <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <dd class="mt-2 text-lg font-semibold text-green-600">
                    {{ number_format($aidDisaster->total_received ?? 0) }} orang
                </dd>
                <p class="text-xs text-gray-500">({{ $aidDisaster->received_percentage }}%)</p>
            </div>

            {{-- Sisa Bantuan --}}
            <div class="bg-white shadow rounded-lg p-5">
                <div class="flex items-center justify-between">
                    <dt class="text-sm font-medium text-gray-500">Belum Menerima</dt>
                    <svg class="h-5 w-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                    </svg>
                </div>
                <dd class="mt-2 text-lg font-semibold {{ $aidDisaster->remaining_aid > 0 ? 'text-orange-600' : 'text-green-600' }}">
                    {{ number_format($aidDisaster->remaining_aid) }} orang
                </dd>
            </div>
        </div>

        
    </div>
</div>
@endsection