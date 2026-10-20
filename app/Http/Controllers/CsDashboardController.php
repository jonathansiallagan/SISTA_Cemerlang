<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CsDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        // Query Dasar: Kunci data hanya untuk cabang tempat CS bekerja
        $queryCabang = Transaksi::query()
            ->where('cabang_id', $user->cabang_id)
            ->whereDate('tanggal', $tanggal);

        // Sub-query: Kunci data hanya untuk transaksi yang diinput oleh CS ini
        $transaksiSaya = (clone $queryCabang)->where('user_id', $user->id);

        $statistik = [
            'transaksi_saya'   => (clone $transaksiSaya)->count(),
            'omzet_saya'       => (clone $transaksiSaya)->sum('total_transaksi'),
            'transaksi_cabang' => (clone $queryCabang)->count(),
            'omzet_cabang'     => (clone $queryCabang)->sum('total_transaksi'),
        ];

        // 5 Transaksi terbaru di cabang tersebut
        $transaksiTerbaru = (clone $queryCabang)
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();

        $namaCabang = optional($user->cabang)->nama ?? 'Belum ditentukan';

        return view('cs.dashboard', compact('user', 'tanggal', 'statistik', 'transaksiTerbaru', 'namaCabang'));
    }
}