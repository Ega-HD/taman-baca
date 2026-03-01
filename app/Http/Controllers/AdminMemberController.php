<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminMemberController extends Controller
{
    // 1. TAMPILKAN DAFTAR MEMBER
    public function index()
    {
        // Hanya ambil user dengan role 'member'
        // Kita hitung juga berapa buku yang sedang dipinjam
        $members = User::where('role', 'member')
                    ->withCount(['transaksiPeminjaman as sedang_dipinjam' => function($query){
                        $query->whereIn('status', ['Menunggu Persetujuan', 'Sedang Dipinjam', 'Menunggu Pengembalian']);
                    }])
                    ->orderBy('id', 'desc')
                    ->get();

        return view('admin.member.index', compact('members'));
    }

    // 2. TAMPILKAN FORM TAMBAH
    public function create()
    {
        return view('admin.member.create');
    }

    // 3. PROSES SIMPAN MEMBER BARU
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_hp' => 'required|numeric',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'no_hp' => $request->no_hp,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'member', // Wajib set sebagai member
        ]);

        return redirect('/admin/members')->with('success', 'Akun member baru berhasil ditambahkan!');
    }

    // 4. TAMPILKAN FORM EDIT
    public function edit($id)
    {
        $member = User::where('role', 'member')->findOrFail($id);
        return view('admin.member.edit', compact('member'));
    }

    // 5. PROSES UPDATE MEMBER
    public function update(Request $request, $id)
    {
        $member = User::where('role', 'member')->findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => ['required', 'string', 'max:255', Rule::unique('users')->ignore($member->id)],
            'email'        => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($member->id)],
            'no_hp'        => 'required|numeric',
            'tempat_lahir' => 'nullable|string|max:100',
            'tgl_lahir'    => 'nullable|date',
            'alamat'       => 'nullable|string',
            'password'     => 'nullable|string|min:6', // Opsional
        ]);

        $dataUpdate = [
            'nama_lengkap' => $request->nama_lengkap,
            'username'     => $request->username,
            'email'        => $request->email,
            'no_hp'        => $request->no_hp,
            'tempat_lahir' => $request->tempat_lahir,
            'tgl_lahir'    => $request->tgl_lahir,
            'alamat'       => $request->alamat,
        ];

        // Hanya update password jika admin mengisinya
        if ($request->filled('password')) {
            $dataUpdate['password'] = Hash::make($request->password);
        }

        $member->update($dataUpdate);

        return redirect('/admin/members')->with('success', 'Data member berhasil diperbarui!');
    }

    // 6. HAPUS MEMBER
    public function destroy($id)
    {
        $member = User::withCount(['transaksiPeminjaman as sedang_dipinjam' => function($query){
            $query->whereIn('status', ['Menunggu Persetujuan', 'Sedang Dipinjam', 'Menunggu Pengembalian']);
        }])->findOrFail($id);

        // Validasi: Jangan hapus jika member masih bawa buku!
        if ($member->sedang_dipinjam > 0) {
            return back()->withErrors(['error' => 'Gagal menghapus! Member ini masih meminjam buku. Harap proses pengembalian terlebih dahulu.']);
        }

        $member->delete();

        return redirect('/admin/members')->with('success', 'Akun member berhasil dihapus.');
    }
}