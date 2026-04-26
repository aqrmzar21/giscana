@extends('layouts.admin')

@section('title', 'Edit Data Bantuan Bencana - Admin')

@section('page-title', 'Edit Data Bantuan Bencana')

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
    <span class="text-sm font-medium text-gray-500">Edit</span>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">
            Form Edit Data Bantuan Bencana
        </h3>

        @if($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-4">
            <ul class="list-disc list-inside text-sm text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Info terakhir disync --}}
        @if($aidDisaster->last_synced_at)
            <div class="mb-4 rounded-md bg-blue-50 p-3 flex items-center gap-2">
                <svg class="h-4 w-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <p class="text-sm text-blue-700">
                    Last synced from API: <strong>{{ $aidDisaster->last_synced_at->format('d/m/Y H:i:s') }}</strong>
                </p>
            </div>
        @endif

        <form action="{{ route('admin.aid-disasters.update', $aidDisaster) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-6">

                <div>
                    <label for="district_name" class="block text-sm font-medium text-gray-700">
                        District Name <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1">
                        <input type="text" name="district_name" id="district_name"
                            value="{{ old('district_name', $aidDisaster->district_name) }}" required
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('district_name') border-red-300 @enderror">
                        @error('district_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="total_recipients" class="block text-sm font-medium text-gray-700">
                            Total Recipients
                        </label>
                        <div class="mt-1">
                            <input type="number" name="total_recipients" id="total_recipients"
                                value="{{ old('total_recipients', $aidDisaster->total_recipients) }}" min="0"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('total_recipients') border-red-300 @enderror">
                            @error('total_recipients')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="distributed_aid" class="block text-sm font-medium text-gray-700">
                            Distributed Aid
                        </label>
                        <div class="mt-1">
                            <input type="number" name="distributed_aid" id="distributed_aid"
                                value="{{ old('distributed_aid', $aidDisaster->distributed_aid) }}" min="0"
                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md @error('distributed_aid') border-red-300 @enderror">
                            @error('distributed_aid')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </form>


                <div class="flex items-center">
                    <input id="is_active" name="is_active" type="checkbox" value="1"
                           {{ old('is_active', $aidDisaster->is_active) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Aktif</label>
                </div>

            </div>

            <div class="mt-6 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.aid-disasters.index') }}"
                   class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
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
@endsection
