<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\DetailJurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\PerhitunganSista;

class LaporanLabaRugiController extends Controller
{
    /**
     * Membatasi akses berdasarkan role.
     */
    private function batasiAkses(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && in_array(
                $user->role,
                ['Admin Pusat', 'Admin Cabang'],
                true
            ),
            403
        );

        if ($user->role === 'Admin Cabang' && !$user->cabang_id) {
            abort(403, 'Akun belum terhubung dengan cabang.');
        }
    }

    /**
     * Menampilkan laporan laba rugi.
     */
    public function index(Request $request)
    {
        $this->batasiAkses();

        // Validasi dan Default Bulan Berjalan (Best Practice #4 & #5)
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'cabang_id' => ['nullable', 'integer', 'exists:cabangs,id'],
        ]);

        // Menggunakan Carbon (Best Practice #2)
        $start_date = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end_date   = $request->input('end_date', now()->endOfMonth()->toDateString());

        $user = Auth::user();

        $cabangs = Cabang::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        $cabangId = $user->role === 'Admin Cabang'
            ? (int) $user->cabang_id
            : (
                $user->role === 'Admin Pusat' && $request->filled('cabang_id')
                ? (int) $request->input('cabang_id')
                : null
            );

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

        // Filter tanggal pada query dengan presisi waktu (Best Practice #3)
        $mutasi = DetailJurnal::query()
            ->select('detail_jurnals.*')
            ->join('jurnals', 'detail_jurnals.jurnal_id', '=', 'jurnals.id')
            ->whereBetween('jurnals.tanggal', [
                $start_date,
                $end_date
            ])
            ->whereIn('detail_jurnals.akun_id', $akuns->pluck('id'))
            ->when($cabangId !== null, function ($query) use ($cabangId) {
                $query->where('jurnals.cabang_id', $cabangId);
            })
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
                $item['saldo'] = $totalKredit - $totalDebit;
                $pendapatan->push($item);
            } elseif ($akun->kelompok_akun === 'HPP') {
                $item['saldo'] = $totalDebit - $totalKredit;
                $hpp->push($item);
            } else {
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
            'labaBersih',
            'cabangs',
            'cabangId'
        ));
    }
}
