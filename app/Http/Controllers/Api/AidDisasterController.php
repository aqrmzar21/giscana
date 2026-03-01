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
            ->orderBy('nama_kecamatan')
            ->get()
            ->map(function ($item) {
                return [
                    'id'                       => $item->id,
                    'nama_kecamatan'           => $item->nama_kecamatan,
                    'jumlah_penerima_bantuan'  => $item->jumlah_penerima_bantuan,
                    'bantuan_terdistribusi'    => $item->bantuan_terdistribusi,
                    'sisa_bantuan'             => $item->sisa_bantuan,
                    'persentase_distribusi'    => $item->persentase_distribusi,
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
            'nama_kecamatan'           => 'required|string|max:255',
            'jumlah_penerima_bantuan'  => 'nullable|integer|min:0',
            'bantuan_terdistribusi'    => 'nullable|integer|min:0',
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
                'sisa_bantuan'          => $aidDisaster->sisa_bantuan,
                'persentase_distribusi' => $aidDisaster->persentase_distribusi,
            ]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AidDisaster $aidDisaster): JsonResponse
    {
        $validated = $request->validate([
            'nama_kecamatan'           => 'sometimes|required|string|max:255',
            'jumlah_penerima_bantuan'  => 'nullable|integer|min:0',
            'bantuan_terdistribusi'    => 'nullable|integer|min:0',
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
