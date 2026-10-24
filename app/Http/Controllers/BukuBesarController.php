<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\DetailJurnal;
use Illuminate\Http\Request;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $akuns = Akun::orderBy('kode_akun')->get();

        // Parameter filter (Default: awal hingga akhir bulan ini)
        $akun_id = $request->input('akun_id');   // id akun, atau 'semua'
        $start_date = $request->input('start_date', date('Y-m-01'));
        $end_date = $request->input('end_date', date('Y-m-t'));

        $semuaAkun = $akun_id === 'semua';
        $laporan = collect();

        // Tentukan akun mana yang akan ditampilkan
        if ($semuaAkun) {
            $daftarAkun = $akuns;
        } elseif ($akun_id) {
            $daftarAkun = collect([Akun::findOrFail($akun_id)]);
        } else {
            $daftarAkun = collect();
        }

        if ($daftarAkun->isNotEmpty()) {
            $laporan = $this->susunLaporan($daftarAkun, $start_date, $end_date);

            // Mode "Semua Akun": sembunyikan akun yang tidak punya saldo awal & mutasi
            if ($semuaAkun) {
                $laporan = $laporan->filter(function ($item) {
                    return abs($item['saldoAwal']) > 0 || $item['baris']->isNotEmpty();
                })->values();
            }
        }

        // Ringkasan total (dipakai pada mode "Semua Akun")
        $grandDebit = (float) $laporan->sum('totalDebit');
        $grandKredit = (float) $laporan->sum('totalKredit');

        return view('admin.buku_besar.index', compact(
            'akuns', 'laporan', 'semuaAkun',
            'grandDebit', 'grandKredit',
            'start_date', 'end_date', 'akun_id'
        ));
    }

    /**
     * Mengubah saldo (dihitung menurut sisi normal akun) menjadi
     * angka POSITIF + label sisinya ('Debit' / 'Kredit').
     *
     * Contoh akun Kas (normal Debit):
     *   saldo  800.000 => nilai 800.000, sisi Debit
     *   saldo -500.000 => nilai 500.000, sisi Kredit  (tidak tampil minus)
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

        // 3. Hitung saldo awal, saldo berjalan, dan total untuk tiap akun
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

                // Yang dikirim ke view: angka positif + label sisi
                $tampil = $this->sisiSaldo($saldo, $isDebitNormal);
                $detail->saldo = $tampil['nilai'];        // $detail->saldo
                $detail->sisi_saldo = $tampil['sisi'];    // $detail->sisi_saldo

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
                'totalDebit' => (float) $mutasi->sum('debit'),
                'totalKredit' => (float) $mutasi->sum('kredit'),
            ];
        });
    }
}