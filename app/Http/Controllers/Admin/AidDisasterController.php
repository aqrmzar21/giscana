<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PartialRenderable;
use App\Models\AidDisaster;
use Illuminate\Http\Request;

class AidDisasterController extends Controller
{
    use PartialRenderable;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aidDisasters = AidDisaster::latest()->paginate(15);
        return $this->partialView('admin.aid-disasters.index', compact('aidDisasters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->partialView('admin.aid-disasters.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kecamatan'           => 'required|string|max:255',
            'jumlah_penerima_bantuan'  => 'nullable|integer|min:0',
            'bantuan_terdistribusi'    => 'nullable|integer|min:0',
            'is_active'                => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        AidDisaster::create($validated);

        return redirect()->route('admin.aid-disasters.index')
            ->with('success', 'Data bantuan bencana berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AidDisaster $aidDisaster)
    {
        return $this->partialView('admin.aid-disasters.show', compact('aidDisaster'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AidDisaster $aidDisaster)
    {
        return $this->partialView('admin.aid-disasters.edit', compact('aidDisaster'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AidDisaster $aidDisaster)
    {
        $validated = $request->validate([
            'nama_kecamatan'           => 'required|string|max:255',
            'jumlah_penerima_bantuan'  => 'nullable|integer|min:0',
            'bantuan_terdistribusi'    => 'nullable|integer|min:0',
            'is_active'                => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $aidDisaster->update($validated);

        return redirect()->route('admin.aid-disasters.index')
            ->with('success', 'Data bantuan bencana berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AidDisaster $aidDisaster)
    {
        $aidDisaster->delete();

        return redirect()->route('admin.aid-disasters.index')
            ->with('success', 'Data bantuan bencana berhasil dihapus.');
    }
}
