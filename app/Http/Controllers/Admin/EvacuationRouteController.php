<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvacuationRoute;
use App\Models\EvacuationFacility;
use Illuminate\Http\Request;

class EvacuationRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $routes = EvacuationRoute::with('evacuationFacility')->latest()->paginate(15);
        return view('admin.evacuation-routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $facilities = EvacuationFacility::active()->orderBy('name')->get();
        return view('admin.evacuation-routes.create', compact('facilities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'evacuation_facility_id' => 'nullable|exists:evacuation_facilities,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'line_coordinates' => 'required|json',
            'route_type' => 'required|in:primary,secondary,emergency',
            'is_accessible' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['line_coordinates'] = json_decode($validated['line_coordinates'], true);
        $validated['is_accessible'] = $request->has('is_accessible');
        $validated['is_active'] = $request->has('is_active');

        if (!empty($validated['evacuation_facility_id'])) {
            $facility = EvacuationFacility::find($validated['evacuation_facility_id']);
            $validated['nama_fasilitas'] = $facility?->name;
        }

        EvacuationRoute::create($validated);

        return redirect()->route('admin.evacuation-routes.index')
            ->with('success', 'Rute evakuasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EvacuationRoute $evacuationRoute)
    {
        return view('admin.evacuation-routes.show', compact('evacuationRoute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EvacuationRoute $evacuationRoute)
    {
        $facilities = EvacuationFacility::active()->orderBy('name')->get();
        return view('admin.evacuation-routes.edit', compact('evacuationRoute', 'facilities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EvacuationRoute $evacuationRoute)
    {
        $validated = $request->validate([
            'evacuation_facility_id' => 'nullable|exists:evacuation_facilities,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'line_coordinates' => 'required|json',
            'route_type' => 'required|in:primary,secondary,emergency',
            'is_accessible' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['line_coordinates'] = json_decode($validated['line_coordinates'], true);
        $validated['is_accessible'] = $request->has('is_accessible');
        $validated['is_active'] = $request->has('is_active');

        if (!empty($validated['evacuation_facility_id'])) {
            $facility = EvacuationFacility::find($validated['evacuation_facility_id']);
            $validated['nama_fasilitas'] = $facility?->name;
        } else {
            $validated['nama_fasilitas'] = null;
        }

        $evacuationRoute->update($validated);

        return redirect()->route('admin.evacuation-routes.index')
            ->with('success', 'Rute evakuasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EvacuationRoute $evacuationRoute)
    {
        $evacuationRoute->delete();

        return redirect()->route('admin.evacuation-routes.index')
            ->with('success', 'Rute evakuasi berhasil dihapus.');
    }
}

