<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // 2. Cek apakah role-nya Admin atau Super Admin
        $user = Auth::user();
        if ($user->role === 'admin') {
            return $next($request); // Silakan masuk
        }

        // 3. Jika bukan admin (misal: pengunjung), tendang ke beranda
        return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman Administrator.');
    }
}