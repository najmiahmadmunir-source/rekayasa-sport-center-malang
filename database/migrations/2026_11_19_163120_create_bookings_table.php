<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Membuat tabel 'bookings' untuk mencatat transaksi pemesanan lapangan[cite: 1]
        Schema::create('bookings', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->string('booking_code')->unique(); // Kode booking unik yang diberikan ke pelanggan setelah memesan[cite: 1]
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // ID pelanggan yang melakukan booking
            $table->foreignId('court_id')->constrained('courts')->cascadeOnDelete(); // ID lapangan yang dipesan
            $table->date('booking_date'); // Tanggal bermain
            $table->time('start_time'); // Jam mulai bermain (operasional 07:00 - 22:00)[cite: 1]
            $table->time('end_time'); // Jam selesai bermain
            $table->unsignedTinyInteger('duration_hours'); // Durasi sewa dalam satuan jam
            $table->unsignedInteger('total_price'); // Total biaya hasil perhitungan durasi dikali harga per jam[cite: 1]
            $table->enum('status', ['aktif', 'selesai', 'dibatalkan'])->default('aktif'); // Status booking untuk pengecekan bentrok jadwal aktif[cite: 1]
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
