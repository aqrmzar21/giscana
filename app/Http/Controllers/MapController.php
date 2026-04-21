<?php

namespace App\Http\Controllers;

use App\Models\DisasterZone;
use App\Models\EvacuationRoute;
use App\Models\EvacuationFacility;
use App\Models\AidDisaster;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /** Batas administrasi GeoJSON: hanya fitur Kabupaten Bone Bolango (Gorontalo). */
    private const MAP_REGENCY_NAME = 'Bone Bolango';

    /** Batas peta jika perhitungan dari GeoJSON gagal. */
    private const FALLBACK_BBOX = [
        'min_lng' => 122.92,
        'max_lng' => 123.52,
        'min_lat' => 0.28,
        'max_lat' => 0.62,
    ];

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
     * Peta di dalam layout admin (hanya pengguna terautentikasi).
     */
    public function dashboard(Request $request)
    {
        $disasterType = $request->get('disaster_type', 'all');
        $riskLevel = $request->get('risk_level', 'all');

        return view('map.dashboard', compact('disasterType', 'riskLevel'));
    }

    /**
     * Get all geospatial data for the map
     */
    public function getMapData(Request $request)
    {
        $disasterType = $request->get('disaster_type');
        $riskLevel = $request->get('risk_level');

        $districtCtx = $this->boneBolangoDistrictContext();
        $bbox = $districtCtx['bbox'];
        $byName = $districtCtx['byName'];

        // Get disaster zones
        $zonesQuery = DisasterZone::active();
        if ($disasterType && $disasterType !== 'all') {
            $zonesQuery->byDisasterType($disasterType);
        }
        if ($riskLevel && $riskLevel !== 'all') {
            $zonesQuery->byRiskLevel($riskLevel);
        }
        $zones = $zonesQuery->get()
            ->filter(fn (DisasterZone $z) => $this->pointInBoundingBox($z->point_coordinates, $bbox))
            ->values();

        // Get evacuation routes (dengan relasi ke evacuation_facility untuk nama_fasilitas)
        $routesQuery = EvacuationRoute::with('evacuationFacility')->active()->accessible();
        if ($disasterType && $disasterType !== 'all') {
            $routesQuery->byDisasterType($disasterType);
        }
        $routes = $routesQuery->get()
            ->filter(fn (EvacuationRoute $r) => $this->lineOverlapsBoundingBox($r->line_coordinates, $bbox))
            ->values();

        // Get evacuation facilities (dengan relasi ke aid_disaster untuk nama_kecamatan)
        $facilities = EvacuationFacility::with('aidDisaster')->active()->accessible()->get()
            ->filter(fn (EvacuationFacility $f) => $this->pointInBoundingBox($f->point_coordinates, $bbox))
            ->values();

        // Get aid disasters data
        $aidDisasters = AidDisaster::active()->get();

        // Batas kecamatan: hanya kecamatan di Kab. Bone Bolango yang punya pasangan di DB
        $districtFeatures = [];
        $nameMap = [
            'Bone Pantai' => 'Bonepantai',
        ];

        foreach ($aidDisasters as $item) {
            $namaKec = $item->nama_kecamatan;
            $namaTrim = preg_replace('/^Kecamatan\\s+/i', '', $namaKec);
            $lookupName = $nameMap[$namaTrim] ?? $namaTrim;

            if (! isset($byName[$lookupName])) {
                continue;
            }

            $src = $byName[$lookupName];

            $districtFeatures[] = [
                'type' => 'Feature',
                'properties' => [
                    'id'                      => $item->id,
                    'nama_kecamatan'          => $item->nama_kecamatan,
                    'jumlah_penerima_bantuan' => $item->jumlah_penerima_bantuan,
                    'bantuan_terdistribusi'   => $item->bantuan_terdistribusi,
                    'persentase_distribusi'   => $item->persentase_distribusi,
                    'luas'                    => $src['properties']['SHAPE_Area'] ?? null,
                ],
                'geometry' => $src['geometry'] ?? null,
            ];
        }

        $matchedAidIds = collect($districtFeatures)->pluck('properties.id')->filter()->all();
        $aidDisastersFiltered = $aidDisasters->filter(fn (AidDisaster $a) => in_array($a->id, $matchedAidIds, true))->values();

        return response()->json([
            'disaster_zones' => [
                'type' => 'FeatureCollection',
                'features' => $zones->map(fn ($zone) => $zone->toGeoJSON()),
            ],
            'evacuation_routes' => [
                'type' => 'FeatureCollection',
                'features' => $routes->map(fn ($route) => $route->toGeoJSON()),
            ],
            'evacuation_facilities' => [
                'type' => 'FeatureCollection',
                'features' => $facilities->map(fn ($facility) => $facility->toGeoJSON()),
            ],
            'aid_disasters' => [
                'type' => 'FeatureCollection',
                'features' => $aidDisastersFiltered->map(fn ($item) => [
                    'type' => 'Feature',
                    'properties' => [
                        'id'                      => $item->id,
                        'nama_kecamatan'          => $item->nama_kecamatan,
                        'jumlah_penerima_bantuan' => $item->jumlah_penerima_bantuan,
                        'bantuan_terdistribusi'   => $item->bantuan_terdistribusi,
                        'persentase_distribusi'   => $item->persentase_distribusi,
                    ],
                    'geometry' => null,
                ]),
            ],
            'district_boundaries' => [
                'type' => 'FeatureCollection',
                'features' => $districtFeatures,
            ],
            'map_extent' => [
                'southwest' => [$bbox['min_lat'], $bbox['min_lng']],
                'northeast' => [$bbox['max_lat'], $bbox['max_lng']],
            ],
            'map_region' => [
                'regency' => self::MAP_REGENCY_NAME,
                'province' => 'Gorontalo',
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

        $bbox = $this->boneBolangoDistrictContext()['bbox'];
        $results = collect();

        $zones = DisasterZone::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->active()
            ->get()
            ->filter(fn (DisasterZone $z) => $this->pointInBoundingBox($z->point_coordinates, $bbox));

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

        $facilities = EvacuationFacility::where('name', 'like', "%{$query}%")
            ->orWhere('address', 'like', "%{$query}%")
            ->active()
            ->get()
            ->filter(fn (EvacuationFacility $f) => $this->pointInBoundingBox($f->point_coordinates, $bbox));

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

    /**
     * Indeks kecamatan (NAMOBJ) dan bbox dari GeoJSON hanya untuk Kab. Bone Bolango.
     *
     * @return array{bbox: array{min_lng: float, max_lng: float, min_lat: float, max_lat: float}, byName: array<string, array>}
     */
    private function boneBolangoDistrictContext(): array
    {
        static $memo;

        if ($memo !== null) {
            return $memo;
        }

        $geojsonPath = public_path('geojson/kawasan-pesisir.geojson');
        $byName = [];
        $minLng = INF;
        $maxLng = -INF;
        $minLat = INF;
        $maxLat = -INF;

        if (file_exists($geojsonPath)) {
            $geo = json_decode(file_get_contents($geojsonPath), true);
            if (is_array($geo) && isset($geo['features']) && is_array($geo['features'])) {
                $regency = self::MAP_REGENCY_NAME;
                foreach ($geo['features'] as $feature) {
                    $props = $feature['properties'] ?? [];
                    if (($props['WADMKK'] ?? $props['WIADKK'] ?? '') !== $regency) {
                        continue;
                    }
                    $geom = $feature['geometry'] ?? null;
                    if (is_array($geom)) {
                        $this->mergeGeometryIntoBounds($geom, $minLng, $maxLng, $minLat, $maxLat);
                    }
                    $name = $props['NAMOBJ'] ?? null;
                    if ($name) {
                        $byName[$name] = $feature;
                    }
                }
            }
        }

        if ($minLng === INF) {
            $bbox = self::FALLBACK_BBOX;
        } else {
            $padLng = max(($maxLng - $minLng) * 0.02, 0.008);
            $padLat = max(($maxLat - $minLat) * 0.02, 0.008);
            $bbox = [
                'min_lng' => $minLng - $padLng,
                'max_lng' => $maxLng + $padLng,
                'min_lat' => $minLat - $padLat,
                'max_lat' => $maxLat + $padLat,
            ];
        }

        $memo = ['bbox' => $bbox, 'byName' => $byName];

        return $memo;
    }

    private function mergeGeometryIntoBounds(array $geometry, float &$minLng, float &$maxLng, float &$minLat, float &$maxLat): void
    {
        $coords = $geometry['coordinates'] ?? null;
        if (! is_array($coords)) {
            return;
        }

        $walk = function ($node) use (&$walk, &$minLng, &$maxLng, &$minLat, &$maxLat) {
            if (! is_array($node)) {
                return;
            }
            if (isset($node[0], $node[1]) && is_numeric($node[0]) && is_numeric($node[1])) {
                $lng = (float) $node[0];
                $lat = (float) $node[1];
                $minLng = min($minLng, $lng);
                $maxLng = max($maxLng, $lng);
                $minLat = min($minLat, $lat);
                $maxLat = max($maxLat, $lat);

                return;
            }
            foreach ($node as $child) {
                $walk($child);
            }
        };

        $walk($coords);
    }

    /**
     * @param  array|null  $lngLat  [longitude, latitude] (GeoJSON)
     */
    private function pointInBoundingBox(?array $lngLat, array $bbox): bool
    {
        if (! is_array($lngLat) || count($lngLat) < 2) {
            return false;
        }
        $lng = (float) $lngLat[0];
        $lat = (float) $lngLat[1];

        return $lng >= $bbox['min_lng'] && $lng <= $bbox['max_lng']
            && $lat >= $bbox['min_lat'] && $lat <= $bbox['max_lat'];
    }

    /**
     * @param  array<int, array>|null  $line  Daftar [lng, lat]
     */
    private function lineOverlapsBoundingBox(?array $line, array $bbox): bool
    {
        if (! is_array($line) || count($line) < 2) {
            return false;
        }

        foreach ($line as $pt) {
            if ($this->pointInBoundingBox($pt, $bbox)) {
                return true;
            }
        }

        $lons = array_map(fn ($p) => (float) ($p[0] ?? 0), $line);
        $lats = array_map(fn ($p) => (float) ($p[1] ?? 0), $line);
        $rb = [
            'min_lng' => min($lons),
            'max_lng' => max($lons),
            'min_lat' => min($lats),
            'max_lat' => max($lats),
        ];

        return ! ($rb['max_lng'] < $bbox['min_lng'] || $rb['min_lng'] > $bbox['max_lng']
            || $rb['max_lat'] < $bbox['min_lat'] || $rb['min_lat'] > $bbox['max_lat']);
    }
}
