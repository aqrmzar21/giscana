<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PartialRenderable;
use App\Models\EvacuationRoute;
use App\Models\EvacuationFacility;
use Illuminate\Http\Request;

class EvacuationRouteController extends Controller
{
    use PartialRenderable;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EvacuationRoute::with('evacuationFacility');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('route_type', 'like', "%{$search}%")
                  ->orWhere('nama_fasilitas', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $routes = $query->latest()->paginate(15)->withQueryString();
        
        return $this->partialView('admin.evacuation-routes.index', compact('routes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $facilities = EvacuationFacility::active()->orderBy('name')->get();
        return $this->partialView('admin.evacuation-routes.create', compact('facilities'));
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
        return $this->partialView('admin.evacuation-routes.show', compact('evacuationRoute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EvacuationRoute $evacuationRoute)
    {
        $facilities = EvacuationFacility::active()->orderBy('name')->get();
        return $this->partialView('admin.evacuation-routes.edit', compact('evacuationRoute', 'facilities'));
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

