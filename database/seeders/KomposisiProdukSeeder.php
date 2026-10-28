<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Produk;
use App\Models\BahanBaku;
use App\Models\KomposisiProduk;

class KomposisiProdukSeeder extends Seeder
{
    public function run(): void
    {
        $komposisi = [
            'Spanduk Flexi' => [
                ['Flexi Banner', 1, 'm2'],
                ['Tinta Printing', 0.03, 'liter'],
            ],

            'Sticker Vinyl' => [
                ['Media Sticker Vinyl', 1, 'm2'],
                ['Tinta Printing', 0.03, 'liter'],
            ],

            'Sticker Transparan' => [
                ['Media Sticker Transparan', 1, 'm2'],
                ['Tinta Printing', 0.03, 'liter'],
            ],

            'Undangan Art Paper' => [
                ['Kertas Art Paper', 1, 'lembar'],
                ['Tinta Printing', 0.005, 'liter'],
            ],

            'Undangan Jasmine' => [
                ['Kertas Jasmine', 1, 'lembar'],
                ['Tinta Printing', 0.005, 'liter'],
            ],

            'Stempel' => [
                ['Karet Stempel', 1, 'pcs'],
                ['Gagang Stempel', 1, 'pcs'],
            ],

            'Neon Box' => [
                ['Akrilik', 1, 'm2'],
                ['Lampu LED', 10, 'pcs'],
                ['Rangka Neon Box', 2, 'meter'],
            ],

            'Sablon Kaos' => [
                ['Tinta Sablon', 0.05, 'liter'],
            ],
        ];

        foreach ($komposisi as $namaProduk => $bahanList) {
            $produk = Produk::where(
                'nama_produk',
                $namaProduk
            )->first();

            if (!$produk) {
                $this->command?->warn(
                    "Produk {$namaProduk} tidak ditemukan."
                );
                continue;
            }

            foreach ($bahanList as [$namaBahan, $jumlah, $satuan]) {
                $bahan = BahanBaku::where(
                    'nama_bahan',
                    $namaBahan
                )->first();

                if (!$bahan) {
                    $this->command?->warn(
                        "Bahan {$namaBahan} tidak ditemukan."
                    );
                    continue;
                }

                KomposisiProduk::updateOrCreate(
                    [
                        'produk_id' => $produk->id,
                        'bahan_baku_id' => $bahan->id,
                    ],
                    [
                        'jumlah_pemakaian' => $jumlah,
                        'satuan_pemakaian' => $satuan,
                    ]
                );
            }
        }
    }
}
