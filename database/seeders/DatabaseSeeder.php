<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SectionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(DowntimecodeSeeder::class);
        $this->call(GolonganSeeder::class);
        $this->call(SubgolonganSeeder::class);
        $this->call(DowntimeSeeder::class);
        $this->call(EffectiveSeeder::class);
    }
}
