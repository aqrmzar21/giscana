<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvacuationRoute;
use Illuminate\Http\Request;

class EvacuationRouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $routes = EvacuationRoute::latest()->paginate(15);
        return view('admin.evacuation-routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.evacuation-routes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'disaster_type' => 'required|in:longsor,banjir,other',
            'line_coordinates' => 'required|json',
            'length_km' => 'nullable|numeric|min:0',
            'route_type' => 'required|in:primary,secondary,emergency',
            'capacity_per_hour' => 'nullable|integer|min:0',
            'is_accessible' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['line_coordinates'] = json_decode($validated['line_coordinates'], true);
        $validated['is_accessible'] = $request->has('is_accessible');
        $validated['is_active'] = $request->has('is_active');

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
        return view('admin.evacuation-routes.edit', compact('evacuationRoute'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EvacuationRoute $evacuationRoute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'disaster_type' => 'required|in:longsor,banjir,other',
            'line_coordinates' => 'required|json',
            'length_km' => 'nullable|numeric|min:0',
            'route_type' => 'required|in:primary,secondary,emergency',
            'capacity_per_hour' => 'nullable|integer|min:0',
            'is_accessible' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['line_coordinates'] = json_decode($validated['line_coordinates'], true);
        $validated['is_accessible'] = $request->has('is_accessible');
        $validated['is_active'] = $request->has('is_active');

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

