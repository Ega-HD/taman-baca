<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // // 1. Akun Super Admin
        // User::create([
        //     'role' => 'super_admin',
        //     'username' => 'superadmin',
        //     'password' => Hash::make('password123'), // Password akan di-enkripsi
        //     'nama_lengkap' => 'Super Administrator',
        //     'no_hp' => '081234567890',
        // ]);

        // 2. Akun Admin Biasa
        User::create([
            'role' => 'admin',
            'username' => 'admin',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Admin Taman Baca',
            'no_hp' => '081234567891',
        ]);

        // 3. Akun Pengunjung (Member)
        User::create([
            'role' => 'pengunjung',
            'username' => 'p',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Siswa Assyfa',
            'alamat' => 'Jl. Pendidikan No. 1',
            'no_hp' => '081234567892',
        ]);
    }
}
