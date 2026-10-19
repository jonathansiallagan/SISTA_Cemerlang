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
        $akun_id = $request->input('akun_id');
        $start_date = $request->input('start_date', date('Y-m-01'));
        $end_date = $request->input('end_date', date('Y-m-t'));

        $akunTerpilih = null;
        $baris = collect();      // baris mutasi (sudah lengkap dengan saldo berjalan)
        $saldoAwal = 0;
        $saldoAkhir = 0;
        $totalDebit = 0;
        $totalKredit = 0;

        if ($akun_id) {
            $akunTerpilih = Akun::findOrFail($akun_id);
            $isDebitNormal = $akunTerpilih->saldo_normal === 'Debit';

            // 1. Saldo awal = semua mutasi SEBELUM start_date
            $sebelumnya = DetailJurnal::where('akun_id', $akun_id)
                ->whereHas('jurnal', function ($q) use ($start_date) {
                    $q->where('tanggal', '<', $start_date);
                })->get();

            $debitSebelumnya = (float) $sebelumnya->sum('debit');
            $kreditSebelumnya = (float) $sebelumnya->sum('kredit');

            $saldoAwal = $isDebitNormal
                ? $debitSebelumnya - $kreditSebelumnya
                : $kreditSebelumnya - $debitSebelumnya;

            // 2. Mutasi pada rentang tanggal terpilih (urut tanggal, lalu urutan input)
            $mutasi = DetailJurnal::with('jurnal')
                ->select('detail_jurnals.*')
                ->join('jurnals', 'detail_jurnals.jurnal_id', '=', 'jurnals.id')
                ->where('detail_jurnals.akun_id', $akun_id)
                ->whereBetween('jurnals.tanggal', [$start_date, $end_date])
                ->orderBy('jurnals.tanggal')
                ->orderBy('jurnals.id')
                ->orderBy('detail_jurnals.id')
                ->get();

            // 3. Hitung saldo berjalan per baris -> tersedia di view sebagai $detail->saldo
            $saldo = $saldoAwal;
            $baris = $mutasi->map(function ($detail) use (&$saldo, $isDebitNormal) {
                $debit = (float) $detail->debit;
                $kredit = (float) $detail->kredit;

                $saldo += $isDebitNormal ? ($debit - $kredit) : ($kredit - $debit);
                $detail->saldo = $saldo;

                return $detail;
            });

            $saldoAkhir = $saldo;
            $totalDebit = (float) $mutasi->sum('debit');
            $totalKredit = (float) $mutasi->sum('kredit');
        }

        return view('admin.buku_besar.index', compact(
            'akuns', 'akunTerpilih', 'baris',
            'saldoAwal', 'saldoAkhir', 'totalDebit', 'totalKredit',
            'start_date', 'end_date', 'akun_id'
        ));
    }
}