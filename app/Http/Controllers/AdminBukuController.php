<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use App\Models\ItemBuku;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Tambahkan ini untuk transaksi

class AdminBukuController extends Controller
{
    // Menampilkan daftar katalog buku
    public function index()
    {
        // Mengambil buku beserta relasi item fisiknya untuk menghitung stok
        $buku = Buku::with('itemBuku')->orderBy('id', 'desc')->get();
        return view('admin.buku.index', compact('buku'));
    }

    // Menampilkan form tambah buku
    public function create()
    {
        return view('admin.buku.create');
    }

    // Memproses data form
    public function store(Request $request)
    {
        // 1. Validasi inputan admin
        $request->validate([
            'judul_buku' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|numeric',
            'asal_buku' => 'required|in:Baru,Donasi',
            'jumlah_buku' => 'required|integer|min:1',
        ]);

        // 2. Gunakan Database Transaction
        DB::beginTransaction();
        
        try {
            // A. Simpan ke tabel `buku` (Katalog Induk)
            $katalogBuku = Buku::create([
                'judul_buku' => $request->judul_buku,
                'penulis' => $request->penulis,
                'penerbit' => $request->penerbit,
                'tahun_terbit' => $request->tahun_terbit,
            ]);

            // B. Cari kode buku terakhir di tabel item_buku
            $lastItem = ItemBuku::orderBy('id', 'desc')->first();
            $lastNumber = 0; // Default jika tabel masih kosong

            if ($lastItem) {
                // Ambil string kode terakhir (contoh: "PAUD-023")
                $lastCode = $lastItem->kode_buku; 
                
                // Pecah string berdasarkan tanda hubung '-'
                $parts = explode('-', $lastCode); 
                
                // Ambil bagian angkanya saja dan jadikan tipe data integer
                if (count($parts) == 2) {
                    $lastNumber = (int) $parts[1];
                }
            }

            // C. Simpan ke tabel `item_buku` (Salinan Fisik) melalui looping
            for ($i = 0; $i < $request->jumlah_buku; $i++) {
                
                // Tambahkan 1 ke angka terakhir untuk setiap salinan baru
                $lastNumber++;
                
                // Format ulang menjadi string dengan 3 digit (contoh: 1 menjadi "001")
                // Jika angkanya tembus 1000, str_pad otomatis menyesuaikan jadi 4 digit
                $kodeUnik = 'PAUD-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);

                ItemBuku::create([
                    'buku_id' => $katalogBuku->id, 
                    'kode_buku' => $kodeUnik,
                    'status_buku' => 'Tersedia', 
                    'asal_buku' => $request->asal_buku,
                    'tgl_ditambahkan' => Carbon::now(),
                ]);
            }

            // Jika semua lancar, simpan permanen ke database
            DB::commit();

            return redirect('/admin/buku')->with('success', 'Katalog dan ' . $request->jumlah_buku . ' salinan fisik buku berhasil ditambahkan!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan data: ' . $e->getMessage()])->withInput();
        }
    }

    // Menampilkan halaman detail buku
    public function show($id)
    {
        // Ambil data buku berdasarkan ID, sekalian bawa relasi item_buku-nya
        $buku = Buku::with('itemBuku')->findOrFail($id);
        
        return view('admin.buku.show', compact('buku'));
    }

    // Memproses penambahan salinan fisik baru ke katalog existing
    public function storeFisik(Request $request, $id)
    {
        $request->validate([
            'jumlah_buku' => 'required|integer|min:1',
            'asal_buku' => 'required|in:Baru,Donasi',
        ]);

        // Pastikan katalog bukunya ada
        $buku = Buku::findOrFail($id);

        DB::beginTransaction();
        try {
            // Logika auto-increment kode unik (sama seperti saat create awal)
            $lastItem = ItemBuku::orderBy('id', 'desc')->first();
            $lastNumber = 0;

            if ($lastItem) {
                $lastCode = $lastItem->kode_buku;
                $parts = explode('-', $lastCode);
                if (count($parts) == 2) {
                    $lastNumber = (int) $parts[1];
                }
            }

            // Looping sebanyak jumlah buku fisik yang ditambahkan
            for ($i = 0; $i < $request->jumlah_buku; $i++) {
                $lastNumber++;
                $kodeUnik = 'PAUD-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);

                ItemBuku::create([
                    'buku_id' => $buku->id, // Menggunakan ID dari parameter URL
                    'kode_buku' => $kodeUnik,
                    'status_buku' => 'Tersedia',
                    'asal_buku' => $request->asal_buku,
                    'tgl_ditambahkan' => Carbon::now(),
                ]);
            }

            DB::commit();
            return back()->with('success', $request->jumlah_buku . ' salinan fisik baru berhasil ditambahkan ke katalog ini!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menambah buku fisik: ' . $e->getMessage()]);
        }
    }
}