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
        // Simpan ke variabel berbeda agar ID-nya bisa diambil secara akurat

        $pusat = Cabang::create([
            'nama' => 'Kantor Pusat',
            'alamat' => 'Jl. Sudirman No. 123',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ]);

        $cabangPekanbaru = Cabang::create([
            'nama' => 'Cabang Pekanbaru',
            'alamat' => 'Ruko Cemerlang, Jl. Riau No. 456',
            'no_hp' => '081234567891',
            'status' => 'aktif',
        ]);

        $cabangKerinci = Cabang::create([
            'nama' => 'Cabang Kerinci',
            'alamat' => 'Jl. Merdeka No. 789',
            'no_hp' => '081234567892', // nomor dibedakan sedikit
            'status' => 'aktif',
        ]);

        // Siapkan default password untuk semua user agar lebih rapi
        $defaultPassword = Hash::make('password');


        // 2. Buat Data User Dummy (1 Admin & 1 CS untuk setiap cabang)

        // --- A. AKUN KANTOR PUSAT ---
        User::create([
            'name' => 'Admin Pusat Utama',
            'email' => 'admin@contoh.com',
            'username' => 'adminpusat',
            'password' => $defaultPassword,
            'role' => 'Admin Pusat',
            'cabang_id' => $pusat->id,
        ]);

        User::create([
            'name' => 'CS Pusat',
            'email' => 'cs.pusat@contoh.com',
            'username' => 'cspusat',
            'password' => $defaultPassword,
            'role' => 'Customer Service',
            'cabang_id' => $pusat->id,
        ]);


        // --- B. AKUN CABANG PEKANBARU ---
        User::create([
            'name' => 'Admin Pekanbaru',
            'email' => 'admin.pku@contoh.com',
            'username' => 'adminpku',
            'password' => $defaultPassword,
            'role' => 'Admin Cabang',
            'cabang_id' => $cabangPekanbaru->id,
        ]);

        User::create([
            'name' => 'CS Pekanbaru',
            'email' => 'cs.pku@contoh.com',
            'username' => 'cspku',
            'password' => $defaultPassword,
            'role' => 'Customer Service',
            'cabang_id' => $cabangPekanbaru->id,
        ]);


        // --- C. AKUN CABANG KERINCI ---
        User::create([
            'name' => 'Admin Kerinci',
            'email' => 'admin.kerinci@contoh.com',
            'username' => 'adminkerinci',
            'password' => $defaultPassword,
            'role' => 'Admin Cabang',
            'cabang_id' => $cabangKerinci->id,
        ]);

        User::create([
            'name' => 'CS Kerinci',
            'email' => 'cs.kerinci@contoh.com',
            'username' => 'cskerinci',
            'password' => $defaultPassword,
            'role' => 'Customer Service',
            'cabang_id' => $cabangKerinci->id,
        ]);


        // 3. Seeder Lainnya
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
