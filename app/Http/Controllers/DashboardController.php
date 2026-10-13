<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin(Request $request)
    {
        $user = Auth::user();

        // =====================
        // Tentukan role & judul
        // =====================
        $isAdminPusat = $user->role === 'Admin Pusat'; // sesuaikan nama kolom role jika berbeda

        // Ambil nama cabang dengan aman (hindari muncul object JSON)
        $namaCabang = 'Cabang Anda';
        if (!$isAdminPusat) {
            if (is_object($user->cabang) && isset($user->cabang->nama)) {
                $namaCabang = $user->cabang->nama;          // relasi model Cabang
            } elseif (is_string($user->cabang)) {
                $namaCabang = $user->cabang;                // kolom string
            } elseif (!empty($user->nama_cabang)) {
                $namaCabang = $user->nama_cabang;
            }
        }

        $judulDashboard = $isAdminPusat 
            ? 'Dashboard Admin Pusat' 
            : 'Dashboard Admin Cabang';

        $subJudul = $isAdminPusat 
            ? 'Ringkasan aktivitas seluruh cabang hari ini.' 
            : 'Ringkasan aktivitas cabang ' . $namaCabang . ' hari ini.';

        // =====================
        // FILTER
        // =====================
        $cabang  = $request->get('cabang', 'Semua Cabang');
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        // Jika Admin Cabang → paksa hanya cabang miliknya
        if (!$isAdminPusat) {
            $cabang = $namaCabang;
        }
        
        // =====================
        // DETEKSI KOLOM OTOMATIS
        // =====================
        $columns = Schema::getColumnListing('transaksis');

        $possibleMoney = [
            'nominal', 'total', 'total_harga', 'jumlah', 
            'harga', 'nilai', 'grand_total', 'subtotal', 'harga_total'
        ];
        $kolomUang = collect($possibleMoney)->first(fn($col) => in_array($col, $columns));
        $possibleCabang = ['cabang', 'nama_cabang', 'branch', 'cabang_id'];
        $kolomCabang = collect($possibleCabang)->first(fn($col) => in_array($col, $columns)) ?? 'cabang';

        // =====================
        // QUERY DASAR
        // =====================
        $query = Transaksi::query()->whereDate('created_at', $tanggal);

        if ($cabang && $cabang !== 'Semua Cabang' && $kolomCabang) {
            $query->where($kolomCabang, $cabang);
        }
        // 5 transaksi terbaru
        $transaksiTerbaru = (clone $query)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        // =====================
        // STATISTIK
        // =====================
        $omzet = 0;
        $jumlahTransaksi = (clone $query)->count();

        if ($kolomUang) {
            $omzet = (clone $query)->sum($kolomUang) ?? 0;
        }
        $laba = $omzet * 0.33; // sesuaikan rumus laba jika perlu
        // =====================
        // DATA CHART (per jam)
        // =====================
        $chartLabels = [];
        $chartData   = [];

        for ($h = 8; $h <= 16; $h++) {
            $jam = str_pad($h, 2, '0', STR_PAD_LEFT) . ':00';
            $chartLabels[] = $jam;

            $start = Carbon::parse($tanggal)->setTime($h, 0, 0);
            $end   = Carbon::parse($tanggal)->setTime($h, 59, 59);

            $sum = 0;
            if ($kolomUang) {
                $sum = Transaksi::whereBetween('created_at', [$start, $end])
                    ->when($cabang && $cabang !== 'Semua Cabang', function ($q) use ($cabang, $kolomCabang) {
                        $q->where($kolomCabang, $cabang);
                    })
                    ->sum($kolomUang) ?? 0;
            }

            $chartData[] = round($sum / 1000000, 1); // dalam jutaan
        }

        // =====================
        // DAFTAR CABANG
        // =====================
        if ($isAdminPusat) {
            $daftarCabang = [
                'Semua Cabang',
                'Cabang Sudirman',
                'Cabang Bukit',
                'Cabang Pekanbaru',
                'Cabang Bagansiapiapi',
            ];
        } else {
            $daftarCabang = [$namaCabang];
        }

        return view('admin.dashboard', compact(
            'transaksiTerbaru',
            'omzet',
            'jumlahTransaksi',
            'laba',
            'chartLabels',
            'chartData',
            'cabang',
            'tanggal',
            'daftarCabang',
            'judulDashboard',
            'subJudul',
            'isAdminPusat'
        ));
    }

    public function cs(Request $request)
    {
        $user    = Auth::user();
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        // CS hanya melihat data cabang tempat dia ditugaskan
        $queryCabang = Transaksi::query()
            ->where('cabang_id', $user->cabang_id)
            ->whereDate('tanggal', $tanggal);

        $transaksiSaya = (clone $queryCabang)->where('user_id', $user->id);

        $statistik = [
            'transaksi_saya'   => (clone $transaksiSaya)->count(),
            'omzet_saya'       => (clone $transaksiSaya)->sum('total_transaksi'),
            'transaksi_cabang' => (clone $queryCabang)->count(),
            'omzet_cabang'     => (clone $queryCabang)->sum('total_transaksi'),
        ];

        $transaksiTerbaru = (clone $queryCabang)
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        $namaCabang = optional($user->cabang)->nama ?? 'Belum ditentukan';

        return view('cs.dashboard', compact('user', 'tanggal', 'statistik', 'transaksiTerbaru', 'namaCabang'));
    }
}