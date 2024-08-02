<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubgolonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Subgolongan untuk Golongan 1T
            ['id' => 1, 'nama' => '1T-1', 'id_golongan' => 1],
            ['id' => 2, 'nama' => '1T-2', 'id_golongan' => 1],

            // Subgolongan untuk Golongan 2T
            ['id' => 3, 'nama' => '2T-1', 'id_golongan' => 2],
            ['id' => 4, 'nama' => '2T-2', 'id_golongan' => 2],
            ['id' => 5, 'nama' => '2T-3', 'id_golongan' => 2],

            // Subgolongan untuk Golongan 3T
            ['id' => 6, 'nama' => '3T-1', 'id_golongan' => 3],
            ['id' => 7, 'nama' => '3T-2', 'id_golongan' => 3],

            // Subgolongan untuk Golongan 4T
            ['id' => 8, 'nama' => '4T-1', 'id_golongan' => 4],
            ['id' => 9, 'nama' => '4T-2', 'id_golongan' => 4],

            // Subgolongan untuk Golongan 5T
            ['id' => 10, 'nama' => '5T-1', 'id_golongan' => 5],
            ['id' => 11, 'nama' => '5T-2', 'id_golongan' => 5],
            ['id' => 12, 'nama' => '5T-3', 'id_golongan' => 5],

            // Subgolongan untuk Golongan 1B
            ['id' => 13, 'nama' => '1B-1', 'id_golongan' => 6],
            ['id' => 14, 'nama' => '1B-2', 'id_golongan' => 6],

            // Subgolongan untuk Golongan 2B
            ['id' => 15, 'nama' => '2B-1', 'id_golongan' => 7],
            ['id' => 16, 'nama' => '2B-2', 'id_golongan' => 7],
            ['id' => 17, 'nama' => '2B-3', 'id_golongan' => 7],

            // Subgolongan untuk Golongan 3B
            ['id' => 18, 'nama' => '3B-1', 'id_golongan' => 8],
            ['id' => 19, 'nama' => '3B-2', 'id_golongan' => 8],

            // Subgolongan untuk Golongan 4B
            ['id' => 20, 'nama' => '4B-1', 'id_golongan' => 9],
            ['id' => 21, 'nama' => '4B-2', 'id_golongan' => 9],

            // Subgolongan untuk Golongan 5B
            ['id' => 22, 'nama' => '5B-1', 'id_golongan' => 10],
            ['id' => 23, 'nama' => '5B-2', 'id_golongan' => 10],
            ['id' => 24, 'nama' => '5B-3', 'id_golongan' => 10],
        ];

        DB::table('subgolongan')->insert($data);
    }
}
