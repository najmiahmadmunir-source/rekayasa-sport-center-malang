<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // Kelas autentikasi bawaan Laravel
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // Daftar kolom yang diizinkan untuk diisi secara massal (Mass Assignment)
    protected $fillable = ['name', 'email', 'phone', 'password', 'role'];

    // Kolom sensitif yang disembunyikan saat data model diubah menjadi array/JSON
    protected $hidden = ['password', 'remember_token'];

    // Casting tipe data otomatis agar password selalu ter-hash
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Relasi One-to-Many: Satu pengguna (pelanggan) dapat memiliki banyak data booking
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
