<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DisasterZone;
use App\Models\EvacuationRoute;
use App\Models\EvacuationFacility;
use App\Models\AidDistributionPoint;
use App\Models\User;

class GiscanaDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample users
        User::create([
            'name' => 'BPBD Admin',
            'email' => 'admin@giscana.local',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '+6281234567890',
            'organization' => 'BPBD Bone Bolango',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'BPBD Staff',
            'email' => 'staff@giscana.l
            ocal',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'phone' => '+6281234567891',
            'organization' => 'BPBD Bone Bolango',
            'is_active' => true,
        ]);

        // Sample disaster zones for Bone Bolango Regency
        DisasterZone::create([
            'name' => 'banjir Risk Zone - Bone River',
            'disaster_type' => 'banjir',
            'description' => 'High-risk banjir zone along the Bone River, prone to seasonal banjiring during rainy season.',
            'risk_level' => 'high',
            'polygon_coordinates' => [
                [
                    [123.15, 0.45], [123.18, 0.45], [123.18, 0.48], [123.15, 0.48], [123.15, 0.45]
                ]
            ],
            'area_hectares' => 125.5,
            'affected_population' => 2500,
            'is_active' => true,
        ]);

        DisasterZone::create([
            'name' => 'longsor Risk Zone - Hillside Area',
            'disaster_type' => 'longsor',
            'description' => 'Critical longsor risk area on steep hillsides, especially vulnerable during heavy rainfall.',
            'risk_level' => 'critical',
            'polygon_coordinates' => [
                [
                    [123.20, 0.52], [123.25, 0.52], [123.25, 0.55], [123.20, 0.55], [123.20, 0.52]
                ]
            ],
            'area_hectares' => 85.2,
            'affected_population' => 1200,
            'is_active' => true,
        ]);

        DisasterZone::create([
            'name' => 'Low Risk Coastal Area',
            'disaster_type' => 'banjir',
            'description' => 'Low-risk coastal area with minimal banjir risk.',
            'risk_level' => 'low',
            'polygon_coordinates' => [
                [
                    [123.10, 0.40], [123.12, 0.40], [123.12, 0.42], [123.10, 0.42], [123.10, 0.40]
                ]
            ],
            'area_hectares' => 45.8,
            'affected_population' => 800,
            'is_active' => true,
        ]);

        // Sample evacuation routes
        EvacuationRoute::create([
            'name' => 'Primary Evacuation Route - Main Road',
            'description' => 'Main evacuation route connecting high-risk areas to evacuation centers.',
            'disaster_type' => 'other',
            'line_coordinates' => [
                [123.16, 0.46], [123.17, 0.47], [123.18, 0.48], [123.19, 0.49]
            ],
            'length_km' => 3.2,
            'route_type' => 'primary',
            'capacity_per_hour' => 500,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        EvacuationRoute::create([
            'name' => 'Secondary Route - Village Path',
            'description' => 'Secondary evacuation route through village paths.',
            'disaster_type' => 'banjir',
            'line_coordinates' => [
                [123.14, 0.44], [123.15, 0.45], [123.16, 0.46]
            ],
            'length_km' => 2.1,
            'route_type' => 'secondary',
            'capacity_per_hour' => 200,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        EvacuationRoute::create([
            'name' => 'Emergency Route - Hillside',
            'description' => 'Emergency evacuation route for longsor-prone areas.',
            'disaster_type' => 'longsor',
            'line_coordinates' => [
                [123.21, 0.53], [123.22, 0.54], [123.23, 0.55]
            ],
            'length_km' => 1.8,
            'route_type' => 'emergency',
            'capacity_per_hour' => 150,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        // Sample evacuation facilities
        EvacuationFacility::create([
            'name' => 'SD Negeri Bone 1',
            'description' => 'Primary evacuation center located in Bone village.',
            'facility_type' => 'school',
            'point_coordinates' => [123.19, 0.49],
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
            'name' => 'Masjid Al-Ikhlas',
            'description' => 'Mosque serving as evacuation center with basic facilities.',
            'facility_type' => 'mosque',
            'point_coordinates' => [123.17, 0.47],
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
            'name' => 'Balai Desa Bone',
            'description' => 'Village hall equipped for emergency situations.',
            'facility_type' => 'government_building',
            'point_coordinates' => [123.15, 0.45],
            'capacity' => 150,
            'address' => 'Jl. Pemuda No. 5, Bone, Bone Bolango',
            'contact_person' => 'Kepala Desa Bone',
            'contact_phone' => '+6281234567894',
            'has_medical_facility' => false,
            'has_food_storage' => false,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        // Sample aid distribution points
        AidDistributionPoint::create([
            'name' => 'Posko Bantuan Desa Bone',
            'description' => 'Main aid distribution point for Bone village.',
            'aid_type' => 'mixed',
            'point_coordinates' => [123.18, 0.48],
            'address' => 'Jl. Bantuan No. 10, Bone, Bone Bolango',
            'contact_person' => 'Ibu Siti',
            'contact_phone' => '+6281234567895',
            'capacity_per_day' => 500,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        AidDistributionPoint::create([
            'name' => 'Puskesmas Bone',
            'description' => 'Health center providing medical aid.',
            'aid_type' => 'medical',
            'point_coordinates' => [123.16, 0.46],
            'address' => 'Jl. Kesehatan No. 3, Bone, Bone Bolango',
            'contact_person' => 'Dr. Fatimah',
            'contact_phone' => '+6281234567896',
            'capacity_per_day' => 200,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        AidDistributionPoint::create([
            'name' => 'Gudang Logistik BPBD',
            'description' => 'BPBD logistics warehouse for emergency supplies.',
            'aid_type' => 'food',
            'point_coordinates' => [123.20, 0.50],
            'address' => 'Jl. Logistik No. 1, Bone, Bone Bolango',
            'contact_person' => 'Pak Joko',
            'contact_phone' => '+6281234567897',
            'capacity_per_day' => 1000,
            'is_accessible' => true,
            'is_active' => true,
        ]);
    }
}
