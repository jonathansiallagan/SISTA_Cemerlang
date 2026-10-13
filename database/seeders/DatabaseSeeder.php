<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Cabang;
use App\Models\Produk;
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

        // 3. Buat Data Produk Dummy
        Produk::create([
            'nama_produk' => 'Spanduk Banner (Meteran)',
            'satuan' => 'm2',
            'harga_jual' => 25000,
            'hpp' => 15000,
        ]);
        
        Produk::create([
            'nama_produk' => 'Brosur A4',
            'satuan' => 'lembar',
            'harga_jual' => 1500,
            'hpp' => 1000,
        ]);
    
        // Seeder Akun
        $this->call([
            AkunSeeder::class,
        ]);
    
    }
}