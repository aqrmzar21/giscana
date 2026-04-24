<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DisasterZone;
use Illuminate\Http\Request;

class DisasterZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $zones = DisasterZone::latest()->paginate(15);
        return view('admin.disaster-zones.index', compact('zones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.disaster-zones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'disaster_type' => 'required|in:longsor,banjir,other',
            'description' => 'nullable|string',
            'risk_level' => 'required|in:low,medium,high,critical',
            'point_coordinates' => 'json',
            'area_hectares' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);
        $validated['point_coordinates'] = json_decode($validated['point_coordinates'], true);
        $validated['is_active'] = $request->has('is_active');
        
        // dd($validated);
        DisasterZone::create($validated);

        return redirect()->route('admin.disaster-zones.index')
            ->with('success', 'Zona bencana berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DisasterZone $disasterZone)
    {
        return view('admin.disaster-zones.show', compact('disasterZone'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DisasterZone $disasterZone)
    {
        return view('admin.disaster-zones.edit', compact('disasterZone'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DisasterZone $disasterZone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'disaster_type' => 'required|in:longsor,banjir,other',
            'description' => 'nullable|string',
            'risk_level' => 'required|in:low,medium,high,critical',
            'point_coordinates' => 'required|json',
            'area_hectares' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['point_coordinates'] = json_decode($validated['point_coordinates'], true);
        $validated['is_active'] = $request->has('is_active');

        $disasterZone->update($validated);

        return redirect()->route('admin.disaster-zones.index')
            ->with('success', 'Zona bencana berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DisasterZone $disasterZone)
    {
        $disasterZone->delete();

        return redirect()->route('admin.disaster-zones.index')
            ->with('success', 'Zona bencana berhasil dihapus.');
    }
}