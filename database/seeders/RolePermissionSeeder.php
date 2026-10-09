<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Definisikan semua Permission
        $permissions = [
            'create data',
            'read data',
            'update data',
            'delete data',
            'export data',
            'manage staff',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Role Admin -> Akses Penuh (Semua Permission)
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->givePermissionTo(Permission::all());

        // 3. Role Staff -> Create & Read & Export
        $roleStaff = Role::firstOrCreate(['name' => 'staff']);
        $roleStaff->syncPermissions(['create data', 'read data', 'export data']);

        // 4. Role Pimpinan -> HANYA Read & Export (Read-Only & Print)
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan']);
        $rolePimpinan->syncPermissions(['read data', 'export data']);

        // 5. Sinkronkan role pengguna berdasarkan kolom `role` di tabel users
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role === 'admin') {
                $user->syncRoles(['admin']);
            } elseif ($user->role === 'staff') {
                $user->syncRoles(['staff']);
            } elseif ($user->role === 'pimpinan') {
                $user->syncRoles(['pimpinan']);
            }
        }
    }
}
