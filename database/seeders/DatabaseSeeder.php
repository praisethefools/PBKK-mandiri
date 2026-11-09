<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Eksekusi Seeder Role & Permission Spatie
        $this->call(RoleAndPermissionSeeder::class);

        // 2. Akun Uji Coba (Auto-Login Test Accounts untuk Evaluasi Dosen/Asdos)
        // Akun Admin (Akses Penuh)
        $admin = User::firstOrCreate(
            ['email' => 'admin@akademik.its.ac.id'],
            [
                'name' => 'Administrator Utama (Akses Penuh)',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        // Akun Staf (Akses Terbatas - Tanpa Hak Hapus)
        $staf = User::firstOrCreate(
            ['email' => 'staf@akademik.its.ac.id'],
            [
                'name' => 'Staf Tata Usaha FTEIC (Akses Terbatas)',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $staf->syncRoles(['staf']);

        // Akun Dosen (Peran Akademik)
        $dosen = User::firstOrCreate(
            ['email' => 'dosen@akademik.its.ac.id'],
            [
                'name' => 'Dr. Bambang Setiawan, S.Kom., M.Kom.',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $dosen->syncRoles(['dosen']);

        // Akun Mahasiswa / User Publik
        $mahasiswa = User::firstOrCreate(
            ['email' => 'mahasiswa@student.its.ac.id'],
            [
                'name' => 'Abdurrahman Arrafi (Mahasiswa)',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $mahasiswa->syncRoles(['mahasiswa']);

        // 3. Siram Data Dummy Realistis (Departemen, Dosen, Mahasiswa, Tugas)
        $this->call(AcademicDataSeeder::class);
    }
}
