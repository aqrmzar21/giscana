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
        $roleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $roleAdmin->syncPermissions(Permission::all());

        // 3. Role Staff -> Akses Operasional CRUD Lengkap (Create, Read, Update, Delete & Export)
        $roleStaff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $roleStaff->syncPermissions([
            'create data',
            'read data',
            'update data',
            'delete data',
            'export data',
        ]);

        // 4. Role Pimpinan -> HANYA Read & Export (Read-Only & Print)
        $rolePimpinan = Role::firstOrCreate(['name' => 'pimpinan', 'guard_name' => 'web']);
        $rolePimpinan->syncPermissions(['read data', 'export data']);

        // 5. Role Public -> Akses Publik (Tanpa izin operasional backend)
        $rolePublic = Role::firstOrCreate(['name' => 'public', 'guard_name' => 'web']);
        $rolePublic->syncPermissions([]);

        // 6. Sinkronkan role pengguna berdasarkan kolom `role` di tabel users
        $users = User::all();
        foreach ($users as $user) {
            if (!empty($user->role)) {
                $user->syncRoles([$user->role]);
            }
        }
    }
}
