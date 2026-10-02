<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\DisasterZone;
use App\Models\District;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PimpinanRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles & permissions
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    /** @test */
    public function pimpinan_can_view_disaster_zones_index()
    {
        $pimpinan = User::factory()->create([
            'role' => 'pimpinan',
            'is_active' => true,
        ]);
        $pimpinan->assignRole('pimpinan');

        $response = $this->actingAs($pimpinan)->get(route('admin.disaster-zones.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function pimpinan_cannot_access_create_disaster_zone_page()
    {
        $pimpinan = User::factory()->create([
            'role' => 'pimpinan',
            'is_active' => true,
        ]);
        $pimpinan->assignRole('pimpinan');

        $response = $this->actingAs($pimpinan)->get(route('admin.disaster-zones.create'));

        $response->assertStatus(403);
    }

    /** @test */
    public function pimpinan_cannot_store_disaster_zone()
    {
        $pimpinan = User::factory()->create([
            'role' => 'pimpinan',
            'is_active' => true,
        ]);
        $pimpinan->assignRole('pimpinan');

        $district = District::create(['name' => 'Kabila', 'regency' => 'Bone Bolango']);

        $response = $this->actingAs($pimpinan)->post(route('admin.disaster-zones.store'), [
            'name' => 'Test Zone',
            'district_id' => $district->id,
            'disaster_type' => 'banjir',
            'risk_level' => 'high',
            'point_coordinates' => json_encode([123.1, 0.4]),
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function pimpinan_cannot_delete_disaster_zone()
    {
        $pimpinan = User::factory()->create([
            'role' => 'pimpinan',
            'is_active' => true,
        ]);
        $pimpinan->assignRole('pimpinan');

        $zone = DisasterZone::create([
            'name' => 'Zone Test Delete',
            'disaster_type' => 'longsor',
            'risk_level' => 'medium',
            'point_coordinates' => [123.1, 0.4],
        ]);

        $response = $this->actingAs($pimpinan)->delete(route('admin.disaster-zones.destroy', $zone));

        $response->assertStatus(403);
        $this->assertDatabaseHas('disaster_zones', ['id' => $zone->id]);
    }
}
