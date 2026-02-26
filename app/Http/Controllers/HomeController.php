<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Buku; // Jangan lupa panggil model Buku

class HomeController extends Controller
{
    public function index()
    {
        // Mengambil semua data buku dari database
        $buku = Buku::all(); 
        
        // Mengirim data $buku ke view bernama 'beranda'
        return view('beranda', compact('buku'));
    }
}