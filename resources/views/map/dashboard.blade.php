@extends('layouts.admin')

@section('title', 'Peta Interaktif - Admin')

@section('page-title', 'Peta Interaktif')

@section('breadcrumb')
<li class="inline-flex items-center">
    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
        Dashboard
    </a>
</li>
<li>
    <div class="flex items-center">
        <svg class="w-4 h-4 text-gray-400 mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
        <span class="text-sm font-medium text-gray-500">Peta</span>
    </div>
</li>
@endsection

@section('content')
<div class="bg-white shadow rounded-lg p-4 sm:p-6">
    <p class="text-md">
        Klik disini untuk
        <button class="text-sm text-blue-500" onclick="location.reload()">Load peta </button>
        jika peta tidak muncul
    </p>
    @include('map.partials.interactive')
</div>
@endsection
