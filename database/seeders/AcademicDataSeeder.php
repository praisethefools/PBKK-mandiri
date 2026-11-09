<?php

namespace Database\Seeders;

use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Tugas;
use Illuminate\Database\Seeder;

class AcademicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Departemen di Fakultas FTEIC ITS
        $departemensData = [
            ['kode' => 'IF', 'nama' => 'Teknik Informatika', 'fakultas' => 'FTEIC'],
            ['kode' => 'SI', 'nama' => 'Sistem Informasi', 'fakultas' => 'FTEIC'],
            ['kode' => 'TE', 'nama' => 'Teknik Elektro', 'fakultas' => 'FTEIC'],
            ['kode' => 'TI', 'nama' => 'Teknologi Informasi', 'fakultas' => 'FTEIC'],
            ['kode' => 'BME', 'nama' => 'Teknik Biomedik', 'fakultas' => 'FTEIC'],
        ];

        $departemens = [];
        foreach ($departemensData as $data) {
            $departemens[$data['kode']] = Departemen::firstOrCreate(['kode' => $data['kode']], $data);
        }

        // 2. Dosen Wali FTEIC ITS Realistis
        $dosenList = [
            ['nip' => '198001152005011001', 'nama' => 'Dr. Bambang Setiawan, S.Kom., M.Kom.', 'email' => 'bambang.setiawan@if.its.ac.id', 'dept' => 'IF', 'keahlian' => 'Kecerdasan Buatan & Data Mining'],
            ['nip' => '198203222008121002', 'nama' => 'Prof. Dr. Ir. Siti Aminah, M.T.', 'email' => 'siti.aminah@if.its.ac.id', 'dept' => 'IF', 'keahlian' => 'Rekayasa Perangkat Lunak'],
            ['nip' => '197906102003121001', 'nama' => 'Ahmad Fauzi, S.T., M.Sc., Ph.D.', 'email' => 'ahmad.fauzi@if.its.ac.id', 'dept' => 'IF', 'keahlian' => 'Komputasi Berbasis Jaringan'],
            ['nip' => '198509142010122001', 'nama' => 'Dwi Handayani, S.Kom., M.Cs.', 'email' => 'dwi.handayani@si.its.ac.id', 'dept' => 'SI', 'keahlian' => 'Sistem Informasi Enterprise & ERP'],
            ['nip' => '197511032002121003', 'nama' => 'Ir. Hendra Wijaya, M.Eng.', 'email' => 'hendra.wijaya@si.its.ac.id', 'dept' => 'SI', 'keahlian' => 'Tata Kelola TI & Manajemen Data'],
            ['nip' => '198802202014041001', 'nama' => 'Rizky Pratama, S.T., M.T.', 'email' => 'rizky.pratama@te.its.ac.id', 'dept' => 'TE', 'keahlian' => 'Sistem Tertanam & Robotika Cerdas'],
            ['nip' => '198307082008012002', 'nama' => 'Dr. Maya Kartika, S.T., M.Sc.', 'email' => 'maya.kartika@ti.its.ac.id', 'dept' => 'TI', 'keahlian' => 'Keamanan Siber & Forensik Digital'],
            ['nip' => '199004122019031004', 'nama' => 'Bayu Wicaksono, S.Kom., M.T.', 'email' => 'bayu.wicaksono@if.its.ac.id', 'dept' => 'IF', 'keahlian' => 'Algoritma & Grafika Komputer'],
        ];

        $createdDosens = [];
        foreach ($dosenList as $d) {
            $createdDosens[] = Dosen::firstOrCreate(
                ['nip' => $d['nip']],
                [
                    'departemen_id' => $departemens[$d['dept']]->id,
                    'nama' => $d['nama'],
                    'email' => $d['email'],
                    'keahlian' => $d['keahlian'],
                ]
            );
        }

        // Tambah dosen via Faker generator agar puluhan data dummy
        for ($i = 0; $i < 12; $i++) {
            $dept = fake()->randomElement($departemens);
            $createdDosens[] = Dosen::factory()->create([
                'departemen_id' => $dept->id,
            ]);
        }

        // 3. Mahasiswa Dummy Realistis (Puluhan data via Faker Generator)
        $namaSampel = [
            'Abdurrahman Arrafi Ravsan Zarnadi',
            'Muhammad Raihan Fadhillah',
            'Adinda Putri Maharani',
            'Bagas Tri Wicaksono',
            'Clara Stephanie Hutapea',
            'Dimas Arya Nugraha',
            'Elvira Damayanti',
            'Farhan Ramadhan',
            'Grace Nathania Siregar',
            'Hafizh Al-Ghifari',
            'Intan Permatasari',
            'Jonathan Kevin Prasetya',
            'Kirana Dewi Anjani',
            'Lukman Hakim Maulana',
            'Nadya Ayu Lestari',
        ];

        $ifDept = $departemens['IF'];
        $dosenIf = collect($createdDosens)->where('departemen_id', $ifDept->id)->first();

        // Akun mahasiswa referensi user
        Mahasiswa::firstOrCreate(
            ['nrp' => '5025241241'],
            [
                'departemen_id' => $ifDept->id,
                'dosen_wali_id' => $dosenIf?->id,
                'nama' => 'Abdurrahman Arrafi Ravsan Zarnadi',
                'email' => '5025241241@student.its.ac.id',
                'angkatan' => 2024,
                'ipk' => 3.92,
                'is_active' => true,
            ]
        );

        foreach ($namaSampel as $index => $nama) {
            $dept = fake()->randomElement($departemens);
            $dosenWali = collect($createdDosens)->where('departemen_id', $dept->id)->random() ?? $createdDosens[0];
            $angkatan = fake()->randomElement([2021, 2022, 2023, 2024]);
            $prefix = match ($dept->kode) {
                'IF' => '5025',
                'SI' => '5026',
                'TE' => '5022',
                'TI' => '5027',
                default => '5028',
            };
            $nrp = $prefix.substr((string) $angkatan, -2).'1'.str_pad((string) ($index + 10), 3, '0', STR_PAD_LEFT);

            Mahasiswa::firstOrCreate(
                ['nrp' => $nrp],
                [
                    'departemen_id' => $dept->id,
                    'dosen_wali_id' => $dosenWali->id,
                    'nama' => $nama,
                    'email' => strtolower(str_replace(' ', '.', substr($nama, 0, 15))).'@student.its.ac.id',
                    'angkatan' => $angkatan,
                    'ipk' => fake()->randomFloat(2, 3.10, 3.98),
                    'is_active' => fake()->boolean(85),
                ]
            );
        }

        // Siram 45 mahasiswa tambahan menggunakan Faker
        for ($i = 0; $i < 45; $i++) {
            $dept = fake()->randomElement($departemens);
            $dosenWali = collect($createdDosens)->where('departemen_id', $dept->id)->random() ?? $createdDosens[0];

            Mahasiswa::factory()->create([
                'departemen_id' => $dept->id,
                'dosen_wali_id' => $dosenWali->id,
            ]);
        }

        // 4. Tugas Kuliah Realistis
        $tugasList = [
            [
                'judul' => 'Tugas 4: Integrasi Dashboard Backoffice & Otorisasi RBAC Spatie',
                'mata_kuliah' => 'Pemrograman Berbasis Kerangka Kerja',
                'deskripsi' => 'Pengembangan panel admin dengan AdminLTE, Spatie Permission, dan query when().',
                'deadline' => now()->addDays(7)->format('Y-m-d'),
                'status' => 'Aktif',
                'dept' => 'IF',
            ],
            [
                'judul' => 'Tugas 3: Algoritma Pencarian Rute Terpendek (A* & Dijkstra)',
                'mata_kuliah' => 'Desain & Analisis Algoritma',
                'deskripsi' => 'Simulasi graf navigasi kampus ITS dengan benchmarking memori.',
                'deadline' => now()->addDays(14)->format('Y-m-d'),
                'status' => 'Aktif',
                'dept' => 'IF',
            ],
            [
                'judul' => 'Proyek Akhir: Perancangan Arsitektur Enterprise BPMN',
                'mata_kuliah' => 'Sistem Informasi Enterprise',
                'deskripsi' => 'Pemodelan rantai pasok dan sistem akuntansi terintegrasi.',
                'deadline' => now()->addDays(20)->format('Y-m-d'),
                'status' => 'Aktif',
                'dept' => 'SI',
            ],
            [
                'judul' => 'Laporan Praktikum: Keamanan Protokol TLS dan Mitigasi MITM',
                'mata_kuliah' => 'Keamanan Jaringan & Informasi',
                'deskripsi' => 'Analisis packet sniffing Wireshark dan implementasi enkripsi asimetris.',
                'deadline' => now()->addDays(5)->format('Y-m-d'),
                'status' => 'Aktif',
                'dept' => 'TI',
            ],
            [
                'judul' => 'Tugas 1: Pengenalan Sintaks PHP 8.5 dan MVC Dasar',
                'mata_kuliah' => 'Pemrograman Berbasis Kerangka Kerja',
                'deskripsi' => 'Eksplorasi routing, blade templating, dan controller sederhana.',
                'deadline' => now()->subDays(10)->format('Y-m-d'),
                'status' => 'Selesai',
                'dept' => 'IF',
            ],
        ];

        foreach ($tugasList as $t) {
            $dept = $departemens[$t['dept']];
            $dosen = collect($createdDosens)->where('departemen_id', $dept->id)->first() ?? $createdDosens[0];

            Tugas::firstOrCreate(
                ['judul' => $t['judul']],
                [
                    'departemen_id' => $dept->id,
                    'dosen_id' => $dosen->id,
                    'mata_kuliah' => $t['mata_kuliah'],
                    'deskripsi' => $t['deskripsi'],
                    'deadline' => $t['deadline'],
                    'status' => $t['status'],
                ]
            );
        }

        // Siram 20 tugas tambahan via Faker
        for ($i = 0; $i < 20; $i++) {
            $dept = fake()->randomElement($departemens);
            $dosen = collect($createdDosens)->where('departemen_id', $dept->id)->random() ?? $createdDosens[0];

            Tugas::factory()->create([
                'departemen_id' => $dept->id,
                'dosen_id' => $dosen->id,
            ]);
        }
    }
}
