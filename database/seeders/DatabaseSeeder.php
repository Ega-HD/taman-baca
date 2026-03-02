<?php

// namespace Database\Seeders;

// use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Illuminate\Database\Seeder;

// class DatabaseSeeder extends Seeder
// {
//     use WithoutModelEvents;

//     /**
//      * Seed the application's database.
//      */
//     public function run(): void
//     {
//         // User::factory(10)->create();

//         // User::factory()->create([
//         //     'name' => 'Test User',
//         //     'email' => 'test@example.com',
//         // ]);

//         // Memanggil seeder yang sudah kita buat
//         $this->call([
//             UserSeeder::class,
//             BukuSeeder::class,
//             PengaturanSeeder::class,
//         ]);
//     }
// }

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Faker\Factory as Faker;

// Panggil Model
use App\Models\User;
use App\Models\Buku;
use App\Models\ItemBuku;
use App\Models\TransaksiPeminjaman;
use App\Models\Pengaturan;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('id_ID');

        // 1. BERSIHKAN DATABASE
        Schema::disableForeignKeyConstraints();
        TransaksiPeminjaman::truncate();
        ItemBuku::truncate();
        Buku::truncate();
        User::truncate();
        Pengaturan::truncate();
        Schema::enableForeignKeyConstraints();

        echo "🚀 Memulai Seeding Data...\n";

        // 2. SETUP PENGATURAN & TARIF DEFAULT
        $tarif_denda_default = 1000; // Tarif saat ini
        Pengaturan::create([
            'denda_per_hari' => $tarif_denda_default
        ]);
        echo "✅ Pengaturan Denda dibuat (Rp $tarif_denda_default).\n";

        // 3. BUAT USER (ADMIN & MEMBER)
        $admin = User::create([
            'nama_lengkap' => 'Administrator Utama',
            'username'     => 'admin',
            'password'     => Hash::make('password'),
            'role'         => 'admin',
            'no_hp'        => '081234567890',
            'alamat'       => 'Kantor Perpustakaan Pusat',
            'created_at'   => Carbon::now(),
        ]);

        $members = [];
        for ($i = 0; $i < 30; $i++) {
            $members[] = User::create([
                'nama_lengkap' => $faker->name,
                'username'     => $faker->unique()->userName,
                'password'     => Hash::make('password'),
                'role'         => 'member',
                'no_hp'        => $faker->phoneNumber,
                'alamat'       => $faker->address,
                'email'        => $faker->unique()->safeEmail,
                'created_at'   => $faker->dateTimeBetween('-1 year', 'now'),
            ]);
        }
        echo "✅ 30 Member Dummy & 1 Admin dibuat.\n";

        // 4. BUAT KATALOG BUKU
        $katalog_buku = [];
        for ($i = 0; $i < 20; $i++) {
            $katalog_buku[] = Buku::create([
                'judul_buku'   => $faker->sentence(rand(2, 5)),
                'penulis'      => $faker->name,
                'penerbit'     => $faker->company,
                'tahun_terbit' => $faker->year,
                'created_at'   => Carbon::now(),
            ]);
        }
        echo "✅ 20 Katalog Buku dibuat.\n";

        // 5. BUAT ITEM BUKU FISIK & TRANSAKSI
        echo "🔄 Membuat Fisik Buku & Menyinkronkan Transaksi...\n";

        foreach ($katalog_buku as $buku) {
            $jumlah_copy = rand(3, 5);

            for ($urutan = 1; $urutan <= $jumlah_copy; $urutan++) {
                
                // Kode: PAUD-001-001
                $part2 = str_pad($buku->id, 3, '0', STR_PAD_LEFT);
                $part3 = str_pad($urutan, 3, '0', STR_PAD_LEFT);
                $kode_buku = "PAUD-{$part2}-{$part3}";

                // Random Status
                $rand = rand(1, 100);
                
                $status_buku = 'Tersedia';
                $status_transaksi = null; // Default null
                $peminjam = $faker->randomElement($members);

                // --- TENTUKAN NASIB BUKU ---
                if ($rand <= 20) { // 20% Dipinjam
                    $status_buku = 'Dipinjam';
                    $status_transaksi = 'Sedang Dipinjam';
                }
                elseif ($rand <= 30) { // 10% Menunggu Kembali
                    $status_buku = 'Dipinjam';
                    $status_transaksi = 'Menunggu Pengembalian';
                }
                elseif ($rand <= 40) { // 10% Menunggu ACC
                    $status_buku = 'Di-booking';
                    $status_transaksi = 'Menunggu Persetujuan';
                }
                // Sisanya Tersedia (Bisa jadi buku baru / history)

                // 1. SIMPAN ITEM BUKU
                $itemBuku = ItemBuku::create([
                    'buku_id'         => $buku->id,
                    'kode_buku'       => $kode_buku,
                    'status_buku'     => $status_buku,
                    'tgl_ditambahkan' => $faker->dateTimeBetween('-2 years', '-1 year'),
                ]);

                // 2. SIMPAN TRANSAKSI
                
                // KASUS A: TRANSAKSI AKTIF (Sedang Dipinjam / Menunggu Kembali)
                if ($status_buku == 'Dipinjam') {
                    $tgl_pinjam = Carbon::now()->subDays(rand(1, 10));
                    
                    $dataTransaksi = [
                        'user_id' => $peminjam->id,
                        'item_buku_id' => $itemBuku->id,
                        'approved_by_id' => $admin->id,
                        
                        'tgl_pengajuan_pinjam' => $tgl_pinjam->copy()->subHours(2),
                        'tgl_disetujui' => $tgl_pinjam,
                        'tgl_pinjam' => $tgl_pinjam,
                        'deadline' => $tgl_pinjam->copy()->addDays(7),
                        
                        'status' => $status_transaksi,
                        
                        // [UPDATE] ISI TARIF DENDA KARENA SUDAH DI-ACC
                        'tarif_denda_berlaku' => $tarif_denda_default, 
                    ];

                    if ($status_transaksi == 'Menunggu Pengembalian') {
                        $dataTransaksi['tgl_pengajuan_pengembalian'] = Carbon::now();
                    }

                    TransaksiPeminjaman::create($dataTransaksi);
                }

                // KASUS B: MENUNGGU PERSETUJUAN
                elseif ($status_transaksi == 'Menunggu Persetujuan') {
                    TransaksiPeminjaman::create([
                        'user_id' => $peminjam->id,
                        'item_buku_id' => $itemBuku->id,
                        'tgl_pengajuan_pinjam' => Carbon::now()->subMinutes(rand(10, 300)),
                        'status' => 'Menunggu Persetujuan',
                        
                        // [UPDATE] TARIF NULL (Belum berlaku / belum di-ACC)
                        'tarif_denda_berlaku' => null, 
                    ]);
                }

                // KASUS C: TERSEDIA (HISTORY)
                else {
                    // 50% punya history
                    if (rand(1, 100) <= 50) {
                        
                        // C1. History Sukses (Dikembalikan)
                        if (rand(1, 100) <= 80) {
                            $tgl_pinjam_lama = Carbon::now()->subMonths(rand(1, 6));
                            $deadline = $tgl_pinjam_lama->copy()->addDays(7);
                            
                            $is_telat = rand(0, 1); 
                            $hari_telat = 0;
                            $total_denda = 0;
                            $tgl_kembali = $deadline->copy()->subDays(rand(0, 2));

                            if ($is_telat) {
                                $hari_telat = rand(1, 5);
                                $tgl_kembali = $deadline->copy()->addDays($hari_telat);
                                $total_denda = $hari_telat * $tarif_denda_default;
                            }

                            TransaksiPeminjaman::create([
                                'user_id' => $faker->randomElement($members)->id,
                                'item_buku_id' => $itemBuku->id,
                                'approved_by_id' => $admin->id,
                                'retrieved_by_id' => $admin->id,
                                
                                'tgl_pengajuan_pinjam' => $tgl_pinjam_lama->copy()->subHours(3),
                                'tgl_disetujui' => $tgl_pinjam_lama,
                                'tgl_pinjam' => $tgl_pinjam_lama,
                                'deadline' => $deadline,
                                
                                'tgl_pengajuan_pengembalian' => $tgl_kembali,
                                'tgl_kembali' => $tgl_kembali,
                                'tgl_pelunasan' => $total_denda > 0 ? $tgl_kembali : null,
                                
                                'hari_telat' => $hari_telat,
                                'total_denda' => $total_denda,
                                'status' => 'Dikembalikan',
                                
                                // [UPDATE] ISI TARIF DENDA (Karena sudah terjadi)
                                'tarif_denda_berlaku' => $tarif_denda_default,
                            ]);
                        } 
                        
                        // C2. History Ditolak
                        else {
                            $tgl_tolak = Carbon::now()->subMonths(rand(1, 3));
                            TransaksiPeminjaman::create([
                                'user_id' => $faker->randomElement($members)->id,
                                'item_buku_id' => $itemBuku->id,
                                'rejected_by_id' => $admin->id,
                                'tgl_pengajuan_pinjam' => $tgl_tolak->copy()->subHours(5),
                                'tgl_ditolak' => $tgl_tolak,
                                'status' => 'Ditolak',
                                
                                // [UPDATE] TARIF NULL (Tidak berlaku)
                                'tarif_denda_berlaku' => null,
                            ]);
                        }
                    }
                }
            }
        }

        echo "✅ Selesai! Login Admin: 'admin' / 'password'\n";
    }
}