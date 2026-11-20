<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Kolom yang boleh diisi secara massal
    protected $fillable = ['name', 'is_active'];

    // Relasi One-to-Many: Satu kategori olahraga memiliki banyak lapangan
    public function courts()
    {
        return $this->hasMany(Court::class);
    }
}
