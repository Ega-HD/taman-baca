<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\ItemBuku;
use App\Models\TransaksiPeminjaman;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    // Menampilkan halaman daftar buku yang sedang dipinjam oleh user yang login
    public function index()
    {
        // Ambil data transaksi milik user ini saja, beserta relasi ke fisik buku dan katalognya
        $transaksi = TransaksiPeminjaman::with(['itemBuku.buku'])
                        ->where('user_id', Auth::id())
                        ->orderBy('id', 'desc')
                        ->get();
                        
        return view('member.peminjaman', compact('transaksi'));
    }

    // Memproses klik tombol pinjam dari Beranda
    public function store(Request $request, $katalog_id)
    {
        // 1. Pastikan user yang meminjam adalah role 'pengunjung'
        if (Auth::user()->role !== 'pengunjung') {
            return back()->with('error', 'Hanya akun pengunjung/member yang dapat meminjam buku.');
        }

        DB::beginTransaction();
        try {
            // 2. Cari 1 salinan fisik dari katalog ini yang statusnya masih "Tersedia"
            $fisikBuku = ItemBuku::where('buku_id', $katalog_id)
                                 ->where('status_buku', 'Tersedia')
                                 ->first();

            // Jika ternyata sudah dipinjam orang lain saat user baru klik tombol
            if (!$fisikBuku) {
                return back()->with('error', 'Mohon maaf, stok buku ini baru saja habis dipinjam.');
            }

            // 3. Ubah status fisik buku menjadi 'Dipinjam'
            $fisikBuku->update([
                'status_buku' => 'Dipinjam'
            ]);

            // 4. Catat ke tabel transaksi_peminjaman (Misal: Deadline 7 hari dari sekarang)
            TransaksiPeminjaman::create([
                'user_id' => Auth::id(),
                'item_buku_id' => $fisikBuku->id, // Yang dicatat kode fisiknya!
                'tgl_pinjam' => Carbon::now(),
                'deadline' => Carbon::now()->addDays(14), // Atur masa pinjam (contoh: 7 hari)
                'status' => 'Sedang Dipinjam',
            ]);

            DB::commit();
            return redirect('/member/peminjaman')->with('success', 'Berhasil meminjam buku! Silakan ambil fisik buku di admin Taman Baca dengan menyebutkan Kode Buku.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}