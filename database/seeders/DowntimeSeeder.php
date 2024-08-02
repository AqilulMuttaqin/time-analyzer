<?php

namespace Database\Seeders;

use App\Models\Downtime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DowntimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Downtime::factory()->count(1000)->create();
    }
}
