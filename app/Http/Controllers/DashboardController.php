<?php

namespace App\Http\Controllers;

use App\Http\Traits\PartialRenderable;
use App\Models\DisasterZone;
use App\Models\EvacuationRoute;
use App\Models\EvacuationFacility;
use App\Models\AidDisaster;
use App\Models\AidInventory;

class DashboardController extends Controller
{
    use PartialRenderable;

    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Top 5 kecamatan berdasarkan distributed_aid untuk pie chart
        $aidByDistrict = AidDisaster::select('district_name', 'distributed_aid', 'total_recipients')
            ->whereNotNull('district_name')
            ->orderByDesc('distributed_aid')
            ->limit(5)
            ->get();

        // Semua data bantuan kecamatan untuk tabel
        $aidDisasters = AidDisaster::orderBy('district_name')->get();

        // Top 5 barang logistik berdasarkan stok tersisa untuk pie chart
        $inventories = AidInventory::select('item_name', 'category', 'initial_stock', 'remaining_stock')
            ->where('is_active', true)
            ->orderByDesc('remaining_stock')
            ->limit(5)
            ->get();

        
        return $this->partialView('dashboard', compact('inventories', 'aidByDistrict', 'aidDisasters'));
    }
}
