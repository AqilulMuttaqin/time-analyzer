<?php

namespace Database\Factories;

use App\Models\Downtime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Downtime>
 */
class DowntimeFactory extends Factory
{
    protected $model = Downtime::class;

    public function definition()
    {
        $tanggal = $this->faker->dateTimeBetween('2024-06-01', '2024-07-31')->format('Y-m-d');

        return [
            'tanggal' => $tanggal,
            'week' => $this->faker->numberBetween(1, 5),
            'shift' => $this->faker->randomElement(['A', 'B']),
            'id_subgolongan' => \App\Models\Subgolongan::inRandomOrder()->first()->id,
            'id_downtimecode' => \App\Models\Downtimecode::inRandomOrder()->first()->id,
            'detail' => $this->faker->sentence(),
            'minute' => $this->faker->numberBetween(3, 10),
            'man_hours' => $this->faker->randomFloat(2, 1.0, 12.0),
        ];
    }
}
