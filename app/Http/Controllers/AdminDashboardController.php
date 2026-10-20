<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Models\Cabang;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdminPusat = $user->role === 'Admin Pusat';
        
        $namaCabang = optional($user->cabang)->nama ?? 'Cabang Anda';

        $judulDashboard = $isAdminPusat ? 'Dashboard Admin Pusat' : 'Dashboard Admin Cabang';
        $subJudul = $isAdminPusat 
            ? 'Ringkasan aktivitas seluruh cabang hari ini.' 
            : "Ringkasan aktivitas cabang {$namaCabang} hari ini.";

        // LOGIKA MENGAMBIL DAFTAR CABANG DARI DATABASE ---
        if ($isAdminPusat) {
            // Admin Pusat: Ambil seluruh data cabang
            $daftarCabang = Cabang::all(); 
        } else {
            // Admin Cabang: Ambil hanya cabangnya sendiri (dalam format array/collection agar struktur konsisten)
            $daftarCabang = Cabang::where('id', $user->cabang_id)->get();
        }
        // -------------------------------------------------------

        // Parameter Filter
        $cabangId = $request->get('cabang_id', 'semua');
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        // Query Dasar berdasarkan tanggal
        $query = Transaksi::query()->whereDate('tanggal', $tanggal);

        // Filter Hak Akses Cabang
        if (!$isAdminPusat) {
            $query->where('cabang_id', $user->cabang_id);
        } elseif ($cabangId !== 'semua') {
            $query->where('cabang_id', $cabangId);
        }

        // Kalkulasi Statistik
        $jumlahTransaksi = (clone $query)->count();
        $omzet = (clone $query)->sum('total_transaksi');
        $laba = $omzet * 0.33; // Margin laba 33%

        // 5 Transaksi Terbaru
        $transaksiTerbaru = (clone $query)->with(['user', 'cabang'])->latest()->limit(5)->get();

        // Data Grafik Per Jam (08:00 - 16:00)
        $chartLabels = [];
        $chartData = [];

        for ($h = 8; $h <= 16; $h++) {
            $jam = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            $chartLabels[] = $jam;

            $start = Carbon::parse($tanggal)->setTime($h, 0, 0);
            $end   = Carbon::parse($tanggal)->setTime($h, 59, 59);

            $sum = (clone $query)
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_transaksi');

            $chartData[] = round($sum / 1000000, 1); // Disajikan dalam jutaan
        }

        return view('admin.dashboard', compact(
            'transaksiTerbaru', 'omzet', 'jumlahTransaksi', 'laba',
            'chartLabels', 'chartData', 'tanggal', 'cabangId', 
            'judulDashboard', 'subJudul', 'isAdminPusat', 'daftarCabang'
        ));
    }
}