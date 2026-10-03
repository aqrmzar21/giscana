<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PartialRenderable;
use App\Models\AidInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AidInventoryController extends Controller
{
    use PartialRenderable;

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = AidInventory::latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%");
            });
        }

        if ($request->has('active_only')) {
            $query->where('is_active', true);
        }

        $inventories = $query->paginate($perPage)->withQueryString();
        return $this->partialView('admin.aid-inventories.index', compact('inventories'));
    }

    public function print(Request $request)
    {
        abort_if(!auth()->user()->can('export data'), 403);
        $query = AidInventory::latest();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%");
            });
        }

        $inventories = $query->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.aid-inventories.pdf', compact('inventories'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream('laporan-stok-logistik-bantuan.pdf');
    }

    public function create()
    {
        return $this->partialView('admin.aid-inventories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name'       => 'required|string|max:255',
            'category'        => 'required|string|max:100',
            'date_stock'      => 'date',
            'source'          => 'required|string|max:255',
            'initial_stock'   => 'required|integer|min:0',
            // 'remaining_stock' => 'nullable|integer|min:0',
            'is_active'       => 'boolean',
        ]);

        // remaining_stock default sama dengan initial_stock saat pertama buat
        if (!isset($validated['remaining_stock'])) {
            $validated['remaining_stock'] = $validated['initial_stock'];
        }
        $validated['is_active'] = $request->boolean('is_active', true);

        AidInventory::create($validated);

        return redirect()->route('admin.aid-inventories.index')
            ->with('success', 'Data logistik bantuan berhasil ditambahkan.');
    }

    public function show(AidInventory $aidInventory)
    {
        $aidInventory->load(['distributions.beneficiary']);
        return $this->partialView('admin.aid-inventories.show', compact('aidInventory'));
    }

    public function edit(AidInventory $aidInventory)
    {
        abort_if(!auth()->user()->can('update data'), 403);
        return $this->partialView('admin.aid-inventories.edit', compact('aidInventory'));
    }

    public function update(Request $request, AidInventory $aidInventory)
    {
        abort_if(!auth()->user()->can('update data'), 403);

        $validated = $request->validate([
            'item_name'       => 'required|string|max:255',
            'category'        => 'required|string|max:100',
            'date_stock'      => 'date',
            'source'          => 'required|string|max:255',
            'initial_stock'   => 'required|integer|min:0',
            'remaining_stock' => 'integer|min:0',
            'is_active'       => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $aidInventory->update($validated);

        return redirect()->route('admin.aid-inventories.index')
            ->with('success', 'Data logistik bantuan berhasil diperbarui.');
    }

    public function destroy(AidInventory $aidInventory)
    {
        abort_if(!auth()->user()->can('delete data'), 403);
        $aidInventory->delete();

        return redirect()->route('admin.aid-inventories.index')
            ->with('success', 'Data logistik bantuan berhasil dihapus.');
    }
}

