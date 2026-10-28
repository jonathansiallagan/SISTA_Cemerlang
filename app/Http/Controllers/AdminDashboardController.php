<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\DetailTransaksi;
use App\Models\Jurnal;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdminPusat = $user->role === 'Admin Pusat';
        $isAdminCabang = $user->role === 'Admin Cabang';

        /*
        |--------------------------------------------------------------------------
        | FILTER
        |--------------------------------------------------------------------------
        */

        $tanggal = $request->input('tanggal', Carbon::today()->format('Y-m-d'));
        $cabangId = $request->input('cabang_id', 'semua');

        if ($cabangId === 'semua' || $cabangId === '') {
            $cabangId = null;
        }

        if (!$isAdminPusat) {
            $cabangId = $user->cabang_id;
        }

        /*
        |--------------------------------------------------------------------------
        | DAFTAR CABANG (Untuk Dropdown)
        |--------------------------------------------------------------------------
        */

        if ($isAdminPusat) {
            $daftarCabang = Cabang::orderBy('nama')->get();
            $semuaCabang = $daftarCabang; // Alias untuk menjaga kecocokan dengan view lama
        } else {
            $daftarCabang = Cabang::where('id', $user->cabang_id)->get();
            $semuaCabang = $daftarCabang;
        }

        /*
        |--------------------------------------------------------------------------
        | JUDUL DASHBOARD
        |--------------------------------------------------------------------------
        */

        $namaCabang = optional($user->cabang)->nama ?? 'Cabang Anda';

        if ($isAdminPusat) {
            $judulDashboard = 'Dashboard Admin Pusat';
            $subjudulDashboard = 'Pantau seluruh aktivitas dan performa percetakan.';
            $subJudul = $subjudulDashboard; // Alias
        } else {
            $judulDashboard = 'Dashboard Admin Cabang';
            $subjudulDashboard = "Pantau aktivitas dan performa cabang {$namaCabang} hari ini.";
            $subJudul = $subjudulDashboard; // Alias
        }

        /*
        |--------------------------------------------------------------------------
        | QUERY TRANSAKSI HARI INI
        |--------------------------------------------------------------------------
        */

        $transaksiQuery = Transaksi::query()->whereDate('tanggal', $tanggal);

        if ($cabangId !== null) {
            $transaksiQuery->where('cabang_id', $cabangId);
        }

        /*
        |--------------------------------------------------------------------------
        | STATISTIK HARI INI
        |--------------------------------------------------------------------------
        */

        $jumlahTransaksi = (clone $transaksiQuery)->count();
        $omzet = (clone $transaksiQuery)->sum('total_transaksi');
        $totalHpp = (clone $transaksiQuery)->sum('total_hpp');
        
        // Cek jika kolom total_hpp ada nilainya, jika tidak pakai estimasi 33% margin
        $laba = $totalHpp > 0 ? ($omzet - $totalHpp) : ($omzet * 0.33);

        /*
        |--------------------------------------------------------------------------
        | PERBANDINGAN HARI SEBELUMNYA (Untuk Persentase)
        |--------------------------------------------------------------------------
        */

        $tanggalSebelumnya = Carbon::parse($tanggal)->subDay()->format('Y-m-d');
        $transaksiSebelumnyaQuery = Transaksi::query()->whereDate('tanggal', $tanggalSebelumnya);

        if ($cabangId !== null) {
            $transaksiSebelumnyaQuery->where('cabang_id', $cabangId);
        }

        $jumlahTransaksiSebelumnya = (clone $transaksiSebelumnyaQuery)->count();
        $omzetSebelumnya = (clone $transaksiSebelumnyaQuery)->sum('total_transaksi');
        $totalHppSebelumnya = (clone $transaksiSebelumnyaQuery)->sum('total_hpp');
        
        $labaSebelumnya = $totalHppSebelumnya > 0 
            ? ($omzetSebelumnya - $totalHppSebelumnya) 
            : ($omzetSebelumnya * 0.33);

        /*
        |--------------------------------------------------------------------------
        | HITUNG PERSENTASE
        |--------------------------------------------------------------------------
        */

        $hitungPersentase = function ($sekarang, $sebelumnya) {
            if ((float) $sebelumnya == 0) {
                return $sekarang > 0 ? 100 : 0;
            }
            return (($sekarang - $sebelumnya) / $sebelumnya) * 100;
        };

        $persenOmzet = $hitungPersentase($omzet, $omzetSebelumnya);
        $persenTransaksi = $hitungPersentase($jumlahTransaksi, $jumlahTransaksiSebelumnya);
        $persenHpp = $hitungPersentase($totalHpp, $totalHppSebelumnya);
        $persenLaba = $hitungPersentase($laba, $labaSebelumnya);

        /*
        |--------------------------------------------------------------------------
        | 5 TRANSAKSI TERBARU
        |--------------------------------------------------------------------------
        */

        $transaksiTerbaru = (clone $transaksiQuery)
            ->with(['cabang', 'user', 'detailTransaksis.produk'])
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DATA MASTER (Untuk Widget Angka Total)
        |--------------------------------------------------------------------------
        */

        $totalCabang = $isAdminPusat ? Cabang::count() : 1;
        $totalUser = $isAdminPusat ? User::count() : User::where('cabang_id', $user->cabang_id)->count();
        $totalProduk = Produk::count();
        $totalAkun = Akun::count();

        /*
        |--------------------------------------------------------------------------
        | PRODUK TERLARIS
        |--------------------------------------------------------------------------
        */

        $produkTerlaris = DetailTransaksi::query()
            ->select('produk_id')
            ->selectRaw('SUM(jumlah) as total_terjual')
            ->selectRaw('SUM(subtotal) as total_penjualan')
            ->whereHas('transaksi', function ($query) use ($tanggal, $cabangId) {
                $query->whereDate('tanggal', $tanggal);
                if ($cabangId !== null) {
                    $query->where('cabang_id', $cabangId);
                }
            })
            ->with('produk')
            ->groupBy('produk_id')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        
    /*
    |--------------------------------------------------------------------------
    | JURNAL TERBARU
    |--------------------------------------------------------------------------
    */

    $jurnalQuery = Jurnal::query();

    if ($cabangId !== null) {
        $jurnalQuery->where('cabang_id', $cabangId);
    }

    $jurnalTerbaru = (clone $jurnalQuery)
        ->with(['cabang', 'user', 'detailJurnals.akun'])
        ->orderByDesc('tanggal')
        ->orderByDesc('id')
        ->limit(5)
        ->get();

    /*
    |--------------------------------------------------------------------------
    | RINGKASAN DEBIT DAN KREDIT BERDASARKAN TANGGAL TERPILIH
    |--------------------------------------------------------------------------
    */

    $jurnalTanggalQuery = Jurnal::query()
        ->whereDate('tanggal', $tanggal);

    if ($cabangId !== null) {
        $jurnalTanggalQuery->where('cabang_id', $cabangId);
    }

    $totalDebit = (clone $jurnalTanggalQuery)
        ->whereHas('detailJurnals')
        ->with('detailJurnals')
        ->get()
        ->sum(function ($jurnal) {
            return $jurnal->detailJurnals->sum('debit');
        });

    $totalKredit = (clone $jurnalTanggalQuery)
        ->whereHas('detailJurnals')
        ->with('detailJurnals')
        ->get()
        ->sum(function ($jurnal) {
            return $jurnal->detailJurnals->sum('kredit');
        });


        /*
        |--------------------------------------------------------------------------
        | DATA GRAFIK
        |--------------------------------------------------------------------------
        */

        // CHART OMZET 7 HARI TERAKHIR (Dari branch lama)
        $tanggalChart = [];
        $omzetChart = [];

        for ($i = 6; $i >= 0; $i--) {
            $hari = Carbon::parse($tanggal)->subDays($i);
            $tanggalChart[] = $hari->translatedFormat('d M');

            $queryGrafik = Transaksi::query()->whereDate('tanggal', $hari->format('Y-m-d'));
            if ($cabangId !== null) {
                $queryGrafik->where('cabang_id', $cabangId);
            }

            $omzetChart[] = (float) $queryGrafik->sum('total_transaksi');
        }

        // CHART PER JAM (Dari AdminDashboardController kita sebelumnya)
        $chartLabels = [];
        $chartData = [];

        for ($h = 8; $h <= 16; $h++) {
            $jam = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            $chartLabels[] = $jam;

            $start = Carbon::parse($tanggal)->setTime($h, 0, 0);
            $end   = Carbon::parse($tanggal)->setTime($h, 59, 59);

            $sumJam = (clone $transaksiQuery)
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_transaksi');

            $chartData[] = round($sumJam / 1000000, 1); 
        }

        /*
        |--------------------------------------------------------------------------
        | RINGKASAN SELURUH CABANG (Hanya untuk Admin Pusat)
        |--------------------------------------------------------------------------
        */

        $ringkasanCabang = collect();

        if ($isAdminPusat) {
            $ringkasanCabang = Cabang::query()
                ->withCount(['transaksis as jumlah_transaksi' => function ($q) use ($tanggal) {
                    $q->whereDate('tanggal', $tanggal);
                }])
                ->withSum(['transaksis as omzet' => function ($q) use ($tanggal) {
                    $q->whereDate('tanggal', $tanggal);
                }], 'total_transaksi')
                ->withSum(['transaksis as hpp' => function ($q) use ($tanggal) {
                    $q->whereDate('tanggal', $tanggal);
                }], 'total_hpp')
                ->get();

            foreach ($ringkasanCabang as $cabang) {
                $cabang->laba = ($cabang->omzet ?? 0) - ($cabang->hpp ?? 0);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | KIRIM DATA KE VIEW
        |--------------------------------------------------------------------------
        */

        return view('admin.dashboard', compact(
            'isAdminPusat', 'isAdminCabang', 'judulDashboard', 'subjudulDashboard', 'subJudul',
            'tanggal', 'cabangId', 'semuaCabang', 'daftarCabang', 'namaCabang',
            
            'jumlahTransaksi', 'omzet', 'totalHpp', 'laba',
            'persenOmzet', 'persenTransaksi', 'persenHpp', 'persenLaba',
            
            'transaksiTerbaru', 'produkTerlaris', 'jurnalTerbaru',
            'totalCabang', 'totalUser', 'totalProduk', 'totalAkun',
            'totalDebit', 'totalKredit',
            
            'tanggalChart', 'omzetChart', 
            'chartLabels', 'chartData',
            
            'ringkasanCabang'
        ));
    }
}