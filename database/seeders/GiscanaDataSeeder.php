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
            'email' => 'staff@giscana.local',
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
            123.2070090488328,
            0.37848842897793133
        ],
        [
            123.207395845135,
            0.37865659761217785
        ],
        [
            123.20808535158405,
            0.3783370772048755
        ],
        [
            123.20870758911286,
            0.3781352748361826
        ],
        [
            123.20904393372189,
            0.3778830218684987
        ],
        [
            123.20948118171452,
            0.377462600240591
        ],
        [
            123.20946436448486,
            0.3771430797893771
        ],
        [
            123.20904393372189,
            0.3768235593264393
        ],
        [
            123.20856462132303,
            0.37680024489897335
        ],
        [
            123.20828008766956,
            0.3769353954610608
        ],
        [
            123.20817338754682,
            0.3770420932729195
        ],
        [
            123.20756875352988,
            0.3775542427471521
        ],
        [
            123.2070067995615,
            0.37849318337418936
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
            123.26951353046944,
            0.37884733350300337
        ],
        [
            123.28218716059575,
            0.3850770812959894
        ],
        [
            123.28663727376016,
            0.38032956096917303
        ],
        [
            123.29118472681398,
            0.3760771536035037
        ],
        [
            123.28111698382496,
            0.3726016802589811
        ],
        [
            123.27646372146684,
            0.3686179726272627
        ],
        [
            123.2717941672999,
            0.368507931195694
        ],
        [
            123.26950645799565,
            0.3697982700158491
        ],
        [
            123.26492552138996,
            0.37128483907936527
        ],
        [
            123.26712377868417,
            0.37546863892517024
        ],
        [
            123.26971286894953,
            0.3789463729096809
        ],
        [
            123.2695135803706,
            0.37882995504054406
        ]
            ],
            'area_hectares' => 85.2,
            'affected_population' => 1200,
            'is_active' => true,
        ]);

        // DisasterZone::create([
        //     'name' => 'Low Risk Coastal Area',
        //     'disaster_type' => 'banjir',
        //     'description' => 'Low-risk coastal area with minimal banjir risk.',
        //     'risk_level' => 'low',
        //     'polygon_coordinates' => [
        //         [
        //             [123.10, 0.40], [123.12, 0.40], [123.12, 0.42], [123.10, 0.42], [123.10, 0.40]
        //         ]
        //     ],
        //     'area_hectares' => 45.8,
        //     'affected_population' => 800,
        //     'is_active' => true,
        // ]);

        // Sample evacuation routes
        EvacuationRoute::create([
            'name' => 'Secondary Route - Village Path',
            'description' => 'Secondary evacuation route through village paths.',
            'disaster_type' => 'banjir',
            'line_coordinates' => [
               [123.27274354360497,0.32568160865849904], [123.27333441180394,0.32810857616267697], [123.27329629127473,0.3285787216328657]
            ],
            'length_km' => 2.1,
            'route_type' => 'secondary',
            'capacity_per_hour' => 200,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        EvacuationRoute::create([
            'name' => 'Primary Evacuation Route - Main Road',
            'description' => 'Main evacuation route connecting high-risk areas to evacuation centers.',
            'disaster_type' => 'other',
            'line_coordinates' => [
                [
            123.28008744958447,
            0.32173069129049736
          ],
          [
            123.28015140297714,
            0.3216027865242239
          ],
          [
            123.28018693263726,
            0.3214393526544086
          ],
          [
            123.2801158733148,
            0.3211906489323013
          ],
          [
            123.28005191992446,
            0.32087088699624644
          ],
          [
            123.27979610635828,
            0.3204090086274647
          ],
          [
            123.27977478856224,
            0.32019583398793827
          ],
          [
            123.27974636483196,
            0.31984764873459426
          ],
          [
            123.27976057669827,
            0.319584733330899
          ],
          [
            123.27989558941107,
            0.3192720771675823
          ],
          [
            123.28023667416363,
            0.31907311414806827
          ],
          [
            123.28056354705217,
            0.3190091617491788
          ],
          [
            123.2805493351882,
            0.3192720771675823
          ],
          [
            123.28054222925607,
            0.31951367511246076
          ],
          [
            123.28054222925607,
            0.31982633126952464
          ],
          [
            123.2806630301049,
            0.3200608233813682
          ],
          [
            123.2807269834953,
            0.3202313630942797
          ],
          [
            123.2808477843464,
            0.32043743191221097
          ]
            ],
            'length_km' => 3.2,
            'route_type' => 'primary',
            'capacity_per_hour' => 500,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        EvacuationRoute::create([
            'name' => 'Emergency Route - Hillside',
            'description' => 'Emergency evacuation route for longsor-prone areas.',
            'line_coordinates' => [
    [
        123.24333913601816,
        0.3519398498250865
    ],
    [
        123.24432902864044,
        0.3518498612835259
    ],
    [
        123.24486897007154,
        0.35179361844471657
    ],
    [
        123.24518393590591,
        0.3516248899271801
    ],
    [
        123.245678882218,
        0.35144491283801926
    ],
    [
        123.24606134073171,
        0.35135492429174064
    ],
    [
        123.24623007242826,
        0.351073710079433
    ],
    [
        123.24663502850068,
        0.35103996437410956
    ],
    [
        123.24732164250496,
        0.3510036620480719
    ],
    [
        123.24741163274284,
        0.35075619353349907
    ],
    [
        123.24754661812584,
        0.35058746497502113
    ],
    [
        123.24815405223428,
        0.3505312221286232
    ],
    [
        123.2481877986387,
        0.35036249354658366
    ],
    [
        123.24895271566449,
        0.34987880504563407
    ],
    [
        123.24914394492134,
        0.35003628502531114
    ],
    [
        123.24980762292984,
        0.34986755647604184
    ],
    [
        123.24996510584714,
        0.3496988279229072
    ],
    [
        123.25014508632466,
        0.3496088393598171
    ],
    [
        123.25075252043324,
        0.3495638450783787
    ],
    [
        123.25107873504884,
        0.34933887366621263
    ],
    [
        123.25146119356252,
        0.34940636509077194
    ]
            ],
            'length_km' => 1.8,
            'route_type' => 'emergency',
            'capacity_per_hour' => 150,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        // Sample evacuation facilities
        EvacuationFacility::create([
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
            'name' => 'Masjid Al-Ikhlas',
            'description' => 'Mosque serving as evacuation center with basic facilities.',
            'point_coordinates' => [123.2514177981202,0.3494345195302344],
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
            'point_coordinates' => [123.2514177981202,0.3494345195302344],
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
