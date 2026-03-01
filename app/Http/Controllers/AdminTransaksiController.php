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
    public function index(Request $request)
    {
        // Ambil data transaksi beserta nama peminjam dan detail buku fisiknya
        // $transaksi = TransaksiPeminjaman::with(['user', 'itemBuku.buku', 'approvedBy', 'retrievedBy'])
        //                 ->whereIn('status', [
        //                     'Menunggu Persetujuan', 
        //                     'Sedang Dipinjam', 
        //                     'Menunggu Pengembalian',
        //                     'Dikembalikan'])
        //                 ->orderBy('deadline', 'asc') // Urutkan dari deadline yang paling dekat/lewat
        //                 ->get();

        $perPage = $request->input('per_page', 10);
        // Mulai Query
        $query = TransaksiPeminjaman::with(['user', 'itemBuku.buku', 'approvedBy', 'retrievedBy', 'rejectedBy', 'updatedBy'])
                    ->orderBy('id', 'desc');

        // 1. FILTER PENCARIAN (Nama Member / Judul Buku / Kode Buku)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($u) use ($search) {
                    $u->where('nama_lengkap', 'like', "%$search%");
                })
                ->orWhereHas('itemBuku', function($b) use ($search) {
                    $b->where('kode_buku', 'like', "%$search%")
                      ->orWhereHas('buku', function($k) use ($search) {
                          $k->where('judul_buku', 'like', "%$search%");
                      });
                });
            });
        }

        // 2. FILTER STATUS & KONDISI
        if ($request->filled('status')) {
            if ($request->status == 'terlambat') {
                // Logika Khusus: Cari yang deadline-nya sudah lewat DAN belum dikembalikan
                $query->whereIn('status', ['Sedang Dipinjam', 'Menunggu Pengembalian'])
                      ->whereDate('deadline', '<', Carbon::now());
            } 
            elseif ($request->status == 'denda_belum_lunas') {
                // Logika Khusus: Sudah kembali TAPI denda > 0 DAN tgl_pelunasan kosong
                $query->where('status', 'Dikembalikan')
                      ->where('total_denda', '>', 0)
                      ->whereNull('tgl_pelunasan');
            }
            else {
                // Filter status standar (Enum)
                $query->where('status', $request->status);
            }
        }

        // Eksekusi
        // $transaksi = $query->paginate(10); // Gunakan paginate agar halaman tidak berat

        $transaksi = $query->paginate($perPage)->appends($request->query());

        return view('admin.transaksi.index', compact('transaksi'));
    }

    public function tolak($id)
    {
        $transaksi = TransaksiPeminjaman::findOrFail($id);

        if ($transaksi->status !== 'Menunggu Persetujuan') {
            return back()->withErrors(['error' => 'Hanya status Menunggu Persetujuan yang bisa ditolak.']);
        }

        DB::transaction(function () use ($transaksi) {
            // Update Status Transaksi
            $transaksi->update([
                'status' => 'Ditolak',
                'rejected_by_id' => Auth::id(), // Catat siapa yang menolak
                'tgl_ditolak' => Carbon::now(), // Catat waktu
            ]);

            // PENTING: Kembalikan status buku fisik jadi Tersedia
            ItemBuku::where('id', $transaksi->item_buku_id)->update([
                'status_buku' => 'Tersedia'
            ]);
        });

        return back()->with('success', 'Peminjaman ditolak. Stok buku telah dikembalikan.');
    }

    public function setujui($id)
    {
        $tarifDenda = Pengaturan::first()->denda_per_hari ?? 1000;

        $transaksi = TransaksiPeminjaman::findOrFail($id);

        DB::beginTransaction();
        try {
            // Saat di-ACC, barulah argo waktu berjalan (tgl pinjam hari ini, deadline 7 hari ke depan)
            $transaksi->update([
                'approved_by_id' => Auth::id(), // Siapa admin yang menyetujui
                'tgl_disetujui' => Carbon::now(), // Kapan disetujui
                'tgl_pinjam' => Carbon::now(), // Argo peminjaman dimulai
                'tarif_denda_berlaku' => $tarifDenda,
                'deadline' => Carbon::now()->addDays(-7), // Batas waktu 7 hari
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
        $tarif_denda = $transaksi->tarif_denda_berlaku;
        
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

        $tgl_pengajuan_pengembalian_fix = $transaksi->tgl_pengajuan_pengembalian;
        if (empty($tgl_pengajuan_pengembalian_fix)) {
            $tgl_pengajuan_pengembalian_fix = $tgl_kembali_aktual;
        }
        
        DB::beginTransaction();
        try {
            $transaksi->update([
                'tgl_pengajuan_pengembalian' => $tgl_pengajuan_pengembalian_fix,
                'tgl_kembali' => $tgl_kembali_aktual,
                'hari_telat' => $hari_telat,
                // 'tarif_denda_berlaku' => $tarif_denda,
                'retrieved_by_id' => Auth::id(),
                'total_denda' => $total_denda,
                'status' => 'Dikembalikan' // Transaksi dianggap selesai, denda dicatat
            ]);
            // 1. Update data transaksi

            // 2. Bebaskan fisik buku agar statusnya kembali 'Tersedia'
            ItemBuku::where('id', $transaksi->item_buku_id)->update([
                'status_buku' => 'Tersedia'
            ]);

            DB::commit();
            
            // Buat pesan dinamis, apakah ada denda atau tidak
            $pesan = 'Buku berhasil dikembalikan.';
            if ($total_denda > 0) {
                $pesan .= ' member terlambat ' . $hari_telat . ' hari dan dikenakan denda Rp ' . number_format($total_denda, 0, ',', '.');
            }

            return back()->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memproses pengembalian: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $transaksi = TransaksiPeminjaman::findOrFail($id);

        // Contoh: Admin ingin memperpanjang deadline atau ubah status manual
        $request->validate([
            'deadline' => 'nullable|date',
            'status' => 'required|in:Menunggu Persetujuan,Sedang Dipinjam,Menunggu Pengembalian,Dikembalikan,Ditolak',
        ]);

        $transaksi->update([
            'deadline' => $request->deadline,
            'status' => $request->status,
            'updated_by_id' => Auth::id(), // Catat siapa yang mengedit
            'tgl_diupdate' => Carbon::now(),
        ]);

        return back()->with('success', 'Data transaksi berhasil diperbarui manual.');
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