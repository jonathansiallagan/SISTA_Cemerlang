<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use RuntimeException;

class PengeluaranSeeder extends Seeder
{
    public function run(): void
    {
        $cabangs = DB::table('cabangs')->get();

        if ($cabangs->isEmpty()) {
            $this->command->error('Data cabang harus ada terlebih dahulu.');
            return;
        }

        $akunKas = DB::table('akuns')->where('nama_akun', 'like', '%Kas%')->first();
        $akunBebans = DB::table('akuns')->where('nama_akun', 'like', '%Beban%')->orWhere('nama_akun', 'like', '%Biaya%')->get();

        if (!$akunKas || $akunBebans->isEmpty()) {
            throw new RuntimeException('Data Master Akun Kas atau Beban tidak ditemukan.');
        }

        $jumlahDibuat = 0;
        $counterGlobal = 1;
        $satuans = ['Unit', 'Bulan', 'Lembar', 'Paket', 'Liter'];

        // Loop setiap cabang
        foreach ($cabangs as $cabang) {
            $userIds = DB::table('users')->where('cabang_id', $cabang->id)->pluck('id')->all();

            if (empty($userIds)) continue;

            // Buat 10 Pengeluaran untuk cabang ini
            for ($i = 1; $i <= 10; $i++) {
                $waktu = Carbon::now()->subDays(rand(1, 30)); // Waktu acak sebulan terakhir
                $tanggal = $waktu->toDateString();

                // Format unik: PNG-C{ID Cabang}-TANGGAL-URUTAN
                $noReferensi = 'PNG-C' . $cabang->id . '-' . $waktu->format('Ymd') . '-' . str_pad($counterGlobal, 3, '0', STR_PAD_LEFT);
                $userIdTerpilih = $userIds[array_rand($userIds)];
                $akunBebanTerpilih = $akunBebans->random();

                $qty = rand(1, 5);
                $harga = rand(5, 50) * 10000;
                $totalPengeluaran = $qty * $harga;
                $satuanTerpilih = $satuans[array_rand($satuans)];

                DB::table('pengeluarans')->insert([
                    'no_referensi' => $noReferensi,
                    'tanggal' => $tanggal,
                    'cabang_id' => $cabang->id,
                    'akun_id' => $akunBebanTerpilih->id,
                    'keterangan' => 'Pembayaran ' . $akunBebanTerpilih->nama_akun . ' (Simulasi)',
                    'qty' => $qty,
                    'satuan' => $satuanTerpilih,
                    'harga' => $harga,
                    'total_pengeluaran' => $totalPengeluaran,
                    'user_id' => $userIdTerpilih,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ]);

                $jurnalId = DB::table('jurnals')->insertGetId([
                    'no_referensi' => $noReferensi,
                    'tanggal' => $tanggal,
                    'keterangan' => 'Pencatatan ' . $akunBebanTerpilih->nama_akun,
                    'cabang_id' => $cabang->id,
                    'user_id' => $userIdTerpilih,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ]);

                DB::table('detail_jurnals')->insert([
                    ['jurnal_id' => $jurnalId, 'akun_id' => $akunBebanTerpilih->id, 'keterangan' => 'Beban', 'debit' => $totalPengeluaran, 'kredit' => 0, 'created_at' => $waktu, 'updated_at' => $waktu],
                    ['jurnal_id' => $jurnalId, 'akun_id' => $akunKas->id, 'keterangan' => 'Kas', 'debit' => 0, 'kredit' => $totalPengeluaran, 'created_at' => $waktu, 'updated_at' => $waktu]
                ]);

                $jumlahDibuat++;
                $counterGlobal++;
            }
        }

        $this->command->info("Seeder selesai. {$jumlahDibuat} data pengeluaran berhasil dibuat per-cabang.");
    }
}
