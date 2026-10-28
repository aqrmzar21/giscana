<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class EvacuationRouteSeeder extends Seeder
{
    public function run()
    {
        // 1. Matikan cek relasi sementara agar bisa truncate tabel child dengan aman
        Schema::disableForeignKeyConstraints();
        
        // 2. Kosongkan hanya tabel evacuation_routes
        DB::table('evacuation_routes')->truncate();

        // 3. Nyalakan kembali cek relasi
        Schema::enableForeignKeyConstraints();

        // 4. Cari ID fasilitas yang sudah ada di database agar relasi valid
        $facilitySmk = DB::table('evacuation_facilities')->where('name', 'SMK Negeri Bone 1')->first();
        $facilityMasjid = DB::table('evacuation_facilities')->where('name', 'Masjid Oluhuta')->first();
        $facilityBalai = DB::table('evacuation_facilities')->where('name', 'Balai Desa Bone')->first();
        $facilityGedung = DB::table('evacuation_facilities')->where('name', 'Gedung Pertemuan')->first();

        // 5. Masukkan data rute baru
        $routes = [
            [
                'uuid' => 'ef6c61b1-02a3-4108-962a-02398c0196dc',
                'evacuation_facility_id' => $facilitySmk ? $facilitySmk->id : null,
                'facility_name' => $facilitySmk ? $facilitySmk->name : 'SMK Negeri Bone 1',
                'name' => 'Secondary Route - Village Path',
                'description' => 'Secondary evacuation route through village paths.',
                'line_coordinates' => '[[123.30000216080441,0.32138895529709544], [123.29920593791996,0.31983634498158153], [123.29917408912803,0.3195974820427381], [123.30064608692544,0.3188280472324152], [123.30104419836829,0.3189713651314037], [123.30154581878514,0.3187165777539036], [123.30124950328184,0.3181495056982868]]',
                'route_type' => 'secondary',
                'is_accessible' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => '9be4d6dd-05cf-487f-a54a-85eea89d203a',
                'evacuation_facility_id' => $facilityGedung ? $facilityGedung->id : null,
                'facility_name' => $facilityGedung ? $facilityGedung->name : 'Gedung Pertemuan',
                'name' => 'Primary Evacuation Route - Main Road',
                'description' => 'Main evacuation route connecting high-risk areas to evacuation centers.',
                'line_coordinates' => '[[123.28008744958447, 0.32173069129049736], [123.28015140297714, 0.3216027865242239], [123.28018693263726, 0.3214393526544086], [123.2801158733148, 0.3211906489323013], [123.28005191992446, 0.32087088699624644], [123.27979610635828, 0.3204090086274647], [123.27977478856224, 0.32019583398793827], [123.27974636483196, 0.31984764873459426], [123.27976057669827, 0.319584733330899], [123.27989558941107, 0.3192720771675823], [123.28023667416363, 0.31907311414806827], [123.28056354705217, 0.3190091617491788], [123.2805493351882, 0.3192720771675823], [123.28054222925607, 0.31951367511246076], [123.28054222925607, 0.31982633126952464], [123.2806630301049, 0.3200608233813682], [123.2807269834953, 0.3202313630942797], [123.2808477843464, 0.32043743191221097]]',
                'route_type' => 'primary',
                'is_accessible' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => 'd9fc27ae-dc8a-4936-b879-c0cb85544065',
                'evacuation_facility_id' => $facilityMasjid ? $facilityMasjid->id : null,
                'facility_name' => $facilityMasjid ? $facilityMasjid->name : 'Masjid Oluhuta',
                'name' => 'Emergency Route - Hillside',
                'description' => 'Emergency evacuation route for longsor-prone areas.',
                'line_coordinates' => '[[123.24333913601816, 0.3519398498250865], [123.24432902864044, 0.3518498612835259], [123.24486897007154, 0.35179361844471657], [123.24518393590591, 0.3516248899271801], [123.245678882218, 0.35144491283801926], [123.24606134073171, 0.35135492429174064], [123.24623007242826, 0.351073710079433], [123.24663502850068, 0.35103996437410956], [123.24732164250496, 0.3510036620480719], [123.24741163274284, 0.35075619353349907], [123.24754661812584, 0.35058746497502113], [123.24815405223428, 0.3505312221286232], [123.2481877986387, 0.35036249354658366], [123.24895271566449, 0.34987880504563407], [123.24914394492134, 0.35003628502531114], [123.24980762292984, 0.34986755647604184], [123.24996510584714, 0.3496988279229072], [123.25014508632466, 0.3496088393598171], [123.25075252043324, 0.3495638450783787], [123.25107873504884, 0.34933887366621263], [123.25146119356252, 0.34940636509077194]]',
                'route_type' => 'emergency',
                'is_accessible' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => '108334d7-39aa-4a3a-aa2f-2a1e70455287',
                'evacuation_facility_id' => $facilityBalai ? $facilityBalai->id : null,
                'facility_name' => $facilityBalai ? $facilityBalai->name : 'Balai Desa Bone',
                'name' => 'Jalur Sekunder – Jalan Desa Bone',
                'description' => 'Jalur sekunder evakuasi warga Desa Bone menuju balai desa. Jalan beton, lebar ±3 m, bisa dilalui motor dan mobil kecil.',
                'line_coordinates' => '[[123.20484638214111, 0.3833169952859225], [123.20870876312256, 0.3827161938911204], [123.21085453033449, 0.3807421318693689], [123.21243703365326, 0.3825445363419969], [123.2163155078888, 0.3872651176859056], [123.21711480617525, 0.3906446231715906], [123.22015911340716, 0.3910791309213815]]',
                'route_type' => 'secondary',
                'is_accessible' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => '274ab07a-0738-4c21-a00d-ed1421f8f9b7',
                'evacuation_facility_id' => $facilityMasjid ? $facilityMasjid->id : null,
                'facility_name' => $facilityMasjid ? $facilityMasjid->name : 'Masjid Oluhuta',
                'name' => 'Jalur Darurat – Bukit Oluhuta',
                'description' => 'Jalur alternatif darurat melewati jalan setapak menuju masjid Oluhuta. Kondisi jalan tanah berbatu, hanya bisa dilalui pejalan kaki.',
                'line_coordinates' => '[[123.14475417137147, 0.4230341622506397], [123.14725399017335, 0.42327019020682927], [123.14918518066408, 0.4240855594547537], [123.15589070320132, 0.42627418174775766], [123.15877676010132, 0.4291172440912424], [123.15960824489596, 0.4292191651745525], [123.16002130508424, 0.42977168470776006]]',
                'route_type' => 'emergency',
                'is_accessible' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => '096d5bdf-8cc6-48e5-9330-5f5797f1a0e8',
                'evacuation_facility_id' => null,
                'facility_name' => null,
                'name' => 'Jalur Utama – Perbukitan',
                'description' => 'Jalur darurat melewati perbukitan Muara Bone menuju posko. Jalan tanah, curam, hanya bisa dilalui pejalan kaki.',
                'line_coordinates' => '[[123.46431255340578, 0.33688351117679666], [123.46675872802736, 0.339630045343773], [123.4681534767151, 0.33853572322978975], [123.46946239471436, 0.3400484625899394], [123.47228407859804, 0.34057416628374637], [123.47662925720216, 0.3614306320939934], [123.47379684448244, 0.3723309060439422]]',
                'route_type' => 'primary',
                'is_accessible' => 1,
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('evacuation_routes')->insert($routes);
    }
}