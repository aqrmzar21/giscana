<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PartialRenderable;
use App\Models\EvacuationFacility;
use App\Models\AidDisaster;
use Illuminate\Http\Request;

class EvacuationFacilityController extends Controller
{
    use PartialRenderable;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EvacuationFacility::with('aidDisaster');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('nama_kecamatan', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $facilities = $query->latest()->paginate(15)->withQueryString();
        
        return $this->partialView('admin.evacuation-facilities.index', compact('facilities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $aidDisasters = AidDisaster::active()->orderBy('nama_kecamatan')->get();
        return $this->partialView('admin.evacuation-facilities.create', compact('aidDisasters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'aid_disaster_id' => 'nullable|exists:aid_disasters,id',
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

        if (!empty($validated['aid_disaster_id'])) {
            $aid = AidDisaster::find($validated['aid_disaster_id']);
            $validated['nama_kecamatan'] = $aid?->nama_kecamatan;
        }

        EvacuationFacility::create($validated);

        return redirect()->route('admin.evacuation-facilities.index')
            ->with('success', 'Fasilitas evakuasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EvacuationFacility $evacuationFacility)
    {
        return $this->partialView('admin.evacuation-facilities.show', compact('evacuationFacility'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EvacuationFacility $evacuationFacility)
    {
        $aidDisasters = AidDisaster::active()->orderBy('nama_kecamatan')->get();
        return $this->partialView('admin.evacuation-facilities.edit', compact('evacuationFacility', 'aidDisasters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EvacuationFacility $evacuationFacility)
    {
        $validated = $request->validate([
            'aid_disaster_id' => 'nullable|exists:aid_disasters,id',
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

        if (!empty($validated['aid_disaster_id'])) {
            $aid = AidDisaster::find($validated['aid_disaster_id']);
            $validated['nama_kecamatan'] = $aid?->nama_kecamatan;
        } else {
            $validated['nama_kecamatan'] = null;
        }

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

