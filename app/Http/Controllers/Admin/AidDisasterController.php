<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PartialRenderable;
use App\Models\District;
use App\Models\Village;
use App\Models\AidDisaster;
use App\Models\AidDistribution;
use Illuminate\Http\Request;

class AidDisasterController extends Controller
{
    use PartialRenderable;

    public function index(Request $request)
    {
        // 1. Ambil daftar tahun unik
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

        $selectedYear = $request->input('year', 'all');

        // Query master kecamatan
        $aidDisasters = AidDisaster::orderBy('district_name')->paginate(15)->withQueryString();

        // 2. Breakdown desa (Semua desa di kecamatan tetap muncul walau belum ada distribusi)
        $breakdownData = $this->buildVillageBreakdown($aidDisasters, $selectedYear);
        $villageBreakdown = $breakdownData['villageBreakdown'];
        $totalDistributedSum = $breakdownData['totalDistributedSum'];
        $totalBeneficiariesCount = $breakdownData['totalBeneficiariesCount'];
        $allVillageKeys = $breakdownData['allVillageKeys'];

        $chartLabels = [];
        $chartTargets = [];
        $chartDistributed = [];

        foreach ($aidDisasters as $disaster) {
            $distDistributedSum = $villageBreakdown[$disaster->id]['year_received'] ?? 0;

            // Data untuk Bar Chart
            $chartLabels[] = $disaster->district_name;
            $chartTargets[] = (int) $disaster->total_recipients;
            $chartDistributed[] = (int) ($selectedYear !== 'all' ? $distDistributedSum : ($disaster->distributed_aid ?? $distDistributedSum));
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

        // 3. Statistik kategori barang
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
            'aidDisasters','villageBreakdown','availableYears','selectedYear',
            'totalRecipientsSum','totalDistributedSum','totalBeneficiariesCount',
            'totalVillagesReached','overallPercentage','chartLabels','chartTargets',
            'chartDistributed','categoryLabels','categoryValues'
        ));
    }

    public function print(Request $request)
    {
        abort_if(!auth()->user()->can('export data'), 403);

        $type = $request->input('type', 'district');
        $selectedYear = $request->input('year', 'all');
        $disasterId = $request->input('disaster_id');

        $query = AidDisaster::query();
        if ($disasterId) {
            $query->where('id', $disasterId);
        }
        $aidDisasters = $query->orderBy('district_name')->get();

        $breakdownData = $this->buildVillageBreakdown($aidDisasters, $selectedYear);
        $villageBreakdown = $breakdownData['villageBreakdown'];
        $totalDistributedSum = $breakdownData['totalDistributedSum'];
        $totalRecipientsCount = $breakdownData['totalBeneficiariesCount'];

        $viewName = ($type === 'village') ? 'admin.aid-disasters.pdf-village' : 'admin.aid-disasters.pdf-district';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, compact(
            'aidDisasters','villageBreakdown','selectedYear',
            'totalDistributedSum','totalRecipientsCount','type','disasterId'
        ))->setPaper('a4', 'landscape');

        $distName = ($disasterId && $aidDisasters->first())
            ? \Illuminate\Support\Str::slug($aidDisasters->first()->district_name)
            : 'semua-kecamatan';

        $filename = "laporan-bantuan-{$type}-{$distName}-{$selectedYear}.pdf";

        return $pdf->stream($filename);
    }

    /**
     * Helper untuk memproses rincian penyaluran logistik per desa.
     * Menjamin semua desa di kecamatan tetap muncul meski belum ada data distribusi.
     */
    private function buildVillageBreakdown($disasters, $selectedYear = 'all'): array
    {
        if ($disasters instanceof \Illuminate\Database\Eloquent\Model) {
            $disasters = collect([$disasters]);
        }

        $villageBreakdown = [];
        $totalDistributedSum = 0;
        $totalBeneficiariesCount = 0;
        $allVillageKeys = [];

        $allDistricts = District::all();

        foreach ($disasters as $disaster) {
            $district = null;

            // 1. PRIORITAS UTAMA: Cocokkan nama kecamatan (district_name) terlebih dahulu ke tabel districts!
            // Karena nama kecamatan adalah identitas tampilan utama pada kartu rekapitulasi.
            if ($disaster->district_name) {
                $cleanDisasterName = strtolower(trim($disaster->district_name));
                // Hilangkan prefiks 'kecamatan ' jika tidak sengaja tersimpan
                $cleanDisasterName = preg_replace('/^kecamatan\s+/i', '', $cleanDisasterName);

                // 1a. Pencocokan nama eksak
                $district = $allDistricts->first(function ($d) use ($cleanDisasterName) {
                    $cleanDName = strtolower(trim(preg_replace('/^kecamatan\s+/i', '', $d->name)));
                    return $cleanDName === $cleanDisasterName;
                });

                // 1b. Fallback normalisasi variasi spasi (misal "Bonepantai" vs "Bone Pantai", "Kabila Bone" vs "KabilaBone")
                if (!$district) {
                    $slugTarget = preg_replace('/[^a-z0-9]/', '', $cleanDisasterName);
                    $district = $allDistricts->first(function ($d) use ($slugTarget) {
                        $slugD = preg_replace('/[^a-z0-9]/', '', strtolower(trim($d->name)));
                        return $slugD === $slugTarget;
                    });
                }
            }

            // 2. Fallback: jika tidak ditemukan via nama, baru cocokkan via district_id
            if (!$district && $disaster->district_id) {
                $district = $allDistricts->firstWhere('id', $disaster->district_id);
            }

            $districtId = $district?->id ?? $disaster->district_id;

            // 3. Auto-heal: Perbarui district_id pada tabel aid_disasters di database agar selalu sinkron dan akurat
            if ($district && $disaster->exists && (int)$disaster->district_id !== (int)$district->id) {
                try {
                    $disaster->district_id = $district->id;
                    $disaster->saveQuietly();
                } catch (\Throwable $e) {}
            }

            // 4. Ambil HANYA desa-desa yang terdaftar di kecamatan ini dari tabel villages
            if ($districtId) {
                $allVillages = Village::where('district_id', $districtId)
                    ->orderBy('yard')
                    ->get();
            } elseif ($district) {
                $allVillages = Village::where('zone', $district->name)
                    ->orderBy('yard')
                    ->get();
            } else {
                $allVillages = Village::whereRaw('LOWER(TRIM(zone)) = ?', [strtolower(trim($disaster->district_name))])
                    ->orderBy('yard')
                    ->get();
            }

            // Fallback jika belum terisi district_id pada sebagian data desa
            if ($allVillages->isEmpty() && $district) {
                $allVillages = Village::where('zone', $district->name)
                    ->orderBy('yard')
                    ->get();
            }

            $villageIds = $allVillages->pluck('id')->filter()->all();

            // 5. Query transaksi distribusi bantuan khusus untuk desa-desa di kecamatan ini
            $distQuery = AidDistribution::with(['beneficiary.village', 'aidInventory', 'village'])
                ->where(function ($q) use ($disaster, $villageIds) {
                    $q->where('aid_disaster_id', $disaster->id);
                    if (!empty($villageIds)) {
                        $q->orWhereIn('village_id', $villageIds)
                          ->orWhereHas('beneficiary', function ($bq) use ($villageIds) {
                              $bq->whereIn('village_id', $villageIds);
                          });
                    }
                });

            if ($selectedYear !== 'all') {
                $distQuery->whereYear('distribution_date', $selectedYear);
            }

            $distributions = $distQuery->get();

            // 6. Group distribusi berdasarkan ID desa dan Nama Desa
            $groupedByVillageId = $distributions->groupBy(function ($d) {
                return $d->village_id ?: ($d->beneficiary?->village_id ?: 0);
            });

            $groupedByVillageName = $distributions->groupBy(function ($d) {
                $v = $d->village ?: $d->beneficiary?->village;
                $name = $v?->yard ?: ($v?->full_name ?: '');
                return strtolower(trim($name));
            });

            // 7. Gabungkan master desa dengan data distribusi
            // HANYA desa-desa di kecamatan ini yang dimasukkan, tidak menambah desa luar!
            $mergedVillages = collect();

            foreach ($allVillages as $village) {
                $vName = $village->yard ?: ($village->full_name ?: ('Desa ' . $village->id));

                // Cocokkan via ID desa
                $records = $groupedByVillageId->get($village->id);

                // Fallback cocokkan via nama desa persis jika ID belum sesuai
                if ((!$records || $records->isEmpty()) && $groupedByVillageName->has(strtolower(trim($vName)))) {
                    $records = $groupedByVillageName->get(strtolower(trim($vName)));
                }

                $keyName = $vName;
                if ($mergedVillages->has($keyName)) {
                    $keyName = $vName . ' (' . ($village->full_name ?: $village->id) . ')';
                }

                $mergedVillages->put($keyName, $records ?: collect());
                $allVillageKeys[] = $village->id;
            }

            // Hitung total unit dan penerima untuk kecamatan ini
            $districtDistributions = $distributions->filter(function ($d) use ($villageIds, $disaster) {
                $vId = $d->village_id ?: ($d->beneficiary?->village_id ?: 0);
                if ($vId && !empty($villageIds)) {
                    return in_array($vId, $villageIds);
                }
                return (int)$d->aid_disaster_id === (int)$disaster->id;
            });

            $distDistributedSum = (int) $districtDistributions->sum('quantity_received');
            $distRecipientsCount = (int) $districtDistributions->pluck('beneficiary_id')->filter()->unique()->count();

            $villageBreakdown[$disaster->id] = [
                'villages'        => $mergedVillages,
                'total_villages'  => $mergedVillages->count(),
                'year_received'   => $distDistributedSum,
                'year_recipients' => $distRecipientsCount,
            ];

            $totalDistributedSum += $distDistributedSum;
            $totalBeneficiariesCount += $distRecipientsCount;
        }

        return [
            'villageBreakdown'        => $villageBreakdown,
            'totalDistributedSum'    => $totalDistributedSum,
            'totalBeneficiariesCount'=> $totalBeneficiariesCount,
            'allVillageKeys'          => $allVillageKeys,
        ];
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
        $breakdownData = $this->buildVillageBreakdown($aidDisaster);
        $villageStats = $breakdownData['villageBreakdown'][$aidDisaster->id]['villages'] ?? collect();

        $distributions = AidDistribution::with(['beneficiary', 'aidInventory', 'village', 'user'])
            ->where('aid_disaster_id', $aidDisaster->id)
            ->latest('distribution_date')
            ->get();

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

        if (!empty($validated['district_name'])) {
            $cleanName = strtolower(trim(preg_replace('/^kecamatan\s+/i', '', $validated['district_name'])));
            $matchedDistrict = District::all()->first(function ($d) use ($cleanName) {
                $cleanD = strtolower(trim(preg_replace('/^kecamatan\s+/i', '', $d->name)));
                return $cleanD === $cleanName || preg_replace('/[^a-z0-9]/', '', $cleanD) === preg_replace('/[^a-z0-9]/', '', $cleanName);
            });
            if ($matchedDistrict) {
                $validated['district_id'] = $matchedDistrict->id;
                $validated['district_name'] = $matchedDistrict->name;
            }
        }

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
