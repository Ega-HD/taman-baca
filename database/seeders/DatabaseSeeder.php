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
        $faker = Faker::create('id_ID'); // Data dummy Indonesia

        // 1. BERSIHKAN DATABASE
        Schema::disableForeignKeyConstraints();
        TransaksiPeminjaman::truncate();
        ItemBuku::truncate();
        Buku::truncate();
        User::truncate();
        Pengaturan::truncate();
        Schema::enableForeignKeyConstraints();

        echo "🚀 Memulai Seeding Data...\n";

        // 2. SETUP PENGATURAN
        Pengaturan::create([
            'denda_per_hari' => 1000
        ]);
        echo "✅ Pengaturan Denda dibuat (Rp 1.000).\n";

        // 3. BUAT USER (ADMIN & MEMBER)
        
        // A. Admin Utama
        $admin = User::create([
            'nama_lengkap' => 'Administrator Utama',
            'username'     => 'admin',
            'password'     => Hash::make('password'),
            'role'         => 'admin',
            'no_hp'        => '081234567890',
            'alamat'       => 'Kantor Perpustakaan Pusat',
            'created_at'   => Carbon::now(),
        ]);

        // B. Member (30 Orang)
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

        // 4. BUAT KATALOG BUKU (20 Judul)
        // Kita simpan ID-nya untuk looping item fisik
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
        // Format Kode: PAUD-{ID_KATALOG}-{URUTAN} (3 digit padding)
        
        echo "🔄 Membuat Fisik Buku & Menyinkronkan Transaksi...\n";

        foreach ($katalog_buku as $buku) {
            // Setiap judul buku punya 3-5 salinan fisik (random)
            $jumlah_copy = rand(3, 5);

            for ($urutan = 1; $urutan <= $jumlah_copy; $urutan++) {
                
                // Generate Kode: PAUD-001-001
                $part2 = str_pad($buku->id, 3, '0', STR_PAD_LEFT); // ID Katalog
                $part3 = str_pad($urutan, 3, '0', STR_PAD_LEFT);   // Urutan
                $kode_buku = "PAUD-{$part2}-{$part3}";

                // Tentukan Nasib Buku Ini (Random Percentage)
                $rand = rand(1, 100);
                
                // Default Variable
                $status_buku = 'Tersedia';
                $status_transaksi = null;
                $peminjam = $faker->randomElement($members);

                // LOGIKA SINKRONISASI STATUS
                
                // KASUS A: SEDANG DIPINJAM (20% Peluang)
                if ($rand <= 20) {
                    $status_buku = 'Dipinjam';
                    $status_transaksi = 'Sedang Dipinjam';
                }
                // KASUS B: MENUNGGU PENGEMBALIAN (10% Peluang)
                elseif ($rand <= 30) {
                    $status_buku = 'Dipinjam';
                    $status_transaksi = 'Menunggu Pengembalian';
                }
                // KASUS C: MENUNGGU PERSETUJUAN / DI-BOOKING (10% Peluang)
                elseif ($rand <= 40) {
                    $status_buku = 'Di-booking';
                    $status_transaksi = 'Menunggu Persetujuan';
                }
                // KASUS D: TERSEDIA (Sisanya 60%)
                else {
                    $status_buku = 'Tersedia';
                    // Buku tersedia bisa jadi:
                    // 1. Buku baru (belum pernah dipinjam)
                    // 2. Pernah dipinjam lalu dikembalikan (History)
                    // 3. Pernah diajukan tapi ditolak (History)
                }

                // 1. SIMPAN ITEM BUKU
                $itemBuku = ItemBuku::create([
                    'buku_id'         => $buku->id,
                    'kode_buku'       => $kode_buku,
                    'status_buku'     => $status_buku,
                    'tgl_ditambahkan' => $faker->dateTimeBetween('-2 years', '-1 year'),
                ]);

                // 2. SIMPAN TRANSAKSI (SESUAI STATUS DI ATAS)

                // --- JIKA STATUS AKTIF (Dipinjam / Menunggu Kembali) ---
                if ($status_buku == 'Dipinjam') {
                    $tgl_pinjam = Carbon::now()->subDays(rand(1, 10)); // Pinjam 1-10 hari lalu
                    
                    $dataTransaksi = [
                        'user_id' => $peminjam->id,
                        'item_buku_id' => $itemBuku->id,
                        'approved_by_id' => $admin->id,
                        
                        'tgl_pengajuan_pinjam' => $tgl_pinjam->copy()->subHours(2),
                        'tgl_disetujui' => $tgl_pinjam,
                        'tgl_pinjam' => $tgl_pinjam,
                        'deadline' => $tgl_pinjam->copy()->addDays(7),
                        
                        'status' => $status_transaksi, // Sedang Dipinjam / Menunggu Pengembalian
                    ];

                    // Jika statusnya Menunggu Pengembalian, isi tgl pengajuan kembali
                    if ($status_transaksi == 'Menunggu Pengembalian') {
                        $dataTransaksi['tgl_pengajuan_pengembalian'] = Carbon::now();
                    }

                    TransaksiPeminjaman::create($dataTransaksi);
                }

                // --- JIKA STATUS MENUNGGU PERSETUJUAN ---
                elseif ($status_transaksi == 'Menunggu Persetujuan') {
                    TransaksiPeminjaman::create([
                        'user_id' => $peminjam->id,
                        'item_buku_id' => $itemBuku->id,
                        'tgl_pengajuan_pinjam' => Carbon::now()->subMinutes(rand(10, 300)),
                        'status' => 'Menunggu Persetujuan',
                    ]);
                }

                // --- JIKA STATUS TERSEDIA (BUAT HISTORY DUMMY) ---
                else {
                    // 50% kemungkinan buku ini punya riwayat peminjaman masa lalu
                    if (rand(1, 100) <= 50) {
                        
                        // Skenario 1: Pernah Pinjam & Kembali (Sukses)
                        if (rand(1, 100) <= 80) {
                            $tgl_pinjam_lama = Carbon::now()->subMonths(rand(1, 6));
                            $deadline = $tgl_pinjam_lama->copy()->addDays(7);
                            
                            // Acak: Apakah telat atau tepat waktu?
                            $is_telat = rand(0, 1); 
                            $hari_telat = 0;
                            $total_denda = 0;
                            $tgl_kembali = $deadline->copy()->subDays(rand(0, 2)); // Tepat waktu

                            if ($is_telat) {
                                $hari_telat = rand(1, 5);
                                $tgl_kembali = $deadline->copy()->addDays($hari_telat);
                                $total_denda = $hari_telat * 1000;
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
                                'tarif_denda_berlaku' => 1000,
                                'total_denda' => $total_denda,
                                'status' => 'Dikembalikan'
                            ]);
                        } 
                        
                        // Skenario 2: Pernah Diajukan tapi Ditolak
                        else {
                            $tgl_tolak = Carbon::now()->subMonths(rand(1, 3));
                            TransaksiPeminjaman::create([
                                'user_id' => $faker->randomElement($members)->id,
                                'item_buku_id' => $itemBuku->id,
                                'rejected_by_id' => $admin->id,
                                
                                'tgl_pengajuan_pinjam' => $tgl_tolak->copy()->subHours(5),
                                'tgl_ditolak' => $tgl_tolak,
                                'status' => 'Ditolak'
                            ]);
                        }
                    }
                }
                // End Logic Transaksi
            }
        }

        echo "✅ Selesai! User Admin: 'admin' / Pass: 'password'\n";
    }
}