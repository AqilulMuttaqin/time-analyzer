<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GolonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Lokasi T
            ['id' => 1, 'nama' => 'Golongan 1T', 'lokasi' => 'T'],
            ['id' => 2, 'nama' => 'Golongan 2T', 'lokasi' => 'T'],
            ['id' => 3, 'nama' => 'Golongan 3T', 'lokasi' => 'T'],
            ['id' => 4, 'nama' => 'Golongan 4T', 'lokasi' => 'T'],
            ['id' => 5, 'nama' => 'Golongan 5T', 'lokasi' => 'T'],

            // Lokasi B
            ['id' => 6, 'nama' => 'Golongan 1B', 'lokasi' => 'B'],
            ['id' => 7, 'nama' => 'Golongan 2B', 'lokasi' => 'B'],
            ['id' => 8, 'nama' => 'Golongan 3B', 'lokasi' => 'B'],
            ['id' => 9, 'nama' => 'Golongan 4B', 'lokasi' => 'B'],
            ['id' => 10, 'nama' => 'Golongan 5B', 'lokasi' => 'B'],
        ];

        DB::table('golongan')->insert($data);
    }
}
