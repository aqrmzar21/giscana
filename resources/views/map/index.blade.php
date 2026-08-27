@extends('layouts.landing-map')

@section('title', 'Peta Interaktif - Giscana')

@include('map.partials.map-styles', ['mapUiVariant' => 'landing-fs'])

@section('content')

<div id="map" class="absolute inset-0 h-screen w-screen z-50"></div>

    <!-- Legenda di pojok kiri bawah -->
    <div class="absolute bottom-4 left-4 z-50 p-3">
        @include('map.partials.map-legend-content')
    </div>
    
<!-- Toolbar melayang transparan -->
<div class="absolute bottom-6 right-0 z-50">
    <div class="flex flex-col lg:flex-row items-end justify-end gap-6 items-end px-6">
        
        <!-- Panel Legenda -->
        <!-- <div class="bg-white/80 backdrop-blur-md rounded-lg shadow-lg p-4 h-auto w-auto">
        </div> -->

        <!-- Panel Filter + Checkbox -->
        <div class="bg-white/80 backdrop-blur-md rounded-lg shadow-lg p-4 space-y-4 h-auto">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Filter Peta</h3>
            @include('map.partials.map-filters-fields')

            <div class="border-t border-gray-200 pt-3">
                @include('map.partials.map-district-checkbox')
            </div>
        </div>

        <!-- Panel Layer Rawan Bencana -->
        <div class="bg-white/80 backdrop-blur-md rounded-lg shadow-lg p-4 space-y-4 h-auto">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Layer Rawan Bencana</h3>
            @include('map.partials.map-panel-hazard')
        </div>

    </div>
</div>
@endsection

@include('map.partials.map-scripts', ['mapUiVariant' => 'landing-fs'])
