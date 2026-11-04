<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\DetailJurnal;
use App\Models\Akun;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Access\AuthorizationException;

class JurnalController extends Controller
{
    /**
     * Memastikan hanya Admin Pusat dan Admin Cabang
     */
    private function authorizeJurnal(): void
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['Admin Pusat', 'Admin Cabang'], true)) {
            throw new AuthorizationException(
                'Kamu tidak memiliki akses ke menu Jurnal Umum.'
            );
        }

        if ($user->role === 'Admin Cabang' && !$user->cabang_id) {
            throw new AuthorizationException(
                'Akun Admin Cabang belum terhubung dengan cabang.'
            );
        }
    }

    /**
     * Daftar jurnal.
     */
    public function index(Request $request)
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

        // Validasi dan Default Bulan Berjalan
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'cabang_id' => ['nullable', 'integer', 'exists:cabangs,id'],
        ]);

        // Validasi dan default bulan berjalan
        $startDate = $request->input(
            'start_date'
        ) ?? now()->startOfMonth()->toDateString();

        $endDate = $request->input(
            'end_date'
        ) ?? now()->endOfMonth()->toDateString();

        // Filter berdasarkan tanggal DATE
        $query = Jurnal::with([
            'user',
            'cabang',
            'detailJurnals.akun',
            'transaksi.detailTransaksis.produk',
        ])
            ->whereBetween('tanggal', [
                $startDate,
                $endDate
            ]);

        if ($user->role === 'Admin Cabang') {
            $query->where('cabang_id', $user->cabang_id);
        }

        if ($user->role === 'Admin Pusat' && $request->filled('cabang_id')) {
            $query->where('cabang_id', $request->input('cabang_id'));
        }

        $jurnals = $query
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $cabangs = $user->role === 'Admin Pusat'
            ? Cabang::where('status', 'aktif')->orderBy('nama')->get()
            : collect();

        return view('admin.jurnal.index', compact(
            'jurnals',
            'cabangs',
            'startDate',
            'endDate'
        ));
    }
}
