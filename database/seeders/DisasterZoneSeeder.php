<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DisasterZoneSeeder extends Seeder
{
    public function run()
    {
        // 1. Matikan sementara pengecekan foreign key agar truncate aman
        Schema::disableForeignKeyConstraints();
        
        // 2. Kosongkan hanya tabel disaster_zones
        DB::table('disaster_zones')->truncate();

        // 3. Nyalakan kembali pengecekan foreign key
        Schema::enableForeignKeyConstraints();

        // 4. Masukkan data zona bencana sesuai pembaruan terkini
        $zones = [
            [
                'id' => 1,
                'uuid' => '56f4fc72-6a35-484b-9169-dc3482f8eeaf',
                'name' => 'Banjir Bilungala',
                'disaster_type' => 'banjir',
                'description' => 'High-risk banjir zone along the Bone River, prone to seasonal banjiring during rainy season.',
                'risk_level' => 'high',
                'point_coordinates' => json_encode([123.20808535158405, 0.3783370772048755]),
                'affected_population' => 15,
                'area_hectares' => 125.50,
                'is_active' => 1,
                'created_at' => '2026-05-10 19:25:38',
                'updated_at' => '2026-09-05 21:18:43',
                'district_id' => 4,
            ],
            [
                'id' => 2,
                'uuid' => 'ef0b21a8-9c02-4782-b97d-b254cec105f3',
                'name' => 'Longsor Oluhuta',
                'disaster_type' => 'longsor',
                'description' => 'Critical longsor risk area on steep hillsides, especially vulnerable during heavy rainfall.',
                'risk_level' => 'critical',
                'point_coordinates' => json_encode([123.16263718062282, 0.42377850503008574]),
                'affected_population' => 36,
                'area_hectares' => 85.20,
                'is_active' => 1,
                'created_at' => '2026-05-10 19:25:38',
                'updated_at' => '2026-09-05 21:21:14',
                'district_id' => 5,
            ],
            [
                'id' => 3,
                'uuid' => 'f0b189bd-8367-4f03-b7a6-20cf5619044c',
                'name' => 'Desa Olele',
                'disaster_type' => 'banjir',
                'description' => null,
                'risk_level' => 'low',
                'point_coordinates' => json_encode([123.17827115564272, 0.40636962321801207]),
                'affected_population' => 24,
                'area_hectares' => null,
                'is_active' => 1,
                'created_at' => '2026-05-10 22:15:36',
                'updated_at' => '2026-05-10 22:23:53',
                'district_id' => 4,
            ],
            [
                'id' => 4,
                'uuid' => 'dd9ef8d7-e278-4feb-b7b6-30a6e1d69fc8',
                'name' => 'Banjir Bandang Bone',
                'disaster_type' => 'banjir',
                'description' => null,
                'risk_level' => 'low',
                'point_coordinates' => json_encode([123.457647587101, 0.3605071518966412]),
                'affected_population' => 2,
                'area_hectares' => null,
                'is_active' => 1,
                'created_at' => '2026-05-10 22:32:41',
                'updated_at' => '2026-05-10 22:32:41',
                'district_id' => 3,
            ],
            [
                'id' => 5,
                'uuid' => 'c1fbd2c2-0abe-4de1-b1f5-12b6755ce68c',
                'name' => 'Longsor Taludaa',
                'disaster_type' => 'longsor',
                'description' => '9 September 2022',
                'risk_level' => 'medium',
                'point_coordinates' => json_encode([123.457647587101, 0.3605071518966412]),
                'affected_population' => 3,
                'area_hectares' => null,
                'is_active' => 1,
                'created_at' => '2026-05-10 22:39:12',
                'updated_at' => '2026-06-03 03:52:02',
                'district_id' => 1,
            ],
            [
                'id' => 6,
                'uuid' => '208ec15b-1e59-46bd-9407-448205968862',
                'name' => 'Longsor Inomata',
                'disaster_type' => 'longsor',
                'description' => null,
                'risk_level' => 'medium',
                'point_coordinates' => json_encode([123.32968711853027, 0.33611104830258337]),
                'affected_population' => 32,
                'area_hectares' => null,
                'is_active' => 1,
                'created_at' => '2026-09-05 21:30:58',
                'updated_at' => '2026-09-19 16:58:19',
                'district_id' => 1,
            ],
            [
                'id' => 7,
                'uuid' => 'e0dc9b14-a307-42dc-81a3-53ac73f9bcc8',
                'name' => 'Banjir Rob',
                'disaster_type' => 'banjir',
                'description' => null,
                'risk_level' => 'high',
                'point_coordinates' => json_encode([123.4193801879882954608547152020037174224853515625, 0.356938942267295278298888661083765327930450439453125]),
                'affected_population' => 3,
                'area_hectares' => null,
                'is_active' => 1,
                'created_at' => '2026-09-14 18:33:11',
                'updated_at' => '2026-09-19 17:25:57',
                'district_id' => 1,
            ],
        ];

        DB::table('disaster_zones')->insert($zones);
    }
}