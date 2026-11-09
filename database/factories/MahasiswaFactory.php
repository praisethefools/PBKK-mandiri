<?php

namespace Database\Factories;

use App\Models\Departemen;
use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mahasiswa>
 */
class MahasiswaFactory extends Factory
{
    protected $model = Mahasiswa::class;

    public function definition(): array
    {
        $angkatan = fake()->randomElement([2021, 2022, 2023, 2024]);
        $prefixNrp = match ($angkatan) {
            2021 => '502521',
            2022 => '502522',
            2023 => '502523',
            default => '502524',
        };

        return [
            'departemen_id' => Departemen::factory(),
            'dosen_wali_id' => null,
            'nrp' => $prefixNrp.fake()->unique()->numerify('1###'),
            'nama' => fake()->name(),
            'email' => fake()->unique()->userName().'@student.its.ac.id',
            'angkatan' => $angkatan,
            'ipk' => fake()->randomFloat(2, 2.75, 4.00),
            'is_active' => fake()->boolean(85), // 85% aktif, 15% cuti
        ];
    }
}
