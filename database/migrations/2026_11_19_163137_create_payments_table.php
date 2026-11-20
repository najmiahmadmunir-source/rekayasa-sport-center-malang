<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Membuat tabel 'payments' untuk menyimpan bukti bayar gambar dan status verifikasi admin
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete(); // Relasi one-to-one ke tabel bookings
            $table->unsignedInteger('amount'); // Nominal tagihan pembayaran
            $table->string('proof_image')->nullable(); // Menyimpan path/lokasi file gambar bukti transfer pelanggan[cite: 4]
            $table->enum('status', ['belum_bayar', 'menunggu_cek', 'terverifikasi', 'lunas'])->default('belum_bayar'); // Tahapan status pembayaran hingga lunas[cite: 1, 2, 4]
            $table->timestamp('uploaded_at')->nullable(); // Waktu pelanggan mengunggah bukti bayar
            $table->timestamp('verified_at')->nullable(); // Waktu admin memverifikasi bukti bayar[cite: 1]
            $table->timestamp('paid_at')->nullable(); // Waktu admin mengubah status menjadi lunas[cite: 1]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
