<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id' => 1, 'nama' => 'Human Resources'],
            ['id' => 2, 'nama' => 'Finance and Accounting'],
            ['id' => 3, 'nama' => 'Marketing'],
            ['id' => 4, 'nama' => 'Sales'],
            ['id' => 5, 'nama' => 'Operations'],
            ['id' => 6, 'nama' => 'Production'],
            ['id' => 7, 'nama' => 'Information Technology'],
            ['id' => 8, 'nama' => 'Customer Service'],
            ['id' => 9, 'nama' => 'Legal'],
            ['id' => 10, 'nama' => 'Product Development'],
        ];

        DB::table('section')->insert($data);
    }
}
