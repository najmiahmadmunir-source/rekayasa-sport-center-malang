<?php

namespace App\Http\Middleware; // Namespace wajib sesuai lokasi file di app/Http/Middleware

use Closure; // Mengimpor Closure untuk meneruskan request ke middleware/controller berikutnya
use Illuminate\Http\Request; // Mengimpor Request HTTP masuk
use Illuminate\Support\Facades\Auth; // Mengimpor Auth untuk mengecek status & data user login

class RoleMiddleware
{
    // Method handle() memeriksa apakah user sudah login dan memiliki role sesuai rute
    public function handle(Request $request, Closure $next, string $role)
    {
        // Jika belum login, arahkan ke halaman login sesuai tipe portal yang diminta
        if (!Auth::check()) {
            return redirect()->route($role === 'admin' ? 'admin.login' : 'login');
        }

        // Jika role user tidak sesuai dengan portal yang diakses, arahkan ke dashboard milik role-nya sendiri
        if (Auth::user()->role !== $role) {
            return Auth::user()->role === 'admin'
                ? redirect()->route('admin.dashboard')
                : redirect()->route('user.dashboard');
        }

        // Jika sudah login dan role sesuai, lanjutkan request ke halaman tujuan
        return $next($request);
    }
}
