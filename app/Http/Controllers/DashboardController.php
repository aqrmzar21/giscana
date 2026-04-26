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
        return $this->partialView('dashboard');
    }
}
