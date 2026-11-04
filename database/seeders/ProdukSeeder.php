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
        $cabangs = Cabang::all();

        if ($cabangs->isEmpty()) {
            $this->command->warn('Harap jalankan CabangSeeder terlebih dahulu.');
            return;
        }

        // 6 Produk yang SAMA di setiap cabang
        $produkUmum = [
            ['nama_produk' => 'Spanduk Flexi', 'satuan' => 'm2', 'upah_operator' => 2000],
            ['nama_produk' => 'Sticker Vinyl', 'satuan' => 'm2', 'upah_operator' => 2000],
            ['nama_produk' => 'Sticker Transparan', 'satuan' => 'm2', 'upah_operator' => 2500],
            ['nama_produk' => 'Undangan Art Paper', 'satuan' => 'pcs', 'upah_operator' => 50],
            ['nama_produk' => 'Stempel', 'satuan' => 'pcs', 'upah_operator' => 1000],
            ['nama_produk' => 'Neon Box', 'satuan' => 'pcs', 'upah_operator' => 10000],
        ];

        // 4 Produk BERBEDA untuk masing-masing cabang (Set 1, Set 2, Set 3)
        $produkUnik = [
            [ // Unik Cabang 1 (Pusat)
                ['nama_produk' => 'Brosur Premium', 'satuan' => 'lembar', 'upah_operator' => 100],
                ['nama_produk' => 'Plakat Akrilik', 'satuan' => 'pcs', 'upah_operator' => 5000],
                ['nama_produk' => 'ID Card PVC', 'satuan' => 'pcs', 'upah_operator' => 500],
                ['nama_produk' => 'Kalender Dinding', 'satuan' => 'pcs', 'upah_operator' => 1500],
            ],
            [ // Unik Cabang 2 (Pekanbaru)
                ['nama_produk' => 'Sablon Kaos', 'satuan' => 'pcs', 'upah_operator' => 1500],
                ['nama_produk' => 'Mug Custom', 'satuan' => 'pcs', 'upah_operator' => 1000],
                ['nama_produk' => 'Pin Peniti', 'satuan' => 'pcs', 'upah_operator' => 200],
                ['nama_produk' => 'Gantungan Kunci', 'satuan' => 'pcs', 'upah_operator' => 300],
            ],
            [ // Unik Cabang 3 (Kerinci)
                ['nama_produk' => 'Yasin Hardcover', 'satuan' => 'buku', 'upah_operator' => 2500],
                ['nama_produk' => 'Buku Nota', 'satuan' => 'buku', 'upah_operator' => 1000],
                ['nama_produk' => 'Sertifikat', 'satuan' => 'lembar', 'upah_operator' => 150],
                ['nama_produk' => 'Kop Surat', 'satuan' => 'rim', 'upah_operator' => 5000],
            ]
        ];

        $jumlahDibuat = 0;

        foreach ($cabangs as $index => $cabang) {
            // Ambil salah satu user admin/cs dari cabang ini
            $user = User::where('cabang_id', $cabang->id)->first();
            $userId = $user ? $user->id : null;

            // Gabungkan 6 produk umum + 4 produk unik milik cabang ini
            // Gunakan modulo (%) agar tidak error jika jumlah cabang bertambah
            $kumpulanProduk = array_merge($produkUmum, $produkUnik[$index % 3]);

            foreach ($kumpulanProduk as $produk) {
                Produk::updateOrCreate(
                    [
                        'nama_produk' => $produk['nama_produk'],
                        'cabang_id' => $cabang->id,
                    ],
                    [
                        'satuan' => $produk['satuan'],
                        'upah_operator' => $produk['upah_operator'],
                        'user_id' => $userId,
                        'status' => 'aktif',
                    ]
                );
                $jumlahDibuat++;
            }
        }

        $this->command->info("Seeder selesai. {$jumlahDibuat} produk berhasil dibagikan ke seluruh cabang.");
    }
}
