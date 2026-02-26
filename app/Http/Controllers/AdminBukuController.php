<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku;
use Carbon\Carbon;

class AdminBukuController extends Controller
{
    // Menampilkan daftar buku (Read)
    public function index()
    {
        // Mengambil semua data buku, diurutkan dari yang terbaru
        $buku = Buku::orderBy('id', 'desc')->get();
        return view('admin.buku.index', compact('buku'));
    }

    // Menampilkan form tambah buku (Create)
    public function create()
    {
        return view('admin.buku.create');
    }

    // Memproses data dari form ke database (Store)
    public function store(Request $request)
    {
        // Validasi inputan admin
        $request->validate([
            'judul_buku' => 'required|string|max:255',
            'asal_buku' => 'required|in:Baru,Donasi',
            'penulis' => 'nullable|string|max:255',
            'penerbit' => 'nullable|string|max:255',
            'tahun_terbit' => 'nullable|numeric',
        ]);

        // Simpan ke database
        Buku::create([
            'judul_buku' => $request->judul_buku,
            'tgl_ditambahkan' => Carbon::now(), // Tanggal otomatis hari ini
            'status_buku' => 'Tersedia', // Default selalu Tersedia saat baru ditambah
            'asal_buku' => $request->asal_buku,
            'penulis' => $request->penulis,
            'penerbit' => $request->penerbit,
            'tahun_terbit' => $request->tahun_terbit,
        ]);

        // Kembalikan ke halaman daftar buku dengan pesan sukses
        return redirect('/admin/buku')->with('success', 'Buku berhasil ditambahkan ke dalam sistem!');
    }
}