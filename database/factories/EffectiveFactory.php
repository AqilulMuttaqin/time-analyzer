<?php

namespace Database\Factories;

use App\Models\Effective;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Effective>
 */
class EffectiveFactory extends Factory
{
    protected $model = Effective::class;

    public function definition()
    {
        $tanggal = $this->faker->dateTimeBetween('2024-06-01', '2024-07-31')->format('Y-m-d');

        return [
            'tanggal' => $tanggal,
            'week' => $this->faker->numberBetween(1, 5),
            'shift' => $this->faker->randomElement(['A', 'B']),
            'standart' => $this->faker->randomFloat(2, 1, 200),
            'indirect' => $this->faker->randomFloat(2, 0.1, 400),
            'overtime' => $this->faker->randomFloat(2, 0, 2),
            'reguler_eh' => $this->faker->randomFloat(2, 3.1, 8),
            'id_subgolongan' => \App\Models\Subgolongan::inRandomOrder()->first()->id,
        ];
    }
}
