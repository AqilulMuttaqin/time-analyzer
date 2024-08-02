<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'nik' => '123456',
                'nama' => 'Admin',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'pw' => '123456',
                'id_section' => 1
            ],
            [
                'id' => 2,
                'nik' => '654321',
                'nama' => 'User',
                'password' => Hash::make('654321'),
                'role' => 'user',
                'pw' => '654321',
                'id_section' => 2
            ]
        ];

        DB::table('users')->insert($data);
    }
}
