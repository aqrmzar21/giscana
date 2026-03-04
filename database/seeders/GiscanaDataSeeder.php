<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\DisasterZone;
use App\Models\EvacuationRoute;
use App\Models\EvacuationFacility;
use App\Models\AidDisaster;
use App\Models\User;

class GiscanaDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key constraints
        Schema::disableForeignKeyConstraints();

        // Truncate tables to ensure a clean state
        User::truncate();
        DisasterZone::truncate();
        EvacuationRoute::truncate();
        EvacuationFacility::truncate();
        AidDisaster::truncate();

        // Re-enable foreign key constraints
        Schema::enableForeignKeyConstraints();

        // Create sample users
        User::updateOrCreate(
            ['email' => 'admin@giscana.local'],
            [
                'name' => 'BPBD Admin',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'phone' => '+6281234567890',
                'organization' => 'BPBD Bone Bolango',
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@giscana.local'],
            [
                'name' => 'BPBD Staff',
                'password' => bcrypt('password'),
                'role' => 'staff',
                'phone' => '+6281234567891',
                'organization' => 'BPBD Bone Bolango',
                'is_active' => true,
            ]
        );

        // Sample disaster zones for Bone Bolango Regency (sebagai titik lokasi, bukan poligon)
        DisasterZone::create([
            'name' => 'banjir Risk Zone - Bone River',
            'disaster_type' => 'banjir',
            'description' => 'High-risk banjir zone along the Bone River, prone to seasonal banjiring during rainy season.',
            'risk_level' => 'high',
            // Titik representatif (lng, lat)
            'point_coordinates' => [123.20808535158405, 0.3783370772048755],
            'area_hectares' => 125.5,
            'affected_population' => 2500,
            'is_active' => true,
        ]);

        DisasterZone::create([
            'name' => 'longsor Risk Zone - Hillside Area',
            'disaster_type' => 'longsor',
            'description' => 'Critical longsor risk area on steep hillsides, especially vulnerable during heavy rainfall.',
            'risk_level' => 'critical',
            // Titik representatif (lng, lat)
            'point_coordinates' => [123.16263718062282, 0.42377850503008574],
            'area_hectares' => 85.2,
            'affected_population' => 1200,
            'is_active' => true,
        ]);

        // 5 kecamatan inti (default) — data bantuan bencana per kecamatan
        AidDisaster::create([
            'nama_kecamatan'          => 'Kecamatan Kabila Bone',
            'jumlah_penerima_bantuan' => 350,
            'bantuan_terdistribusi'   => 150,
            'is_active'               => true,
        ]);
        AidDisaster::create([
            'nama_kecamatan'          => 'Kecamatan Bone',
            'jumlah_penerima_bantuan' => 100,
            'bantuan_terdistribusi'   => 80,
            'is_active'               => true,
        ]);
        AidDisaster::create([
            'nama_kecamatan'          => 'Kecamatan Bone Pantai',
            'jumlah_penerima_bantuan' => 700,
            'bantuan_terdistribusi'   => 460,
            'is_active'               => true,
        ]);
        AidDisaster::create([
            'nama_kecamatan'          => 'Kecamatan Bone Raya',
            'jumlah_penerima_bantuan' => 200,
            'bantuan_terdistribusi'   => 130,
            'is_active'               => true,
        ]);
        AidDisaster::create([
            'nama_kecamatan'          => 'Kecamatan Bulawa',
            'jumlah_penerima_bantuan' => 500,
            'bantuan_terdistribusi'   => 280,
            'is_active'               => true,
        ]);

        $kecamatanBone = AidDisaster::where('nama_kecamatan', 'Kecamatan Bone')->first();
        $kecamatanKabilaBone = AidDisaster::where('nama_kecamatan', 'Kecamatan Kabila Bone')->first();
        $kecamatanBulawa = AidDisaster::where('nama_kecamatan', 'Kecamatan Bulawa')->first();

        // Sample evacuation facilities (terkait kecamatan via aid_disaster_id + nama_kecamatan)
        EvacuationFacility::create([
            'aid_disaster_id' => $kecamatanBone->id,
            'nama_kecamatan' => $kecamatanBone->nama_kecamatan,
            'name' => 'SMK Negeri Bone 1',
            'description' => 'Primary evacuation center located in Bone village.',
            'point_coordinates' => [123.2180174509013, 0.3739372685927513],
            'capacity' => 300,
            'address' => 'Jl. Pendidikan No. 1, Bone, Bone Bolango',
            'contact_person' => 'Bapak Ahmad',
            'contact_phone' => '+6281234567892',
            'has_medical_facility' => true,
            'has_food_storage' => true,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        EvacuationFacility::create([
            'aid_disaster_id' => $kecamatanKabilaBone->id,
            'nama_kecamatan' => $kecamatanKabilaBone->nama_kecamatan,
            'name' => 'Masjid Oluhuta',
            'description' => 'Mosque serving as evacuation center with basic facilities.',
            'point_coordinates' => [123.16027286986488, 0.4298848194426226],
            'capacity' => 200,
            'address' => 'Jl. Masjid Raya, Desa Olele',
            'contact_person' => 'Ustadz Rahman',
            'contact_phone' => '+6281234567893',
            'has_medical_facility' => false,
            'has_food_storage' => true,
            'is_accessible' => true,
            'is_active' => true,
        ]);
        
        EvacuationFacility::create([
            'aid_disaster_id' => $kecamatanBone->id,
            'nama_kecamatan' => $kecamatanBone->nama_kecamatan,
            'name' => 'Balai Desa Bone',
            'description' => 'Village hall equipped for emergency situations.',
            'point_coordinates' => [123.220285, 0.391023],
            'capacity' => 150,
            'address' => 'Jl. Pemuda No. 5, Bone, Bone Bolango',
            'contact_person' => 'Bapak Rusi',
            'contact_phone' => '+6281234567894',
            'has_medical_facility' => false,
            'has_food_storage' => false,
            'is_accessible' => true,
            'is_active' => true,
        ]);
        
        EvacuationFacility::create([
            'aid_disaster_id' => $kecamatanBulawa->id,
            'nama_kecamatan' => $kecamatanBulawa->nama_kecamatan,
            'name' => 'Titik Kumpul',
            'description' => 'area null as evacuation center with basic facilities.',
            'point_coordinates' => [123.2514177981202, 0.3494345195302344],
            'capacity' => 200,
            'address' => 'Jl. Masjid Raya, Bone, Bone Bolango',
            'contact_person' => 'Ustadz Rahman',
            'contact_phone' => '+6281234567893',
            'has_medical_facility' => false,
            'has_food_storage' => true,
            'is_accessible' => true,
            'is_active' => true,
        ]);
        
        EvacuationFacility::create([
            'aid_disaster_id' => $kecamatanBulawa->id,
            'nama_kecamatan' => $kecamatanBulawa->nama_kecamatan,
            'name' => 'Gedung Pertemuan',
            'description' => 'Gedung pertemuan yang sering digunakan untuk kegiatan masyarakat',
            'point_coordinates' => [123.28091613555262,0.3204120222883091],
            'capacity' => 150,
            'address' => 'Jl. Raya No. 8, Desa Patoa',
            'contact_person' => 'Bapak Supriyadi',
            'contact_phone' => '+6282198765432',
            'has_medical_facility' => false,
            'has_food_storage' => false,
            'is_accessible' => true,
            'is_active' => true,
        ]);
        
        EvacuationFacility::create([
            'aid_disaster_id' => $kecamatanBulawa->id,
            'nama_kecamatan' => $kecamatanBulawa->nama_kecamatan,
            'name' => 'Lapangan Pertemuan',
            'description' => 'lahan kososng tempat kumpul masyarakat dengan ketinggian yang cukup',
            'point_coordinates' => [123.30000155640084,0.3213922903642299],
            'capacity' => 50,
            'address' => 'Jl. Trans Sulawesi, Desa Mamunga Timur',
            'contact_person' => 'Bapak Hasan',
            'contact_phone' => '+6287889900112',
            'has_medical_facility' => false,
            'has_food_storage' => false,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        $facilitySmk = EvacuationFacility::where('name', 'SMK Negeri Bone 1')->first();
        $facilityMasjid = EvacuationFacility::where('name', 'Masjid Oluhuta')->first();
        $facilityBalai = EvacuationFacility::where('name', 'Balai Desa Bone')->first();
        $facilityGedung = EvacuationFacility::where('name', 'Gedung Pertemuan')->first();

        // Sample evacuation routes (terkait fasilitas via evacuation_facility_id + nama_fasilitas dari evacuation_facilities.name)
        EvacuationRoute::create([
            'evacuation_facility_id' => $facilitySmk->id,
            'nama_fasilitas' => $facilitySmk->name,
            'name' => 'Secondary Route - Village Path',
            'description' => 'Secondary evacuation route through village paths.',
            'disaster_type' => 'banjir',
            'line_coordinates' => [
                [123.30000216080441,0.32138895529709544],
                [123.29920593791996,0.31983634498158153],
                [123.29917408912803,0.3195974820427381],
                [123.30064608692544,0.3188280472324152],
                [123.30104419836829,0.3189713651314037],
                [123.30154581878514,0.3187165777539036],
                [123.30124950328184,0.3181495056982868]
            ],
            'length_km' => 2.1,
            'route_type' => 'secondary',
            'capacity_per_hour' => 200,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        EvacuationRoute::create([
            'evacuation_facility_id' => $facilityGedung->id,
            'nama_fasilitas' => $facilityGedung->name,
            'name' => 'Primary Evacuation Route - Main Road',
            'description' => 'Main evacuation route connecting high-risk areas to evacuation centers.',
            'disaster_type' => 'other',
            'line_coordinates' => [
                [123.28008744958447, 0.32173069129049736],
                [123.28015140297714, 0.3216027865242239],
                [123.28018693263726, 0.3214393526544086],
                [123.2801158733148, 0.3211906489323013],
                [123.28005191992446, 0.32087088699624644],
                [123.27979610635828, 0.3204090086274647],
                [123.27977478856224, 0.32019583398793827],
                [123.27974636483196, 0.31984764873459426],
                [123.27976057669827, 0.319584733330899],
                [123.27989558941107, 0.3192720771675823],
                [123.28023667416363, 0.31907311414806827],
                [123.28056354705217, 0.3190091617491788],
                [123.2805493351882, 0.3192720771675823],
                [123.28054222925607, 0.31951367511246076],
                [123.28054222925607, 0.31982633126952464],
                [123.2806630301049, 0.3200608233813682],
                [123.2807269834953, 0.3202313630942797],
                [123.2808477843464, 0.32043743191221097]
            ],
            'length_km' => 3.2,
            'route_type' => 'primary',
            'capacity_per_hour' => 500,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        EvacuationRoute::create([
            'evacuation_facility_id' => $facilityMasjid->id,
            'nama_fasilitas' => $facilityMasjid->name,
            'name' => 'Emergency Route - Hillside',
            'description' => 'Emergency evacuation route for longsor-prone areas.',
            'disaster_type' => 'longsor',
            'line_coordinates' => [
                [123.24333913601816, 0.3519398498250865],
                [123.24432902864044, 0.3518498612835259],
                [123.24486897007154, 0.35179361844471657],
                [123.24518393590591, 0.3516248899271801],
                [123.245678882218, 0.35144491283801926],
                [123.24606134073171, 0.35135492429174064],
                [123.24623007242826, 0.351073710079433],
                [123.24663502850068, 0.35103996437410956],
                [123.24732164250496, 0.3510036620480719],
                [123.24741163274284, 0.35075619353349907],
                [123.24754661812584, 0.35058746497502113],
                [123.24815405223428, 0.3505312221286232],
                [123.2481877986387, 0.35036249354658366],
                [123.24895271566449, 0.34987880504563407],
                [123.24914394492134, 0.35003628502531114],
                [123.24980762292984, 0.34986755647604184],
                [123.24996510584714, 0.3496988279229072],
                [123.25014508632466, 0.3496088393598171],
                [123.25075252043324, 0.3495638450783787],
                [123.25107873504884, 0.34933887366621263],
                [123.25146119356252, 0.34940636509077194]
            ],
            'length_km' => 1.8,
            'route_type' => 'emergency',
            'capacity_per_hour' => 150,
            'is_accessible' => true,
            'is_active' => true,
        ]);
    }
}
