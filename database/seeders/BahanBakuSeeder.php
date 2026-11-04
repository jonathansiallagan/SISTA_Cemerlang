<?php

namespace Database\Seeders;

use App\Models\BahanBaku;
use App\Models\Cabang;
use Illuminate\Database\Seeder;

class BahanBakuSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua id cabang yang ada di sistem
        $cabangs = Cabang::pluck('id')->toArray();

        // Jika belum ada cabang yang ter-seed, lewati
        if (empty($cabangs)) return;

        // Data master bahan baku (tanpa cabang_id)
        $bahanBakus = [
            [
                'kode_bahan' => 'BB001',
                'nama_bahan' => 'Kertas A4',
                'spesifikasi' => 'Ukuran 210 x 297 mm, 500 lembar per rim',
                'satuan' => 'rim',
                'stok' => 100,
                'harga_rata_rata' => 40000,
                'status' => 'aktif',
            ],
            [
                'kode_bahan' => 'BB002',
                'nama_bahan' => 'Flexi Banner',
                'spesifikasi' => 'Frontlit, gramasi 280 gsm',
                'satuan' => 'm2',
                'stok' => 88,
                'harga_rata_rata' => 10000,
                'status' => 'aktif',
            ],
            // ... (masukkan sisa BB003 sampai BB020 milikmu di sini persis seperti sebelumnya) ...
            [
                'kode_bahan' => 'BB020',
                'nama_bahan' => 'Bahan Rangka Papan Reklame',
                'spesifikasi' => 'Besi hollow untuk rangka papan reklame',
                'satuan' => 'meter',
                'stok' => 100,
                'harga_rata_rata' => 30000,
                'status' => 'aktif',
            ],
        ];

        // Looping untuk menyebarkan semua bahan baku ini ke SETIAP cabang
        foreach ($cabangs as $cabangId) {
            foreach ($bahanBakus as $bahan) {
                // Menentukan unique constraint ganda: cabang_id & kode_bahan
                BahanBaku::updateOrCreate(
                    [
                        'cabang_id'  => $cabangId,
                        'kode_bahan' => $bahan['kode_bahan']
                    ],
                    array_merge($bahan, ['cabang_id' => $cabangId])
                );
            }
        }
    }
}
