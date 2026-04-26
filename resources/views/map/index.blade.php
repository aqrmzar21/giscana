@extends('layouts.landing-map')

@section('title', 'Peta Interaktif - Giscana')

@include('map.partials.map-styles', ['mapUiVariant' => 'landing-fs'])

@section('content')
<div class="map-landing-fs" id="map-landing-wrapper">
    <div id="map"></div>
</div>
@endsection

@section('map-toolbar')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 sm:py-4">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="shrink-0 lg:pr-6">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kontrol peta</p>
            <p class="text-xs text-gray-500 mb-2">Provinsi Gorontalo — filter dan legenda mengatur lapisan yang ditampilkan</p>
            <div class="mt-2">
                @include('map.partials.map-legend-content')
            </div>
        </div>
        <div class="flex-1 min-w-0 space-y-3 border-l border-gray-200 pl-4">
            @include('map.partials.map-controls-inner', ['toolbarContext' => 'footer', 'hideToolbarHeading' => false, 'hideLegend' => true])
        </div>
    </div>
</div>
@endsection

@include('map.partials.map-scripts', ['mapUiVariant' => 'landing-fs'])
