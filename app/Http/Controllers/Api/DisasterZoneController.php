<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DisasterZone;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DisasterZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = DisasterZone::active();

        // Filter by disaster type
        if ($request->has('disaster_type')) {
            $query->byDisasterType($request->disaster_type);
        }

        // Filter by risk level
        if ($request->has('risk_level')) {
            $query->byRiskLevel($request->risk_level);
        }

        $zones = $query->get();

        // Convert to GeoJSON format
        $features = $zones->map(function ($zone) {
            return $zone->toGeoJSON();
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Check if user can manage data
        if (!Auth::user()->canManageData()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'disaster_type' => 'required|in:longsor,flood,other',
            'description' => 'nullable|string',
            'risk_level' => 'required|in:low,medium,high,critical',
            'polygon_coordinates' => 'required|array',
            'area_hectares' => 'nullable|numeric|min:0',
            'affected_population' => 'nullable|integer|min:0',
        ]);

        $zone = DisasterZone::create($validated);

        return response()->json([
            'message' => 'Disaster zone created successfully',
            'data' => $zone->toGeoJSON(),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $zone = DisasterZone::findOrFail($id);
        return response()->json($zone->toGeoJSON());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        // Check if user can manage data
        if (!Auth::user()->canManageData()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $zone = DisasterZone::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'disaster_type' => 'sometimes|in:longsor,flood,other',
            'description' => 'nullable|string',
            'risk_level' => 'sometimes|in:low,medium,high,critical',
            'polygon_coordinates' => 'sometimes|array',
            'area_hectares' => 'nullable|numeric|min:0',
            'affected_population' => 'nullable|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        $zone->update($validated);

        return response()->json([
            'message' => 'Disaster zone updated successfully',
            'data' => $zone->toGeoJSON(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        // Check if user can manage data
        if (!Auth::user()->canManageData()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $zone = DisasterZone::findOrFail($id);
        $zone->delete();

        return response()->json(['message' => 'Disaster zone deleted successfully']);
    }
}
