<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisasterZone;
use App\Models\EvacuationRoute;
use App\Models\EvacuationFacility;
use App\Models\AidDistributionPoint;

class HomeController extends Controller
{
    /**
     * Display the landing page
     */
    public function index()
    {
        // Get statistics for the landing page
        $stats = [
            'disaster_zones' => DisasterZone::active()->count(),
            'evacuation_routes' => EvacuationRoute::active()->count(),
            'evacuation_facilities' => EvacuationFacility::active()->count(),
            'aid_distribution_points' => AidDistributionPoint::active()->count(),
        ];

        // Get recent disaster zones for showcase
        $recent_zones = DisasterZone::active()
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('home', compact('stats', 'recent_zones'));
    }
}
