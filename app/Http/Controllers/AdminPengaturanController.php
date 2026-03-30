<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request;
// use App\Models\Pengaturan;

// class AdminPengaturanController extends Controller
// {
//     public function index()
//     {
//         // Ambil pengaturan pertama, jika tidak ada buat baru
//         $pengaturan = Pengaturan::firstOrCreate([], ['denda_per_hari' => 1000]);
//         return view('admin.pengaturan.index', compact('pengaturan'));
//     }

//     public function update(Request $request)
//     {
//         $request->validate(['denda_per_hari' => 'required|integer|min:0']);
        
//         $pengaturan = Pengaturan::first();
//         $pengaturan->update(['denda_per_hari' => $request->denda_per_hari]);

//         return back()->with('success', 'Tarif denda berhasil diperbarui!');
//     }
// }

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaturan;
use App\Models\TransaksiPeminjaman; // Tambahkan Model Transaksi
use Carbon\Carbon; // Tambahkan Carbon untuk filter tahun

class AdminPengaturanController extends Controller
{
    // Tambahkan Request $request di parameter index
    public function index(Request $request) 
    {
        // 1. Ambil pengaturan pertama, jika tidak ada buat baru
        $pengaturan = Pengaturan::firstOrCreate([], ['denda_per_hari' => 1000]);

        // 2. Ambil parameter filter dari URL (default: 'semua')
        $filterTahun = $request->input('filter_tahun', 'semua');

        // 3. Mulai query ke Transaksi Peminjaman (Hanya yang sudah dilunasi)
        $queryDenda = TransaksiPeminjaman::whereNotNull('tgl_pelunasan')
                                         ->where('total_denda', '>', 0);

        // 4. Terapkan filter berdasarkan pilihan Admin
        if ($filterTahun == 'tahun_ini') {
            $queryDenda->whereYear('tgl_pelunasan', Carbon::now()->year);
        } elseif ($filterTahun == 'tahun_kemarin') {
            $queryDenda->whereYear('tgl_pelunasan', Carbon::now()->subYear()->year);
        }

        // 5. Hitung total uangnya
        $totalPendapatan = $queryDenda->sum('total_denda');

        return view('admin.pengaturan.index', compact('pengaturan', 'totalPendapatan', 'filterTahun'));
    }

    public function update(Request $request)
    {
        $request->validate(['denda_per_hari' => 'required|integer|min:0']);
        
        $pengaturan = Pengaturan::first();
        $pengaturan->update(['denda_per_hari' => $request->denda_per_hari]);

        return back()->with('success', 'Tarif denda berhasil diperbarui!');
    }
}