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
        // 1. Ambil daftar tahun unik dari data transaksi & master disaster
        $distributionYears = AidDistribution::selectRaw('YEAR(distribution_date) as yr')
            ->whereNotNull('distribution_date')
            ->distinct()
            ->pluck('yr');

        $disasterYears = AidDisaster::selectRaw('YEAR(created_at) as yr')
            ->whereNotNull('created_at')
            ->distinct()
            ->pluck('yr');

        $availableYears = $distributionYears->merge($disasterYears)
            ->filter()
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int) date('Y')];
        }

        // Selected year filter (default 'all' atau tahun spesifik)
        $selectedYear = $request->input('year', 'all');

        // Query master kecamatan
        $query = AidDisaster::query();
        $aidDisasters = $query->orderBy('district_name')->paginate(15)->withQueryString();

        // 2. Kalkulasi breakdown desa & statistik terfilter tahun
        $villageBreakdown = [];
        $totalDistributedSum = 0;
        $totalBeneficiariesCount = 0;
        $allVillageKeys = [];

        $chartLabels = [];
        $chartTargets = [];
        $chartDistributed = [];

        foreach ($aidDisasters as $disaster) {
            $distQuery = AidDistribution::with(['beneficiary.village', 'aidInventory'])
                ->where('aid_disaster_id', $disaster->id);

            if ($selectedYear !== 'all') {
                $distQuery->whereYear('distribution_date', $selectedYear);
            }

            $distributions = $distQuery->get();

            $groupedByVillage = $distributions->groupBy(function ($d) {
                $v = $d->beneficiary?->village;
                return $v?->full_name ?? $v?->name ?? 'Desa Lainnya';
            });

            $distDistributedSum = $distributions->sum('quantity_received');
            $distRecipientsCount = $distributions->pluck('beneficiary_id')->unique()->count();

            $villageBreakdown[$disaster->id] = [
                'villages'       => $groupedByVillage,
                'total_villages' => $groupedByVillage->count(),
                'year_received'  => $distDistributedSum,
                'year_recipients'=> $distRecipientsCount,
            ];

            $totalDistributedSum += $distDistributedSum;
            $totalBeneficiariesCount += $distRecipientsCount;

            foreach ($groupedByVillage->keys() as $vName) {
                $allVillageKeys[] = $vName;
            }

            // Data untuk Bar Chart
            $chartLabels[] = $disaster->district_name;
            $chartTargets[] = (int) $disaster->total_recipients;
            $chartDistributed[] = (int) ($selectedYear !== 'all' ? $distDistributedSum : ($disaster->total_received ?? $distDistributedSum));
        }

        $totalVillagesReached = count(array_unique($allVillageKeys));
        $totalRecipientsSum  = AidDisaster::sum('total_recipients');

        if ($selectedYear !== 'all') {
            $overallPercentage = $totalRecipientsSum > 0 
                ? round(($totalBeneficiariesCount / $totalRecipientsSum) * 100, 1) 
                : 0;
        } else {
            $totalDistributedAll = AidDisaster::sum('distributed_aid');
            $overallPercentage   = $totalRecipientsSum > 0 
                ? round(($totalDistributedAll / $totalRecipientsSum) * 100, 1) 
                : 0;
            if ($totalDistributedSum === 0) {
                $totalDistributedSum = $totalDistributedAll;
            }
        }

        // 3. Data untuk Donut/Pie Chart (Kategori Barang Logistik)
        $categoryDistQuery = AidDistribution::with('aidInventory');
        if ($selectedYear !== 'all') {
            $categoryDistQuery->whereYear('distribution_date', $selectedYear);
        }
        $categoryStats = $categoryDistQuery->get()
            ->groupBy(fn($d) => $d->aidInventory?->category ?? $d->aidInventory?->item_name ?? 'Logistik Umum')
            ->map(fn($group) => $group->sum('quantity_received'));

        $categoryLabels = $categoryStats->keys()->toArray();
        $categoryValues = $categoryStats->values()->toArray();

        if (empty($categoryLabels)) {
            $categoryLabels = ['Sembako', 'Obat-obatan', 'Pakaian', 'Material'];
            $categoryValues = [0, 0, 0, 0];
        }

        return $this->partialView('admin.aid-disasters.index', compact(
            'aidDisasters',
            'villageBreakdown',
            'availableYears',
            'selectedYear',
            'totalRecipientsSum',
            'totalDistributedSum',
            'totalBeneficiariesCount',
            'totalVillagesReached',
            'overallPercentage',
            'chartLabels',
            'chartTargets',
            'chartDistributed',
            'categoryLabels',
            'categoryValues'
        ));
    }

    public function print(Request $request)
    {
        abort_if(!auth()->user()->can('export data'), 403);
        
        $selectedYear = $request->input('year', 'all');
        $aidDisasters = AidDisaster::orderBy('district_name')->get();

        $villageBreakdown = [];
        $totalDistributedSum = 0;
        $totalRecipientsCount = 0;

        foreach ($aidDisasters as $disaster) {
            $distQuery = AidDistribution::with(['beneficiary.village', 'aidInventory'])
                ->where('aid_disaster_id', $disaster->id);

            if ($selectedYear !== 'all') {
                $distQuery->whereYear('distribution_date', $selectedYear);
            }

            $distributions = $distQuery->get();

            $groupedByVillage = $distributions->groupBy(function ($d) {
                $v = $d->beneficiary?->village;
                return $v?->full_name ?? $v?->name ?? 'Desa Lainnya';
            });

            $distDistributedSum = $distributions->sum('quantity_received');
            $distRecipientsCount = $distributions->pluck('beneficiary_id')->unique()->count();

            $villageBreakdown[$disaster->id] = [
                'villages'       => $groupedByVillage,
                'total_villages' => $groupedByVillage->count(),
                'year_received'  => $distDistributedSum,
                'year_recipients'=> $distRecipientsCount,
            ];

            $totalDistributedSum += $distDistributedSum;
            $totalRecipientsCount += $distRecipientsCount;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.aid-disasters.pdf', compact(
            'aidDisasters', 
            'villageBreakdown', 
            'selectedYear',
            'totalDistributedSum',
            'totalRecipientsCount'
        ))->setPaper('a4', 'landscape');

        $filename = $selectedYear !== 'all' 
            ? "laporan-bantuan-bencana-{$selectedYear}.pdf" 
            : "laporan-bantuan-bencana-semua-tahun.pdf";

        return $pdf->stream($filename);
    }

    public function create()
    {
        abort_if(!auth()->user()->can('create data'), 403);
        $districts = \App\Models\District::orderBy('name')->get();
        return $this->partialView('admin.aid-disasters.create', compact('districts'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('create data'), 403);
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
        $distributions = AidDistribution::with(['beneficiary', 'aidInventory', 'village', 'user'])
            ->where('aid_disaster_id', $aidDisaster->id)
            ->latest('distribution_date')
            ->get();

        $villageStats = $distributions->groupBy(function ($d) {
            $v = $d->beneficiary?->village;
            return $v?->full_name ?? $v?->name ?? 'Desa Lainnya';
        });

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
