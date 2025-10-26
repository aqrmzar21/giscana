<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\DisasterZone;
use App\Models\EvacuationRoute;
use App\Models\EvacuationFacility;
use App\Models\AidDistributionPoint;

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
            'name' => 'Test Flood Zone',
            'disaster_type' => 'flood',
            'description' => 'Test flood zone for testing',
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
            'name' => 'Test Flood Zone',
            'disaster_type' => 'flood',
            'risk_level' => 'high',
        ]);

        // Test GeoJSON conversion
        $geoJson = $zone->toGeoJSON();
        $this->assertEquals('Feature', $geoJson['type']);
        $this->assertEquals('Polygon', $geoJson['geometry']['type']);
        $this->assertEquals('Test Flood Zone', $geoJson['properties']['name']);
    }

    /**
     * Test evacuation route creation and retrieval
     */
    public function test_evacuation_route_crud(): void
    {
        $route = EvacuationRoute::create([
            'name' => 'Test Evacuation Route',
            'description' => 'Test route for testing',
            'disaster_type' => 'flood',
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
            'disaster_type' => 'flood',
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
     * Test aid distribution point creation and retrieval
     */
    public function test_aid_distribution_point_crud(): void
    {
        $aidPoint = AidDistributionPoint::create([
            'name' => 'Test Aid Point',
            'description' => 'Test aid point for testing',
            'aid_type' => 'food',
            'point_coordinates' => [123.0, 0.0],
            'address' => 'Test Address',
            'contact_person' => 'Test Person',
            'contact_phone' => '+6281234567890',
            'capacity_per_day' => 500,
            'is_accessible' => true,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('aid_distribution_points', [
            'name' => 'Test Aid Point',
            'aid_type' => 'food',
        ]);

        // Test GeoJSON conversion
        $geoJson = $aidPoint->toGeoJSON();
        $this->assertEquals('Feature', $geoJson['type']);
        $this->assertEquals('Point', $geoJson['geometry']['type']);
    }

    /**
     * Test map data API endpoint
     */
    public function test_map_data_api(): void
    {
        // Create test data
        DisasterZone::create([
            'name' => 'Test Zone',
            'disaster_type' => 'flood',
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
            'aid_distribution_points' => [
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
            'name' => 'Flood Zone Test',
            'disaster_type' => 'flood',
            'description' => 'Test flood zone',
            'risk_level' => 'high',
            'polygon_coordinates' => [[[123.0, 0.0], [123.1, 0.0], [123.1, 0.1], [123.0, 0.1], [123.0, 0.0]]],
            'is_active' => true,
        ]);

        $response = $this->get('/map/search?q=flood');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'results'
        ]);
    }
}
