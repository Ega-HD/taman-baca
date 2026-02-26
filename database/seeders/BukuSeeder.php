<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Buku;
use Carbon\Carbon;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        Buku::create([
            'judul_buku' => 'Belajar Membaca Jilid 1',
            'tgl_ditambahkan' => Carbon::now(),
            'status_buku' => 'Tersedia',
            'asal_buku' => 'Baru',
            'penulis' => 'Budi Santoso',
            'penerbit' => 'Erlangga',
        ]);

        Buku::create([
            'judul_buku' => 'Kisah Nabi dan Rasul',
            'tgl_ditambahkan' => Carbon::now(),
            'status_buku' => 'Tersedia',
            'asal_buku' => 'Donasi',
            'penulis' => 'Tim Redaksi',
            'penerbit' => 'Mizan',
        ]);

        Buku::create([
            'judul_buku' => 'Mengenal Angka dan Huruf',
            'tgl_ditambahkan' => Carbon::now(),
            'status_buku' => 'Dipinjam', // Contoh buku yang sedang dipinjam
            'asal_buku' => 'Baru',
        ]);
    }
}