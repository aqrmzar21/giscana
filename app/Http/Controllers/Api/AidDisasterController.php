<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AidDisaster;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AidDisasterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $aidDisasters = AidDisaster::active()
            ->orderBy('district_name')
            ->get()
            ->map(function ($item) {
                return [
                    'id'                       => $item->id,
                    'district_name'           => $item->district_name,
                    'total_recipients'  => $item->total_recipients,
                    'distributed_aid'    => $item->distributed_aid,
                    'remaining_aid'             => $item->remaining_aid,
                    'distribution_percentage'    => $item->distribution_percentage,
                    'is_active'                => $item->is_active,
                    'last_synced_at'           => $item->last_synced_at,
                    'created_at'               => $item->created_at,
                    'updated_at'               => $item->updated_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $aidDisasters,
            'total'   => $aidDisasters->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'district_name'           => 'required|string|max:255',
            'total_recipients'  => 'nullable|integer|min:0',
            'distributed_aid'    => 'nullable|integer|min:0',
            'is_active'                => 'boolean',
        ]);

        $aidDisaster = AidDisaster::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data bantuan bencana berhasil ditambahkan.',
            'data'    => $aidDisaster,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(AidDisaster $aidDisaster): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => array_merge($aidDisaster->toArray(), [
                'remaining_aid'          => $aidDisaster->remaining_aid,
                'distribution_percentage' => $aidDisaster->distribution_percentage,
            ]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AidDisaster $aidDisaster): JsonResponse
    {
        $validated = $request->validate([
            'district_name'           => 'sometimes|required|string|max:255',
            'total_recipients'  => 'nullable|integer|min:0',
            'distributed_aid'    => 'nullable|integer|min:0',
            'is_active'                => 'boolean',
        ]);

        $aidDisaster->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data bantuan bencana berhasil diperbarui.',
            'data'    => $aidDisaster->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AidDisaster $aidDisaster): JsonResponse
    {
        $aidDisaster->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data bantuan bencana berhasil dihapus.',
        ]);
    }
}
