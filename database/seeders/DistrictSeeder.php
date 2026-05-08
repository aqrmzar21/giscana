<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\District;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        District::truncate();

        $geojsonPath = public_path('geojson/kawasan-pesisir.geojson');
        
        if (!file_exists($geojsonPath)) {
            $this->command->warn("GeoJSON file not found at: {$geojsonPath}");
            return;
        }

        $geo = json_decode(file_get_contents($geojsonPath), true);
        
        if (!is_array($geo) || !isset($geo['features'])) {
            $this->command->error("Invalid GeoJSON format");
            return;
        }

        $count = 0;
        foreach ($geo['features'] as $feature) {
            $props = $feature['properties'] ?? [];
            $geometry = $feature['geometry'] ?? null;
            
            if (!$geometry) {
                continue;
            }

            // Optional: We can filter by Regency just like in MapController
            // $regency = $props['WADMKK'] ?? $props['WIADKK'] ?? '';
            // if ($regency !== 'Bone Bolango') { continue; }

            $geometryJson = json_encode($geometry);

            DB::table('districts')->insert([
                'code' => $props['LCODE'] ?? null,
                'name' => $props['NAMOBJ'] ?? 'Unknown',
                'regency' => $props['WADMKK'] ?? $props['WIADKK'] ?? null,
                'province' => $props['WADMPR'] ?? $props['WIADPR'] ?? null,
                'remark' => $props['REMARK'] ?? null,
                'geom' => DB::raw("ST_GeomFromGeoJSON('" . addslashes($geometryJson) . "', 2)"),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $count++;
        }

        $this->command->info("Successfully seeded {$count} districts from GeoJSON.");
    }
}
