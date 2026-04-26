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
                <a href="{{ route('admin.aid-disasters.edit', $aidDisaster) }}"
                   class="inline-flex items-center justify-center rounded-md border border-transparent bg-yellow-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="mr-2 -ml-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            </div>
        </div>

        {{-- Progress distribusi --}}
        @if(!is_null($aidDisaster->distribution_percentage))
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm font-medium text-gray-700 mb-2">Aid Distribution Progress</p>
                <div class="flex items-center gap-3">
                    <div class="flex-1 bg-gray-200 rounded-full h-4">
                        <div class="h-4 rounded-full transition-all duration-500 {{ $aidDisaster->distribution_percentage >= 100 ? 'bg-green-500' : ($aidDisaster->distribution_percentage >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}"
                            style="width: {{ min($aidDisaster->distribution_percentage, 100) }}%"></div>
                    </div>
                    <span class="text-sm font-bold text-gray-700 w-12 text-right">{{ $aidDisaster->distribution_percentage }}%</span>
                </div>
            </div>
        @endif

        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">ID</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidDisaster->id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">District Name</dt>
                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $aidDisaster->district_name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Total Recipients</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ number_format($aidDisaster->total_recipients ?? 0) }} people</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Distributed Aid</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ number_format($aidDisaster->distributed_aid ?? 0) }} people</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Remaining Aid</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if(!is_null($aidDisaster->remaining_aid))
                        <span class="{{ $aidDisaster->remaining_aid > 0 ? 'text-orange-600 font-semibold' : 'text-green-600 font-semibold' }}">
                            {{ number_format($aidDisaster->remaining_aid) }} people
                        </span>
                    @else
                        -
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @if($aidDisaster->is_active)
                        <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">Active</span>
                    @else
                        <span class="inline-flex rounded-full bg-gray-100 px-2 text-xs font-semibold leading-5 text-gray-800">Inactive</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Last API Sync</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $aidDisaster->last_synced_at ? $aidDisaster->last_synced_at->format('d/m/Y H:i:s') : 'Never synced' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Created At</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidDisaster->created_at->format('d/m/Y H:i:s') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Updated At</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $aidDisaster->updated_at->format('d/m/Y H:i:s') }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex items-center justify-end">
            <a href="{{ route('admin.aid-disasters.index') }}"
               class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Kembali
            </a>
        </div>
    </div>
</div>
@endsection
