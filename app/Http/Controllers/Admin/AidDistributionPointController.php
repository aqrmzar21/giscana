<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AidDistributionPoint;
use Illuminate\Http\Request;

class AidDistributionPointController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $points = AidDistributionPoint::latest()->paginate(15);
        return view('admin.aid-distribution-points.index', compact('points'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.aid-distribution-points.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'aid_type' => 'required|in:food,medical,clothing,shelter,mixed',
            'point_coordinates' => 'required|json',
            'address' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'capacity_per_day' => 'nullable|integer|min:0',
            'is_accessible' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['point_coordinates'] = json_decode($validated['point_coordinates'], true);
        $validated['is_accessible'] = $request->has('is_accessible');
        $validated['is_active'] = $request->has('is_active');

        AidDistributionPoint::create($validated);

        return redirect()->route('admin.aid-distribution-points.index')
            ->with('success', 'Titik distribusi bantuan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AidDistributionPoint $aidDistributionPoint)
    {
        return view('admin.aid-distribution-points.show', compact('aidDistributionPoint'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AidDistributionPoint $aidDistributionPoint)
    {
        return view('admin.aid-distribution-points.edit', compact('aidDistributionPoint'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AidDistributionPoint $aidDistributionPoint)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'aid_type' => 'required|in:food,medical,clothing,shelter,mixed',
            'point_coordinates' => 'required|json',
            'address' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:20',
            'capacity_per_day' => 'nullable|integer|min:0',
            'is_accessible' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['point_coordinates'] = json_decode($validated['point_coordinates'], true);
        $validated['is_accessible'] = $request->has('is_accessible');
        $validated['is_active'] = $request->has('is_active');

        $aidDistributionPoint->update($validated);

        return redirect()->route('admin.aid-distribution-points.index')
            ->with('success', 'Titik distribusi bantuan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AidDistributionPoint $aidDistributionPoint)
    {
        $aidDistributionPoint->delete();

        return redirect()->route('admin.aid-distribution-points.index')
            ->with('success', 'Titik distribusi bantuan berhasil dihapus.');
    }
}

