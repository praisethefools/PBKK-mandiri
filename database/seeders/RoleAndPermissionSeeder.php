<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Inisialisasi Permissions
        $permissions = [
            'lihat-dashboard',
            'kelola-mahasiswa',
            'hapus-mahasiswa',
            'kelola-dosen',
            'hapus-dosen',
            'kelola-tugas',
            'hapus-tugas',
            'kelola-nilai',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Inisialisasi Peran Utama (Slide 39: minimal 'Admin' dan 'Staf')
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleStaf = Role::firstOrCreate(['name' => 'staf']);
        $roleDosen = Role::firstOrCreate(['name' => 'dosen']);
        $roleMahasiswa = Role::firstOrCreate(['name' => 'mahasiswa']);

        // 3. Hak Akses Admin (Akses Penuh - Hak Eksklusif Hapus Data)
        $roleAdmin->syncPermissions(Permission::all());

        // 4. Hak Akses Staf (Akses Terbatas - Tanpa Izin Hapus Data)
        $roleStaf->syncPermissions([
            'lihat-dashboard',
            'kelola-mahasiswa',
            'kelola-dosen',
            'kelola-tugas',
        ]);

        // 5. Hak Akses Dosen
        $roleDosen->syncPermissions([
            'lihat-dashboard',
            'kelola-tugas',
            'kelola-nilai',
        ]);
    }
}
