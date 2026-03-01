<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/admin', function () { return view('admin'); })->name('layouts.admin');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Map routes
Route::get('/map', [MapController::class, 'index'])->name('map.index');
Route::get('/map/data', [MapController::class, 'getMapData'])->name('map.data');
Route::get('/map/search', [MapController::class, 'search'])->name('map.search');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes - hanya untuk admin
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('disaster-zones', \App\Http\Controllers\Admin\DisasterZoneController::class);
    Route::resource('evacuation-routes', \App\Http\Controllers\Admin\EvacuationRouteController::class);
    Route::resource('evacuation-facilities', \App\Http\Controllers\Admin\EvacuationFacilityController::class);
    Route::resource('aid-disasters', \App\Http\Controllers\Admin\AidDisasterController::class);
});

require __DIR__.'/auth.php';