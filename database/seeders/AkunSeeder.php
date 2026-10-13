<?php

namespace Database\Seeders;

use App\Models\Akun;
use Illuminate\Database\Seeder;

class AkunSeeder extends Seeder
{
    public function run(): void
    {
        $akuns = [
            [
                'kode_akun' => '1-1100',
                'nama_akun' => 'Kas',
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'status' => 'aktif',
            ],
            [
                'kode_akun' => '1-1200',
                'nama_akun' => 'Bank',
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'status' => 'aktif',
            ],
            [
                'kode_akun' => '1-1300',
                'nama_akun' => 'Persediaan',
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'status' => 'aktif',
            ],
            [
                'kode_akun' => '4-1100',
                'nama_akun' => 'Pendapatan Penjualan',
                'tipe_akun' => 'Pendapatan',
                'kelompok_akun' => 'Pendapatan Usaha',
                'status' => 'aktif',
            ],
            [
                'kode_akun' => '5-1100',
                'nama_akun' => 'Harga Pokok Produksi',
                'tipe_akun' => 'Beban/Biaya',
                'kelompok_akun' => 'HPP',
                'status' => 'aktif',
            ],
            [
                'kode_akun' => '5-2100',
                'nama_akun' => 'Beban Gaji/Upah',
                'tipe_akun' => 'Beban/Biaya',
                'kelompok_akun' => 'Beban Operasional',
                'status' => 'aktif',
            ],
            [
                'kode_akun' => '5-2200',
                'nama_akun' => 'Beban Listrik dan Air',
                'tipe_akun' => 'Beban/Biaya',
                'kelompok_akun' => 'Beban Operasional',
                'status' => 'aktif',
            ],
            [
                'kode_akun' => '5-2300',
                'nama_akun' => 'Beban Transportasi',
                'tipe_akun' => 'Beban/Biaya',
                'kelompok_akun' => 'Beban Operasional',
                'status' => 'aktif',
            ],
            [
                'kode_akun' => '5-2400',
                'nama_akun' => 'Beban Promosi',
                'tipe_akun' => 'Beban/Biaya',
                'kelompok_akun' => 'Beban Operasional',
                'status' => 'aktif',
            ],
            [
                'kode_akun' => '5-2500',
                'nama_akun' => 'Beban Administrasi',
                'tipe_akun' => 'Beban/Biaya',
                'kelompok_akun' => 'Beban Operasional',
                'status' => 'aktif',
            ],
            [
                'kode_akun' => '5-2600',
                'nama_akun' => 'Beban Pemeliharaan',
                'tipe_akun' => 'Beban/Biaya',
                'kelompok_akun' => 'Beban Operasional',
                'status' => 'aktif',
            ],
        ];

        foreach ($akuns as $akun) {
            Akun::updateOrCreate(
                ['kode_akun' => $akun['kode_akun']],
                $akun
            );
        }
    }
}