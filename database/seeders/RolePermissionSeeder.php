<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            'create data',
            'read data',
            'update data',
            'delete data',
            'manage staff',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission]);
        }

        // create roles and assign created permissions
        $roleAdmin = \Spatie\Permission\Models\Role::create(['name' => 'admin']);
        $roleAdmin->givePermissionTo(\Spatie\Permission\Models\Permission::all());

        $roleStaff = \Spatie\Permission\Models\Role::create(['name' => 'staff']);
        $roleStaff->givePermissionTo(['create data', 'read data']);

        // Sync existing users
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            if ($user->role === 'admin') {
                $user->assignRole('admin');
            } elseif ($user->role === 'staff') {
                $user->assignRole('staff');
            }
        }
    }
}
