<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Tampilkan Form Profil
    public function edit()
    {
        $user = Auth::user();
        return view('member.profile', compact('user'));
    }

    // Update Profil
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username'     => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email'        => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'no_hp'        => 'required|numeric',
            'tempat_lahir' => 'nullable|string|max:100',
            'tgl_lahir'    => 'nullable|date',
            'alamat'       => 'nullable|string',
            'password'     => 'nullable|string|min:6|confirmed', // confirmed butuh field password_confirmation
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

        if ($request->filled('password')) {
            $dataUpdate['password'] = Hash::make($request->password);
        }

        // Kita gunakan Model User langsung agar intellisense jalan, atau $request->user()->update(...)
        /** @var \App\Models\User $user */
        $user->update($dataUpdate);

        return redirect('/')->with('success', 'Profil Anda berhasil diperbarui!');
    }
}