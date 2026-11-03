<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;
use App\Models\Cabang;
use App\Models\User;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil data cabang dan user pertama dari database sebagai relasi default
        $cabang = Cabang::first();
        $user = User::first();

        if (!$cabang || !$user) {
            $this->command->warn('Harap jalankan CabangSeeder dan UserSeeder terlebih dahulu.');
            return;
        }

        $produks = [
            [
                'nama_produk' => 'Spanduk Flexi',
                'satuan' => 'm2',
                'upah_operator' => 2000,
            ],
            [
                'nama_produk' => 'Sticker Vinyl',
                'satuan' => 'm2',
                'upah_operator' => 2000,
            ],
            [
                'nama_produk' => 'Sticker Transparan',
                'satuan' => 'm2',
                'upah_operator' => 2500,
            ],
            [
                'nama_produk' => 'Undangan Art Paper',
                'satuan' => 'pcs',
                'upah_operator' => 50,
            ],
            [
                'nama_produk' => 'Undangan Jasmine',
                'satuan' => 'pcs',
                'upah_operator' => 50,
            ],
            [
                'nama_produk' => 'Stempel',
                'satuan' => 'pcs',
                'upah_operator' => 1000,
            ],
            [
                'nama_produk' => 'Neon Box',
                'satuan' => 'pcs',
                'upah_operator' => 10000,
            ],
            [
                'nama_produk' => 'Sablon Kaos',
                'satuan' => 'pcs',
                'upah_operator' => 1500,
            ],
        ];

        foreach ($produks as $produk) {
            Produk::updateOrCreate(
                [
                    // Pencarian unik berdasarkan nama produk DAN cabang
                    'nama_produk' => $produk['nama_produk'],
                    'cabang_id' => $cabang->id,
                ],
                [
                    'satuan' => $produk['satuan'],
                    'upah_operator' => $produk['upah_operator'],
                    'user_id' => $user->id,
                    'status' => 'aktif', // Status default
                ]
            );
        }
    }
}
