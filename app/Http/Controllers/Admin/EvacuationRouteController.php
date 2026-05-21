<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\PartialRenderable;
use App\Models\EvacuationFacility;
use App\Models\EvacuationRoute;
use Illuminate\Http\Request;

class EvacuationRouteController extends Controller
{
    use PartialRenderable;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EvacuationRoute::with('evacuationFacility');

        // Ambil daftar kecamatan unik dari tabel aid_disasters
        $districts = \App\Models\AidDisaster::select('district_name')
            ->distinct()
            ->whereNotNull('district_name')
            ->orderBy('district_name')
            ->get();

        if ($request->filled('district_name')) {
            $districtName = $request->district_name;
            $query->whereHas('evacuationFacility', function($q) use ($districtName) {
                $q->where('district_name', $districtName);
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $perPage = $request->get('per_page', 10);
        $routes = $query->latest()->paginate($perPage)->withQueryString();
        
        return $this->partialView('admin.evacuation-routes.index', compact('routes', 'districts'));
    }

    /**
     * Print PDF report of evacuation routes.
     */
    public function print(Request $request)
    {
        $query = EvacuationRoute::with('evacuationFacility');

        if ($request->filled('district_name')) {
            $districtName = $request->district_name;
            $query->whereHas('evacuationFacility', function($q) use ($districtName) {
                $q->where('district_name', $districtName);
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $routes = $query->latest()->get();
        $districtName = $request->filled('district_name') ? $request->district_name : 'Semua Kecamatan';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.evacuation-routes.print', compact('routes', 'districtName'))
            ->setPaper('a4', 'landscape');

        $fileName = 'laporan-rute-evakuasi';
        if ($request->filled('district_name')) {
            $fileName .= '-' . \Illuminate\Support\Str::slug($request->district_name);
        }
        $fileName .= '.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $facilities = EvacuationFacility::active()->orderBy('name')->get();
        return $this->partialView('admin.evacuation-routes.create', compact('facilities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'line_coordinates' => 'required|json',
            'route_type' => 'required|in:primary,secondary,emergency',
            'is_accessible' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['line_coordinates'] = json_decode($validated['line_coordinates'], true);
        $validated['is_accessible'] = $request->has('is_accessible');
        $validated['is_active'] = $request->has('is_active');

        EvacuationRoute::create($validated);

        return redirect()->route('admin.evacuation-routes.index')
            ->with('success', 'Rute evakuasi berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EvacuationRoute $evacuationRoute)
    {
        return $this->partialView('admin.evacuation-routes.show', compact('evacuationRoute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EvacuationRoute $evacuationRoute)
    {
        $facilities = EvacuationFacility::active()->orderBy('name')->get();
        return $this->partialView('admin.evacuation-routes.edit', compact('evacuationRoute', 'facilities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EvacuationRoute $evacuationRoute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'line_coordinates' => 'required|json',
            'route_type' => 'required|in:primary,secondary,emergency',
            'is_accessible' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['line_coordinates'] = json_decode($validated['line_coordinates'], true);
        $validated['is_accessible'] = $request->has('is_accessible');
        $validated['is_active'] = $request->has('is_active');

        $evacuationRoute->update($validated);

        return redirect()->route('admin.evacuation-routes.index')
            ->with('success', 'Rute evakuasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EvacuationRoute $evacuationRoute)
    {
        $evacuationRoute->delete();

        return redirect()->route('admin.evacuation-routes.index')
            ->with('success', 'Rute evakuasi berhasil dihapus.');
    }
}

