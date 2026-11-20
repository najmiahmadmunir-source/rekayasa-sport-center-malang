<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Halaman utama langsung diarahkan ke halaman login pelanggan
Route::get('/', fn() => redirect()->route('login'));

// Grup rute untuk pengguna yang belum login (Guest)
// Jika sudah login sebagai pelanggan, diarahkan ke user.dashboard; jika admin, ke admin.dashboard
Route::middleware('guest')->group(function () {
    // Rute Login & Registrasi Pelanggan
    Route::get('/login', [AuthController::class, 'showUserAuth'])->name('login');
    Route::post('/login', [AuthController::class, 'userLogin'])->name('user.login.post');
    Route::post('/register', [AuthController::class, 'userRegister'])->name('user.register.post');

    // Rute Login Khusus Admin (tanpa rute registrasi)
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.post');
});

// Rute Logout untuk semua user yang sudah terautentikasi
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Grup rute khusus Portal Admin (dilindungi middleware auth dan role:admin)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard'); // Dashboard Admin

    // Manajemen Kategori & Lapangan (CRUD + Toggle Aktif/Nonaktif)
    Route::get('/kategori-lapangan', [AdminController::class, 'courts'])->name('courts');
    Route::post('/kategori', [AdminController::class, 'storeCategory'])->name('category.store');
    Route::put('/kategori/{category}', [AdminController::class, 'updateCategory'])->name('category.update');
    Route::patch('/kategori/{category}/toggle', [AdminController::class, 'toggleCategory'])->name('category.toggle');

    Route::post('/lapangan', [AdminController::class, 'storeCourt'])->name('court.store');
    Route::put('/lapangan/{court}', [AdminController::class, 'updateCourt'])->name('court.update');
    Route::patch('/lapangan/{court}/toggle', [AdminController::class, 'toggleCourt'])->name('court.toggle');

    // Manajemen Booking & Verifikasi Pembayaran
    Route::get('/booking-pembayaran', [AdminController::class, 'bookings'])->name('bookings');
    Route::get('/booking-pembayaran/{booking}', [AdminController::class, 'bookingDetail'])->name('booking.detail');
    Route::patch('/booking-pembayaran/{booking}/verify', [AdminController::class, 'verifyPayment'])->name('booking.verify');
    Route::patch('/booking-pembayaran/{booking}/reject', [AdminController::class, 'rejectPayment'])->name('booking.reject'); // Tolak pembayaran tidak valid
    Route::patch('/booking-pembayaran/{booking}/paid', [AdminController::class, 'markAsPaid'])->name('booking.paid');

    // Laporan Transaksi & Pendapatan Lunas
    Route::get('/laporan', [AdminController::class, 'reports'])->name('reports');
});

// Grup rute khusus Portal Pelanggan (dilindungi middleware auth dan role:pelanggan)
Route::prefix('user')->name('user.')->middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard'); // Katalog & pencarian lapangan
    Route::get('/lapangan/{court}', [UserController::class, 'courtDetail'])->name('court.detail'); // Detail lapangan & cek jadwal
    Route::post('/lapangan/{court}/booking', [UserController::class, 'storeBooking'])->name('booking.store'); // Proses booking & validasi

    Route::get('/booking-saya', [UserController::class, 'myBookings'])->name('bookings'); // Daftar booking milik sendiri
    Route::get('/booking-saya/{booking}', [UserController::class, 'myBookingDetail'])->name('booking.detail'); // Detail booking & pembayaran milik sendiri
    Route::post('/booking-saya/{booking}/upload-bukti', [UserController::class, 'uploadProof'])->name('booking.upload'); // Upload bukti bayar gambar
});
