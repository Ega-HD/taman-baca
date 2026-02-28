<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\ItemBuku;
use App\Models\TransaksiPeminjaman;
use App\Models\Pengaturan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PeminjamanController extends Controller
{
    // Menampilkan halaman daftar buku yang sedang dipinjam oleh user yang login
    public function index()
    {
        $tarifDenda = Pengaturan::first()->denda_per_hari ?? 1000;

        // Ambil data transaksi milik user ini saja, beserta relasi ke fisik buku dan katalognya
        $transaksi = TransaksiPeminjaman::with(['itemBuku.buku', 'admin'])
                        ->where('user_id', Auth::id())
                        ->orderBy('id', 'desc')
                        ->get();
                        
        return view('member.peminjaman', compact('transaksi', 'tarifDenda'));
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

            // 3. Ubah status fisik buku supaya tidak bentrok bersamaan dipinjam
            $fisikBuku->update([
                'status_buku' => 'Di-booking'
            ]);

            // 4. Catat ke tabel transaksi_peminjaman (Misal: Deadline 7 hari dari sekarang)
            TransaksiPeminjaman::create([
                'user_id' => Auth::id(),
                'item_buku_id' => $fisikBuku->id, // Yang dicatat kode fisiknya!
                'tgl_pengajuan' => Carbon::now(),
                'status' => 'Menunggu Persetujuan',
            ]);

            DB::commit();
            return redirect('/member/peminjaman')->with('success', 'Permintaan peminjaman terkirim! Silakan tunggu persetujuan Admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }

    }
    
    // Memproses pengajuan pengembalian oleh member
    public function ajukanKembali($id)
    {
        $transaksi = TransaksiPeminjaman::where('id', $id)
                        ->where('user_id', Auth::id()) // Pastikan hanya miliknya
                        ->firstOrFail();

        $transaksi->update([
            'status' => 'Menunggu Pengembalian',
            'tgl_pengajuan_kembali' => Carbon::now()
        ]);

        return back()->with('success', 'Pengajuan pengembalian terkirim! Silakan bawa fisik buku ke Admin.');
    }
}