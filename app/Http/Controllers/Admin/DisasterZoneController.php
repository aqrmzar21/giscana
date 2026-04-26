<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PartialRenderable;
use App\Models\DisasterZone;
use Illuminate\Http\Request;

class DisasterZoneController extends Controller
{
    use PartialRenderable;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DisasterZone::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('disaster_type', 'like', "%{$search}%")
                  ->orWhere('risk_level', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $perPage = $request->get('per_page', 10);
        $zones = $query->latest()->paginate($perPage)->withQueryString();
        
        return $this->partialView('admin.disaster-zones.index', compact('zones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->partialView('admin.disaster-zones.create');
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
            'affected_population' => 'nullable|numeric|min:0',
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
        return $this->partialView('admin.disaster-zones.show', compact('disasterZone'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DisasterZone $disasterZone)
    {
        return $this->partialView('admin.disaster-zones.edit', compact('disasterZone'));
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
            'affected_population' => 'nullable|numeric|min:0',
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