<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BahanBakuSeeder extends Seeder
{
    public function run(): void
    {
        $bahanBakus = [
            [
                'kode_bahan' => 'BB001',
                'nama_bahan' => 'Kertas A4',
                'spesifikasi' => 'Ukuran 210 x 297 mm, gramasi 80 gsm, 500 lembar per rim',
                'satuan' => 'rim',
                'stok' => 100,
                'harga_rata_rata' => 40000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB002',
                'nama_bahan' => 'Flexi Banner',
                'spesifikasi' => 'Frontlit, gramasi 280 gsm, lebar roll asumsi 3,2 meter',
                'satuan' => 'm2',
                'stok' => 88,
                'harga_rata_rata' => 10000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB003',
                'nama_bahan' => 'Tinta Printer',
                'spesifikasi' => 'Tinta inkjet cair, warna CMYK, kompatibilitas printer sesuai tipe',
                'satuan' => 'liter',
                'stok' => 4.640,
                'harga_rata_rata' => 150000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB004',
                'nama_bahan' => 'Kertas Art Paper',
                'spesifikasi' => 'Ukuran A3, gramasi 260 gsm',
                'satuan' => 'lembar',
                'stok' => 1000,
                'harga_rata_rata' => 500,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB005',
                'nama_bahan' => 'Tinta Printing',
                'spesifikasi' => 'Tinta digital printing solvent, warna CMYK',
                'satuan' => 'liter',
                'stok' => 2,
                'harga_rata_rata' => 120000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB006',
                'nama_bahan' => 'Kertas Jasmine',
                'spesifikasi' => 'Kertas bertekstur untuk undangan, gramasi asumsi 220 gsm',
                'satuan' => 'lembar',
                'stok' => 2000,
                'harga_rata_rata' => 800,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB007',
                'nama_bahan' => 'Media Sticker Vinyl',
                'spesifikasi' => 'Vinyl putih glossy, lebar roll asumsi 1,22 meter',
                'satuan' => 'm2',
                'stok' => 50,
                'harga_rata_rata' => 15000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB008',
                'nama_bahan' => 'Media Sticker Transparan',
                'spesifikasi' => 'Vinyl transparan, lebar roll asumsi 1,22 meter',
                'satuan' => 'm2',
                'stok' => 30,
                'harga_rata_rata' => 20000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB009',
                'nama_bahan' => 'Kertas HVS F4',
                'spesifikasi' => 'Ukuran 210 x 330 mm, gramasi 70 gsm, 500 lembar per rim',
                'satuan' => 'rim',
                'stok' => 80,
                'harga_rata_rata' => 45000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB010',
                'nama_bahan' => 'Toner Printer',
                'spesifikasi' => 'Toner bubuk untuk printer laser, monokrom',
                'satuan' => 'gram',
                'stok' => 5000,
                'harga_rata_rata' => 200,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB011',
                'nama_bahan' => 'Karet Stempel',
                'spesifikasi' => 'Karet polimer untuk pembuatan cap stempel, ukuran sesuai pesanan',
                'satuan' => 'pcs',
                'stok' => 100,
                'harga_rata_rata' => 5000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB012',
                'nama_bahan' => 'Gagang Stempel',
                'spesifikasi' => 'Gagang plastik stempel, ukuran standar',
                'satuan' => 'pcs',
                'stok' => 100,
                'harga_rata_rata' => 7000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB013',
                'nama_bahan' => 'Akrilik',
                'spesifikasi' => 'Lembaran akrilik, tebal asumsi 3 mm, ukuran 122 x 244 cm',
                'satuan' => 'm2',
                'stok' => 20,
                'harga_rata_rata' => 150000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB014',
                'nama_bahan' => 'Lampu LED',
                'spesifikasi' => 'Modul LED 12 V untuk pencahayaan neon box',
                'satuan' => 'pcs',
                'stok' => 100,
                'harga_rata_rata' => 10000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB015',
                'nama_bahan' => 'Rangka Neon Box',
                'spesifikasi' => 'Profil aluminium untuk rangka neon box, ukuran sesuai pesanan',
                'satuan' => 'meter',
                'stok' => 100,
                'harga_rata_rata' => 25000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB016',
                'nama_bahan' => 'Tinta Sablon',
                'spesifikasi' => 'Tinta sablon tekstil, warna mengikuti kebutuhan desain',
                'satuan' => 'liter',
                'stok' => 10,
                'harga_rata_rata' => 80000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB017',
                'nama_bahan' => 'Kertas Undangan',
                'spesifikasi' => 'Kertas cetak undangan, gramasi asumsi 220 gsm',
                'satuan' => 'lembar',
                'stok' => 3000,
                'harga_rata_rata' => 1000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB018',
                'nama_bahan' => 'Kertas Isi Yasin',
                'spesifikasi' => 'Kertas isi buku Yasin, gramasi asumsi 70 gsm',
                'satuan' => 'lembar',
                'stok' => 5000,
                'harga_rata_rata' => 300,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB019',
                'nama_bahan' => 'Sampul Yasin',
                'spesifikasi' => 'Sampul buku Yasin, bahan Art Paper, gramasi asumsi 260 gsm',
                'satuan' => 'pcs',
                'stok' => 500,
                'harga_rata_rata' => 2500,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB020',
                'nama_bahan' => 'Bahan Rangka Papan Reklame',
                'spesifikasi' => 'Besi hollow untuk rangka papan reklame, ukuran sesuai desain',
                'satuan' => 'meter',
                'stok' => 100,
                'harga_rata_rata' => 30000,
                'status' => 'aktif',
            ],
        ];

        foreach ($bahanBakus as $bahan) {
            DB::table('bahan_baku')->updateOrInsert(
                ['kode_bahan' => $bahan['kode_bahan']],
                array_merge($bahan, [
                    'updated_at' => now(),
                ])
            );
        }
    }
}