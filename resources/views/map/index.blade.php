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
    <div class="flex flex-col lg:grid lg:grid-cols-12 lg:gap-x-0 lg:items-start">
        <!-- Text: Mobile Bottom (order 3), Desktop Top-Left -->
        <div class="order-3 lg:order-none lg:col-span-4 lg:col-start-1 lg:row-start-1 lg:pr-6 border-t border-gray-200 pt-5 mt-5 lg:border-t-0 lg:pt-0 lg:mt-0">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Kontrol peta</p>
            <p class="text-xs text-gray-500 mb-0">Provinsi Gorontalo — filter dan legenda mengatur lapisan yang ditampilkan</p>
        </div>

        <!-- Legend: Mobile Top (order 1), Desktop Bottom-Left -->
        <div class="order-1 lg:order-none lg:col-span-4 lg:col-start-1 lg:row-start-2 lg:pr-6 lg:mt-4">
            @include('map.partials.map-legend-content')
        </div>

        <!-- Controls: Mobile Middle (order 2), Desktop Right (spans 2 rows) -->
        <div class="order-2 lg:order-none lg:col-span-8 lg:col-start-5 lg:row-start-1 lg:row-span-2 min-w-0 space-y-3 lg:border-l border-gray-200 lg:pl-6 border-t border-gray-200 pt-5 mt-5 lg:border-t-0 lg:pt-0 lg:mt-0">
            @include('map.partials.map-controls-inner', ['toolbarContext' => 'footer', 'hideToolbarHeading' => false, 'hideLegend' => true])
        </div>
    </div>
</div>
@endsection

@include('map.partials.map-scripts', ['mapUiVariant' => 'landing-fs'])
