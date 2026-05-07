<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VillageSeeder extends Seeder
{
    /**
     * Mapping nama kecamatan di GeoJSON ke nama district di tabel DB.
     * Key   = nilai field 'kecamatan' di GeoJSON
     * Value = nilai field 'name' di tabel districts
     */
    protected array $districtNameMap = [
        'Kabila Bone' => 'Kabila Bone',
        'Bone'        => 'Bone',
        'Bone Raya'   => 'Bone Raya',
        'Bonepantai'  => 'Bonepantai',  // nama di GeoJSON = nama di DB
        'Bulawa'      => 'Bulawa',
    ];

    /**
     * File GeoJSON yang akan di-seed (relatif terhadap public_path()).
     */
    protected array $geojsonFiles = [
        'geojson/Kecamatan Kabila Bone-KEL_DESA.geojson',
        'geojson/Kecamatan Bone-KEL_DESA.geojson',
        'geojson/Kecamatan Bone Raya-KEL_DESA.geojson',
        'geojson/Kecamatan Bonepantai-KEL_DESA.geojson',
        'geojson/Kecamatan Bulawa-KEL_DESA.geojson',
    ];

    public function run(): void
    {
        // Hapus data lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('villages')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Cache semua districts agar tidak query berulang
        $districtsCache = DB::table('districts')
            ->select('id', 'name')
            ->get()
            ->keyBy('name');

        $totalInserted = 0;
        $totalSkipped  = 0;

        foreach ($this->geojsonFiles as $relPath) {
            $path = public_path($relPath);

            if (!file_exists($path)) {
                $this->command->warn("File tidak ditemukan: {$path}");
                continue;
            }

            $geo = json_decode(file_get_contents($path), true);

            if (!isset($geo['features'])) {
                $this->command->error("Format GeoJSON tidak valid: {$relPath}");
                continue;
            }

            $this->command->info("Memproses: {$relPath} (" . count($geo['features']) . " fitur)");

            foreach ($geo['features'] as $feature) {
                $props    = $feature['properties'] ?? [];
                $geometry = $feature['geometry']   ?? null;

                if (!$geometry) {
                    $totalSkipped++;
                    continue;
                }

                // Resolve district_id menggunakan mapping nama
                $kecGeoJson  = $props['kecamatan'] ?? null;
                $districtName = $this->districtNameMap[$kecGeoJson] ?? $kecGeoJson;

                if (!$districtName || !isset($districtsCache[$districtName])) {
                    $this->command->warn(
                        "  District tidak ditemukan untuk kecamatan: '{$kecGeoJson}' " .
                        "(dicari sebagai: '{$districtName}'). Desa dilewati: {$props['kel_desa']}"
                    );
                    $totalSkipped++;
                    continue;
                }

                $districtId   = $districtsCache[$districtName]->id;
                $geometryJson = json_encode($geometry);

                DB::table('villages')->insert([
                    'district_id'   => $districtId,
                    'code'          => $props['kode_kd']  ?? null,
                    'name'          => $props['kel_desa'] ?? 'Unknown',
                    'full_name'     => $props['nama']     ?? null,
                    'regency'       => $props['kab_kota'] ?? null,
                    'province'      => $props['provinsi'] ?? null,
                    'geom'          => DB::raw("ST_GeomFromGeoJSON('" . addslashes($geometryJson) . "', 2)"),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                $totalInserted++;
            }
        }

        $this->command->info("==============================");
        $this->command->info("Selesai! Total desa berhasil di-seed : {$totalInserted}");
        if ($totalSkipped > 0) {
            $this->command->warn("Total desa dilewati                  : {$totalSkipped}");
        }
    }
}
