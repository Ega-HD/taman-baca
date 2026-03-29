<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\ItemBuku;
use App\Models\User;
use App\Models\TransaksiPeminjaman;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Menghitung ringkasan data
        $totalKatalog = Buku::count();
        $totalmember = User::where('role', 'member')->count();
        $bukuDipinjam = ItemBuku::where('status_buku', 'Dipinjam')->count();
        $bukuPengembalian = TransaksiPeminjaman::where('status', 'Menunggu Pengembalian')->count();
        $totalBukuFisik = ItemBuku::count();
        $totalBukuFisikTersedia = ItemBuku::where('status_buku', 'Tersedia')->count();
        $bukuDibooking = ItemBuku::where('status_buku', 'Di-booking')->count();

        // Mengirim data ke view admin/dashboard
        return view('admin.dashboard', compact('totalKatalog', 'totalBukuFisik', 'totalBukuFisikTersedia', 'totalmember', 'bukuDipinjam', 'bukuDibooking', 'bukuPengembalian'));
    }
}