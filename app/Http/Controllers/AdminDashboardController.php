<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\User;
use App\Models\TransaksiPeminjaman;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Menghitung ringkasan data
        $totalKatalog = Buku::count();
        $totalmember = User::where('role', 'member')->count();
        $bukuDipinjam = TransaksiPeminjaman::where('status', 'Sedang Dipinjam')->count();

        // Mengirim data ke view admin/dashboard
        return view('admin.dashboard', compact('totalKatalog', 'totalmember', 'bukuDipinjam'));
    }
}