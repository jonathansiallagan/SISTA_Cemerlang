<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;

class CustomerServiceController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Transaksi hari ini; kalau CS terikat ke satu cabang, hanya cabang itu yang dihitung.
        $query = Transaksi::query()->whereDate('tanggal', now()->toDateString());

        if ($user->cabang_id) {
            $query->where('cabang_id', $user->cabang_id);
        }

        $ringkasan = [
            'jumlah_transaksi' => (clone $query)->count(),
            'jumlah_pelanggan' => (clone $query)->distinct()->count('nama_pelanggan'),
            'total_penjualan'  => (clone $query)->sum('total_transaksi'),
        ];

        $transaksiTerbaru = (clone $query)->latest()->limit(5)->get();

        return view('customer-service.dashboard', compact('ringkasan', 'transaksiTerbaru'));
    }
}