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
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        // Ambil tahun-tahun unik yang ada di database
        $list_tahun = Buku::select('tahun_terbit')
                        ->distinct()
                        ->orderBy('tahun_terbit', 'desc')
                        ->pluck('tahun_terbit');

        // Ambil nama penulis unik
        $list_penulis = Buku::select('penulis')
                        ->distinct()
                        ->orderBy('penulis', 'asc')
                        ->pluck('penulis');

        // 2. MULAI QUERY UTAMA
        $query = Buku::withCount('itemBuku'); // Hitung stok fisik

        // A. Logika Search (Judul, Penulis, Penerbit)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul_buku', 'like', "%$search%")
                  ->orWhere('penulis', 'like', "%$search%")
                  ->orWhere('penerbit', 'like', "%$search%");
            });
        }

        // B. Filter Tahun Terbit
        $query->when($request->filled('tahun_terbit'), function ($q) use ($request) {
            $q->where('tahun_terbit', $request->tahun_terbit);
        });

        // C. Filter Penulis
        $query->when($request->filled('penulis'), function ($q) use ($request) {
            $q->where('penulis', $request->penulis);
        });

        // 3. EKSEKUSI & PAGINATION
        // appends() wajib ada agar filter tidak hilang saat pindah halaman
        $buku = $query->orderBy('id', 'desc')
                      ->paginate($perPage)
                      ->appends($request->query());

        return view('admin.buku.index', compact('buku', 'list_tahun', 'list_penulis'));
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
            // 'asal_buku' => 'required|in:Baru,Donasi',
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
            $lastItem = ItemBuku::where('buku_id', $katalogBuku->id)->orderBy('id', 'desc')->first();
            $lastNumber = 0; // Default jika tabel masih kosong

            if ($lastItem) {
                // Ambil string kode terakhir (contoh: "PAUD-023")
                $lastCode = $lastItem->kode_buku; 
                
                // Pecah string berdasarkan tanda hubung '-'
                $parts = explode('-', $lastCode); 
                
                // Ambil bagian angkanya saja dan jadikan tipe data integer
                if (count($parts) == 3) {
                    $lastNumber = (int) $parts[2];
                }
            }

            // C. Simpan ke tabel `item_buku` (Salinan Fisik) melalui looping
            for ($i = 0; $i < $request->jumlah_buku; $i++) {
                
                // Tambahkan 1 ke angka terakhir untuk setiap salinan baru
                $lastNumber++;
                
                // Format ulang menjadi string dengan 3 digit (contoh: 1 menjadi "001")
                // Jika angkanya tembus 1000, str_pad otomatis menyesuaikan jadi 4 digit
                $kodeUnik = 'PAUD-'. str_pad($katalogBuku->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);

                ItemBuku::create([
                    'buku_id' => $katalogBuku->id, 
                    'kode_buku' => $kodeUnik,
                    'status_buku' => 'Tersedia', 
                    // 'asal_buku' => $request->asal_buku,
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
            // 'asal_buku' => 'required|in:Baru,Donasi',
        ]);

        // Pastikan katalog bukunya ada
        $buku = Buku::findOrFail($id);

        DB::beginTransaction();
        try {
            // Logika auto-increment kode unik (sama seperti saat create awal)
            $lastItem = ItemBuku::where('buku_id', $id)->orderBy('id', 'desc')->first();
            $lastNumber = 0;

            if ($lastItem) {
                $lastCode = $lastItem->kode_buku;
                $parts = explode('-', $lastCode);
                if (count($parts) == 3) {
                    $lastNumber = (int) $parts[2];
                }
            }

            // Looping sebanyak jumlah buku fisik yang ditambahkan
            for ($i = 0; $i < $request->jumlah_buku; $i++) {
                $lastNumber++;
                $kodeUnik = 'PAUD-' . str_pad($id, 3, '0', STR_PAD_LEFT) . '-' . str_pad($lastNumber, 3, '0', STR_PAD_LEFT);

                ItemBuku::create([
                    'buku_id' => $buku->id, // Menggunakan ID dari parameter URL
                    'kode_buku' => $kodeUnik,
                    'status_buku' => 'Tersedia',
                    // 'asal_buku' => $request->asal_buku,
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

    // MENAMPILKAN FORM EDIT KATALOG
    public function edit($id)
    {
        $buku = Buku::findOrFail($id);
        return view('admin.buku.edit', compact('buku'));
    }

    // MEMPROSES UPDATE KATALOG
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul_buku' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'tahun_terbit' => 'required|numeric',
        ]);

        $buku = Buku::findOrFail($id);
        
        $buku->update([
            'judul_buku' => $request->judul_buku,
            'penulis' => $request->penulis,
            'penerbit' => $request->penerbit,
            'tahun_terbit' => $request->tahun_terbit,
        ]);

        return redirect('/admin/buku')->with('success', 'Data katalog buku berhasil diperbarui!');
    }

    // MENGHAPUS KATALOG (HATI-HATI: INI AKAN MENGHAPUS SEMUA FISIK & TRANSAKSI)
    public function destroy($id)
    {
        $buku = Buku::with('itemBuku')->findOrFail($id);

        // Cek apakah ada buku fisik yang sedang dipinjam?
        $sedangDipinjam = $buku->itemBuku->whereIn('status_buku', ['Dipinjam', 'Di-booking'])->count();

        if ($sedangDipinjam > 0) {
            return back()->withErrors(['error' => 'Gagal menghapus! Masih ada ' . $sedangDipinjam . ' salinan fisik dari buku ini yang sedang dipinjam member.']);
        }

        // Jika aman, hapus katalog (Item fisik & Transaksi akan terhapus otomatis karena onCascadeDelete di migration)
        $buku->delete();

        return redirect('/admin/buku')->with('success', 'Katalog buku beserta seluruh salinan fisiknya berhasil dihapus.');
    }

    // MENGHAPUS SALAH SATU ITEM FISIK
    public function destroyFisik($id)
    {
        $item = ItemBuku::findOrFail($id);

        // Validasi: Jangan hapus jika sedang dipinjam
        if ($item->status_buku !== 'Tersedia') {
            return back()->withErrors(['error' => 'Gagal menghapus! Buku fisik dengan kode ' . $item->kode_buku . ' sedang dipinjam atau dibooking.']);
        }

        $item->delete();

        return back()->with('success', 'Salinan fisik dengan kode ' . $item->kode_buku . ' berhasil dihapus.');
    }



}