<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
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
                    'nama_produk' => $produk['nama_produk'],
                ],
                [
                    'satuan' => $produk['satuan'],
                    'upah_operator' => $produk['upah_operator'],
                ]
            );
        }
    }
}