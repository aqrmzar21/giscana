<?php

namespace App\Http\Controllers;

use App\Http\Traits\PartialRenderable;
use App\Models\DisasterZone;
use App\Models\EvacuationRoute;
use App\Models\EvacuationFacility;
use App\Models\AidDisaster;

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

        return $this->partialView('dashboard', compact('aidByDistrict', 'aidDisasters'));
    }
}
