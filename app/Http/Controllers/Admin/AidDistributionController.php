<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PartialRenderable;
use App\Models\AidDistribution;
use App\Models\Beneficiary;
use App\Models\AidInventory;
use App\Models\Village;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AidDistributionController extends Controller
{
    use PartialRenderable;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = AidDistribution::with(['beneficiary.village.district', 'aidInventory'])->latest('distribution_date');

        if ($search = $request->input('search')) {
            $query->whereHas('beneficiary', function ($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('identity_card_number', 'like', "%{$search}%");
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'beneficiary_id'    => 'required|exists:beneficiaries,id',
            'aid_inventory_id'  => 'required|exists:aid_inventories,id',
            'quantity_received' => 'required|integer|min:1',
            'distribution_date' => 'required|date',
            'description'       => 'nullable|string',
        ]);

        $stock = AidInventory::findOrFail($validated['aid_inventory_id']);
        $beneficiary = Beneficiary::findOrFail($validated['beneficiary_id']);

        // 1. CEK DOUBLE CLAIM
        $isDoubleClaim = AidDistribution::where('beneficiary_id', $validated['beneficiary_id'])
            ->whereHas('aidInventory', function ($q) use ($stock) {
                $q->where('category', $stock->category);
            })->exists();

        if ($isDoubleClaim) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Warga tersebut sudah pernah menerima bantuan kategori ini!');
        }

        // 2. CEK STOK LOGISTIK
        if ($stock->remaining_stock < $validated['quantity_received']) {
            return redirect()->back()->withInput()->with('error', 'Gagal: Stok barang logistik tidak mencukupi!');
        }

        // 3. EKSKUSI TRANSAKSI ATOMIC
        DB::transaction(function () use ($validated, $stock, $beneficiary) {
            AidDistribution::create([
                'beneficiary_id'    => $beneficiary->id,
                'aid_inventory_id'  => $stock->id,
                'village_id'        => $beneficiary->village_id,
                'quantity_received' => $validated['quantity_received'],
                'distribution_date' => $validated['distribution_date'],
                'user_id'           => auth()->id(),
                'description'       => $validated['description'] ?? null,
            ]);

            // Potong stok barang
            $stock->decrement('remaining_stock', $validated['quantity_received']);

            // Update status penerima
            $beneficiary->update(['aid_status' => 'received']);
        });

        return redirect()->route('admin.aid-distributions.index')->with('success', 'Transaksi distribusi bantuan berhasil dicatat.');
    }
}