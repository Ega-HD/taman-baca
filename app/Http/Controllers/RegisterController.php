<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Menampilkan form register
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Memproses data register
    public function register(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users', // Username tidak boleh kembar
            'no_hp' => 'required|string|max:15',
            'password' => 'required|string|min:6|confirmed', // 'confirmed' menuntut adanya input name="password_confirmation"
        ]);

        // 2. Simpan ke Database
        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->password), // Password wajib di-hash/enkripsi
            'role' => 'member', // Default role otomatis jadi member
        ]);

        // 3. Redirect ke halaman login dengan pesan sukses
        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login dengan akun baru Anda.');
    }
}