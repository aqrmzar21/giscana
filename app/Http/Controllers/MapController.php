<?php

namespace App\Http\Controllers;

use App\Models\DisasterZone;
use App\Models\EvacuationRoute;
use App\Models\EvacuationFacility;
use App\Models\AidDisaster;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Display the main map page
     */
    public function index(Request $request)
    {
        $disasterType = $request->get('disaster_type', 'all');
        $riskLevel = $request->get('risk_level', 'all');

        return view('map.index', compact('disasterType', 'riskLevel'));
    }

    /**
     * Get all geospatial data for the map
     */
    public function getMapData(Request $request)
    {
        $disasterType = $request->get('disaster_type');
        $riskLevel = $request->get('risk_level');

        // Get disaster zones
        $zonesQuery = DisasterZone::active();
        if ($disasterType && $disasterType !== 'all') {
            $zonesQuery->byDisasterType($disasterType);
        }
        if ($riskLevel && $riskLevel !== 'all') {
            $zonesQuery->byRiskLevel($riskLevel);
        }
        $zones = $zonesQuery->get();

        // Get evacuation routes (dengan relasi ke evacuation_facility untuk nama_fasilitas)
        $routesQuery = EvacuationRoute::with('evacuationFacility')->active()->accessible();
        if ($disasterType && $disasterType !== 'all') {
            $routesQuery->byDisasterType($disasterType);
        }
        $routes = $routesQuery->get();

        // Get evacuation facilities (dengan relasi ke aid_disaster untuk nama_kecamatan)
        $facilities = EvacuationFacility::with('aidDisaster')->active()->accessible()->get();

        // Get aid disasters data
        $aidDisasters = AidDisaster::active()->get();

        // Build district boundaries (batas kecamatan) from static GeoJSON + database aid_disasters
        $districtFeatures = [];
        $geojsonPath = public_path('geojson/kawasan-pesisir.geojson');

        if (file_exists($geojsonPath)) {
            $raw = file_get_contents($geojsonPath);
            $geo = json_decode($raw, true);

            if (is_array($geo) && isset($geo['features']) && is_array($geo['features'])) {
                // Index fitur berdasarkan nama objek (NAMOBJ)
                $byName = [];
                foreach ($geo['features'] as $feature) {
                    $name = $feature['properties']['NAMOBJ'] ?? null;
                    if ($name) {
                        $byName[$name] = $feature;
                    }
                }

                // Cocokkan setiap aid_disaster (\"Kecamatan X\") dengan NAMOBJ di GeoJSON
                // Catatan: beberapa nama di GeoJSON bisa sedikit berbeda penulisan (mis. \"Bonepantai\")
                $nameMap = [
                    'Bone Pantai' => 'Bonepantai',
                ];

                foreach ($aidDisasters as $item) {
                    $namaKec = $item->nama_kecamatan;
                    $namaTrim = preg_replace('/^Kecamatan\\s+/i', '', $namaKec);

                    $lookupName = $nameMap[$namaTrim] ?? $namaTrim;

                    if (isset($byName[$lookupName])) {
                        $src = $byName[$lookupName];

                        $districtFeatures[] = [
                            'type' => 'Feature',
                            'properties' => [
                                'id'                      => $item->id,
                                'nama_kecamatan'          => $item->nama_kecamatan,
                                'jumlah_penerima_bantuan' => $item->jumlah_penerima_bantuan,
                                'bantuan_terdistribusi'   => $item->bantuan_terdistribusi,
                                'persentase_distribusi'   => $item->persentase_distribusi,
                                // properti tambahan dari file geojson jika dibutuhkan
                                'luas'                    => $src['properties']['SHAPE_Area'] ?? null,
                            ],
                            'geometry' => $src['geometry'] ?? null,
                        ];
                    }
                }
            }
        }

        return response()->json([
            'disaster_zones' => [
                'type' => 'FeatureCollection',
                'features' => $zones->map(fn($zone) => $zone->toGeoJSON()),
            ],
            'evacuation_routes' => [
                'type' => 'FeatureCollection',
                'features' => $routes->map(fn($route) => $route->toGeoJSON()),
            ],
            'evacuation_facilities' => [
                'type' => 'FeatureCollection',
                'features' => $facilities->map(fn($facility) => $facility->toGeoJSON()),
            ],
            'aid_disasters' => [
                'type' => 'FeatureCollection',
                'features' => $aidDisasters->map(fn($item) => [
                    'type' => 'Feature',
                    'properties' => [
                        'id'                      => $item->id,
                        'nama_kecamatan'          => $item->nama_kecamatan,
                        'jumlah_penerima_bantuan' => $item->jumlah_penerima_bantuan,
                        'bantuan_terdistribusi'   => $item->bantuan_terdistribusi,
                        'persentase_distribusi'   => $item->persentase_distribusi,
                    ],
                    'geometry' => null, // Data statistik tanpa batas wilayah
                ]),
            ],
            'district_boundaries' => [
                'type' => 'FeatureCollection',
                'features' => $districtFeatures,
            ],
        ]);
    }

    /**
     * Search for locations
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (empty($query)) {
            return response()->json(['results' => []]);
        }

        $results = collect();

        // Search in disaster zones
        $zones = DisasterZone::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->active()
            ->get();

        foreach ($zones as $zone) {
            $results->push([
                'type' => 'disaster_zone',
                'id' => $zone->id,
                'name' => $zone->name,
                'description' => $zone->description,
                'disaster_type' => $zone->disaster_type,
                'risk_level' => $zone->risk_level,
                'coordinates' => $zone->point_coordinates ?? null,
            ]);
        }

        // Search in evacuation facilities
        $facilities = EvacuationFacility::where('name', 'like', "%{$query}%")
            ->orWhere('address', 'like', "%{$query}%")
            ->active()
            ->get();

        foreach ($facilities as $facility) {
            $results->push([
                'type' => 'evacuation_facility',
                'id' => $facility->id,
                'name' => $facility->name,
                'description' => $facility->description,
                'facility_type' => $facility->facility_type,
                'address' => $facility->address,
                'coordinates' => $facility->point_coordinates,
            ]);
        }

        return response()->json(['results' => $results->take(10)]);
    }
}
