<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EvacuationRoute;
use App\Services\EvacuationRoutingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvacuationRouteController extends Controller
{
    protected EvacuationRoutingService $routingService;

    public function __construct(EvacuationRoutingService $routingService)
    {
        $this->routingService = $routingService;
    }

    /**
     * Endpoint API: Dapatkan rute evakuasi cerdas (Hybrid Evacuation Routing).
     * Mendukung backward-compatibility penuh untuk endpoint `/api/nearest-evacuation-with-route`.
     *
     * Query Parameters:
     * - lat: float (Latitude lokasi user)
     * - lng: float (Longitude lokasi user)
     * - threshold: float (Opsional, jarak batas Mode 1 dalam meter, default 100m)
     */
    public function getNearestWithRoute(Request $request): JsonResponse
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        if (!is_numeric($lat) || !is_numeric($lng)) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter koordinat lat dan lng wajib diisi dengan format numerik yang valid.',
            ], 400);
        }

        $userLat = (float) $lat;
        $userLng = (float) $lng;
        $threshold = $request->has('threshold') ? (float) $request->query('threshold') : null;

        $result = $this->routingService->calculateHybridRoute($userLat, $userLng, $threshold);

        if (!$result['success']) {
            return response()->json($result, 404);
        }

        // Pastikan distance_meters di root untuk kompatibilitas script lama jika ada
        $result['distance_meters'] = $result['summary']['distance_meters'] ?? 0;

        return response()->json($result);
    }

    /**
     * Alias endpoint untuk konsistensi penamaan hybrid route.
     */
    public function hybridRoute(Request $request): JsonResponse
    {
        return $this->getNearestWithRoute($request);
    }

    /**
     * Display a listing of active evacuation routes.
     */
    public function index()
    {
        $routes = EvacuationRoute::with('evacuationFacility')->active()->get();
        return response()->json([
            'success' => true,
            'data' => $routes,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $route = EvacuationRoute::with('evacuationFacility')->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $route,
        ]);
    }
}
