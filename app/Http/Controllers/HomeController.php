<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku; // Jangan lupa panggil model Buku

class HomeController extends Controller
{
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

        // Mengambil semua katalog buku, beserta relasi item fisiknya (menggunakan Eloquent with)
        // Asumsinya kamu sudah membuat relasi hasMany('App\Models\ItemBuku') di Model Buku
        // $buku = Buku::with('itemBuku')->get(); 
        
        return view('beranda', compact('buku', 'list_tahun', 'list_penulis'));
    }
}