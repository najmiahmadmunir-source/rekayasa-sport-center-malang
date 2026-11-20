<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman Login & Registrasi Pelanggan (dipanggil oleh GET /login)
    public function showUserAuth()
    {
        // Jika view auth.user-auth ada gunakan itu, jika tidak fallback ke auth.login
        if (view()->exists('auth.user-auth')) {
            return view('auth.user-auth');
        }

        return view('auth.login');
    }

    // Memproses login pelanggan (dipanggil oleh POST /login)
    public function userLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Jika ternyata yang login akun admin, arahkan ke dashboard admin
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('user.dashboard'));
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    // Memproses registrasi akun pelanggan baru lalu arahkan ke halaman login terlebih dahulu
    public function userRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar, silakan gunakan email lain atau login.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Simpan akun baru dengan role 'pelanggan'
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'pelanggan',
        ]);

        // Tidak login otomatis: arahkan ke halaman login agar user login terlebih dahulu
        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Silakan login terlebih dahulu menggunakan akun Anda.');
    }

    // Menampilkan halaman login khusus admin (dipanggil oleh GET /admin/login)
    public function showAdminLogin()
    {
        if (view()->exists('auth.admin-login')) {
            return view('auth.admin-login');
        }

        return view('auth.login');
    }

    // Memproses login khusus admin (dipanggil oleh POST /admin/login)
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withErrors(['email' => 'Akun ini bukan akun admin.'])
                    ->onlyInput('email');
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()
            ->withErrors(['email' => 'Email atau password admin salah.'])
            ->onlyInput('email');
    }

    // Memproses logout semua user
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah keluar dari akun.');
    }
}
