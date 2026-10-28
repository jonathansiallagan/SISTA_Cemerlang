<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produk;

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $produks = [
            ['nama_produk' => 'Spanduk Flexi', 'satuan' => 'm2'],
            ['nama_produk' => 'Sticker Vinyl', 'satuan' => 'm2'],
            ['nama_produk' => 'Sticker Transparan', 'satuan' => 'm2'],
            ['nama_produk' => 'Undangan Art Paper', 'satuan' => 'pcs'],
            ['nama_produk' => 'Undangan Jasmine', 'satuan' => 'pcs'],
            ['nama_produk' => 'Stempel', 'satuan' => 'pcs'],
            ['nama_produk' => 'Neon Box', 'satuan' => 'pcs'],
            ['nama_produk' => 'Sablon Kaos', 'satuan' => 'pcs'],
        ];

        foreach ($produks as $produk) {
            Produk::firstOrCreate(
                [
                    'nama_produk' => $produk['nama_produk'],
                ],
                [
                    'satuan' => $produk['satuan'],
                ]
            );
        }
    }
}