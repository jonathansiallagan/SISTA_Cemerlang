<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Produk;
use App\Models\BahanBaku;
use App\Models\KomposisiProduk;
use App\Models\Cabang;

class KomposisiProdukSeeder extends Seeder
{
    public function run(): void
    {
        $komposisi = [
            // ==========================================
            // PRODUK UMUM (Ada di semua cabang)
            // ==========================================
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

            // ==========================================
            // PRODUK UNIK CABANG 1 (Pusat)
            // ==========================================
            'Brosur Premium' => [
                ['Kertas Art Paper', 1, 'lembar'],
                ['Tinta Printer', 0.005, 'liter'],
            ],
            'Plakat Akrilik' => [
                ['Akrilik', 0.05, 'm2'],
            ],
            'ID Card PVC' => [
                ['Kertas Art Paper', 0.2, 'lembar'],
                ['Tinta Printer', 0.001, 'liter'],
            ],
            'Kalender Dinding' => [
                ['Kertas Art Paper', 5, 'lembar'],
                ['Tinta Printer', 0.02, 'liter'],
            ],

            // ==========================================
            // PRODUK UNIK CABANG 2 (Pekanbaru)
            // ==========================================
            'Sablon Kaos' => [
                ['Tinta Sablon', 0.05, 'liter'],
            ],
            'Mug Custom' => [
                ['Tinta Sablon', 0.01, 'liter'],
            ],
            'Pin Peniti' => [
                ['Kertas Art Paper', 0.1, 'lembar'],
            ],
            'Gantungan Kunci' => [
                ['Akrilik', 0.01, 'm2'],
            ],

            // ==========================================
            // PRODUK UNIK CABANG 3 (Kerinci)
            // ==========================================
            'Yasin Hardcover' => [
                ['Kertas Isi Yasin', 100, 'lembar'],
                ['Sampul Yasin', 1, 'pcs'],
                ['Toner Printer', 15, 'gram'],
            ],
            'Buku Nota' => [
                ['Kertas HVS F4', 0.1, 'rim'],
                ['Tinta Printer', 0.02, 'liter'],
            ],
            'Sertifikat' => [
                ['Kertas Art Paper', 1, 'lembar'],
                ['Tinta Printer', 0.002, 'liter'],
            ],
            'Kop Surat' => [
                ['Kertas A4', 1, 'rim'],
                ['Tinta Printer', 0.05, 'liter'],
            ],
        ];

        $cabangs = Cabang::pluck('id');

        foreach ($cabangs as $cabangId) {
            foreach ($komposisi as $namaProduk => $bahanList) {

                $produk = Produk::where('nama_produk', $namaProduk)
                    ->where('cabang_id', $cabangId)
                    ->first();

                if (!$produk) {
                    continue;
                }

                foreach ($bahanList as [$namaBahan, $jumlah, $satuan]) {

                    $bahan = BahanBaku::where('nama_bahan', $namaBahan)
                        ->where('cabang_id', $cabangId)
                        ->first();

                    if (!$bahan) {
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
}
