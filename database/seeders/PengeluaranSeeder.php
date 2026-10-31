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
        // 1. Ambil ID relasi Master
        $cabangIds = DB::table('cabangs')->pluck('id')->all();
        $userIds = DB::table('users')->pluck('id')->all();

        if (empty($cabangIds) || empty($userIds)) {
            $this->command->error('Data cabang dan user harus ada terlebih dahulu.');
            return;
        }

        // 2. Ambil ID Akun untuk Kas dan Beban
        $akunKas = DB::table('akuns')->where('nama_akun', 'like', '%Kas%')->first();
        // Mengambil semua akun beban operasional berdasarkan Seeder Akun Anda
        $akunBebans = DB::table('akuns')
            ->where('nama_akun', 'like', '%Beban%')
            ->orWhere('nama_akun', 'like', '%Biaya%')
            ->get();

        if (!$akunKas || $akunBebans->isEmpty()) {
            throw new RuntimeException('Data Master Akun Kas atau Beban tidak ditemukan.');
        }

        $jumlahDibuat = 0;
        $satuans = ['Unit', 'Bulan', 'Lembar', 'Paket', 'Liter'];

        for ($i = 1; $i <= 5; $i++) {
            $waktu = Carbon::now()->subDays($i * 2);
            $tanggal = $waktu->toDateString();
            $noReferensi = 'PNG-' . $waktu->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);

            $cabangIdTerpilih = $cabangIds[array_rand($cabangIds)];
            $userIdTerpilih = $userIds[array_rand($userIds)];

            // Pilih satu akun beban secara acak dari database
            $akunBebanTerpilih = $akunBebans->random();

            // Kalkulasi detail pengeluaran sesuai kolom baru (qty, harga, total_pengeluaran)
            $qty = rand(1, 5);
            $harga = rand(5, 50) * 10000; // Contoh: 50.000 hingga 500.000
            $totalPengeluaran = $qty * $harga;
            $satuanTerpilih = $satuans[array_rand($satuans)];

            // 3. INSERT HEADER PENGELUARAN (Sesuai kolom asli di file Model/Migrasi)
            DB::table('pengeluarans')->insert([
                'no_referensi' => $noReferensi,
                'tanggal' => $tanggal,
                'cabang_id' => $cabangIdTerpilih,
                'akun_id' => $akunBebanTerpilih->id,
                'keterangan' => 'Pembayaran ' . $akunBebanTerpilih->nama_akun . ' simulasi',
                'qty' => $qty,
                'satuan' => $satuanTerpilih,
                'harga' => $harga,
                'total_pengeluaran' => $totalPengeluaran,
                'user_id' => $userIdTerpilih,
                'created_at' => $waktu,
                'updated_at' => $waktu,
            ]);

            // 4. INSERT HEADER JURNAL
            $jurnalId = DB::table('jurnals')->insertGetId([
                'no_referensi' => $noReferensi,
                'tanggal' => $tanggal,
                'keterangan' => 'Pencatatan ' . $akunBebanTerpilih->nama_akun,
                'cabang_id' => $cabangIdTerpilih,
                'user_id' => $userIdTerpilih,
                'created_at' => $waktu,
                'updated_at' => $waktu,
            ]);

            // 5. INSERT DETAIL JURNAL (DEBIT: Beban, KREDIT: Kas) menggunakan nominal total_pengeluaran
            DB::table('detail_jurnals')->insert([
                [
                    'jurnal_id' => $jurnalId,
                    'akun_id' => $akunBebanTerpilih->id,
                    'keterangan' => 'Beban - ' . $noReferensi,
                    'debit' => $totalPengeluaran,
                    'kredit' => 0,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ],
                [
                    'jurnal_id' => $jurnalId,
                    'akun_id' => $akunKas->id,
                    'keterangan' => 'Pembayaran Beban - ' . $noReferensi,
                    'debit' => 0,
                    'kredit' => $totalPengeluaran,
                    'created_at' => $waktu,
                    'updated_at' => $waktu,
                ]
            ]);

            $jumlahDibuat++;
        }

        $this->command->info("Seeder selesai. {$jumlahDibuat} data pengeluaran & jurnal berhasil dibuat tanpa error.");
    }
}
