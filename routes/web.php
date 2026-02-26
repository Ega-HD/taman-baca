<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; // Panggil Controller-nya
use App\Http\Controllers\AdminDashboardController; // Tambahkan ini di atas
use App\Http\Controllers\AuthController;

// Route::get('/', function () {
//     return view('layouts.base');
// });

// Route::get('/user/detail', function () {
//     return view('layouts.base');
// });
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
});