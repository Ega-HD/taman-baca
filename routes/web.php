<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController; // Panggil Controller-nya

// Route::get('/', function () {
//     return view('layouts.base');
// });

// Route::get('/user/detail', function () {
//     return view('layouts.base');
// });
// Arahkan halaman utama (/) ke method index di HomeController
Route::get('/', [HomeController::class, 'index']);
