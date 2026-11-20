<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    // Kolom transaksi booking yang dapat diisi secara massal
    protected $fillable = [
        'booking_code',
        'user_id',
        'court_id',
        'booking_date',
        'start_time',
        'end_time',
        'duration_hours',
        'total_price',
        'status'
    ];

    // Relasi ke pemilik booking (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke lapangan yang dipesan (Court)
    public function court()
    {
        return $this->belongsTo(Court::class);
    }

    // Relasi One-to-One: Satu booking memiliki satu data pembayaran
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
