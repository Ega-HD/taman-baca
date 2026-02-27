<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; // Panggil Controller-nya
use App\Http\Controllers\AdminDashboardController; // Tambahkan ini di atas
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminBukuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\AdminTransaksiController;

// Arahkan halaman utama (/) ke method index di HomeController
    Route::get('/', [HomeController::class, 'index']);
// Area Guest (Hanya bisa diakses jika belum login)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
    });

// Route untuk Admin Dashboard
// Area Authenticated (Harus login dulu)
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        
        // Nanti kita bisa tambahkan middleware khusus admin di sini, 
        // tapi untuk sekarang kita amankan dengan middleware 'auth' dulu
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index']);

        // Tambahkan 3 baris ini untuk Kelola Buku
        Route::get('/admin/buku', [AdminBukuController::class, 'index']);
        Route::get('/admin/buku/create', [AdminBukuController::class, 'create']);
        Route::post('/admin/buku', [AdminBukuController::class, 'store']);

        // Kelola Transaksi (Peminjaman & Pengembalian)
        Route::get('/admin/transaksi', [AdminTransaksiController::class, 'index']);
        Route::post('/admin/transaksi/{id}/setujui', [AdminTransaksiController::class, 'setujui']); // Route ACC
        Route::post('/admin/transaksi/{id}/kembali', [AdminTransaksiController::class, 'kembalikan']);
        Route::post('/admin/transaksi/{id}/lunas', [AdminTransaksiController::class, 'lunasi']);
    });

// Menampilkan detail katalog dan daftar fisik buku
    Route::get('/admin/buku/{id}', [AdminBukuController::class, 'show']);
    
// Memproses penambahan fisik buku ke katalog yang sudah ada
    Route::post('/admin/buku/{id}/tambah-fisik', [AdminBukuController::class, 'storeFisik']);

// Route khusus Member (Pengunjung)
    Route::get('/member/peminjaman', [PeminjamanController::class, 'index']);
    Route::post('/member/pinjam/{katalog_id}', [PeminjamanController::class, 'store']);