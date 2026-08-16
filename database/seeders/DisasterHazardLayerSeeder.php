<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\DisasterHazardLayer;

class DisasterHazardLayerSeeder extends Seeder
{
    /**
     * Seed data layer kawasan rawan bencana.
     * File GeoJSON sudah tersedia di public/geojson/.
     */
    public function run(): void
    {
        // Bersihkan data lama agar idempotent
        DisasterHazardLayer::truncate();

        $layers = [
            [
                'disaster_type' => 'banjir',
                'label'         => 'Kawasan Rawan Banjir',
                'geojson_path'  => '/geojson/rawan_banjir.geojson',
                'fill_color'    => '#3b82f6',
                'border_color'  => '#1d4ed8',
                'fill_opacity'  => 0.35,
                'border_weight' => 1.5,
                'icon_emoji'    => '🌊',
                'description'   => 'Peta kawasan yang rawan terdampak banjir di Kabupaten Bone Bolango berdasarkan data historis dan analisis topografi.',
                'is_active'     => true,
                'sort_order'    => 1,
            ],
            [
                'disaster_type' => 'gempa',
                'label'         => 'Kawasan Rawan Gempa',
                'geojson_path'  => '/geojson/rawan_gempa.geojson',
                'fill_color'    => '#f97316',
                'border_color'  => '#c2410c',
                'fill_opacity'  => 0.30,
                'border_weight' => 1.5,
                'icon_emoji'    => '🌋',
                'description'   => 'Peta kawasan berpotensi rawan gempa bumi berdasarkan peta seismisitas dan zona patahan aktif.',
                'is_active'     => true,
                'sort_order'    => 2,
            ],
            [
                'disaster_type' => 'gelombang',
                'label'         => 'Kawasan Rawan Gelombang',
                'geojson_path'  => '/geojson/rawan_gelombang.geojson',
                'fill_color'    => '#06b6d4',
                'border_color'  => '#0e7490',
                'fill_opacity'  => 0.35,
                'border_weight' => 1.5,
                'icon_emoji'    => '🌊',
                'description'   => 'Peta kawasan pesisir rawan gelombang tinggi dan abrasi pantai di wilayah pesisir Bone Bolango.',
                'is_active'     => true,
                'sort_order'    => 3,
            ],
            [
                'disaster_type' => 'longsor',
                'label'         => 'Kawasan Rawan Longsor',
                'geojson_path'  => '/geojson/rawan_longsor.geojson',
                'fill_color'    => '#84cc16',
                'border_color'  => '#4d7c0f',
                'fill_opacity'  => 0.35,
                'border_weight' => 1.5,
                'icon_emoji'    => '⛰️',
                'description'   => 'Peta kawasan rawan longsor berdasarkan analisis kemiringan lereng, curah hujan, dan jenis tanah.',
                'is_active'     => true,
                'sort_order'    => 4,
            ],
        ];

        foreach ($layers as $layer) {
            DisasterHazardLayer::create(array_merge($layer, [
                'uuid' => (string) Str::uuid(),
            ]));
        }

        $this->command->info('DisasterHazardLayerSeeder: 4 layer rawan bencana berhasil dibuat.');
    }
}
