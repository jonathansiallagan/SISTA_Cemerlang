<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use RuntimeException;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $cabangs = DB::table('cabangs')->get();

        if ($cabangs->isEmpty()) {
            throw new RuntimeException('Data Cabang belum ada.');
        }

        $akunKas = DB::table('akuns')->where('nama_akun', 'like', '%Kas%')->first();
        $akunPendapatan = DB::table('akuns')->where('nama_akun', 'like', '%Pendapatan%')->orWhere('nama_akun', 'like', '%Penjualan%')->first();
        $akunHpp = DB::table('akuns')->where('nama_akun', 'like', '%Pokok Produksi%')->orWhere('nama_akun', 'like', '%Pokok Penjualan%')->first();
        $akunPersediaan = DB::table('akuns')->where('nama_akun', 'like', '%Persediaan%')->first();

        if (!$akunKas || !$akunPendapatan || !$akunHpp || !$akunPersediaan) {
            throw new RuntimeException('Data Master Akun (Kas, Pendapatan, HPP, Persediaan) tidak ditemukan.');
        }

        $pelanggan = ['Andi Pratama', 'Siti Rahma', 'Budi Santoso', 'Dewi Lestari', 'Rizky Saputra', 'Nadia Putri', 'Fajar Ramadhan', 'Maya Sari', 'Dimas Setiawan', 'Rina Amelia'];
        $nomorHp = ['081234567801', '081234567802', '081234567803', '081234567804', '081234567805', '081234567806', '081234567807', '081234567808', '081234567809', '081234567810'];

        $jumlahDibuat = 0;
        $counterGlobal = 1;

        // Loop setiap cabang
        foreach ($cabangs as $cabang) {

            $userIds = DB::table('users')->where('cabang_id', $cabang->id)->pluck('id')->all();
            // Hanya ambil produk yang dimiliki cabang ini
            $produks = DB::table('produks')->where('cabang_id', $cabang->id)->get();

            if (empty($userIds) || $produks->isEmpty()) {
                continue; // Lewati cabang jika user/produk belum ada
            }

            // Buat 10 Transaksi untuk cabang ini
            for ($i = 1; $i <= 10; $i++) {
                $waktu = Carbon::now()->subDays(10 - $i); // Berurutan dari 10 hari lalu ke hari ini
                $tanggal = $waktu->toDateString();

                // Format: INV-C{ID Cabang}-TANGGAL-URUTAN
                $noReferensi = 'INV-C' . $cabang->id . '-' . $waktu->format('Ymd') . '-' . str_pad($counterGlobal, 4, '0', STR_PAD_LEFT);
                $userIdTerpilih = $userIds[array_rand($userIds)];
                $pelangganTerpilih = $pelanggan[array_rand($pelanggan)];
                $hpTerpilih = $nomorHp[array_rand($nomorHp)];

                if (DB::table('transaksis')->where('no_referensi', $noReferensi)->exists()) {
                    continue;
                }

                $transaksiId = DB::table('transaksis')->insertGetId([
                    'no_referensi' => $noReferensi,
                    'tanggal' => $tanggal,
                    'nama_pelanggan' => $pelangganTerpilih,
                    'nomor_hp' => $hpTerpilih,
                    'cabang_id' => $cabang->id,
                    'user_id' => $userIdTerpilih,
                    'total_transaksi' => 0,
                    'total_hpp' => 0,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ]);

                $totalTransaksi = 0;
                $totalHpp = 0;
                // Ambil 1 sampai 3 produk secara acak
                $jumlahDetail = rand(1, 3);
                $produkTerpilih = $produks->shuffle()->take($jumlahDetail);

                foreach ($produkTerpilih as $produk) {
                    $jumlah = rand(1, 10);
                    $isM2 = in_array(strtolower(trim($produk->satuan ?? '')), ['m2', 'm²', 'meter persegi'], true);

                    $lebar = $isM2 ? rand(1, 5) : null;
                    $tinggi = $isM2 ? rand(1, 5) : null;
                    $luas = $isM2 ? round($lebar * $tinggi * $jumlah, 2) : null;

                    $hargaSatuan = rand(15, 100) * 1000;
                    $jasaEdit = (rand(1, 3) == 1) ? 10000 : 0; // 33% peluang kena jasa edit
                    $subtotal = round(($hargaSatuan * ($isM2 ? $luas : $jumlah)) + $jasaEdit, 2);

                    $hppSatuan = round($hargaSatuan * 0.4, 2); // Anggap HPP 40% dari harga jual
                    $totalHppItem = round($hppSatuan * $jumlah, 2);

                    DB::table('detail_transaksis')->insert([
                        'transaksi_id' => $transaksiId,
                        'produk_id' => $produk->id,
                        'jumlah' => $jumlah,
                        'lebar' => $lebar,
                        'tinggi' => $tinggi,
                        'luas' => $luas,
                        'harga_satuan' => $hargaSatuan,
                        'jasa_edit' => $jasaEdit,
                        'hpp_satuan' => $hppSatuan,
                        'subtotal' => $subtotal,
                        'total_hpp' => $totalHppItem,
                        'created_at' => $waktu,
                        'updated_at' => $waktu,
                    ]);

                    $totalTransaksi += $subtotal;
                    $totalHpp += $totalHppItem;
                }

                DB::table('transaksis')->where('id', $transaksiId)->update([
                    'total_transaksi' => $totalTransaksi,
                    'total_hpp' => $totalHpp,
                    'updated_at' => $waktu,
                ]);

                // Jurnal Penjualan
                $jurnalId = DB::table('jurnals')->insertGetId([
                    'no_referensi' => $noReferensi,
                    'tanggal' => $tanggal,
                    'keterangan' => 'Penjualan ' . $noReferensi,
                    'cabang_id' => $cabang->id,
                    'user_id' => $userIdTerpilih,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ]);

                DB::table('detail_jurnals')->insert([
                    ['jurnal_id' => $jurnalId, 'akun_id' => $akunKas->id, 'keterangan' => 'Kas', 'debit' => $totalTransaksi, 'kredit' => 0, 'created_at' => $waktu, 'updated_at' => $waktu],
                    ['jurnal_id' => $jurnalId, 'akun_id' => $akunPendapatan->id, 'keterangan' => 'Pendapatan', 'debit' => 0, 'kredit' => $totalTransaksi, 'created_at' => $waktu, 'updated_at' => $waktu],
                    ['jurnal_id' => $jurnalId, 'akun_id' => $akunHpp->id, 'keterangan' => 'HPP', 'debit' => $totalHpp, 'kredit' => 0, 'created_at' => $waktu, 'updated_at' => $waktu],
                    ['jurnal_id' => $jurnalId, 'akun_id' => $akunPersediaan->id, 'keterangan' => 'Persediaan', 'debit' => 0, 'kredit' => $totalHpp, 'created_at' => $waktu, 'updated_at' => $waktu]
                ]);

                $jumlahDibuat++;
                $counterGlobal++;
            }
        }

        $this->command->info("Seeder selesai. {$jumlahDibuat} transaksi & jurnal berhasil dibuat per-cabang.");
    }
}
