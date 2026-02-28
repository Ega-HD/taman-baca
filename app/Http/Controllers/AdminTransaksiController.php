<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TransaksiPeminjaman;
use App\Models\ItemBuku;
use App\Models\Pengaturan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminTransaksiController extends Controller
{
    // Menampilkan daftar semua transaksi yang sedang aktif (belum dikembalikan)
    public function index()
    {
        $tarifDenda = Pengaturan::first()->denda_per_hari ?? 1000;

        // Ambil data transaksi beserta nama peminjam dan detail buku fisiknya
        $transaksi = TransaksiPeminjaman::with(['user', 'itemBuku.buku', 'admin'])
                        ->whereIn('status', [
                            'Menunggu Persetujuan', 
                            'Sedang Dipinjam', 
                            'Menunggu Pengembalian',
                            'Dikembalikan'])
                        ->orderBy('deadline', 'asc') // Urutkan dari deadline yang paling dekat/lewat
                        ->get();

        return view('admin.transaksi.index', compact('transaksi', 'tarifDenda'));
    }

    public function setujui($id)
    {
        $transaksi = TransaksiPeminjaman::findOrFail($id);

        DB::beginTransaction();
        try {
            // Saat di-ACC, barulah argo waktu berjalan (tgl pinjam hari ini, deadline 7 hari ke depan)
            $transaksi->update([
                'admin_id' => Auth::id(), // Siapa admin yang menyetujui
                'tgl_disetujui' => Carbon::now(), // Kapan disetujui
                'tgl_pinjam' => Carbon::now(), // Argo peminjaman dimulai
                'deadline' => Carbon::now()->addDays(7), // Batas waktu 7 hari
                'status' => 'Sedang Dipinjam'
            ]);

            // Ubah status fisik buku dari 'Di-booking' menjadi 'Dipinjam'
            ItemBuku::where('id', $transaksi->item_buku_id)->update([
                'status_buku' => 'Dipinjam'
            ]);

            DB::commit(); 
            return back()->with('success', 'Peminjaman disetujui. Waktu peminjaman mulai berjalan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyetujui: ' . $e->getMessage()]);
        }
    }
    // Memproses pengembalian buku dan kalkulasi denda
    public function kembalikan(Request $request, $id)
    {
        $transaksi = TransaksiPeminjaman::findOrFail($id);
        
        // Tarif denda per hari keterlambatan (Misal: Rp 1.000)
        $tarif_denda = Pengaturan::first()->denda_per_hari ?? 1000;
        
        $tgl_kembali_aktual = Carbon::now();
        $deadline = Carbon::parse($transaksi->deadline);
        
        $hari_telat = 0;
        $total_denda = 0;

        // Cek apakah tanggal kembali melewati tanggal deadline
        if ($tgl_kembali_aktual->gt($deadline)) {
            // Gunakan (int) dan startOfDay() agar hasilnya bilangan bulat mutlak
            $hari_telat = (int) $deadline->startOfDay()->diffInDays($tgl_kembali_aktual->startOfDay());
            $total_denda = $hari_telat * $tarif_denda;
        }

        
        DB::beginTransaction();
        try {

            if ($transaksi->tgl_pengajuan_kembali) {
                $transaksi->update([
                    'tgl_kembali' => $tgl_kembali_aktual,
                    'hari_telat' => $hari_telat,
                    'total_denda' => $total_denda,
                    'status' => 'Dikembalikan' // Transaksi dianggap selesai, denda dicatat
                ]);
            }
            else {
                $transaksi->update([
                    'tgl_pengajuan_kembali' => Carbon::now(),
                    'tgl_kembali' => $tgl_kembali_aktual,
                    'hari_telat' => $hari_telat,
                    'total_denda' => $total_denda,
                    'status' => 'Dikembalikan' // Transaksi dianggap selesai, denda dicatat
                ]);
            }
            // 1. Update data transaksi

            // 2. Bebaskan fisik buku agar statusnya kembali 'Tersedia'
            ItemBuku::where('id', $transaksi->item_buku_id)->update([
                'status_buku' => 'Tersedia'
            ]);

            DB::commit();
            
            // Buat pesan dinamis, apakah ada denda atau tidak
            $pesan = 'Buku berhasil dikembalikan.';
            if ($total_denda > 0) {
                $pesan .= ' Pengunjung terlambat ' . $hari_telat . ' hari dan dikenakan denda Rp ' . number_format($total_denda, 0, ',', '.');
            }

            return back()->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses pengembalian: ' . $e->getMessage()]);
        }
    }

    public function lunasi(Request $request, $id)
    {
        $transaksi = TransaksiPeminjaman::findOrFail($id);
        
        $transaksi->update([
            'tgl_pelunasan' => Carbon::now() // Mengisi waktu pelunasan
        ]);

        return back()->with('success', 'Tagihan denda berhasil dilunasi!');
    }
}