<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PartialRenderable;
use App\Models\AidDisaster;
use App\Models\AidBeneficiary;
use App\Models\AidInventory;
use App\Models\AidDistribution;
use Illuminate\Http\Request;

class AidDisasterController extends Controller
{
    use PartialRenderable;

    public function index()
    {
        $aidDisasters = AidDisaster::latest()->paginate(15);

        $villageBreakdown = [];
        foreach ($aidDisasters as $disaster) {
            $records = AidDistribution::with(['beneficiary.village','aidInventory'])
                ->where('aid_disaster_id', $disaster->id)
                ->get()
                ->groupBy(fn($d) => $d->village?->full_name ?? 'Unknown');

            $villageBreakdown[$disaster->id] = [
                'villages' => $records,
                'total_villages' => $records->count(), // jumlah desa unik
            ];
        }

        return $this->partialView('admin.aid-disasters.index', compact('aidDisasters','villageBreakdown'));
    }

    public function create()
    {
        $districts = \App\Models\District::orderBy('name')->get();
        return $this->partialView('admin.aid-disasters.create', compact('districts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'district_id'       => 'nullable|exists:districts,id',
            'district_name'     => 'required|string|max:255',
            'total_recipients'  => 'nullable|integer|min:0',
            'distributed_aid'   => 'nullable|integer|min:0',
            'is_active'         => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Auto-link district_id dari District jika belum disediakan
        if (empty($validated['district_id'])) {
            $district = \App\Models\District::where('name', $validated['district_name'])->first();
            $validated['district_id'] = $district?->id;
        } else {
            // Sinkronkan district_name dari District yang dipilih
            $district = \App\Models\District::find($validated['district_id']);
            if ($district) {
                $validated['district_name'] = $district->name;
            }
        }

        AidDisaster::create($validated);

        return redirect()->route('admin.aid-disasters.index')
            ->with('success', 'Aid disaster data successfully added.');
    }

    public function show(AidDisaster $aidDisaster)
    {
        return $this->partialView('admin.aid-disasters.show', compact('aidDisaster'));
    }

    public function edit(AidDisaster $aidDisaster)
    {
        abort_if(!auth()->user()->can('update data'), 403);
        $districts = \App\Models\District::orderBy('name')->get();
        return $this->partialView('admin.aid-disasters.edit', compact('aidDisaster', 'districts'));
    }

    public function update(Request $request, AidDisaster $aidDisaster)
    {
        abort_if(!auth()->user()->can('update data'), 403);
        $validated = $request->validate([
            'district_name'     => 'required|string|max:255',
            'total_recipients'  => 'nullable|integer|min:0',
            'distributed_aid'   => 'nullable|integer|min:0',
            'is_active'         => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $aidDisaster->update($validated);

        return redirect()->route('admin.aid-disasters.index')
            ->with('success', 'Aid disaster data successfully updated.');
    }

    public function destroy(AidDisaster $aidDisaster)
    {
        abort_if(!auth()->user()->can('delete data'), 403);
        $aidDisaster->delete();

        return redirect()->route('admin.aid-disasters.index')
            ->with('success', 'Aid disaster data successfully deleted.');
    }
}
