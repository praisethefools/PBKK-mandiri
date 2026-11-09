<?php

namespace Database\Factories;

use App\Models\Departemen;
use App\Models\Dosen;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dosen>
 */
class DosenFactory extends Factory
{
    protected $model = Dosen::class;

    public function definition(): array
    {
        return [
            'departemen_id' => Departemen::factory(),
            'nip' => '19'.fake()->unique()->numerify('########').fake()->numerify('######'),
            'nama' => fake()->name().', S.Kom., M.Kom.',
            'email' => fake()->unique()->safeEmail(),
            'keahlian' => fake()->randomElement([
                'Kecerdasan Buatan & Data Mining',
                'Rekayasa Perangkat Lunak',
                'Komputasi Berbasis Jaringan',
                'Arsitektur & Keamanan Komputer',
                'Grafika, Interaksi & Game',
                'Algoritma & Pemrograman',
            ]),
        ];
    }
}
