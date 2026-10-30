<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use RuntimeException;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $produks = DB::table('produks')
            ->select('id', 'satuan')
            ->orderBy('id')
            ->get();

        $cabangIds = DB::table('cabangs')->pluck('id')->all();
        $userIds = DB::table('users')->pluck('id')->all();

        if ($produks->isEmpty()) {
            throw new RuntimeException(
                'Belum ada data produk. Isi data produk terlebih dahulu.'
            );
        }

        if (empty($cabangIds) || empty($userIds)) {
            throw new RuntimeException(
                'Data cabang dan user harus tersedia terlebih dahulu.'
            );
        }

        $pelanggan = [
            'Andi Pratama',
            'Siti Rahma',
            'Budi Santoso',
            'Dewi Lestari',
            'Rizky Saputra',
            'Nadia Putri',
            'Fajar Ramadhan',
            'Maya Sari',
            'Dimas Setiawan',
            'Rina Amelia',
        ];

        $nomorHp = [
            '081234567801',
            '081234567802',
            '081234567803',
            '081234567804',
            '081234567805',
            '081234567806',
            '081234567807',
            '081234567808',
            '081234567809',
            '081234567810',
        ];

        $jumlahDibuat = 0;

        for ($i = 1; $i <= 10; $i++) {
            $waktu = Carbon::now()->subDays($i - 1);

            $tanggalInvoice = $waktu->format('Ymd');

            $noReferensi = 'INV-' . $tanggalInvoice . '-'
                . str_pad($i, 4, '0', STR_PAD_LEFT);

            // Lewati transaksi jika nomor referensi sudah ada.
            if (
                DB::table('transaksis')
                    ->where('no_referensi', $noReferensi)
                    ->exists()
            ) {
                continue;
            }

            $transaksiId = DB::table('transaksis')->insertGetId([
                'no_referensi' => $noReferensi,
                'tanggal' => $waktu->toDateString(),
                'nama_pelanggan' => $pelanggan[$i - 1],
                'nomor_hp' => $nomorHp[$i - 1],
                'cabang_id' => $cabangIds[
                    ($i - 1) % count($cabangIds)
                ],
                'user_id' => $userIds[
                    ($i - 1) % count($userIds)
                ],
                'total_transaksi' => 0,
                'total_hpp' => 0,
                'created_at' => $waktu,
                'updated_at' => $waktu,
            ]);

            $totalTransaksi = 0;
            $totalHpp = 0;

            // Setiap transaksi memiliki 1 sampai 3 detail produk.
            $jumlahDetail = min(
                $produks->count(),
                (($i - 1) % 3) + 1
            );

            $produkTerpilih = $produks
                ->shuffle()
                ->take($jumlahDetail);

            foreach ($produkTerpilih as $index => $produk) {
                $jumlah = (($i + $index) % 5) + 1;

                $satuan = strtolower(trim($produk->satuan ?? ''));

                $isM2 = in_array($satuan, [
                    'm2',
                    'm²',
                    'meter persegi',
                ], true) || str_contains($satuan, 'm²');

                $lebar = $isM2
                    ? [1, 1.5, 2, 2.5, 3][($i + $index) % 5]
                    : null;

                $tinggi = $isM2
                    ? [1, 1.5, 2, 3, 4][($i + $index) % 5]
                    : null;

                // Luas keseluruhan jika produk dihitung dalam m².
                $luas = $isM2
                    ? round($lebar * $tinggi * $jumlah, 2)
                    : null;

                // Harga jual simulasi.
                $hargaSatuan = [
                    15000,
                    25000,
                    35000,
                    50000,
                    75000,
                    100000,
                    125000,
                    150000,
                ][($i + $index) % 8];

                // Biaya jasa edit dimasukkan langsung ke subtotal.
                $jasaEdit = [
                    0,
                    5000,
                    10000,
                    15000,
                    20000,
                ][($i + $index) % 5];

                $quantityHarga = $isM2
                    ? $luas
                    : $jumlah;

                $subtotalCetak = $hargaSatuan * $quantityHarga;

                $subtotal = round(
                    $subtotalCetak + $jasaEdit,
                    2
                );

                // HPP simulasi untuk kebutuhan pengujian.
                $hppSatuan = [
                    3000,
                    5000,
                    7500,
                    10000,
                    12500,
                    15000,
                ][($i + $index) % 6];

                $totalHppItem = round(
                    $hppSatuan * $jumlah,
                    2
                );

                DB::table('detail_transaksis')->insert([
                    'transaksi_id' => $transaksiId,
                    'produk_id' => $produk->id,
                    'jumlah' => $jumlah,
                    'lebar' => $lebar,
                    'tinggi' => $tinggi,
                    'luas' => $luas,
                    'harga_satuan' => $hargaSatuan,
                    'hpp_satuan' => $hppSatuan,
                    'subtotal' => $subtotal,
                    'total_hpp' => $totalHppItem,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ]);

                $totalTransaksi += $subtotal;
                $totalHpp += $totalHppItem;
            }

            // Perbarui total transaksi induk.
            DB::table('transaksis')
                ->where('id', $transaksiId)
                ->update([
                    'total_transaksi' => $totalTransaksi,
                    'total_hpp' => $totalHpp,
                    'updated_at' => $waktu,
                ]);

            $jumlahDibuat++;
        }

        $this->command->info(
            "Seeder selesai. {$jumlahDibuat} transaksi dummy baru dibuat."
        );
    }
}
