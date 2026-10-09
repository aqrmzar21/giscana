<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PartialRenderable;
use App\Models\AidBeneficiary;
use App\Models\AidDisaster;
use App\Models\AidDistribution;
use App\Models\AidInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AidDistributionController extends Controller
{
    use PartialRenderable;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = AidDistribution::with(['beneficiary.village.district', 'aidInventory', 'aidDisaster'])->latest('distribution_date');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('beneficiary', function ($q2) use ($search) {
                    $q2->where('recipient_name', 'like', "%{$search}%")
                    ->orWhere('identity_card_number', 'like', "%{$search}%");
                })
                ->orWhereHas('aidInventory', function ($q2) use ($search) {
                    $q2->where('item_name', 'like', "%{$search}%");
                })
                ->orWhereHas('beneficiary.village', function ($vq) use ($search) {
                    $vq->where('full_name', 'like', "%{$search}%");
                })
                ->orWhereHas('beneficiary.village.district', function ($dq) use ($search) {
                    $dq->where('name', 'like', "%{$search}%");
                });
            });
        }


        if ($startDate = $request->input('start_date')) {
            $query->whereDate('distribution_date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('distribution_date', '<=', $endDate);
        }

        $distributions = $query->paginate($perPage)->withQueryString();
        return $this->partialView('admin.aid-distributions.index', compact('distributions'));
    }

    public function print(Request $request)
    {
        abort_if(!auth()->user()->can('export data'), 403);
        $query = AidDistribution::with(['beneficiary.village.district', 'aidInventory', 'aidDisaster', 'user'])->latest('distribution_date');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('beneficiary', function ($q2) use ($search) {
                    $q2->where('recipient_name', 'like', "%{$search}%")
                    ->orWhere('identity_card_number', 'like', "%{$search}%");
                })
                ->orWhereHas('aidInventory', function ($q2) use ($search) {
                    $q2->where('item_name', 'like', "%{$search}%");
                });
            });
        }

        if ($startDate = $request->input('start_date')) {
            $query->whereDate('distribution_date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('distribution_date', '<=', $endDate);
        }

        $distributions = $query->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.aid-distributions.pdf', compact('distributions'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-distribusi-bantuan.pdf');
    }

    public function create()
    {
        $beneficiaries = AidBeneficiary::with(['village.district'])
            ->orderBy('recipient_name')
            ->get();
        $inventories  = AidInventory::active()->where('remaining_stock', '>', 0)->orderBy('item_name')->get();
        $aidDisasters = AidDisaster::where('is_active', true)->orderBy('district_name')->get();

        return $this->partialView('admin.aid-distributions.create', compact('beneficiaries', 'inventories', 'aidDisasters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'beneficiary_id'    => 'required|exists:aid_beneficiaries,id',
            'aid_inventory_id'  => 'required|exists:aid_inventories,id',
            'aid_disaster_id'   => 'required|exists:aid_disasters,id',
            'quantity_received' => 'required|integer|min:1',
            'distribution_date' => 'required|date',
            'description'       => 'nullable|string|max:1000',
        ]);

        /** @var AidInventory $stock */
        $stock       = AidInventory::findOrFail($validated['aid_inventory_id']);
        /** @var AidBeneficiary $beneficiary */
        $beneficiary = AidBeneficiary::findOrFail($validated['beneficiary_id']);
        /** @var AidDisaster $aidDisaster */
        $aidDisaster = AidDisaster::findOrFail($validated['aid_disaster_id']);

        // 1. CEK STOK LOGISTIK
        if ($stock->remaining_stock < $validated['quantity_received']) {
            return redirect()->back()->withInput()
                ->withErrors(['quantity_received' => 'Stok barang logistik tidak mencukupi! Sisa stok: ' . $stock->remaining_stock]);
        }

        // 2. CEK DOUBLE CLAIM per kategori
        $isDoubleClaim = AidDistribution::where('beneficiary_id', $beneficiary->id)
            ->whereHas('aidInventory', function ($q) use ($stock) {
                $q->where('category', $stock->category);
            })->exists();

        if ($isDoubleClaim) {
            return redirect()->back()->withInput()
                ->with('error', 'Gagal: Warga tersebut sudah pernah menerima bantuan kategori ini!');
        }

        // 3. EKSEKUSI TRANSAKSI ATOMIC
        DB::transaction(function () use ($validated, $stock, $beneficiary, $aidDisaster) {
            AidDistribution::create([
                'beneficiary_id'    => $beneficiary->id,
                'aid_inventory_id'  => $stock->id,
                'village_id'        => $beneficiary->village_id,
                'aid_disaster_id'   => $aidDisaster->id,
                'quantity_received' => $validated['quantity_received'],
                'distribution_date' => $validated['distribution_date'],
                'user_id'           => auth()->id(),
                'description'       => $validated['description'] ?? null,
            ]);

            // Potong stok barang
            $stock->decrement('remaining_stock', $validated['quantity_received']);

            // Update status penerima
            $beneficiary->update(['aid_status' => 'received']);

            // Picu kalkulasi ulang aggregator
            $aidDisaster->recalculate();
        });

        return redirect()->route('admin.aid-distributions.index')
            ->with('success', 'Transaksi distribusi bantuan berhasil dicatat.');
    }

    public function show(AidDistribution $aidDistribution)
    {
        $aidDistribution->load(['beneficiary.village.district', 'aidInventory', 'aidDisaster', 'user']);
        return $this->partialView('admin.aid-distributions.show', compact('aidDistribution'));
    }

    public function edit(AidDistribution $aidDistribution)
    {
        abort_if(!auth()->user()->can('update data'), 403);
        $aidDistribution->load(['beneficiary', 'aidInventory', 'aidDisaster']);

        $beneficiaries = AidBeneficiary::with(['village.district'])->orderBy('recipient_name')->get();
        $inventories   = AidInventory::active()->orderBy('item_name')->get();
        $aidDisasters  = AidDisaster::orderBy('district_name')->get();

        return $this->partialView('admin.aid-distributions.edit', compact('aidDistribution', 'beneficiaries', 'inventories', 'aidDisasters'));
    }

    public function update(Request $request, AidDistribution $aidDistribution)
    {
        abort_if(!auth()->user()->can('update data'), 403);

        $validated = $request->validate([
            'beneficiary_id'    => 'required|exists:aid_beneficiaries,id',
            'aid_inventory_id'  => 'required|exists:aid_inventories,id',
            'aid_disaster_id'   => 'required|exists:aid_disasters,id',
            'quantity_received' => 'required|integer|min:1',
            'distribution_date' => 'required|date',
            'description'       => 'nullable|string|max:1000',
        ]);

        $oldQuantity      = $aidDistribution->quantity_received;
        $oldInventoryId   = $aidDistribution->aid_inventory_id;
        $oldBeneficiaryId = $aidDistribution->beneficiary_id;
        $oldAidDisasterId = $aidDistribution->aid_disaster_id;

        $newStock       = AidInventory::findOrFail($validated['aid_inventory_id']);
        $newBeneficiary = AidBeneficiary::findOrFail($validated['beneficiary_id']);
        $newAidDisaster = AidDisaster::findOrFail($validated['aid_disaster_id']);

        // Cek ketersediaan stok baru (tambahkan stok lama dulu untuk perhitungan)
        $effectiveStock = ($oldInventoryId === $newStock->id)
            ? $newStock->remaining_stock + $oldQuantity
            : $newStock->remaining_stock;

        if ($effectiveStock < $validated['quantity_received']) {
            return redirect()->back()->withInput()
                ->withErrors(['quantity_received' => 'Stok barang logistik tidak mencukupi! Sisa stok efektif: ' . $effectiveStock]);
        }

        DB::transaction(function () use (
            $aidDistribution, $validated,
            $oldQuantity, $oldInventoryId, $oldBeneficiaryId, $oldAidDisasterId,
            $newStock, $newBeneficiary, $newAidDisaster
        ) {
            // Kembalikan stok lama
            $oldStock = AidInventory::find($oldInventoryId);
            if ($oldStock) {
                $oldStock->increment('remaining_stock', $oldQuantity);
            }

            // Potong stok baru
            $newStock->decrement('remaining_stock', $validated['quantity_received']);

            // Kembalikan status penerima lama ke 'pending' jika beda penerima dan tidak ada distribusi lain
            if ((int) $oldBeneficiaryId !== (int) $newBeneficiary->id) {
                $stillHasDistribution = AidDistribution::where('beneficiary_id', $oldBeneficiaryId)
                    ->where('id', '!=', $aidDistribution->id)
                    ->exists();
                if (!$stillHasDistribution) {
                    AidBeneficiary::where('id', $oldBeneficiaryId)->update(['aid_status' => 'pending']);
                }
            }

            // Update distribusi
            $aidDistribution->update([
                'beneficiary_id'    => $newBeneficiary->id,
                'aid_inventory_id'  => $newStock->id,
                'village_id'        => $newBeneficiary->village_id,
                'aid_disaster_id'   => $newAidDisaster->id,
                'quantity_received' => $validated['quantity_received'],
                'distribution_date' => $validated['distribution_date'],
                'description'       => $validated['description'] ?? null,
            ]);

            // Update status penerima baru
            $newBeneficiary->update(['aid_status' => 'received']);

            // Recalculate aggregator baru
            $newAidDisaster->recalculate();
            // Recalculate aggregator lama jika berbeda
            if ($oldAidDisasterId && (int) $oldAidDisasterId !== (int) $newAidDisaster->id) {
                $oldAidDisaster = AidDisaster::find($oldAidDisasterId);
                if ($oldAidDisaster) {
                    $oldAidDisaster->recalculate();
                }
            }
        });

        return redirect()->route('admin.aid-distributions.index')
            ->with('success', 'Data distribusi bantuan berhasil diperbarui.');
    }

    public function destroy(AidDistribution $aidDistribution)
    {
        abort_if(!auth()->user()->can('delete data'), 403);

        DB::transaction(function () use ($aidDistribution) {
            $quantity    = $aidDistribution->quantity_received;
            $inventoryId = $aidDistribution->aid_inventory_id;
            $benefId     = $aidDistribution->beneficiary_id;
            $disasterId  = $aidDistribution->aid_disaster_id;

            // Hapus catatan distribusi
            $aidDistribution->delete();

            // Kembalikan stok logistik
            $stock = AidInventory::find($inventoryId);
            if ($stock) {
                $stock->increment('remaining_stock', $quantity);
            }

            // Kembalikan status penerima ke 'pending' jika tidak ada transaksi lain
            $stillHasDistribution = AidDistribution::where('beneficiary_id', $benefId)->exists();
            if (!$stillHasDistribution) {
                AidBeneficiary::where('id', $benefId)->update(['aid_status' => 'pending']);
            }

            // Picu recalculate aggregator
            $aidDisaster = AidDisaster::find($disasterId);
            if ($aidDisaster) {
                $aidDisaster->recalculate();
            }
        });

        return redirect()->route('admin.aid-distributions.index')
            ->with('success', 'Data distribusi bantuan berhasil dihapus dan stok telah dikembalikan.');
    }
}