<?php

namespace App\Http\Controllers;

use App\Http\Traits\PartialRenderable;
use App\Models\DisasterZone;
use App\Models\EvacuationRoute;
use App\Models\EvacuationFacility;
use App\Models\AidDisaster;
use App\Models\AidInventory;
use App\Models\AidBeneficiary;
use App\Models\AidDistribution;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use PartialRenderable;

    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Aggregated summary statistics
        $stats = [
            'disaster_zones_count'         => DisasterZone::count(),
            'disaster_zones_high_risk'     => DisasterZone::whereIn('risk_level', ['high', 'critical', 'tinggi', 'sangat_tinggi'])->count(),
            'total_affected_population'    => DisasterZone::sum('affected_population'),

            'evacuation_routes_count'      => EvacuationRoute::count(),
            'evacuation_routes_accessible' => EvacuationRoute::where('is_accessible', true)->count(),

            'evacuation_facilities_count'  => EvacuationFacility::count(),
            'total_facility_capacity'      => EvacuationFacility::sum('capacity'),
            'medical_facilities_count'     => EvacuationFacility::where('has_medical_facility', true)->count(),
            'food_facilities_count'        => EvacuationFacility::where('has_food_storage', true)->count(),

            'aid_inventories_count'        => AidInventory::count(),
            'aid_inventory_total_stock'    => AidInventory::sum('remaining_stock'),
            'aid_inventory_initial_stock'  => AidInventory::sum('initial_stock'),

            'aid_beneficiaries_count'      => AidBeneficiary::count(),
            'total_distributed_aid'        => AidDistribution::sum('quantity_received'),
        ];

        // Breakdown disaster types count
        $disasterTypes = DisasterZone::select(
                'disaster_type',
                DB::raw('count(*) as count'),
                DB::raw('sum(affected_population) as total_affected')
            )
            ->whereNotNull('disaster_type')
            ->groupBy('disaster_type')
            ->get();

        // Top 5 kecamatan berdasarkan jumlah KK penerima (total_received) untuk pie chart
        $aidByDistrict = AidDisaster::select('district_name', 'total_received', 'total_recipients')
            ->whereNotNull('district_name')
            ->orderByDesc('total_received')
            ->limit(5)
            ->get();

        // Semua data bantuan kecamatan untuk tabel
        $aidDisasters = AidDisaster::orderBy('district_name')->get();

        // Stok logistik bantuan (diurutkan stok tersisa paling sedikit)
        $aidInventories = AidInventory::orderBy('remaining_stock', 'asc')
            ->limit(5)
            ->get();

        // Riwayat distribusi bantuan terbaru
        $recentDistributions = AidDistribution::with(['beneficiary', 'aidInventory', 'village', 'user'])
            ->latest('distribution_date')
            ->latest('id')
            ->limit(6)
            ->get();

            // Top 5 barang logistik berdasarkan stok tersisa untuk pie chart
        $inventories = AidInventory::select('item_name', 'category', 'initial_stock', 'remaining_stock')
            ->where('is_active', true)
            ->orderByDesc('remaining_stock')
            ->limit(5)
            ->get();

        return $this->partialView('dashboard', compact(
            'stats',
            'disasterTypes',
            'aidByDistrict',
            'aidDisasters',
            'aidInventories',
            'recentDistributions',
            'inventories'
        ));
    }
}
