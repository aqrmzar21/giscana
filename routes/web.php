<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\Api\EvacuationRouteController as ApiEvacuationRouteController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\EvacuationFacility;
use App\Models\EvacuationRoute;

// Hybrid Evacuation Routing API
Route::get('/api/nearest-evacuation-with-route', [ApiEvacuationRouteController::class, 'getNearestWithRoute'])->name('api.nearest-evacuation-with-route');
Route::get('/api/evacuation-hybrid-route', [ApiEvacuationRouteController::class, 'hybridRoute'])->name('api.evacuation-hybrid-route');


Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/admin', function () { return redirect()->route('dashboard'); });

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/map', [MapController::class, 'dashboard'])->name('dashboard.map');
});

// Map routes (halaman publik + endpoint data untuk keduanya)
Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::get('/map/data', [MapController::class, 'getMapData'])->name('map.data');
Route::get('/map/hazard-layers', [MapController::class, 'getHazardLayers'])->name('map.hazard-layers');
Route::get('/map/search', [MapController::class, 'search'])->name('map.search');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes - untuk admin, staff, pimpinan
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('disaster-zones/print', [\App\Http\Controllers\Admin\DisasterZoneController::class, 'print'])->name('disaster-zones.print');
    Route::resource('disaster-zones', \App\Http\Controllers\Admin\DisasterZoneController::class);
    Route::get('evacuation-routes/print', [\App\Http\Controllers\Admin\EvacuationRouteController::class, 'print'])->name('evacuation-routes.print');
    Route::resource('evacuation-routes', \App\Http\Controllers\Admin\EvacuationRouteController::class);
    Route::get('evacuation-facilities/print', [\App\Http\Controllers\Admin\EvacuationFacilityController::class, 'print'])->name('evacuation-facilities.print');
    Route::resource('evacuation-facilities', \App\Http\Controllers\Admin\EvacuationFacilityController::class);
    Route::get('aid-disasters/print', [\App\Http\Controllers\Admin\AidDisasterController::class, 'print'])->name('aid-disasters.print');
    Route::resource('aid-disasters', \App\Http\Controllers\Admin\AidDisasterController::class);
    Route::get('aid-distributions/print', [\App\Http\Controllers\Admin\AidDistributionController::class, 'print'])->name('aid-distributions.print');
    Route::get('aid-distributions/export-excel', [\App\Http\Controllers\Admin\AidDistributionController::class, 'exportExcel'])->name('aid-distributions.export-excel');
    Route::resource('aid-distributions', \App\Http\Controllers\Admin\AidDistributionController::class);
    Route::get('aid-inventories/print', [\App\Http\Controllers\Admin\AidInventoryController::class, 'print'])->name('aid-inventories.print');
    Route::resource('aid-inventories', \App\Http\Controllers\Admin\AidInventoryController::class);
    Route::get('aid-beneficiaries/print', [\App\Http\Controllers\Admin\AidBeneficiaryController::class, 'print'])->name('aid-beneficiaries.print');
    Route::get('aid-beneficiaries/export-excel', [\App\Http\Controllers\Admin\AidBeneficiaryController::class, 'exportExcel'])->name('aid-beneficiaries.export-excel');
    Route::resource('aid-beneficiaries', \App\Http\Controllers\Admin\AidBeneficiaryController::class);
    Route::get('aid-recipients/print', [\App\Http\Controllers\Admin\AidRecipientController::class, 'print'])->name('aid-recipients.print');
    Route::resource('aid-recipients', \App\Http\Controllers\Admin\AidRecipientController::class);
    // Staff Management - Hanya untuk admin
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class);
    });
});

require __DIR__.'/auth.php';