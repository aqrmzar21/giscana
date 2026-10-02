<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PartialRenderable;
use App\Models\AidDisaster;
use App\Models\AidDistribution;
use Illuminate\Http\Request;

class AidDisasterController extends Controller
{
    use PartialRenderable;

    public function index(Request $request)
    {
        $query = AidDisaster::query();

        if ($request->filled('search')) {
            $query->where('district_name', 'like', '%' . $request->search . '%');
        }

        $aidDisasters = $query->orderBy('district_name')->paginate(15)->withQueryString();

        // Overall statistics
        $totalRecipientsSum  = AidDisaster::sum('total_recipients');
        $totalDistributedSum = AidDisaster::sum('distributed_aid');
        $overallPercentage   = $totalRecipientsSum > 0 ? round(($totalDistributedSum / $totalRecipientsSum) * 100, 1) : 0;

        $villageBreakdown = [];
        foreach ($aidDisasters as $disaster) {
            $records = AidDistribution::with(['beneficiary.village', 'aidInventory'])
                ->where('aid_disaster_id', $disaster->id)
                ->get()
                ->groupBy(fn($d) => $d->village?->yard ?? $d->village?->full_name ?? 'Desa Lainnya');

            $villageBreakdown[$disaster->id] = [
                'villages'       => $records,
                'total_villages' => $records->count(),
            ];
        }

        return $this->partialView('admin.aid-disasters.index', compact(
            'aidDisasters',
            'villageBreakdown',
            'totalRecipientsSum',
            'totalDistributedSum',
            'overallPercentage'
        ));
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

        if (empty($validated['district_id'])) {
            $district = \App\Models\District::where('name', $validated['district_name'])->first();
            $validated['district_id'] = $district?->id;
        } else {
            $district = \App\Models\District::find($validated['district_id']);
            if ($district) {
                $validated['district_name'] = $district->name;
            }
        }

        AidDisaster::create($validated);

        return redirect()->route('admin.aid-disasters.index')
            ->with('success', 'Data bantuan bencana kecamatan berhasil ditambahkan.');
    }

    public function show(AidDisaster $aidDisaster)
    {
        // Fetch distributions & village grouping for this disaster
        $distributions = AidDistribution::with(['beneficiary', 'aidInventory', 'village', 'user'])
            ->where('aid_disaster_id', $aidDisaster->id)
            ->latest('distribution_date')
            ->get();

        $villageStats = $distributions->groupBy(fn($d) => $d->village?->yard ?? $d->village?->full_name ?? 'Desa Lainnya');

        return $this->partialView('admin.aid-disasters.show', compact('aidDisaster', 'distributions', 'villageStats'));
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
            ->with('success', 'Data bantuan bencana kecamatan berhasil diperbarui.');
    }

    public function destroy(AidDisaster $aidDisaster)
    {
        abort_if(!auth()->user()->can('delete data'), 403);
        $aidDisaster->delete();

        return redirect()->route('admin.aid-disasters.index')
            ->with('success', 'Data bantuan bencana kecamatan berhasil dihapus.');
    }
}
