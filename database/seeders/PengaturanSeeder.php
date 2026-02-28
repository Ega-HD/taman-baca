<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pengaturan')->insert([
            'denda_per_hari' => 1000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
