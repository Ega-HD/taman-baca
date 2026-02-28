<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Buku;
use App\Models\ItemBuku;
use Faker\Factory as Faker;
use Carbon\Carbon;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan Faker dengan format bahasa Indonesia (opsional, agar nama penulis lokal)
        $faker = Faker::create('id_ID');

        // 1. GENERATE 23 KATALOG BUKU UTAMA
        for ($i = 1; $i <= 23; $i++) {
            Buku::create([
                // Membuat judul buku tiruan (sekitar 3 kata)
                'judul_buku' => ucwords($faker->words(3, true)), 
                'penulis' => $faker->name(), // Nama orang acak
                'penerbit' => $faker->company(), // Nama perusahaan acak
                'tahun_terbit' => $faker->numberBetween(2015, 2024), // Tahun acak
            ]);
        }

        // Ambil semua ID dari 23 buku yang baru saja dibuat
        $katalogBukuIds = Buku::pluck('id')->toArray();


        // 2. GENERATE 50 SALINAN FISIK BUKU (ITEM BUKU)
        for ($j = 1; $j <= 50; $j++) {
            
            $buku_id = $faker->randomElement($katalogBukuIds);    
            
            $lastItem = ItemBuku::where('buku_id', $buku_id)->orderBy('id', 'desc')->first();
            $lastNumber = 0;
    
            if($lastItem) {
                $lastCode = $lastItem->kode_buku;
    
                $parts = explode('-', $lastCode);
    
                if (count($parts) == 3) {
                    $lastNumber = (int) $parts[2];
                }
            }

            $lastNumber++;

            // Membuat kode buku berurutan dan unik: PAUD-001, PAUD-002, dst.
            $kodeBuku = 'PAUD-'. str_pad($buku_id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);

            ItemBuku::create([
                // Memilih ID secara acak dari 23 katalog buku yang ada
                'buku_id' => $buku_id,
                'kode_buku' => $kodeBuku,
                'status_buku' => $faker->randomElement(['Tersedia', 'Dipinjam']),
                'asal_buku' => $faker->randomElement(['Baru', 'Donasi']),
                'tgl_ditambahkan' => Carbon::now()->subDays(rand(1, 60)), // Tanggal masuk acak dalam 60 hari terakhir
            ]);
        }
    }
}