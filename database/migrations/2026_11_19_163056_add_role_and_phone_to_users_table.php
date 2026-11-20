<?php

use Illuminate\Database\Migrations\Migration; // Mengimpor kelas dasar Migration Laravel
use Illuminate\Database\Schema\Blueprint; // Mengimpor Blueprint untuk mendefinisikan struktur kolom tabel
use Illuminate\Support\Facades\Schema; // Mengimpor Facade Schema untuk memodifikasi tabel database

return new class extends Migration
{
    // Method up() dijalankan saat perintah 'php artisan migrate' dieksekusi
    public function up(): void
    {
        // Memodifikasi tabel 'users' bawaan Laravel untuk menambah kolom nomor HP dan hak akses (role)
        Schema::table('users', function (Blueprint $table) {
            // Menambah kolom nomor telepon setelah kolom email jika belum ada
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            // Menambah kolom role pada baris terpisah agar tidak tertutup komentar
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'pelanggan'])->default('pelanggan')->after('password');
            }
        });
    }

    // Method down() dijalankan jika migrasi di-rollback untuk membatalkan perubahan
    public function down(): void
    {
        // Menghapus kolom phone dan role secara aman jika kolomnya memang ada
        Schema::table('users', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('users', 'phone')) {
                $columns[] = 'phone';
            }

            if (Schema::hasColumn('users', 'role')) {
                $columns[] = 'role';
            }

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
