<?php

namespace Database\Factories;

use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Tugas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tugas>
 */
class TugasFactory extends Factory
{
    protected $model = Tugas::class;

    public function definition(): array
    {
        return [
            'departemen_id' => Departemen::factory(),
            'dosen_id' => Dosen::factory(),
            'judul' => fake()->randomElement([
                'Implementasi RBAC & Multi-Auth pada Portal Akademik',
                'Analisis Kompleksitas Algoritma Dijkstra dan A*',
                'Rancang Bangun RESTful API dengan Sanctum & Swagger',
                'Perancangan Skema Database Normalisasi 3NF Toko Online',
                'Penerapan Clean Architecture & Repository Pattern di Laravel',
                'Eksplorasi Model LLM Agentic untuk Automasi CV & Lowongan Kerja',
                'Pengujian Otomatis Unit & Feature Test dengan Pest & PHPUnit',
            ]),
            'mata_kuliah' => fake()->randomElement([
                'Pemrograman Berbasis Kerangka Kerja',
                'Desain & Analisis Algoritma',
                'Rekayasa Perangkat Lunak',
                'Sistem Manajemen Basis Data',
                'Kecerdasan Artifisial & Pembelajaran Mesin',
                'Jaringan Komputer Lanjut',
            ]),
            'deskripsi' => fake()->paragraph(2),
            'deadline' => fake()->dateTimeBetween('+1 days', '+30 days')->format('Y-m-d'),
            'status' => fake()->randomElement(['Aktif', 'Aktif', 'Aktif', 'Selesai']),
        ];
    }
}
