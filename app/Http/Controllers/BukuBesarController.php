<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\DetailJurnal;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BukuBesarController extends Controller
{
    /** Halaman buku besar (tampilan di layar) */
    public function index(Request $request)
    {
        return view('admin.buku_besar.index', $this->siapkanLaporan($request));
    }

    /** Cetak dalam bentuk file PDF */
    public function cetakPdf(Request $request)
    {
        // Paket PDF belum terpasang -> beri pesan jelas
        if (!class_exists(Pdf::class)) {
            return back()->with('error', 'Fitur PDF belum aktif. Jalankan: composer require barryvdh/laravel-dompdf');
        }

        $data = $this->siapkanLaporan($request);

        $pdf = Pdf::loadView('admin.buku_besar.pdf', $data)
            ->setPaper('a4', 'landscape');

        // Kirim sebagai file unduhan (output() menghasilkan isi PDF berupa teks)
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $this->namaFile($data, 'pdf') . '"',
        ]);
    }
    /** Cetak dalam bentuk file CSV (dibuka dengan Excel) */
    public function cetakCsv(Request $request)
    {
        $data = $this->siapkanLaporan($request);

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM UTF-8 supaya Excel membaca dengan benar

            // Pemisah ';' dan desimal ',' agar terbaca benar di Excel berbahasa Indonesia
            $tulis = fn(array $baris) => fputcsv($out, $baris, ';', '"', '');
            $angka = fn($n) => number_format((float) $n, 2, ',', '');

            $tulis([
                'Kode Akun', 'Nama Akun', 'Tanggal', 'No Referensi', 'Keterangan',
                'Kuantitas', 'Satuan', 'Harga', 'Total', 'Debit', 'Kredit',
                'Saldo Normal', 'Sisi Saldo',
            ]);

            foreach ($data['laporan'] as $item) {
                $akun = $item['akun'];

                // Baris saldo awal
                $tulis([
                    $akun->kode_akun, $akun->nama_akun, '', '', 'Saldo Awal',
                    '', '', '', '', '', '',
                    $angka($item['saldoAwal']), $item['sisiAwal'],
                ]);

                // Baris mutasi
                foreach ($item['baris'] as $detail) {
                    $tulis([
                        $akun->kode_akun,
                        $akun->nama_akun,
                        Carbon::parse($detail->jurnal->tanggal)->format('Y-m-d'),
                        $detail->jurnal->no_referensi,
                        $detail->jurnal->keterangan,
                        $detail->quantity !== null ? $angka($detail->quantity) : '',
                        $detail->satuan ?? '',
                        $detail->harga !== null ? $angka($detail->harga) : '',
                        $angka($detail->total_tampil),
                        $detail->debit > 0 ? $angka($detail->debit) : '',
                        $detail->kredit > 0 ? $angka($detail->kredit) : '',
                        $angka($detail->saldo),
                        $detail->sisi_saldo,
                    ]);
                }

                // Baris total & saldo akhir
                $tulis([
                    $akun->kode_akun, $akun->nama_akun, '', '', 'Total Mutasi / Saldo Akhir',
                    '', '', '',
                    $angka($item['totalNilai']),
                    $angka($item['totalDebit']),
                    $angka($item['totalKredit']),
                    $angka($item['saldoAkhir']),
                    $item['sisiAkhir'],
                ]);
            }

            fclose($out);
        }, $this->namaFile($data, 'csv'), [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /** Nama file hasil cetak, contoh: buku-besar_2026-10-01_sd_2026-10-31.pdf */
    private function namaFile(array $data, string $ekstensi): string
    {
        return 'buku-besar_' . $data['start_date'] . '_sd_' . $data['end_date'] . '.' . $ekstensi;
    }

    /**
     * Menyiapkan seluruh data laporan.
     * Dipakai bersama oleh tampilan layar, PDF, dan CSV supaya hasilnya selalu sama.
     */
    private function siapkanLaporan(Request $request): array
    {
        $request->validate([
            'akun_id' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $akuns = Akun::orderBy('kode_akun')->get();

        // Default: langsung tampil SEMUA akun, periode bulan ini
        $akun_id = $request->input('akun_id') ?: 'semua';
        $start_date = $request->input('start_date') ?: date('Y-m-01');
        $end_date = $request->input('end_date') ?: date('Y-m-t');

        $semuaAkun = $akun_id === 'semua';

        if ($semuaAkun) {
            $daftarAkun = $akuns;
        } else {
            $akunTerpilih = $akuns->firstWhere('id', $akun_id);
            abort_if(!$akunTerpilih, 404);
            $daftarAkun = collect([$akunTerpilih]);
        }

        $laporan = $this->susunLaporan($daftarAkun, $start_date, $end_date);

        // Mode "Semua Akun": sembunyikan akun yang tidak punya saldo awal & mutasi
        if ($semuaAkun) {
            $laporan = $laporan->filter(function ($item) {
                return abs($item['saldoAwal']) > 0 || $item['baris']->isNotEmpty();
            })->values();
        }

        return [
            'akuns' => $akuns,
            'laporan' => $laporan,
            'semuaAkun' => $semuaAkun,
            'grandTotal' => (float) $laporan->sum('totalNilai'),
            'grandDebit' => (float) $laporan->sum('totalDebit'),
            'grandKredit' => (float) $laporan->sum('totalKredit'),
            'start_date' => $start_date,
            'end_date' => $end_date,
            'akun_id' => $akun_id,
        ];
    }

    /**
     * Mengubah saldo (dihitung menurut sisi normal akun) menjadi
     * angka POSITIF + label sisinya ('Debit' / 'Kredit').
     */
    private function sisiSaldo(float $saldo, bool $isDebitNormal): array
    {
        $saldo = round($saldo, 2);
        $sisiNormal = $isDebitNormal ? 'Debit' : 'Kredit';
        $sisiLawan = $isDebitNormal ? 'Kredit' : 'Debit';

        return $saldo >= 0
            ? ['nilai' => $saldo, 'sisi' => $sisiNormal]
            : ['nilai' => abs($saldo), 'sisi' => $sisiLawan];
    }

    /**
     * Menyusun data buku besar untuk satu atau banyak akun.
     * Data diambil sekaligus (bukan per akun) supaya query tidak berulang-ulang.
     */
    private function susunLaporan($daftarAkun, $start_date, $end_date)
    {
        $akunIds = $daftarAkun->pluck('id');

        // 1. Total mutasi SEBELUM start_date per akun (untuk saldo awal)
        $sebelumnya = DetailJurnal::select('detail_jurnals.akun_id')
            ->selectRaw('SUM(detail_jurnals.debit) as total_debit, SUM(detail_jurnals.kredit) as total_kredit')
            ->join('jurnals', 'detail_jurnals.jurnal_id', '=', 'jurnals.id')
            ->whereIn('detail_jurnals.akun_id', $akunIds)
            ->where('jurnals.tanggal', '<', $start_date)
            ->groupBy('detail_jurnals.akun_id')
            ->get()
            ->keyBy('akun_id');

        // 2. Mutasi pada rentang tanggal terpilih, dikelompokkan per akun
        $mutasiSemua = DetailJurnal::with('jurnal')
            ->select('detail_jurnals.*')
            ->join('jurnals', 'detail_jurnals.jurnal_id', '=', 'jurnals.id')
            ->whereIn('detail_jurnals.akun_id', $akunIds)
            ->whereBetween('jurnals.tanggal', [$start_date, $end_date])
            ->orderBy('jurnals.tanggal')
            ->orderBy('jurnals.id')
            ->orderBy('detail_jurnals.id')
            ->get()
            ->groupBy('akun_id');

        // 3. Hitung saldo awal, saldo berjalan, total, dan saldo akhir tiap akun
        return $daftarAkun->map(function ($akun) use ($sebelumnya, $mutasiSemua) {
            $isDebitNormal = $akun->saldo_normal === 'Debit';

            $awal = $sebelumnya->get($akun->id);
            $debitAwal = (float) ($awal->total_debit ?? 0);
            $kreditAwal = (float) ($awal->total_kredit ?? 0);

            // Saldo dihitung menurut sisi normal akun (bisa minus sementara)
            $saldo = $isDebitNormal
                ? $debitAwal - $kreditAwal
                : $kreditAwal - $debitAwal;

            $awalTampil = $this->sisiSaldo($saldo, $isDebitNormal);

            $mutasi = $mutasiSemua->get($akun->id, collect());

            $baris = $mutasi->map(function ($detail) use (&$saldo, $isDebitNormal) {
                $debit = (float) $detail->debit;
                $kredit = (float) $detail->kredit;

                $saldo += $isDebitNormal ? ($debit - $kredit) : ($kredit - $debit);

                // Saldo yang ditampilkan: angka positif + label sisi
                $tampil = $this->sisiSaldo($saldo, $isDebitNormal);
                $detail->saldo = $tampil['nilai'];
                $detail->sisi_saldo = $tampil['sisi'];

                                // Total: pakai total_harga dari jurnal; jika kosong (jurnal manual) pakai debit/kredit.
                $detail->total_tampil = $detail->total_harga !== null
                    ? (float) $detail->total_harga
                    : max($debit, $kredit);

                return $detail;
            });

            $akhirTampil = $this->sisiSaldo($saldo, $isDebitNormal);

            return [
                'akun' => $akun,
                'saldoAwal' => $awalTampil['nilai'],
                'sisiAwal' => $awalTampil['sisi'],
                'baris' => $baris,
                'saldoAkhir' => $akhirTampil['nilai'],
                'sisiAkhir' => $akhirTampil['sisi'],
                'totalNilai' => (float) $baris->sum('total_tampil'),
                'totalDebit' => (float) $mutasi->sum('debit'),
                'totalKredit' => (float) $mutasi->sum('kredit'),
            ];
        });
    }
}