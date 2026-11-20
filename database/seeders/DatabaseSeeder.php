<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Court;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Mengimpor Hash untuk mengenkripsi password

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Membuat akun default Admin (karena admin tidak memiliki fitur registrasi)[cite: 1, 7]
        User::updateOrCreate(
            ['email' => 'admin@rekayasasport.id'], // Kunci pencarian agar tidak duplikat saat seeder dijalankan ulang
            [
                'name' => 'Administrator',
                'phone' => '081234567890',
                'password' => Hash::make('admin123'), // Enkripsi password dengan Bcrypt
                'role' => 'admin', // Set role sebagai admin
            ]
        );

        // Membuat akun contoh Pelanggan untuk pengujian cepat
        User::updateOrCreate(
            ['email' => 'alya.putri@email.com'],
            [
                'name' => 'Alya Putri',
                'phone' => '081298765432',
                'password' => Hash::make('user123'),
                'role' => 'pelanggan', // Set role sebagai pelanggan
            ]
        );

        // Membuat 4 kategori olahraga sesuai spesifikasi soal UKK[cite: 1]
        $basket = Category::firstOrCreate(['name' => 'Basket'], ['is_active' => true]);
        $padel = Category::firstOrCreate(['name' => 'Padel'], ['is_active' => true]);
        $badminton = Category::firstOrCreate(['name' => 'Badminton'], ['is_active' => true]);
        $tenis = Category::firstOrCreate(['name' => 'Tenis'], ['is_active' => true]);

        // Daftar 8 lapangan awal beserta kode, kategori, harga per jam, dan status[cite: 1, 5]
        $courts = [
            ['category_id' => $basket->id, 'code' => 'BSK-01', 'name' => 'Basket 1', 'price_per_hour' => 100000, 'is_active' => true],
            ['category_id' => $basket->id, 'code' => 'BSK-02', 'name' => 'Basket 2', 'price_per_hour' => 100000, 'is_active' => true],
            ['category_id' => $padel->id, 'code' => 'PDL-01', 'name' => 'Padel 1', 'price_per_hour' => 150000, 'is_active' => true],
            ['category_id' => $padel->id, 'code' => 'PDL-02', 'name' => 'Padel 2', 'price_per_hour' => 150000, 'is_active' => true],
            ['category_id' => $badminton->id, 'code' => 'BDM-01', 'name' => 'Badminton 1', 'price_per_hour' => 60000, 'is_active' => true],
            ['category_id' => $badminton->id, 'code' => 'BDM-02', 'name' => 'Badminton 2', 'price_per_hour' => 60000, 'is_active' => true],
            ['category_id' => $tenis->id, 'code' => 'TNS-01', 'name' => 'Tenis 1', 'price_per_hour' => 80000, 'is_active' => true],
            ['category_id' => $tenis->id, 'code' => 'TNS-02', 'name' => 'Tenis 2', 'price_per_hour' => 80000, 'is_active' => false],
        ];

        // Menyimpan seluruh data lapangan ke database
        foreach ($courts as $court) {
            Court::updateOrCreate(['code' => $court['code']], $court);
        }
    }
}
