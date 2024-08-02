<?php

namespace Database\Seeders;

use App\Models\Effective;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EffectiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Effective::factory()->count(500)->create();
    }
}
