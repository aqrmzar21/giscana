<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvacuationFacility;
use Illuminate\Http\Request;

class EvacuationFacilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facilities = EvacuationFacility::latest()->paginate(15);
        return view('admin.evacuation-facilities.index', compact('facilities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.evacuation-facilities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'point_coordinates' => 'required|json',
            'capacity' => 'nullable|integer|min:0',
            'address' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'has_medical_facility' => 'boolean',
            'has_food_storage' => 'boolean',
            'is_accessible' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['point_coordinates'] = json_decode($validated['point_coordinates'], true);
        $validated['has_medical_facility'] = $request->has('has_medical_facility');
        $validated['has_food_storage'] = $request->has('has_food_storage');
        $validated['is_accessible'] = $request->has('is_accessible');
        $validated['is_active'] = $request->has('is_active');

        EvacuationFacility::create($validated);

        return redirect()->route('admin.evacuation-facilities.index')
            ->with('success', 'Fasilitas evakuasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EvacuationFacility $evacuationFacility)
    {
        return view('admin.evacuation-facilities.show', compact('evacuationFacility'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EvacuationFacility $evacuationFacility)
    {
        return view('admin.evacuation-facilities.edit', compact('evacuationFacility'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EvacuationFacility $evacuationFacility)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'point_coordinates' => 'required|json',
            'capacity' => 'nullable|integer|min:0',
            'address' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'has_medical_facility' => 'boolean',
            'has_food_storage' => 'boolean',
            'is_accessible' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['point_coordinates'] = json_decode($validated['point_coordinates'], true);
        $validated['has_medical_facility'] = $request->has('has_medical_facility');
        $validated['has_food_storage'] = $request->has('has_food_storage');
        $validated['is_accessible'] = $request->has('is_accessible');
        $validated['is_active'] = $request->has('is_active');

        $evacuationFacility->update($validated);

        return redirect()->route('admin.evacuation-facilities.index')
            ->with('success', 'Fasilitas evakuasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EvacuationFacility $evacuationFacility)
    {
        $evacuationFacility->delete();

        return redirect()->route('admin.evacuation-facilities.index')
            ->with('success', 'Fasilitas evakuasi berhasil dihapus.');
    }
}

