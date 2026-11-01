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
        $produks = DB::table('produks')->select('id', 'satuan')->orderBy('id')->get();
        $cabangIds = DB::table('cabangs')->pluck('id')->all();
        $userIds = DB::table('users')->pluck('id')->all();

        if ($produks->isEmpty() || empty($cabangIds) || empty($userIds)) {
            throw new RuntimeException('Data Master (Produk, Cabang, User) belum lengkap.');
        }

        // 1. CARI ID AKUN STANDAR UNTUK TRANSAKSI PENJUALAN
        // Asumsi: Kamu memiliki nama_akun yang mengandung kata-kata ini di database
        $akunKas = DB::table('akuns')->where('nama_akun', 'like', '%Kas%')->first();
        $akunPendapatan = DB::table('akuns')->where('nama_akun', 'like', '%Pendapatan%')->orWhere('nama_akun', 'like', '%Penjualan%')->first();
        $akunHpp = DB::table('akuns')->where('nama_akun', 'like', '%Pokok Produksi%')->orWhere('nama_akun', 'like', '%Pokok Penjualan%')->first();
        $akunPersediaan = DB::table('akuns')->where('nama_akun', 'like', '%Persediaan%')->first();

        if (!$akunKas || !$akunPendapatan || !$akunHpp || !$akunPersediaan) {
            throw new RuntimeException('Data Master Akun (Kas, Pendapatan, HPP, Persediaan) tidak ditemukan. Pastikan AkunSeeder sudah dijalankan.');
        }

        $pelanggan = ['Andi Pratama', 'Siti Rahma', 'Budi Santoso', 'Dewi Lestari', 'Rizky Saputra', 'Nadia Putri', 'Fajar Ramadhan', 'Maya Sari', 'Dimas Setiawan', 'Rina Amelia'];
        $nomorHp = ['081234567801', '081234567802', '081234567803', '081234567804', '081234567805', '081234567806', '081234567807', '081234567808', '081234567809', '081234567810'];

        $jumlahDibuat = 0;

        for ($i = 1; $i <= 10; $i++) {
            $waktu = Carbon::now()->subDays($i - 1);
            $tanggal = $waktu->toDateString();
            $noReferensi = 'INV-' . $waktu->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT);
            $cabangIdTerpilih = $cabangIds[($i - 1) % count($cabangIds)];
            $userIdTerpilih = $userIds[($i - 1) % count($userIds)];

            if (DB::table('transaksis')->where('no_referensi', $noReferensi)->exists()) {
                continue;
            }

            // 2. BUAT HEADER TRANSAKSI
            $transaksiId = DB::table('transaksis')->insertGetId([
                'no_referensi' => $noReferensi,
                'tanggal' => $tanggal,
                'nama_pelanggan' => $pelanggan[$i - 1],
                'nomor_hp' => $nomorHp[$i - 1],
                'cabang_id' => $cabangIdTerpilih,
                'user_id' => $userIdTerpilih,
                'total_transaksi' => 0,
                'total_hpp' => 0,
                'created_at' => $waktu,
                'updated_at' => $waktu,
            ]);

            $totalTransaksi = 0;
            $totalHpp = 0;
            $jumlahDetail = min($produks->count(), (($i - 1) % 3) + 1);
            $produkTerpilih = $produks->shuffle()->take($jumlahDetail);

            foreach ($produkTerpilih as $index => $produk) {
                $jumlah = (($i + $index) % 5) + 1;
                $isM2 = in_array(strtolower(trim($produk->satuan ?? '')), ['m2', 'm²', 'meter persegi'], true);

                $lebar = $isM2 ? [1, 1.5, 2, 2.5, 3][($i + $index) % 5] : null;
                $tinggi = $isM2 ? [1, 1.5, 2, 3, 4][($i + $index) % 5] : null;
                $luas = $isM2 ? round($lebar * $tinggi * $jumlah, 2) : null;

                $hargaSatuan = [15000, 25000, 35000, 50000, 75000][($i + $index) % 5];
                $jasaEdit = [0, 5000, 10000][($i + $index) % 3];
                $subtotal = round(($hargaSatuan * ($isM2 ? $luas : $jumlah)) + $jasaEdit, 2);

                $hppSatuan = [3000, 5000, 7500, 10000][($i + $index) % 4];
                $totalHppItem = round($hppSatuan * $jumlah, 2);

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

            DB::table('transaksis')->where('id', $transaksiId)->update([
                'total_transaksi' => $totalTransaksi,
                'total_hpp' => $totalHpp,
                'updated_at' => $waktu,
            ]);

            // 3. BUAT HEADER JURNAL (One)
            $jurnalId = DB::table('jurnals')->insertGetId([
                'no_referensi' => $noReferensi,
                'tanggal' => $tanggal,
                'keterangan' => 'Penjualan ' . $noReferensi,
                'cabang_id' => $cabangIdTerpilih,
                'user_id' => $userIdTerpilih,
                'created_at' => $waktu,
                'updated_at' => $waktu,
            ]);

            // 4. BUAT DETAIL JURNAL (Many)
            DB::table('detail_jurnals')->insert([
                [
                    'jurnal_id' => $jurnalId,
                    'akun_id' => $akunKas->id,
                    'keterangan' => 'Penerimaan Kas - ' . $noReferensi,
                    'debit' => $totalTransaksi,
                    'kredit' => 0,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ],
                [
                    'jurnal_id' => $jurnalId,
                    'akun_id' => $akunPendapatan->id,
                    'keterangan' => 'Pendapatan Jasa - ' . $noReferensi,
                    'debit' => 0,
                    'kredit' => $totalTransaksi,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ],
                [
                    'jurnal_id' => $jurnalId,
                    'akun_id' => $akunHpp->id,
                    'keterangan' => 'HPP - ' . $noReferensi,
                    'debit' => $totalHpp,
                    'kredit' => 0,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ],
                [
                    'jurnal_id' => $jurnalId,
                    'akun_id' => $akunPersediaan->id,
                    'keterangan' => 'HPP - Pengurangan Persediaan',
                    'debit' => 0,
                    'kredit' => $totalHpp,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ]
            ]);

            $jumlahDibuat++;
        }

        $this->command->info("Seeder selesai. {$jumlahDibuat} transaksi & jurnal berhasil dibuat.");
    }
}
