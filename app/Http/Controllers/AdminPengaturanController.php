<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengaturan;

class AdminPengaturanController extends Controller
{
    public function index()
    {
        // Ambil pengaturan pertama, jika tidak ada buat baru
        $pengaturan = Pengaturan::firstOrCreate([], ['denda_per_hari' => 1000]);
        return view('admin.pengaturan.index', compact('pengaturan'));
    }

    public function update(Request $request)
    {
        $request->validate(['denda_per_hari' => 'required|integer|min:0']);
        
        $pengaturan = Pengaturan::first();
        $pengaturan->update(['denda_per_hari' => $request->denda_per_hari]);

        return back()->with('success', 'Tarif denda berhasil diperbarui!');
    }
}