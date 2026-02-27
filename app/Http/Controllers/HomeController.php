<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku; // Jangan lupa panggil model Buku

class HomeController extends Controller
{
    public function index()
    {
        // Mengambil semua katalog buku, beserta relasi item fisiknya (menggunakan Eloquent with)
        // Asumsinya kamu sudah membuat relasi hasMany('App\Models\ItemBuku') di Model Buku
        $buku = Buku::with('itemBuku')->get(); 
        
        return view('beranda', compact('buku'));
    }
}