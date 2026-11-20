<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Membuat tabel 'categories' untuk menyimpan kategori olahraga (Basket, Padel, Badminton, Tenis)[cite: 1]
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // Primary key auto-increment
            $table->string('name'); // Nama kategori olahraga[cite: 1]
            $table->boolean('is_active')->default(true); // Status kategori: true = Aktif, false = Nonaktif[cite: 1]
            $table->timestamps(); // Kolom created_at dan updated_at otomatis
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories'); // Menghapus tabel categories saat rollback
    }
};
