<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cabang;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Data Cabang Dummy
        $cabang = Cabang::create([
            'nama' => 'Cabang Utama',
            'alamat' => 'Jl. Sudirman No. 123',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ]);

        $cabang = Cabang::create([
            'nama' => 'Pekanbaru',
            'alamat' => 'Ruko Cemerlang, Jl. Riau No. 456',
            'no_hp' => '081234567891',
            'status' => 'aktif',
        ]);

        $cabang = Cabang::create([
            'nama' => 'Kerinci',
            'alamat' => 'Jl. Merdeka No. 789',
            'no_hp' => '081234567891',
            'status' => 'aktif',
        ]);

        // 2. Buat Data User Dummy
        // A. Admin Pusat
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@contoh.com',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'Admin Pusat',
            'cabang_id' => $cabang->id,
        ]);

        // B. Admin Cabang
        User::create([
            'name' => 'Admin Cabang',
            'email' => 'admin.cabang@contoh.php',
            'username' => 'admin_cabang',
            'password' => Hash::make('password'),
            'role' => 'Admin Cabang',
            'cabang_id' => $cabang->id,
        ]);

        // C. Customer Service
        User::create([
            'name' => 'Customer Service',
            'email' => 'cs@contoh.com',
            'username' => 'customer_service',
            'password' => Hash::make('password'),
            'role' => 'Customer Service',
            'cabang_id' => $cabang->id,
        ]);

        // Seeder Akun
        $this->call([
            AkunSeeder::class,
            BahanBakuSeeder::class,
            ProdukSeeder::class,
            KomposisiProdukSeeder::class,
            TransaksiSeeder::class,
            PengeluaranSeeder::class,
        ]);
    
    }
}