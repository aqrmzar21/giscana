<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DisasterZoneController;
use App\Http\Controllers\Api\EvacuationRouteController;
use App\Http\Controllers\Api\EvacuationFacilityController;
use App\Http\Controllers\Api\AidDistributionPointController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API Routes for geospatial data
Route::apiResource('disaster-zones', DisasterZoneController::class);
Route::apiResource('evacuation-routes', EvacuationRouteController::class);
Route::apiResource('evacuation-facilities', EvacuationFacilityController::class);
Route::apiResource('aid-distribution-points', AidDistributionPointController::class);
