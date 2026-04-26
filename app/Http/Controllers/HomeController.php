<?php

namespace App\Http\Controllers;

use App\Http\Traits\PartialRenderable;
use Illuminate\Http\Request;
use App\Models\DisasterZone;
use App\Models\EvacuationRoute;
use App\Models\EvacuationFacility;
use App\Models\AidDisaster;

class HomeController extends Controller
{
    use PartialRenderable;
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
            'aid_disasters' => AidDisaster::active()->count(),
        ];

        // Get recent disaster zones for showcase
        $recent_zones = DisasterZone::active()
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return $this->partialView('home', compact('stats', 'recent_zones'));
    }
}
