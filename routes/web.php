<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\EvacuationFacility;
use App\Models\EvacuationRoute;

// Mengambil fasilitas dan jalur terdekat berdasarkan lokasi
Route::get('/api/nearest-evacuation-with-route', function (Request $request) {
    $lat = (float) $request->query('lat');
    $lng = (float) $request->query('lng');

    if (!$lat || !$lng) {
        return response()->json(['success' => false, 'message' => 'Koordinat tidak valid'], 400);
    }

    // 1. Ambil semua data fasilitas titik kumpul
    $facilities = EvacuationFacility::all();

    $closestFacility = null;
    $minDistance = INF; // Set awal ke tak hingga

    // 2. Akumulasi & hitung jarak ke setiap titik di DB
    foreach ($facilities as $facility) {
        $coords = is_string($facility->point_coordinates) 
            ? json_decode($facility->point_coordinates, true) 
            : $facility->point_coordinates;

        if (!$coords) continue;

        // Ambil lat & lng dengan fleksibilitas nama key
        $fLat = (float) ($coords['lat'] ?? $coords['latitude'] ?? $coords[1] ?? 0);
        $fLng = (float) ($coords['lng'] ?? $coords['longitude'] ?? $coords[0] ?? 0);

        if ($fLat == 0 || $fLng == 0) continue;

        // Rumus Haversine (menghitung jarak dalam meter)
        $earthRadius = 6371000; // Jari-jari bumi (meter)
        $dLat = deg2rad($fLat - $lat);
        $dLng = deg2rad($fLng - $lng);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat)) * cos(deg2rad($fLat)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        // Cari jarak yang paling minimal
        if ($distance < $minDistance) {
            $minDistance = $distance;
            $closestFacility = $facility;
        }
    }

    if (!$closestFacility) {
        return response()->json(['success' => false, 'message' => 'Fasilitas tidak ditemukan'], 404);
    }

    // 3. Ambil rute evakuasi dari fasilitas terdekat yang ditemukan
    $routes = EvacuationRoute::where('evacuation_facility_id', $closestFacility->id)->get();

    return response()->json([
        'success' => true,
        'facility' => $closestFacility,
        'routes' => $routes,
        'distance_meters' => round($minDistance, 2)
    ]);
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/admin', function () { return redirect()->route('dashboard'); });

Route::middleware(['auth', 'verified'])->group(function () {
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

// Admin routes - hanya untuk admin
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('disaster-zones/print', [\App\Http\Controllers\Admin\DisasterZoneController::class, 'print'])->name('disaster-zones.print');
    Route::resource('disaster-zones', \App\Http\Controllers\Admin\DisasterZoneController::class);
    Route::get('evacuation-routes/print', [\App\Http\Controllers\Admin\EvacuationRouteController::class, 'print'])->name('evacuation-routes.print');
    Route::resource('evacuation-routes', \App\Http\Controllers\Admin\EvacuationRouteController::class);
    Route::get('evacuation-facilities/print', [\App\Http\Controllers\Admin\EvacuationFacilityController::class, 'print'])->name('evacuation-facilities.print');
    Route::resource('evacuation-facilities', \App\Http\Controllers\Admin\EvacuationFacilityController::class);
    Route::resource('aid-disasters', \App\Http\Controllers\Admin\AidDisasterController::class);
    Route::get('aid-recipients/print', [\App\Http\Controllers\Admin\AidRecipientController::class, 'print'])->name('aid-recipients.print');
    Route::resource('aid-recipients', \App\Http\Controllers\Admin\AidRecipientController::class);
    // Staff Management - Hanya untuk admin
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('staff', \App\Http\Controllers\Admin\StaffController::class);
    });
});

require __DIR__.'/auth.php';