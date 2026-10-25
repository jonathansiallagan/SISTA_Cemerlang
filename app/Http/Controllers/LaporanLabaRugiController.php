<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\DetailJurnal;
use Illuminate\Http\Request;

class LaporanLabaRugiController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $start_date = $request->input(
            'start_date',
            now()->startOfMonth()->toDateString()
        );

        $end_date = $request->input(
            'end_date',
            now()->endOfMonth()->toDateString()
        );

        // Ambil akun pendapatan, HPP, dan beban.
        $akuns = Akun::where('status', 'aktif')
            ->where(function ($query) {
                $query->where('tipe_akun', 'Pendapatan')
                    ->orWhere(function ($q) {
                        $q->where('tipe_akun', 'Beban/Biaya')
                            ->whereIn('kelompok_akun', [
                                'HPP',
                                'Beban Operasional',
                                'Biaya Lain-lain',
                            ]);
                    });
            })
            ->orderBy('kode_akun')
            ->get();

        // Ambil mutasi jurnal berdasarkan periode.
        $mutasi = DetailJurnal::query()
            ->select('detail_jurnals.*')
            ->join('jurnals', 'detail_jurnals.jurnal_id', '=', 'jurnals.id')
            ->whereBetween('jurnals.tanggal', [$start_date, $end_date])
            ->whereIn('detail_jurnals.akun_id', $akuns->pluck('id'))
            ->with('akun')
            ->get()
            ->groupBy('akun_id');

        $pendapatan = collect();
        $hpp = collect();
        $beban = collect();

        foreach ($akuns as $akun) {
            $details = $mutasi->get($akun->id, collect());

            $totalDebit = (float) $details->sum('debit');
            $totalKredit = (float) $details->sum('kredit');

            $item = [
                'akun' => $akun,
                'debit' => $totalDebit,
                'kredit' => $totalKredit,
            ];

            if ($akun->tipe_akun === 'Pendapatan') {
                // Pendapatan normalnya kredit.
                $item['saldo'] = $totalKredit - $totalDebit;
                $pendapatan->push($item);
            } elseif ($akun->kelompok_akun === 'HPP') {
                // HPP normalnya debit.
                $item['saldo'] = $totalDebit - $totalKredit;
                $hpp->push($item);
            } else {
                // Beban operasional dan biaya lain-lain.
                $item['saldo'] = $totalDebit - $totalKredit;
                $beban->push($item);
            }
        }

        $totalPendapatan = (float) $pendapatan->sum('saldo');
        $totalHpp = (float) $hpp->sum('saldo');
        $labaKotor = $totalPendapatan - $totalHpp;

        $totalBeban = (float) $beban->sum('saldo');
        $labaBersih = $labaKotor - $totalBeban;

        return view('admin.laporan_laba_rugi.index', compact(
            'start_date',
            'end_date',
            'pendapatan',
            'hpp',
            'beban',
            'totalPendapatan',
            'totalHpp',
            'labaKotor',
            'totalBeban',
            'labaBersih'
        ));
    }
}