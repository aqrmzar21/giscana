<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\DisasterZone;
use App\Models\EvacuationRoute;
use App\Models\EvacuationFacility;
use App\Models\AidDisaster;

class SpatialDataTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test disaster zone creation and retrieval
     */
    public function test_disaster_zone_crud(): void
    {
        // Create a disaster zone
        $zone = DisasterZone::create([
            'name' => 'Test banjir Zone',
            'disaster_type' => 'banjir',
            'description' => 'Test banjir zone for testing',
            'risk_level' => 'high',
            'polygon_coordinates' => [
                [
                    [123.0, 0.0], [123.1, 0.0], [123.1, 0.1], [123.0, 0.1], [123.0, 0.0]
                ]
            ],
            'area_hectares' => 100.0,
            'affected_population' => 1000,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('disaster_zones', [
            'name' => 'Test banjir Zone',
            'disaster_type' => 'banjir',
            'risk_level' => 'high',
        ]);

        // Test GeoJSON conversion
        $geoJson = $zone->toGeoJSON();
        $this->assertEquals('Feature', $geoJson['type']);
        $this->assertEquals('Polygon', $geoJson['geometry']['type']);
        $this->assertEquals('Test banjir Zone', $geoJson['properties']['name']);
    }

    /**
     * Test evacuation route creation and retrieval
     */
    public function test_evacuation_route_crud(): void
    {
        $route = EvacuationRoute::create([
            'name' => 'Test Evacuation Route',
            'description' => 'Test route for testing',
            'disaster_type' => 'banjir',
            'line_coordinates' => [
                [123.0, 0.0], [123.1, 0.1], [123.2, 0.2]
            ],
            'length_km' => 2.5,
            'route_type' => 'primary',
            'capacity_per_hour' => 300,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('evacuation_routes', [
            'name' => 'Test Evacuation Route',
            'disaster_type' => 'banjir',
            'route_type' => 'primary',
        ]);

        // Test GeoJSON conversion
        $geoJson = $route->toGeoJSON();
        $this->assertEquals('Feature', $geoJson['type']);
        $this->assertEquals('LineString', $geoJson['geometry']['type']);
    }

    /**
     * Test evacuation facility creation and retrieval
     */
    public function test_evacuation_facility_crud(): void
    {
        $facility = EvacuationFacility::create([
            'name' => 'Test Evacuation Center',
            'description' => 'Test facility for testing',
            'facility_type' => 'school',
            'point_coordinates' => [123.0, 0.0],
            'capacity' => 200,
            'address' => 'Test Address',
            'contact_person' => 'Test Person',
            'contact_phone' => '+6281234567890',
            'has_medical_facility' => true,
            'has_food_storage' => true,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('evacuation_facilities', [
            'name' => 'Test Evacuation Center',
            'facility_type' => 'school',
        ]);

        // Test GeoJSON conversion
        $geoJson = $facility->toGeoJSON();
        $this->assertEquals('Feature', $geoJson['type']);
        $this->assertEquals('Point', $geoJson['geometry']['type']);
    }

    /**
     * Test aid disaster creation and retrieval
     */
    public function test_aid_disaster_crud(): void
    {
        $aidDisaster = AidDisaster::create([
            'nama_kecamatan'          => 'Kecamatan Bone',
            'jumlah_penerima_bantuan' => 500,
            'bantuan_terdistribusi'   => 300,
            'is_active'               => true,
        ]);

        $this->assertDatabaseHas('aid_disasters', [
            'nama_kecamatan' => 'Kecamatan Bone',
            'is_active'      => true,
        ]);

        // Test computed attributes
        $this->assertEquals(200, $aidDisaster->sisa_bantuan);
        $this->assertEquals(60, $aidDisaster->persentase_distribusi);
    }

    /**
     * Test map data API endpoint
     */
    public function test_map_data_api(): void
    {
        // Create test data
        DisasterZone::create([
            'name' => 'Test Zone',
            'disaster_type' => 'banjir',
            'description' => 'Test',
            'risk_level' => 'high',
            'polygon_coordinates' => [[[123.0, 0.0], [123.1, 0.0], [123.1, 0.1], [123.0, 0.1], [123.0, 0.0]]],
            'is_active' => true,
        ]);

        $response = $this->get('/map/data');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'disaster_zones' => [
                'type',
                'features'
            ],
            'evacuation_routes' => [
                'type',
                'features'
            ],
            'evacuation_facilities' => [
                'type',
                'features'
            ],
            'aid_disasters' => [
                'type',
                'features'
            ],
        ]);
    }

    /**
     * Test search functionality
     */
    public function test_search_api(): void
    {
        // Create test data
        DisasterZone::create([
            'name' => 'banjir Zone Test',
            'disaster_type' => 'banjir',
            'description' => 'Test banjir zone',
            'risk_level' => 'high',
            'polygon_coordinates' => [[[123.0, 0.0], [123.1, 0.0], [123.1, 0.1], [123.0, 0.1], [123.0, 0.0]]],
            'is_active' => true,
        ]);

        $response = $this->get('/map/search?q=banjir');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'results'
        ]);
    }
}
