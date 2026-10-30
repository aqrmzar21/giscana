<?php

namespace App\Services;

use App\Models\EvacuationFacility;
use App\Models\EvacuationRoute;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EvacuationRoutingService
{
    /**
     * Radius bumi dalam meter untuk perhitungan geodesik.
     */
    protected const EARTH_RADIUS_METERS = 6371000;

    /**
     * Kecepatan rata-rata evakuasi pejalan kaki (±4.8 km/jam = 80 meter/menit = 1.33 m/s).
     */
    protected const WALKING_SPEED_METERS_PER_MIN = 80.0;

    /**
     * Hitung rute evakuasi cerdas (Hybrid Evacuation Routing).
     *
     * MODE 1: Jika jarak user ke jalur evakuasi resmi <= threshold (default 100 meter),
     *         gunakan langsung jalur evakuasi resmi BPBD.
     * MODE 2: Jika jarak user ke jalur evakuasi resmi > threshold (100 meter),
     *         gunakan jaringan jalan umum (OSRM/ORS) menuju jalur evakuasi resmi terdekat,
     *         lalu lanjutkan menyusuri jalur evakuasi resmi sampai ke titik kumpul.
     *
     * @param float $userLat
     * @param float $userLng
     * @param float|null $customThreshold
     * @return array
     */
    public function calculateHybridRoute(float $userLat, float $userLng, ?float $customThreshold = null): array
    {
        $threshold = $customThreshold ?? (float) config('services.routing.mode_threshold_meters', 100.0);

        // 1. Ambil seluruh fasilitas titik kumpul aktif & dapat diakses
        $facilities = EvacuationFacility::active()->accessible()->get();
        if ($facilities->isEmpty()) {
            $facilities = EvacuationFacility::all();
        }

        if ($facilities->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Tidak ada data fasilitas titik kumpul yang tersedia.',
            ];
        }

        // Ekstraksi koordinat seluruh fasilitas titik kumpul aktif
        $destinations = [];
        foreach ($facilities as $facility) {
            $fCoords = $this->extractCoordinates($facility->point_coordinates);
            if ($fCoords) {
                $destinations[] = [
                    'facility' => $facility,
                    'lat' => $fCoords['lat'],
                    'lng' => $fCoords['lng'],
                ];
            }
        }

        if (empty($destinations)) {
            return [
                'success' => false,
                'message' => 'Format koordinat fasilitas titik kumpul tidak valid.',
            ];
        }

        // 2. Hitung jarak jalan OSRM ke seluruh titik kumpul (OSRM Table API)
        $tableData = $this->fetchOsrmDistanceTable($userLat, $userLng, $destinations);

        // Lakukan perankingan berdasarkan jarak tempuh jalan raya nyata OSRM (terpendek ke terpanjang)
        $ranked = [];
        foreach ($destinations as $idx => $d) {
            if ($tableData && isset($tableData['distances'][$idx + 1]) && $tableData['distances'][$idx + 1] !== null) {
                $roadDist = (float) $tableData['distances'][$idx + 1];
                $roadDur = (float) ($tableData['durations'][$idx + 1] ?? ($roadDist / self::WALKING_SPEED_METERS_PER_MIN * 60));
                $source = 'OSRM';
            } else {
                $straight = $this->haversineDistance($userLat, $userLng, $d['lat'], $d['lng']);
                $roadDist = $straight * 1.35;
                $roadDur = ($roadDist / self::WALKING_SPEED_METERS_PER_MIN) * 60;
                $source = 'Direct/Haversine';
            }

            $ranked[] = [
                'facility' => $d['facility'],
                'lat' => $d['lat'],
                'lng' => $d['lng'],
                'distance' => $roadDist,
                'duration' => $roadDur,
                'source' => $source,
            ];
        }

        // Urutkan berdasarkan jarak jalan OSRM terpendek
        usort($ranked, fn($a, $b) => $a['distance'] <=> $b['distance']);

        $bestCandidate = $ranked[0];

        // 3. Validasi Titik Kumpul (En-route Shelter Detection):
        // Analisis seluruh rute OSRM. Jika rute menuju kandidat melewati titik kumpul lain
        // yang lebih dekat dan berada di sepanjang rute jalan raya (jarak <= 150m):
        // Batalkan kandidat awal, dan jadikan titik kumpul pertama yang dilewati sebagai tujuan baru.
        $candidateRoute = $this->fetchPublicRoadRoute($userLng, $userLat, $bestCandidate['lng'], $bestCandidate['lat']);
        $candidatePolyline = $candidateRoute['coordinates'];

        $enRouteFacilities = [];
        foreach ($destinations as $d) {
            if ($d['facility']->id == $bestCandidate['facility']->id) {
                continue;
            }

            $proj = $this->pointToPolylineDistance($d['lat'], $d['lng'], $candidatePolyline);
            if ($proj['distance'] <= 150.0) {
                $distAlong = $this->calculateDistanceAlongPolyline($candidatePolyline, $proj['segment_index'], $proj['t']);
                if ($distAlong < ($candidateRoute['distance_meters'] - 150.0)) {
                    $enRouteFacilities[] = [
                        'facility' => $d['facility'],
                        'lat' => $d['lat'],
                        'lng' => $d['lng'],
                        'dist_to_line' => $proj['distance'],
                        'dist_along' => $distAlong,
                    ];
                }
            }
        }

        if (!empty($enRouteFacilities)) {
            // Pilih titik kumpul yang paling pertama dilewati di sepanjang rute perjalanan
            usort($enRouteFacilities, fn($a, $b) => $a['dist_along'] <=> $b['dist_along']);
            $firstEnRoute = $enRouteFacilities[0];
            $targetFacility = $firstEnRoute['facility'];
            $targetCoords = ['lat' => $firstEnRoute['lat'], 'lng' => $firstEnRoute['lng']];
            $publicRoadRoute = $this->fetchPublicRoadRoute($userLng, $userLat, $targetCoords['lng'], $targetCoords['lat']);
        } else {
            $targetFacility = $bestCandidate['facility'];
            $targetCoords = ['lat' => $bestCandidate['lat'], 'lng' => $bestCandidate['lng']];
            $publicRoadRoute = $candidateRoute;
        }

        // 4. Bangun Opsi 1: Jalur Umum (OSRM)
        $publicTotalDist = (float) ($publicRoadRoute['distance_meters'] ?? 0);
        $publicDurMin = max(1, (int) ceil($publicTotalDist / self::WALKING_SPEED_METERS_PER_MIN));
        $publicDurSec = $publicDurMin * 60;

        $publicInstructions = [];
        $pStep = 1;
        if (!empty($publicRoadRoute['steps'])) {
            foreach ($publicRoadRoute['steps'] as $s) {
                if (($s['type'] ?? '') === 'arrive') continue;
                $publicInstructions[] = [
                    'step' => $pStep++,
                    'text' => $s['text'],
                    'distance_meters' => $s['distance_meters'],
                    'distance_formatted' => $this->formatDistance($s['distance_meters']),
                    'type' => $s['type'] ?? 'straight',
                    'icon' => $this->resolveStepIcon($s['type'] ?? 'straight'),
                ];
            }
        } else {
            $publicInstructions[] = [
                'step' => $pStep++,
                'text' => "Ikuti jaringan jalan umum menuju {$targetFacility->name} sejauh " . $this->formatDistance($publicTotalDist),
                'distance_meters' => $publicTotalDist,
                'distance_formatted' => $this->formatDistance($publicTotalDist),
                'type' => 'straight',
                'icon' => 'arrow-up',
            ];
        }
        $publicInstructions[] = [
            'step' => $pStep++,
            'text' => "Tiba di Titik Kumpul: {$targetFacility->name}",
            'distance_meters' => 0,
            'distance_formatted' => '0 m',
            'type' => 'arrive',
            'icon' => 'flag',
        ];

        $publicOption = [
            'key' => 'public',
            'name' => 'Jalur Umum (OSRM)',
            'label' => 'Jalur Umum (OSRM)',
            'source' => $publicRoadRoute['source'] ?? 'OSRM',
            'color' => '#2563eb', // Royal Blue
            'casing_color' => '#174ea6',
            'coordinates' => $publicRoadRoute['coordinates'],
            'distance_meters' => round($publicTotalDist, 1),
            'distance_formatted' => $this->formatDistance($publicTotalDist),
            'duration_seconds' => $publicDurSec,
            'duration_formatted' => $this->formatDuration($publicDurMin),
            'instructions' => $publicInstructions,
        ];

        // 5. Bangun Opsi 2: Jalur Resmi BPBD
        $allRoutes = EvacuationRoute::with('evacuationFacility')->active()->accessible()->get();
        if ($allRoutes->isEmpty()) {
            $allRoutes = EvacuationRoute::all();
        }

        // Cari rute evakuasi resmi yang terhubung ke targetFacility
        $facilityRoutes = $allRoutes->filter(function ($r) use ($targetFacility, $targetCoords) {
            if ($r->evacuation_facility_id == $targetFacility->id) {
                return true;
            }
            if (isset($r->facility_name) && strcasecmp(trim($r->facility_name), trim($targetFacility->name)) === 0) {
                return true;
            }
            $pts = $this->extractRouteCoordinates($r);
            if (empty($pts)) return false;
            $first = reset($pts);
            $last = end($pts);
            $d1 = $this->haversineDistance($targetCoords['lat'], $targetCoords['lng'], $first['lat'], $first['lng']);
            $d2 = $this->haversineDistance($targetCoords['lat'], $targetCoords['lng'], $last['lat'], $last['lng']);
            return min($d1, $d2) <= 350.0;
        });

        if ($facilityRoutes->isEmpty()) {
            $facilityRoutes = $allRoutes;
        }

        $bestRoute = null;
        $bestProj = null;
        $bestOrientedPoints = null;
        $minDistanceToRoute = INF;

        foreach ($facilityRoutes as $route) {
            $rawPoints = $this->extractRouteCoordinates($route);
            if (count($rawPoints) < 2) continue;

            $oriented = $this->orientPolylineTowardsFacility($rawPoints, $targetCoords['lat'], $targetCoords['lng']);
            $proj = $this->pointToPolylineDistance($userLat, $userLng, $oriented);

            if ($proj['distance'] < $minDistanceToRoute) {
                $minDistanceToRoute = $proj['distance'];
                $bestRoute = $route;
                $bestProj = $proj;
                $bestOrientedPoints = $oriented;
            }
        }

        // Fallback jika tidak ada rute BPBD langsung
        if (!$bestRoute || !$bestProj) {
            $bestRoute = $allRoutes->first();
            if ($bestRoute) {
                $rawPoints = $this->extractRouteCoordinates($bestRoute);
                $bestOrientedPoints = $this->orientPolylineTowardsFacility($rawPoints, $targetCoords['lat'], $targetCoords['lng']);
                $bestProj = $this->pointToPolylineDistance($userLat, $userLng, $bestOrientedPoints);
                $minDistanceToRoute = $bestProj['distance'];
            }
        }

        if ($bestRoute && $bestProj) {
            $entryPoint = $bestProj['closest_point'];
            $segmentIndex = $bestProj['segment_index'];

            // Potong rute BPBD resmi dari titik masuk (entry point) menuju fasilitas titik kumpul
            $officialCoords = $this->sliceOfficialRouteFromEntry($bestOrientedPoints, $entryPoint, $segmentIndex, $targetCoords);
            $officialLatLngs = array_map(fn($p) => [$p['lat'], $p['lng']], $officialCoords);
            $officialDistance = $this->calculatePolylineLength($officialLatLngs);

            // Garis penghubung tipis putus-putus (connector / feeder) dari user ke segmen BPBD terdekat
            $connectorCoords = ($minDistanceToRoute > 5)
                ? [[$userLat, $userLng], [$entryPoint['lat'], $entryPoint['lng']]]
                : [];

            $totalBpbdDist = $minDistanceToRoute + $officialDistance;
            $bpbdDurMin = max(1, (int) ceil($totalBpbdDist / self::WALKING_SPEED_METERS_PER_MIN));
            $bpbdDurSec = $bpbdDurMin * 60;

            // Generate instruksi navigasi jalur BPBD
            $bpbdInstructions = [];
            $bStep = 1;
            if ($minDistanceToRoute > 5) {
                $bpbdInstructions[] = [
                    'step' => $bStep++,
                    'text' => "Dari lokasi Anda, bergerak menuju titik masuk jalur resmi BPBD (" . $this->formatDistance($minDistanceToRoute) . ")",
                    'distance_meters' => round($minDistanceToRoute, 1),
                    'distance_formatted' => $this->formatDistance($minDistanceToRoute),
                    'type' => 'straight',
                    'icon' => 'arrow-up',
                ];
            }

            $routeName = $bestRoute->name ?? 'Jalur Evakuasi Resmi BPBD';
            $bpbdInstructions[] = [
                'step' => $bStep++,
                'text' => "Masuk ke Jalur Evakuasi Resmi BPBD ({$routeName})",
                'distance_meters' => 0,
                'distance_formatted' => '0 m',
                'type' => 'enter_official',
                'icon' => 'shield-check',
            ];

            $officialTurnInstructions = $this->generateTurnInstructions(
                $officialLatLngs,
                $routeName,
                $targetFacility->name,
                'BPBD'
            );

            foreach (array_slice($officialTurnInstructions, 1) as $step) {
                $step['step'] = $bStep++;
                $bpbdInstructions[] = $step;
            }

            $bpbdOption = [
                'key' => 'bpbd',
                'name' => 'Jalur BPBD (Resmi)',
                'label' => 'Jalur BPBD (Resmi)',
                'route_name' => $routeName,
                'color' => 'rgb(153, 0, 255)', // Purple
                'casing_color' => '#581c87',
                'connector_color' => '#64748b', // Slate / Abu-Biru
                'coordinates' => $officialLatLngs,
                'connector_coordinates' => $connectorCoords,
                'distance_meters' => round($totalBpbdDist, 1),
                'distance_formatted' => $this->formatDistance($totalBpbdDist),
                'duration_seconds' => $bpbdDurSec,
                'duration_formatted' => $this->formatDuration($bpbdDurMin),
                'instructions' => $bpbdInstructions,
                'entry_point' => ['lat' => $entryPoint['lat'], 'lng' => $entryPoint['lng']],
            ];
        } else {
            $bpbdOption = $publicOption;
            $bpbdOption['key'] = 'bpbd';
            $bpbdOption['name'] = 'Jalur BPBD (Resmi)';
            $bpbdOption['label'] = 'Jalur BPBD (Resmi)';
            $bpbdOption['connector_coordinates'] = [];
            $totalBpbdDist = $publicTotalDist;
            $minDistanceToRoute = 0;
            $entryPoint = ['lat' => $targetCoords['lat'], 'lng' => $targetCoords['lng']];
        }

        // 6. Sistem Otomatis Memilih yang Terbaik Sebagai Default:
        // Prioritaskan jalur resmi BPBD jika dekat (<= 250m) atau jaraknya kompetitif
        if ($minDistanceToRoute <= 250.0 || $totalBpbdDist <= ($publicTotalDist * 1.15)) {
            $selectedRouteKey = 'bpbd';
        } else {
            $selectedRouteKey = 'public';
        }

        $activeOption = ($selectedRouteKey === 'bpbd') ? $bpbdOption : $publicOption;

        return [
            'success' => true,
            'mode' => ($selectedRouteKey === 'bpbd') ? 'jalur_resmi' : 'jalan_umum',
            'mode_title' => ($selectedRouteKey === 'bpbd') ? 'Jalur Evakuasi Resmi BPBD' : 'Jalur Umum (OSRM)',
            'mode_label' => ($selectedRouteKey === 'bpbd') ? 'Jalur BPBD (Resmi)' : 'Jalur Umum (OSRM)',
            'mode_badge_color' => ($selectedRouteKey === 'bpbd') ? 'purple' : 'blue',
            'threshold_meters' => $threshold,
            'distance_to_official_route_meters' => round($minDistanceToRoute, 1),
            'facility' => $targetFacility,
            'routes' => $facilityRoutes,
            'route_options' => [
                'public' => $publicOption,
                'bpbd' => $bpbdOption,
            ],
            'selected_route_key' => $selectedRouteKey,
            'summary' => [
                'distance_meters' => $activeOption['distance_meters'],
                'distance_formatted' => $activeOption['distance_formatted'],
                'duration_seconds' => $activeOption['duration_seconds'],
                'duration_formatted' => $activeOption['duration_formatted'],
                'user_location' => ['lat' => $userLat, 'lng' => $userLng],
                'facility_location' => ['lat' => $targetCoords['lat'], 'lng' => $targetCoords['lng']],
                'entry_point' => $bpbdOption['entry_point'] ?? null,
            ],
            'segments' => [
                'public_road' => [
                    'source' => $publicOption['source'] ?? 'OSRM',
                    'coordinates' => $publicOption['coordinates'],
                    'distance_meters' => $publicOption['distance_meters'],
                    'distance_formatted' => $publicOption['distance_formatted'],
                ],
                'official_evacuation' => [
                    'route_id' => $bestRoute->id ?? null,
                    'route_name' => $bestRoute->name ?? 'Jalur Evakuasi BPBD',
                    'coordinates' => $bpbdOption['coordinates'],
                    'connector_coordinates' => $bpbdOption['connector_coordinates'] ?? [],
                    'distance_meters' => $bpbdOption['distance_meters'],
                    'distance_formatted' => $bpbdOption['distance_formatted'],
                ],
            ],
            'full_route_latlngs' => $activeOption['coordinates'],
            'instructions' => $activeOption['instructions'],
            'distance_meters' => $activeOption['distance_meters'],
        ];
    }

    /**
     * MODE 1: User berada dekat (<= 100m) dari jalur evakuasi resmi.
     * Menggunakan jalur evakuasi resmi BPBD.
     */
    protected function buildMode1OfficialRoute(
        float $userLat,
        float $userLng,
        EvacuationFacility $facility,
        array $facilityCoords,
        EvacuationRoute $route,
        array $entryPoint,
        int $segmentIndex,
        float $distanceToRoute,
        float $threshold
    ): array {
        $rawPoints = $this->extractRouteCoordinates($route);

        // Arahkan susunan koordinat rute resmi agar menuju ke arah titik kumpul
        $orientedPoints = $this->orientPolylineTowardsFacility($rawPoints, $facilityCoords['lat'], $facilityCoords['lng']);

        // Potong rute resmi mulai dari titik masuk user menuju fasilitas
        $officialCoords = $this->sliceOfficialRouteFromEntry($orientedPoints, $entryPoint, $segmentIndex, $facilityCoords);

        // Selipkan posisi user di awal jika sedikit offset dari garis resmi
        $fullPath = [];
        if ($this->haversineDistance($userLat, $userLng, $entryPoint['lat'], $entryPoint['lng']) > 5) {
            $fullPath[] = [$userLat, $userLng];
        }
        foreach ($officialCoords as $pt) {
            $fullPath[] = [$pt['lat'], $pt['lng']];
        }

        // Hitung total jarak rute
        $totalDistance = $this->calculatePolylineLength($fullPath);
        $durationMinutes = max(1, (int) ceil($totalDistance / self::WALKING_SPEED_METERS_PER_MIN));
        $durationSeconds = $durationMinutes * 60;

        // Generate instruksi arah berbahasa Indonesia yang presisi
        $instructions = $this->generateTurnInstructions(
            $fullPath,
            $route->name ?? 'Jalur Evakuasi Resmi BPBD',
            $facility->name,
            'MODE 1'
        );

        $officialRoutes = EvacuationRoute::where('evacuation_facility_id', $facility->id)->get();

        $publicOption = $this->buildDirectPublicRoadOption($userLat, $userLng, $facilityCoords, $facility);
        $bpbdOption = [
            'key' => 'bpbd',
            'name' => 'Jalur Evakuasi Resmi BPBD',
            'label' => 'Jalur Resmi BPBD',
            'route_name' => $route->name ?? 'Jalur Evakuasi Resmi BPBD',
            'color' => 'rgb(153, 0, 255)',
            'casing_color' => '#581c87',
            'coordinates' => $fullPath,
            'distance_meters' => round($totalDistance, 1),
            'distance_formatted' => $this->formatDistance($totalDistance),
            'duration_seconds' => $durationSeconds,
            'duration_formatted' => $this->formatDuration($durationMinutes),
            'instructions' => $instructions,
            'entry_point' => ['lat' => $entryPoint['lat'], 'lng' => $entryPoint['lng']],
        ];

        return [
            'success' => true,
            'mode' => 'jalur_resmi',
            'mode_title' => 'MODE 1: Jalur Evakuasi Resmi',
            'mode_label' => 'Jalur Evakuasi Resmi',
            'mode_badge_color' => 'emerald',
            'threshold_meters' => $threshold,
            'distance_to_official_route_meters' => round($distanceToRoute, 1),
            'facility' => $facility,
            'routes' => $officialRoutes,
            'route_options' => [
                'bpbd' => $bpbdOption,
                'public' => $publicOption,
            ],
            'selected_route_key' => 'bpbd',
            'summary' => [
                'distance_meters' => round($totalDistance, 1),
                'distance_formatted' => $this->formatDistance($totalDistance),
                'duration_seconds' => $durationSeconds,
                'duration_formatted' => $this->formatDuration($durationMinutes),
                'user_location' => ['lat' => $userLat, 'lng' => $userLng],
                'facility_location' => ['lat' => $facilityCoords['lat'], 'lng' => $facilityCoords['lng']],
                'entry_point' => ['lat' => $entryPoint['lat'], 'lng' => $entryPoint['lng']],
            ],
            'segments' => [
                'public_road' => null,
                'official_evacuation' => [
                    'route_id' => $route->id,
                    'route_name' => $route->name,
                    'coordinates' => array_map(fn($p) => [$p['lat'], $p['lng']], $officialCoords),
                    'distance_meters' => round($totalDistance, 1),
                    'distance_formatted' => $this->formatDistance($totalDistance),
                ],
            ],
            'full_route_latlngs' => $fullPath,
            'instructions' => $instructions,
        ];
    }

    /**
     * MODE 2: User berada jauh (> 100m) dari jalur evakuasi resmi.
     * Menggunakan jaringan jalan umum (OSRM/ORS) menuju jalur evakuasi resmi,
     * lalu melanjutkan melalui jalur evakuasi resmi menuju titik kumpul.
     */
    protected function buildMode2HybridRoute(
        float $userLat,
        float $userLng,
        EvacuationFacility $facility,
        array $facilityCoords,
        ?EvacuationRoute $route,
        ?array $entryPoint,
        int $segmentIndex,
        float $distanceToRoute,
        float $threshold
    ): array {
        // Tentukan target perhentian jalan umum
        // Jika ada jalur evakuasi resmi, target jalan umum adalah entryPoint pada jalur resmi
        // Jika tidak ada rute, target jalan umum langsung ke fasilitas
        $hasOfficialRoute = ($route !== null && $entryPoint !== null);
        $targetLat = $hasOfficialRoute ? $entryPoint['lat'] : $facilityCoords['lat'];
        $targetLng = $hasOfficialRoute ? $entryPoint['lng'] : $facilityCoords['lng'];

        // Ambil rute jalan umum via OSRM / OpenRouteService
        $publicRoadRoute = $this->fetchPublicRoadRoute($userLng, $userLat, $targetLng, $targetLat);

        $officialCoords = [];
        $officialDistance = 0.0;
        if ($hasOfficialRoute) {
            $rawPoints = $this->extractRouteCoordinates($route);
            $orientedPoints = $this->orientPolylineTowardsFacility($rawPoints, $facilityCoords['lat'], $facilityCoords['lng']);
            $officialCoords = $this->sliceOfficialRouteFromEntry($orientedPoints, $entryPoint, $segmentIndex, $facilityCoords);
            $officialPathLatLngs = array_map(fn($p) => [$p['lat'], $p['lng']], $officialCoords);
            $officialDistance = $this->calculatePolylineLength($officialPathLatLngs);
        }

        // Sambungkan rute Jalan Umum + Jalur Evakuasi Resmi menjadi satu kesatuan berkesinambungan
        $fullPath = [];
        foreach ($publicRoadRoute['coordinates'] as $pt) {
            $fullPath[] = [$pt[0], $pt[1]]; // [lat, lng]
        }
        foreach ($officialCoords as $pt) {
            // Hindari duplikasi titik perbatasan
            $last = end($fullPath);
            if (!$last || $this->haversineDistance($last[0], $last[1], $pt['lat'], $pt['lng']) > 3) {
                $fullPath[] = [$pt['lat'], $pt['lng']];
            }
        }

        // Pastikan titik kumpul merupakan titik akhir
        $lastPt = end($fullPath);
        if (!$lastPt || $this->haversineDistance($lastPt[0], $lastPt[1], $facilityCoords['lat'], $facilityCoords['lng']) > 5) {
            $fullPath[] = [$facilityCoords['lat'], $facilityCoords['lng']];
        }

        $totalDistance = $publicRoadRoute['distance_meters'] + $officialDistance;
        $durationMinutes = max(1, (int) ceil($totalDistance / self::WALKING_SPEED_METERS_PER_MIN));
        $durationSeconds = $durationMinutes * 60;

        // Gabungkan Instruksi Bernavigasi
        $instructions = [];
        $stepNumber = 1;

        // 1. Instruksi Jalan Umum
        if (!empty($publicRoadRoute['steps'])) {
            foreach ($publicRoadRoute['steps'] as $s) {
                $instructions[] = [
                    'step' => $stepNumber++,
                    'text' => $s['text'],
                    'distance_meters' => $s['distance_meters'],
                    'distance_formatted' => $this->formatDistance($s['distance_meters']),
                    'type' => $s['type'] ?? 'straight',
                    'icon' => $this->resolveStepIcon($s['type'] ?? 'straight'),
                ];
            }
        } else {
            $roadDist = $publicRoadRoute['distance_meters'];
            $instructions[] = [
                'step' => $stepNumber++,
                'text' => "Ikuti jaringan jalan umum sejauh " . $this->formatDistance($roadDist),
                'distance_meters' => $roadDist,
                'distance_formatted' => $this->formatDistance($roadDist),
                'type' => 'straight',
                'icon' => 'arrow-up',
            ];
        }

        // 2. Transisi Masuk ke Jalur Evakuasi Resmi BPBD
        if ($hasOfficialRoute && !empty($officialCoords)) {
            $routeName = $route->name ?? 'Jalur Evakuasi Utama BPBD';
            $instructions[] = [
                'step' => $stepNumber++,
                'text' => "Masuk ke Jalur Evakuasi Resmi ({$routeName})",
                'distance_meters' => 0,
                'distance_formatted' => '0 m',
                'type' => 'enter_official',
                'icon' => 'shield-check',
            ];

            // Instruksi di dalam jalur evakuasi resmi
            $officialPathForInstructions = array_map(fn($p) => [$p['lat'], $p['lng']], $officialCoords);
            $officialInstructions = $this->generateTurnInstructions(
                $officialPathForInstructions,
                $routeName,
                $facility->name,
                'MODE 2'
            );

            // Lewati langkah awal karena sudah diwakili oleh transisi masuk
            foreach (array_slice($officialInstructions, 1) as $step) {
                $step['step'] = $stepNumber++;
                $instructions[] = $step;
            }
        } else {
            $instructions[] = [
                'step' => $stepNumber++,
                'text' => "Tiba di Titik Kumpul: {$facility->name}",
                'distance_meters' => 0,
                'distance_formatted' => '0 m',
                'type' => 'arrive',
                'icon' => 'flag',
            ];
        }

        $officialRoutes = EvacuationRoute::where('evacuation_facility_id', $facility->id)->get();

        $publicOption = $this->buildDirectPublicRoadOption($userLat, $userLng, $facilityCoords, $facility);
        $bpbdOption = [
            'key' => 'bpbd',
            'name' => 'Jalur Evakuasi Resmi BPBD',
            'label' => 'Jalur Resmi BPBD',
            'route_name' => $route ? ($route->name ?? 'Jalur Evakuasi Resmi BPBD') : 'Jalur Evakuasi Resmi BPBD',
            'color' => 'rgb(153, 0, 255)',
            'casing_color' => '#581c87',
            'coordinates' => $fullPath,
            'distance_meters' => round($totalDistance, 1),
            'distance_formatted' => $this->formatDistance($totalDistance),
            'duration_seconds' => $durationSeconds,
            'duration_formatted' => $this->formatDuration($durationMinutes),
            'instructions' => $instructions,
            'entry_point' => $hasOfficialRoute ? ['lat' => $entryPoint['lat'], 'lng' => $entryPoint['lng']] : null,
        ];

        return [
            'success' => true,
            'mode' => 'jalan_umum',
            'mode_title' => 'MODE 2: Routing Jalan Umum',
            'mode_label' => 'Routing Jalan Umum',
            'mode_badge_color' => 'indigo',
            'threshold_meters' => $threshold,
            'distance_to_official_route_meters' => round($distanceToRoute, 1),
            'facility' => $facility,
            'routes' => $officialRoutes,
            'route_options' => [
                'bpbd' => $bpbdOption,
                'public' => $publicOption,
            ],
            'selected_route_key' => 'bpbd',
            'summary' => [
                'distance_meters' => round($totalDistance, 1),
                'distance_formatted' => $this->formatDistance($totalDistance),
                'duration_seconds' => $durationSeconds,
                'duration_formatted' => $this->formatDuration($durationMinutes),
                'user_location' => ['lat' => $userLat, 'lng' => $userLng],
                'facility_location' => ['lat' => $facilityCoords['lat'], 'lng' => $facilityCoords['lng']],
                'entry_point' => $hasOfficialRoute ? ['lat' => $entryPoint['lat'], 'lng' => $entryPoint['lng']] : null,
            ],
            'segments' => [
                'public_road' => [
                    'source' => $publicRoadRoute['source'],
                    'coordinates' => $publicRoadRoute['coordinates'],
                    'distance_meters' => round($publicRoadRoute['distance_meters'], 1),
                    'distance_formatted' => $this->formatDistance($publicRoadRoute['distance_meters']),
                ],
                'official_evacuation' => $hasOfficialRoute ? [
                    'route_id' => $route->id,
                    'route_name' => $route->name,
                    'coordinates' => array_map(fn($p) => [$p['lat'], $p['lng']], $officialCoords),
                    'distance_meters' => round($officialDistance, 1),
                    'distance_formatted' => $this->formatDistance($officialDistance),
                ] : null,
            ],
            'full_route_latlngs' => $fullPath,
            'instructions' => $instructions,
        ];
    }

    /**
     * Bangun opsi rute Jalur Umum murni (Google Maps style) dari user langsung ke titik kumpul.
     */
    protected function buildDirectPublicRoadOption(
        float $userLat,
        float $userLng,
        array $facilityCoords,
        EvacuationFacility $facility
    ): array {
        $roadRoute = $this->fetchPublicRoadRoute($userLng, $userLat, $facilityCoords['lng'], $facilityCoords['lat']);
        $totalDistance = (float) ($roadRoute['distance_meters'] ?? 0);
        $durationMinutes = max(1, (int) ceil($totalDistance / self::WALKING_SPEED_METERS_PER_MIN));
        $durationSeconds = $durationMinutes * 60;

        $instructions = [];
        $stepNumber = 1;

        if (!empty($roadRoute['steps'])) {
            foreach ($roadRoute['steps'] as $s) {
                if (($s['type'] ?? '') === 'arrive') {
                    continue;
                }
                $instructions[] = [
                    'step' => $stepNumber++,
                    'text' => $s['text'],
                    'distance_meters' => $s['distance_meters'],
                    'distance_formatted' => $this->formatDistance($s['distance_meters']),
                    'type' => $s['type'] ?? 'straight',
                    'icon' => $this->resolveStepIcon($s['type'] ?? 'straight'),
                ];
            }
        } else {
            $instructions[] = [
                'step' => $stepNumber++,
                'text' => "Ikuti jaringan jalan umum menuju {$facility->name} sejauh " . $this->formatDistance($totalDistance),
                'distance_meters' => $totalDistance,
                'distance_formatted' => $this->formatDistance($totalDistance),
                'type' => 'straight',
                'icon' => 'arrow-up',
            ];
        }

        $instructions[] = [
            'step' => $stepNumber++,
            'text' => "Tiba di Titik Kumpul: {$facility->name}",
            'distance_meters' => 0,
            'distance_formatted' => '0 m',
            'type' => 'arrive',
            'icon' => 'flag',
        ];

        return [
            'key' => 'public',
            'name' => 'Jalur Umum (Google Maps)',
            'label' => 'Jalur Umum',
            'source' => $roadRoute['source'] ?? 'OSRM',
            'color' => '#1a73e8', // Google Maps Blue
            'casing_color' => '#174ea6',
            'coordinates' => $roadRoute['coordinates'],
            'distance_meters' => round($totalDistance, 1),
            'distance_formatted' => $this->formatDistance($totalDistance),
            'duration_seconds' => $durationSeconds,
            'duration_formatted' => $this->formatDuration($durationMinutes),
            'instructions' => $instructions,
        ];
    }

    /**
     * Panggil OpenRouteService atau OSRM untuk routing jalan umum.
     * Dilengkapi fallback otomatis jika jaringan offline atau API eksternal gagal.
     */
    public function fetchPublicRoadRoute(float $startLng, float $startLat, float $destLng, float $destLat): array
    {
        $orsKey = config('services.routing.ors_api_key');
        $osrmBaseUrl = rtrim(config('services.routing.osrm_url', 'https://router.project-osrm.org'), '/');

        // 1. Coba OpenRouteService jika API Key tersedia
        if (!empty($orsKey)) {
            try {
                $response = Http::timeout(4)->withHeaders([
                    'Authorization' => $orsKey,
                    'Accept' => 'application/json, application/geo+json',
                ])->get('https://api.openrouteservice.org/v2/directions/foot-walking', [
                    'start' => "{$startLng},{$startLat}",
                    'end' => "{$destLng},{$destLat}",
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (!empty($json['features'][0]['geometry']['coordinates'])) {
                        $coords = $json['features'][0]['geometry']['coordinates'];
                        $summary = $json['features'][0]['properties']['summary'] ?? [];
                        $distance = (float) ($summary['distance'] ?? 0);
                        $duration = (float) ($summary['duration'] ?? 0);

                        // Konversi GeoJSON [lng, lat] ke Leaflet [lat, lng]
                        $leafletCoords = array_map(fn($c) => [$c[1], $c[0]], $coords);

                        return [
                            'source' => 'OpenRouteService',
                            'coordinates' => $leafletCoords,
                            'distance_meters' => $distance,
                            'duration_seconds' => $duration,
                            'steps' => $this->parseOrsSteps($json['features'][0]['properties']['segments'][0]['steps'] ?? []),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('ORS Routing gagal, beralih ke OSRM: ' . $e->getMessage());
            }
        }

        // 2. Coba OSRM (Open Source Routing Machine)
        try {
            // Coba rute foot terlebih dahulu, fallback driving
            $url = "{$osrmBaseUrl}/route/v1/driving/{$startLng},{$startLat};{$destLng},{$destLat}?overview=full&geometries=geojson&steps=true";
            $response = Http::timeout(4)->get($url);

            if ($response->successful()) {
                $json = $response->json();
                if (($json['code'] ?? '') === 'Ok' && !empty($json['routes'][0])) {
                    $routeData = $json['routes'][0];
                    $coords = $routeData['geometry']['coordinates'] ?? [];
                    $distance = (float) ($routeData['distance'] ?? 0);
                    $duration = (float) ($routeData['duration'] ?? 0);

                    $leafletCoords = array_map(fn($c) => [$c[1], $c[0]], $coords);
                    $steps = $this->parseOsrmSteps($routeData['legs'][0]['steps'] ?? []);

                    return [
                        'source' => 'OSRM',
                        'coordinates' => $leafletCoords,
                        'distance_meters' => $distance,
                        'duration_seconds' => $duration,
                        'steps' => $steps,
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('OSRM Routing gagal: ' . $e->getMessage());
        }

        // 3. Fallback Graceful (Jika offline/timeout)
        return $this->generateFallbackRoadRoute($startLat, $startLng, $destLat, $destLng);
    }

    /**
     * Parser langkah instruksi dari respons OSRM.
     */
    protected function parseOsrmSteps(array $rawSteps): array
    {
        $steps = [];
        foreach ($rawSteps as $step) {
            $name = trim($step['name'] ?? '');
            $dist = (float) ($step['distance'] ?? 0);
            $maneuver = $step['maneuver'] ?? [];
            $type = $maneuver['type'] ?? 'turn';
            $modifier = $maneuver['modifier'] ?? '';

            if ($dist < 1 && empty($name) && $type !== 'arrive') {
                continue;
            }

            $actionText = $this->translateOsrmManeuver($type, $modifier, $name, $dist);
            $actionType = $this->mapManeuverType($type, $modifier);

            $steps[] = [
                'text' => $actionText,
                'distance_meters' => round($dist, 1),
                'type' => $actionType,
            ];
        }

        // Sederhanakan jika terlalu banyak step kecil
        return $this->condenseInstructions($steps);
    }

    /**
     * Terjemahkan instruksi manuver OSRM ke Bahasa Indonesia yang lugas dan informatif.
     */
    protected function translateOsrmManeuver(string $type, string $modifier, string $roadName, float $distance): string
    {
        $distText = $distance > 0 ? " sejauh " . $this->formatDistance($distance) : "";
        $targetRoad = $roadName !== '' ? " menuju {$roadName}" : "";

        if ($type === 'arrive') {
            return "Tiba di persimpangan masuk jalur evakuasi";
        }

        if ($type === 'depart') {
            return "Mulai perjalanan dari lokasi Anda" . ($roadName ? " melalui {$roadName}" : "") . $distText;
        }

        return match ($modifier) {
            'left' => "Belok kiri{$targetRoad}{$distText}",
            'right' => "Belok kanan{$targetRoad}{$distText}",
            'slight left' => "Ambil sedikit ke kiri{$targetRoad}{$distText}",
            'slight right' => "Ambil sedikit ke kanan{$targetRoad}{$distText}",
            'sharp left' => "Belok tajam ke kiri{$targetRoad}{$distText}",
            'sharp right' => "Belok tajam ke kanan{$targetRoad}{$distText}",
            'straight' => "Lurus terus{$targetRoad}{$distText}",
            'uturn' => "Putar balik{$targetRoad}{$distText}",
            default => "Lurus{$targetRoad}{$distText}",
        };
    }

    /**
     * Parser langkah instruksi dari OpenRouteService.
     */
    protected function parseOrsSteps(array $rawSteps): array
    {
        $steps = [];
        foreach ($rawSteps as $step) {
            $instruction = $step['instruction'] ?? '';
            $dist = (float) ($step['distance'] ?? 0);
            $typeCode = $step['type'] ?? 0;

            $actionType = match ($typeCode) {
                0, 1 => 'left',
                2, 3 => 'right',
                6 => 'straight',
                10 => 'arrive',
                default => 'straight',
            };

            $steps[] = [
                'text' => $instruction ?: "Lurus sejauh " . $this->formatDistance($dist),
                'distance_meters' => round($dist, 1),
                'type' => $actionType,
            ];
        }

        return $steps;
    }

    /**
     * Fallback cerdas saat jaringan OSRM/ORS tidak merespons (Offline Support).
     */
    protected function generateFallbackRoadRoute(float $startLat, float $startLng, float $destLat, float $destLng): array
    {
        $distance = $this->haversineDistance($startLat, $startLng, $destLat, $destLng);
        $duration = ($distance / self::WALKING_SPEED_METERS_PER_MIN) * 60;

        // Buat interpolasi 4 titik untuk visual rute jalan
        $coordinates = [];
        $stepsCount = 4;
        for ($i = 0; $i <= $stepsCount; $i++) {
            $fraction = $i / $stepsCount;
            $lat = $startLat + ($destLat - $startLat) * $fraction;
            $lng = $startLng + ($destLng - $startLng) * $fraction;
            $coordinates[] = [$lat, $lng];
        }

        return [
            'source' => 'Jaringan Jalan Lokal (Direct Interpolation)',
            'coordinates' => $coordinates,
            'distance_meters' => round($distance, 1),
            'duration_seconds' => round($duration, 1),
            'steps' => [
                [
                    'text' => "Arahkan perjalanan menuju titik sambung jalur evakuasi (" . $this->formatDistance($distance) . ")",
                    'distance_meters' => round($distance, 1),
                    'type' => 'straight',
                ],
            ],
        ];
    }

    /**
     * Generate instruksi arah berdasarkan perubahan sudut bearing pada polylines.
     */
    public function generateTurnInstructions(array $latLngPoints, string $routeName, string $facilityName, string $modeLabel): array
    {
        $count = count($latLngPoints);
        if ($count < 2) {
            return [
                [
                    'step' => 1,
                    'text' => "Tiba di Titik Kumpul {$facilityName}",
                    'distance_meters' => 0,
                    'distance_formatted' => '0 m',
                    'type' => 'arrive',
                    'icon' => 'flag',
                ]
            ];
        }

        $instructions = [];
        $stepNumber = 1;

        // Step 1: Permulaan
        $initDist = $this->haversineDistance(
            $latLngPoints[0][0],
            $latLngPoints[0][1],
            $latLngPoints[1][0],
            $latLngPoints[1][1]
        );

        $instructions[] = [
            'step' => $stepNumber++,
            'text' => "Mulai dari lokasi Anda, masuki {$routeName}",
            'distance_meters' => round($initDist, 1),
            'distance_formatted' => $this->formatDistance($initDist),
            'type' => 'straight',
            'icon' => 'arrow-up',
        ];

        $accumulatedDist = $initDist;
        $prevBearing = $this->calculateBearing(
            $latLngPoints[0][0],
            $latLngPoints[0][1],
            $latLngPoints[1][0],
            $latLngPoints[1][1]
        );

        for ($i = 1; $i < $count - 1; $i++) {
            $pA = $latLngPoints[$i];
            $pB = $latLngPoints[$i + 1];

            $segmentDist = $this->haversineDistance($pA[0], $pA[1], $pB[0], $pB[1]);
            $currentBearing = $this->calculateBearing($pA[0], $pA[1], $pB[0], $pB[1]);

            $angleDiff = $this->calculateAngleDifference($prevBearing, $currentBearing);

            if (abs($angleDiff) >= 35 && $segmentDist > 15) {
                // Ada belokan signifikan
                if ($accumulatedDist > 20) {
                    $instructions[] = [
                        'step' => $stepNumber++,
                        'text' => "Lurus " . $this->formatDistance($accumulatedDist),
                        'distance_meters' => round($accumulatedDist, 1),
                        'distance_formatted' => $this->formatDistance($accumulatedDist),
                        'type' => 'straight',
                        'icon' => 'arrow-up',
                    ];
                }

                $turnText = $angleDiff > 0 ? "Belok kanan" : "Belok kiri";
                $turnType = $angleDiff > 0 ? "right" : "left";

                $instructions[] = [
                    'step' => $stepNumber++,
                    'text' => "{$turnText} menuju kelanjutan {$routeName}",
                    'distance_meters' => round($segmentDist, 1),
                    'distance_formatted' => $this->formatDistance($segmentDist),
                    'type' => $turnType,
                    'icon' => $this->resolveStepIcon($turnType),
                ];

                $accumulatedDist = $segmentDist;
                $prevBearing = $currentBearing;
            } else {
                $accumulatedDist += $segmentDist;
            }
        }

        if ($accumulatedDist > 25) {
            $instructions[] = [
                'step' => $stepNumber++,
                'text' => "Lurus " . $this->formatDistance($accumulatedDist),
                'distance_meters' => round($accumulatedDist, 1),
                'distance_formatted' => $this->formatDistance($accumulatedDist),
                'type' => 'straight',
                'icon' => 'arrow-up',
            ];
        }

        // Langkah Terakhir: Tiba di Titik Kumpul
        $instructions[] = [
            'step' => $stepNumber++,
            'text' => "Tiba di Titik Kumpul: {$facilityName}",
            'distance_meters' => 0,
            'distance_formatted' => '0 m',
            'type' => 'arrive',
            'icon' => 'flag',
        ];

        return $instructions;
    }

    /**
     * Hitung jarak Haversine (geodesic distance) dalam meter.
     */
    public function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_METERS * $c;
    }

    /**
     * Jarak dan proyeksi titik P ke segmen garis A-B.
     */
    public function pointToSegmentDistance(
        float $pLat,
        float $pLng,
        float $aLat,
        float $aLng,
        float $bLat,
        float $bLng
    ): array {
        $midLat = deg2rad(($aLat + $bLat) / 2.0);

        // Proyeksi planar lokal
        $dx = deg2rad($bLng - $aLng) * cos($midLat) * self::EARTH_RADIUS_METERS;
        $dy = deg2rad($bLat - $aLat) * self::EARTH_RADIUS_METERS;

        $lengthSq = ($dx * $dx) + ($dy * $dy);

        if ($lengthSq < 1e-6) {
            $dist = $this->haversineDistance($pLat, $pLng, $aLat, $aLng);
            return [
                'distance' => $dist,
                'lat' => $aLat,
                'lng' => $aLng,
                't' => 0.0,
            ];
        }

        $px = deg2rad($pLng - $aLng) * cos($midLat) * self::EARTH_RADIUS_METERS;
        $py = deg2rad($pLat - $aLat) * self::EARTH_RADIUS_METERS;

        $t = ($px * $dx + $py * $dy) / $lengthSq;
        $t = max(0.0, min(1.0, $t));

        $projLat = $aLat + $t * ($bLat - $aLat);
        $projLng = $aLng + $t * ($bLng - $aLng);

        $distance = $this->haversineDistance($pLat, $pLng, $projLat, $projLng);

        return [
            'distance' => $distance,
            'lat' => $projLat,
            'lng' => $projLng,
            't' => $t,
        ];
    }

    /**
     * Hitung jarak minimum dari titik P ke polylines (rute),
     * serta temukan titik proyeksi dan indeks segmen terdekat.
     * Mendukung format koordinat ['lat' => x, 'lng' => y] maupun [lat, lng].
     */
    public function pointToPolylineDistance(float $pLat, float $pLng, array $coordinates): array
    {
        $minDist = INF;
        $closestPoint = ['lat' => $pLat, 'lng' => $pLng];
        $bestSegIndex = 0;
        $bestT = 0.0;

        $count = count($coordinates);
        for ($i = 0; $i < $count - 1; $i++) {
            $a = $coordinates[$i];
            $b = $coordinates[$i + 1];

            $aLat = isset($a['lat']) ? (float)$a['lat'] : (float)$a[0];
            $aLng = isset($a['lng']) ? (float)$a['lng'] : (float)$a[1];
            $bLat = isset($b['lat']) ? (float)$b['lat'] : (float)$b[0];
            $bLng = isset($b['lng']) ? (float)$b['lng'] : (float)$b[1];

            $res = $this->pointToSegmentDistance(
                $pLat,
                $pLng,
                $aLat,
                $aLng,
                $bLat,
                $bLng
            );

            if ($res['distance'] < $minDist) {
                $minDist = $res['distance'];
                $closestPoint = ['lat' => $res['lat'], 'lng' => $res['lng']];
                $bestSegIndex = $i;
                $bestT = $res['t'];
            }
        }

        return [
            'distance' => $minDist,
            'closest_point' => $closestPoint,
            'segment_index' => $bestSegIndex,
            't' => $bestT,
        ];
    }

    /**
     * Hitung jarak tempuh kumulatif di sepanjang garis polyline dari titik awal (user)
     * hingga titik proyeksi pada indeks segmen dan faktor t tertentu.
     */
    public function calculateDistanceAlongPolyline(array $coordinates, int $segmentIndex, float $t): float
    {
        $dist = 0.0;
        $count = count($coordinates);

        for ($i = 0; $i < $segmentIndex && $i < $count - 1; $i++) {
            $a = $coordinates[$i];
            $b = $coordinates[$i + 1];

            $aLat = isset($a['lat']) ? (float)$a['lat'] : (float)$a[0];
            $aLng = isset($a['lng']) ? (float)$a['lng'] : (float)$a[1];
            $bLat = isset($b['lat']) ? (float)$b['lat'] : (float)$b[0];
            $bLng = isset($b['lng']) ? (float)$b['lng'] : (float)$b[1];

            $dist += $this->haversineDistance($aLat, $aLng, $bLat, $bLng);
        }

        if ($segmentIndex < $count - 1) {
            $a = $coordinates[$segmentIndex];
            $b = $coordinates[$segmentIndex + 1];

            $aLat = isset($a['lat']) ? (float)$a['lat'] : (float)$a[0];
            $aLng = isset($a['lng']) ? (float)$a['lng'] : (float)$a[1];
            $bLat = isset($b['lat']) ? (float)$b['lat'] : (float)$b[0];
            $bLng = isset($b['lng']) ? (float)$b['lng'] : (float)$b[1];

            $segLen = $this->haversineDistance($aLat, $aLng, $bLat, $bLng);
            $dist += $t * $segLen;
        }

        return $dist;
    }

    /**
     * Panggil OSRM Table Service untuk mendapatkan matriks jarak dan durasi nyata
     * dari lokasi user ke seluruh titik kumpul aktif sekaligus dalam 1 request cepat.
     */
    public function fetchOsrmDistanceTable(float $userLat, float $userLng, array $destinations): ?array
    {
        if (empty($destinations)) {
            return null;
        }

        $osrmBaseUrl = rtrim(config('services.routing.osrm_url', 'https://router.project-osrm.org'), '/');
        $coords = ["{$userLng},{$userLat}"];
        foreach ($destinations as $d) {
            $coords[] = "{$d['lng']},{$d['lat']}";
        }

        $url = "{$osrmBaseUrl}/table/v1/driving/" . implode(';', $coords) . "?sources=0&annotations=distance,duration";

        try {
            $resp = Http::timeout(4)->get($url);
            if ($resp->successful()) {
                $data = $resp->json();
                if (($data['code'] ?? '') === 'Ok' && !empty($data['distances'][0])) {
                    return [
                        'distances' => $data['distances'][0],
                        'durations' => $data['durations'][0] ?? [],
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::warning('OSRM Table API gagal: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Pastikan arah polylines rute resmi mengalir menuju fasilitas evakuasi.
     */
    protected function orientPolylineTowardsFacility(array $points, float $fLat, float $fLng): array
    {
        if (count($points) < 2) {
            return $points;
        }

        $firstPt = reset($points);
        $lastPt = end($points);

        $firstLat = isset($firstPt['lat']) ? (float)$firstPt['lat'] : (float)$firstPt[0];
        $firstLng = isset($firstPt['lng']) ? (float)$firstPt['lng'] : (float)$firstPt[1];
        $lastLat = isset($lastPt['lat']) ? (float)$lastPt['lat'] : (float)$lastPt[0];
        $lastLng = isset($lastPt['lng']) ? (float)$lastPt['lng'] : (float)$lastPt[1];

        $distFromFirst = $this->haversineDistance($firstLat, $firstLng, $fLat, $fLng);
        $distFromLast = $this->haversineDistance($lastLat, $lastLng, $fLat, $fLng);

        // Jika titik awal lebih dekat ke fasilitas dibanding titik akhir, balik urutan array
        // agar titik akhir mengarah tepat ke fasilitas titik kumpul
        if ($distFromFirst < $distFromLast) {
            return array_reverse($points);
        }

        return $points;
    }

    /**
     * Potong rute resmi mulai dari titik masuk menuju titik kumpul.
     */
    protected function sliceOfficialRouteFromEntry(
        array $orientedPoints,
        array $entryPoint,
        int $segmentIndex,
        array $facilityCoords
    ): array {
        $result = [];
        $result[] = ['lat' => $entryPoint['lat'], 'lng' => $entryPoint['lng']];

        $count = count($orientedPoints);
        $startIndex = min($count - 1, $segmentIndex + 1);

        for ($i = $startIndex; $i < $count; $i++) {
            $pt = $orientedPoints[$i];
            $ptLat = isset($pt['lat']) ? (float)$pt['lat'] : (float)$pt[0];
            $ptLng = isset($pt['lng']) ? (float)$pt['lng'] : (float)$pt[1];

            // Hindari duplikasi jika entryPoint berhimpitan dengan vertex
            if ($this->haversineDistance($entryPoint['lat'], $entryPoint['lng'], $ptLat, $ptLng) > 2) {
                $result[] = ['lat' => $ptLat, 'lng' => $ptLng];
            }
        }

        // Tambahkan koordinat fasilitas di ujung akhir
        $last = end($result);
        if (!$last || $this->haversineDistance($last['lat'], $last['lng'], $facilityCoords['lat'], $facilityCoords['lng']) > 5) {
            $result[] = ['lat' => $facilityCoords['lat'], 'lng' => $facilityCoords['lng']];
        }

        return $result;
    }

    /**
     * Hitung panjang keseluruhan garis polyline dalam meter.
     */
    public function calculatePolylineLength(array $latLngArray): float
    {
        $length = 0.0;
        $count = count($latLngArray);

        for ($i = 0; $i < $count - 1; $i++) {
            $p1 = $latLngArray[$i];
            $p2 = $latLngArray[$i + 1];

            $p1Lat = isset($p1['lat']) ? (float)$p1['lat'] : (float)$p1[0];
            $p1Lng = isset($p1['lng']) ? (float)$p1['lng'] : (float)$p1[1];
            $p2Lat = isset($p2['lat']) ? (float)$p2['lat'] : (float)$p2[0];
            $p2Lng = isset($p2['lng']) ? (float)$p2['lng'] : (float)$p2[1];

            $length += $this->haversineDistance($p1Lat, $p1Lng, $p2Lat, $p2Lng);
        }

        return $length;
    }

    /**
     * Hitung sudut bearing (arah kompas 0 - 360 derajat) antara dua titik koordinat.
     */
    protected function calculateBearing(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $dLng = deg2rad($lng2 - $lng1);
        $y = sin($dLng) * cos(deg2rad($lat2));
        $x = cos(deg2rad($lat1)) * sin(deg2rad($lat2)) -
            sin(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos($dLng);

        $bearing = rad2deg(atan2($y, $x));
        return fmod(($bearing + 360.0), 360.0);
    }

    /**
     * Hitung selisih sudut belokan antara dua bearing (-180 s/d +180 derajat).
     */
    protected function calculateAngleDifference(float $bearing1, float $bearing2): float
    {
        $diff = fmod(($bearing2 - $bearing1 + 540.0), 360.0) - 180.0;
        return $diff;
    }

    /**
     * Sederhanakan daftar instruksi jika ada segmen kecil berulang.
     */
    protected function condenseInstructions(array $steps): array
    {
        $condensed = [];
        foreach ($steps as $s) {
            if (empty($condensed)) {
                $condensed[] = $s;
                continue;
            }

            $lastIdx = count($condensed) - 1;
            if ($condensed[$lastIdx]['type'] === 'straight' && $s['type'] === 'straight') {
                $condensed[$lastIdx]['distance_meters'] += $s['distance_meters'];
                $condensed[$lastIdx]['text'] = "Lurus " . $this->formatDistance($condensed[$lastIdx]['distance_meters']);
            } else {
                $condensed[] = $s;
            }
        }
        return $condensed;
    }

    /**
     * Petakan tipe manuver ke kata kunci standar icon UI.
     */
    protected function mapManeuverType(string $type, string $modifier): string
    {
        if ($type === 'arrive') return 'arrive';
        if ($type === 'depart') return 'depart';

        return match ($modifier) {
            'left', 'slight left', 'sharp left' => 'left',
            'right', 'slight right', 'sharp right' => 'right',
            'uturn' => 'uturn',
            default => 'straight',
        };
    }

    /**
     * Kembalikan icon SVG identifier yang konsisten untuk frontend Tailwind.
     */
    protected function resolveStepIcon(string $type): string
    {
        return match ($type) {
            'left' => 'arrow-left',
            'right' => 'arrow-right',
            'arrive' => 'flag',
            'enter_official' => 'shield-check',
            'uturn' => 'arrow-uturn-left',
            default => 'arrow-up',
        };
    }

    /**
     * Helper ekstraksi koordinat objek/array GeoJSON fleksibel (GeoJSON [lng, lat] ke ['lat', 'lng']).
     */
    public function extractCoordinates(mixed $coords): ?array
    {
        if (is_string($coords)) {
            $decoded = json_decode($coords, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $coords = $decoded;
            }
        }

        if (!is_array($coords)) {
            return null;
        }

        // Format asosiatif ['lat' => x, 'lng' => y]
        if (isset($coords['lat']) && isset($coords['lng'])) {
            return ['lat' => (float) $coords['lat'], 'lng' => (float) $coords['lng']];
        }

        // Format GeoJSON [longitude, latitude]
        if (isset($coords[0]) && isset($coords[1])) {
            return ['lat' => (float) $coords[1], 'lng' => (float) $coords[0]];
        }

        return null;
    }

    /**
     * Ekstraksi linestring coordinates dari Model EvacuationRoute.
     */
    public function extractRouteCoordinates(EvacuationRoute $route): array
    {
        $raw = $route->line_coordinates;
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        if (!is_array($raw)) {
            return [];
        }

        $points = [];
        foreach ($raw as $pt) {
            if (is_array($pt) && count($pt) >= 2) {
                // GeoJSON linestring: [lng, lat]
                $points[] = [
                    'lat' => (float) $pt[1],
                    'lng' => (float) $pt[0],
                ];
            }
        }

        return $points;
    }

    /**
     * Format jarak human-readable: e.g. "250 m" atau "1.2 Km".
     */
    public function formatDistance(float $meters): string
    {
        if ($meters < 1000) {
            return round($meters) . ' meter';
        }

        return round($meters / 1000, 1) . ' Km';
    }

    /**
     * Format durasi waktu human-readable: e.g. "14 Menit".
     */
    public function formatDuration(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} Menit";
        }

        $hours = floor($minutes / 60);
        $rem = $minutes % 60;

        return $rem > 0 ? "{$hours} Jam {$rem} Menit" : "{$hours} Jam";
    }
}
