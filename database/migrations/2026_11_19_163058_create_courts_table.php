<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Membuat tabel 'courts' untuk menyimpan data lapangan olahraga[cite: 1]
        Schema::create('courts', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete(); // Relasi foreign key ke tabel categories; jika kategori dihapus, lapangan ikut terhapus
            $table->string('code')->unique(); // Kode unik lapangan, misalnya BSK-01 atau PDL-01[cite: 1, 5]
            $table->string('name'); // Nama lapangan, misalnya Basket 1 atau Padel 1[cite: 1, 5]
            $table->unsignedInteger('price_per_hour'); // Harga sewa per jam dalam rupiah (bilangan bulat positif)[cite: 1]
            $table->boolean('is_active')->default(true); // Status lapangan: Aktif atau Nonaktif[cite: 1]
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};
