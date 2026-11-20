<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Court extends Model
{
    // Kolom data lapangan yang diizinkan untuk disimpan/diubah
    protected $fillable = ['category_id', 'code', 'name', 'price_per_hour', 'is_active'];

    // Relasi Many-to-One (BelongsTo): Setiap lapangan termasuk dalam satu kategori olahraga
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi One-to-Many: Satu lapangan dapat memiliki banyak riwayat booking
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
