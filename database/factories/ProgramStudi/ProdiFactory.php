<?php

namespace Database\Factories\ProgramStudi;

use App\Models\ProgramStudi\Prodi;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProdiFactory extends Factory
{
    protected $model = Prodi::class;

    public function definition(): array
    {
        return [
            'dp_id' => null,
            'kode_pr' => 'PR'.$this->faker->unique()->numberBetween(1, 999),
            'nama_pr' => 'Program Studi '.$this->faker->word(),
            'strata' => $this->faker->randomElement(['Sarjana', 'Magister', 'Doktor']),
        ];
    }
}
