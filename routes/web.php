<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; // Panggil Controller-nya
use App\Http\Controllers\AdminDashboardController; // Tambahkan ini di atas
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminBukuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\AdminTransaksiController;
use App\Http\Controllers\AdminPengaturanController;
use App\Http\Controllers\AdminMemberController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProfileController;

// Arahkan halaman utama (/) ke method index di HomeController
    Route::get('/', [HomeController::class, 'index']);

// Area Guest (Hanya bisa diakses jika belum login)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);

        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'register']);
    });


    Route::middleware(['auth', 'admin'])->group(function () {
                // ADMIN
            // Route untuk Admin Dashboard
         Route::get('/admin/dashboard', [AdminDashboardController::class, 'index']);

            // Kelola Buku
        Route::get('/admin/buku', [AdminBukuController::class, 'index']);
        Route::get('/admin/buku/create', [AdminBukuController::class, 'create']);
        Route::post('/admin/buku', [AdminBukuController::class, 'store']);
        
            // Kelola Buku Fisik
        // Menampilkan detail katalog dan daftar fisik buku
        Route::get('/admin/buku/{id}', [AdminBukuController::class, 'show']);
        // Memproses penambahan fisik buku ke katalog yang sudah ada
        Route::post('/admin/buku/{id}/tambah-fisik', [AdminBukuController::class, 'storeFisik']);

            // Kelola Transaksi (Peminjaman & Pengembalian)
        Route::get('/admin/transaksi', [AdminTransaksiController::class, 'index']);
        Route::post('/admin/transaksi/{id}/setujui', [AdminTransaksiController::class, 'setujui']); // Route ACC
        Route::post('/admin/transaksi/{id}/kembali', [AdminTransaksiController::class, 'kembalikan']);
        Route::post('/admin/transaksi/{id}/lunas', [AdminTransaksiController::class, 'lunasi']);

        // Fitur Tolak
        Route::post('/admin/transaksi/{id}/tolak', [AdminTransaksiController::class, 'tolak']);
        // Fitur Edit (Update)
        Route::put('/admin/transaksi/{id}', [AdminTransaksiController::class, 'update']);
        

            // Pengaturan Denda
        Route::get('/admin/pengaturan', [AdminPengaturanController::class, 'index']);
        Route::post('/admin/pengaturan', [AdminPengaturanController::class, 'update']);
        

        // 1. Edit & Update Katalog Buku
        Route::get('/admin/buku/{id}/edit', [AdminBukuController::class, 'edit']);
        Route::put('/admin/buku/{id}', [AdminBukuController::class, 'update']);
        
        // 2. Hapus Katalog (Beserta semua isinya)
        Route::delete('/admin/buku/{id}', [AdminBukuController::class, 'destroy']);

        // 3. Hapus Item Fisik Spesifik
        Route::delete('/admin/item-buku/{id}', [AdminBukuController::class, 'destroyFisik']);

        // Route Kelola Member 
        Route::resource('/admin/members', AdminMemberController::class);

    });

// Area Authenticated (Harus login dulu)
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        
                // MEMBER
        // Edit Profil Sendiri
        Route::get('/member/profile', [ProfileController::class, 'edit']);
        Route::put('/member/profile', [ProfileController::class, 'update']);

        // Route khusus Member (member)
        Route::get('/member/peminjaman', [PeminjamanController::class, 'index']);
        Route::post('/member/pinjam/{katalog_id}', [PeminjamanController::class, 'store']);
        Route::post('/member/peminjaman/{id}/ajukan-kembali', [PeminjamanController::class, 'ajukanKembali']);
    });

