<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    // Kolom data pembayaran dan bukti transfer yang diizinkan untuk diisi
    protected $fillable = [
        'booking_id',
        'amount',
        'proof_image',
        'status',
        'uploaded_at',
        'verified_at',
        'paid_at'
    ];

    // Relasi balik ke data booking terkait
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
