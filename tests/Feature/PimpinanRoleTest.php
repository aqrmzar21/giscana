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
    protected function setUp(): void
    {
        parent::setUp();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }
    /** @test */
    public function pimpinan_can_view_aid_disasters_index()
    {
        $pimpinan = User::where('role', 'pimpinan')->first() ?? User::factory()->create(['role' => 'pimpinan', 'is_active' => true]);
        if (!$pimpinan->hasRole('pimpinan')) {
            $pimpinan->assignRole('pimpinan');
        }

        $response = $this->actingAs($pimpinan)->get(route('admin.aid-disasters.index'));
        $response->assertStatus(200);
    }

    /** @test */
    public function pimpinan_cannot_access_create_aid_disaster_page()
    {
        $pimpinan = User::where('role', 'pimpinan')->first() ?? User::factory()->create(['role' => 'pimpinan', 'is_active' => true]);
        if (!$pimpinan->hasRole('pimpinan')) {
            $pimpinan->assignRole('pimpinan');
        }

        $response = $this->actingAs($pimpinan)->get(route('admin.aid-disasters.create'));
        $response->assertStatus(403);
    }

    /** @test */
    public function pimpinan_cannot_store_aid_disaster()
    {
        $pimpinan = User::where('role', 'pimpinan')->first() ?? User::factory()->create(['role' => 'pimpinan', 'is_active' => true]);
        if (!$pimpinan->hasRole('pimpinan')) {
            $pimpinan->assignRole('pimpinan');
        }

        $response = $this->actingAs($pimpinan)->post(route('admin.aid-disasters.store'), [
            'disaster_name' => 'Banjir Test Pimpinan',
            'disaster_type' => 'Banjir',
            'district_name' => 'Kabila',
            'village_name' => 'Dutohe',
            'date' => now()->toDateString(),
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function pimpinan_cannot_delete_aid_disaster()
    {
        $pimpinan = User::where('role', 'pimpinan')->first() ?? User::factory()->create(['role' => 'pimpinan', 'is_active' => true]);
        if (!$pimpinan->hasRole('pimpinan')) {
            $pimpinan->assignRole('pimpinan');
        }

        $disaster = \App\Models\AidDisaster::create([
            'disaster_name' => 'Disaster Delete Test',
            'disaster_type' => 'Longsor',
            'district_name' => 'Suwawa',
            'village_name' => 'Bubeya',
            'date' => now()->toDateString(),
        ]);

        $response = $this->actingAs($pimpinan)->delete(route('admin.aid-disasters.destroy', $disaster));

        $response->assertStatus(403);
        $this->assertDatabaseHas('aid_disasters', ['id' => $disaster->id]);
    }
}
