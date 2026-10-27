<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Cabang;
use App\Models\Jurnal;
use App\Models\DetailJurnal;
use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TransaksiController extends Controller
{
    
    public function index(Request $request)
    {
        $user = $this->user();

        $query = Transaksi::with([
            'user',
            'cabang',
            'detailTransaksis.produk',
        ]);

        // Customer Service hanya boleh melihat transaksi cabangnya sendiri.
        // Batasi akses data berdasarkan role dan cabang.
        if ($user->role === 'Admin Cabang' || $user->isCustomerService()) {
            $query->where('cabang_id', $user->cabang_id);
        } elseif ($user->role === 'Admin Pusat' && $request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        // Filter pencarian berdasarkan nomor referensi atau nama pelanggan.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('no_referensi', 'like', "%{$search}%")
                    ->orWhere('nama_pelanggan', 'like', "%{$search}%");
            });
        }

        // Filter tanggal mulai.
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        // Filter tanggal akhir.
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }

        // Urutkan transaksi terbaru terlebih dahulu.
        $transaksis = $query
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        // Data cabang untuk dropdown filter.
        // Customer Service hanya melihat cabangnya sendiri.
        $cabangs = $user->isCustomerService()
            ? \App\Models\Cabang::where('id', $user->cabang_id)->get()
            : \App\Models\Cabang::orderBy('nama')->get();

        return view('admin.transaksi.index', compact(
            'transaksis',
            'cabangs'
        ));
    }


    public function create()
    {
        $user = $this->user();

        $produks = Produk::all();
        $cabangs = Cabang::all();

        // Customer Service hanya boleh memilih cabangnya sendiri.
        if ($user->isCustomerService()) {
            $cabangs = $cabangs
                ->where('id', $user->cabang_id)
                ->values();
        }

        // Pembuatan nomor referensi otomatis.
        $today = now()->format('Ymd');

        $lastTransaksi = Transaksi::whereDate(
            'created_at',
            now()->today()
        )->count();

        $no_referensi = 'INV-' . $today . '-'
            . str_pad($lastTransaksi + 1, 4, '0', STR_PAD_LEFT);

        return view(
            'admin.transaksi.create',
            compact('produks', 'cabangs', 'no_referensi')
        );
    }

    
    public function store(Request $request)
    {
        $user = $this->user();

        $request->validate([
            'no_referensi' => 'required|unique:transaksis,no_referensi',
            'tanggal' => 'required|date',
            'nama_pelanggan' => 'required|string|max:255',
            'cabang_id' => 'required|exists:cabangs,id',
            'produk_id' => 'required|array|min:1',
            'produk_id.*' => 'required|exists:produks,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|numeric|min:1',
        ]);

        // Customer Service hanya boleh mencatat transaksi cabangnya sendiri.
        if (
            $user->isCustomerService()
            && (int) $request->cabang_id !== (int) $user->cabang_id
        ) {
            abort(
                403,
                'Anda hanya dapat mencatat transaksi untuk cabang Anda sendiri.'
            );
        }

        DB::beginTransaction();

        try {
            // 1. Cari akun yang diperlukan untuk jurnal otomatis.
            $akunKas = Akun::where('nama_akun', 'Kas')
                ->where('status', 'aktif')
                ->first();

            $akunPendapatan = Akun::where(
                'nama_akun',
                'Pendapatan Penjualan'
            )
                ->where('status', 'aktif')
                ->first();

            $akunHpp = Akun::where(
                'nama_akun',
                'Harga Pokok Produksi'
            )
                ->where('status', 'aktif')
                ->first();

            $akunPersediaan = Akun::where('nama_akun', 'Persediaan')
                ->where('status', 'aktif')
                ->first();

            if (
                !$akunKas
                || !$akunPendapatan
                || !$akunHpp
                || !$akunPersediaan
            ) {
                throw new \RuntimeException(
                    'Akun Kas, Pendapatan Penjualan, Harga Pokok Produksi, '
                    . 'atau Persediaan belum tersedia atau tidak aktif.'
                );
            }

            // 2. Simpan transaksi induk.
            $transaksi = Transaksi::create([
                'no_referensi' => $request->no_referensi,
                'tanggal' => $request->tanggal,
                'nama_pelanggan' => $request->nama_pelanggan,
                'cabang_id' => $request->cabang_id,
                'user_id' => $user->id,
                'total_transaksi' => 0,
                'total_hpp' => 0,
            ]);

            $totalTransaksi = 0;
            $totalHpp = 0;

            // 3. Simpan detail setiap produk dan hitung total transaksi.
            foreach ($request->produk_id as $key => $produkId) {
                $produk = Produk::findOrFail($produkId);

                $jumlah = (float) $request->jumlah[$key];
                $luas = (float) ($request->luas[$key] ?? 0);

                $harga = (float) $request->harga_satuan[$key];
                $hpp = (float) $request->hpp_satuan[$key];

                $subtotal = (float) $request->subtotal[$key];
                $totalHppItem = (float) $request->total_hpp_item[$key];

                // Tentukan quantity berdasarkan satuan produk.
                $satuan = strtolower(trim($produk->satuan));

                $quantity = (
                    $satuan === 'm2'
                    || $satuan === 'meter persegi'
                    || str_contains($satuan, 'm²')
                ) ? $luas : $jumlah;

                // Simpan rincian produk ke tabel detail_transaksis.
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $produkId,
                    'jumlah' => $jumlah,
                    'lebar' => $request->lebar[$key] ?? null,
                    'tinggi' => $request->tinggi[$key] ?? null,
                    'luas' => $request->luas[$key] ?? null,
                    'harga_satuan' => $harga,
                    'hpp_satuan' => $hpp,
                    'subtotal' => $subtotal,
                    'total_hpp' => $totalHppItem,
                ]);

                // Akumulasi total seluruh produk.
                $totalTransaksi += $subtotal;
                $totalHpp += $totalHppItem;
            }

            // 4. Perbarui total transaksi induk.
            $transaksi->update([
                'total_transaksi' => $totalTransaksi,
                'total_hpp' => $totalHpp,
            ]);

            // 5. Buat satu header jurnal untuk satu transaksi.
            $jurnal = Jurnal::create([
                'no_referensi' => $transaksi->no_referensi,
                'tanggal' => $transaksi->tanggal,
                'keterangan' => 'Pendapatan Jasa',
                'cabang_id' => $transaksi->cabang_id,
                'user_id' => $user->id,
            ]);

            // 6. Buat empat baris jurnal berdasarkan total transaksi.

            // A. Debit Kas sebesar total penjualan.
            DetailJurnal::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akunKas->id,
                'keterangan' => 'Pendapatan Jasa',
                'debit' => $totalTransaksi,
                'kredit' => 0,
            ]);

            // B. Kredit Pendapatan Penjualan.
            DetailJurnal::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akunPendapatan->id,
                'keterangan' => 'Pendapatan Jasa',
                'debit' => 0,
                'kredit' => $totalTransaksi,
            ]);

            // C. Debit Harga Pokok Produksi sebesar total HPP.
            DetailJurnal::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akunHpp->id,
                'keterangan' => 'Beban Pokok Penjualan',
                'debit' => $totalHpp,
                'kredit' => 0,
            ]);

            // D. Kredit Persediaan sebesar total HPP.
            DetailJurnal::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akunPersediaan->id,
                'keterangan' => 'Beban Pokok Penjualan',
                'debit' => 0,
                'kredit' => $totalHpp,
            ]);

            DB::commit();

            return redirect()
                ->route('transaksi.index')
                ->with(
                    'success',
                    'Transaksi dan jurnal otomatis berhasil disimpan!'
                );

        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            return back()
                ->with(
                    'error',
                    'Transaksi gagal disimpan. Periksa konfigurasi akun dan struktur database.'
                )
                ->withInput();
        }
    }

    public function exportPdf(Request $request)
    {
        $user = $this->user();

        $query = Transaksi::with([
            'user',
            'cabang',
            'detailTransaksis.produk',
        ]);

        // Batasi akses data berdasarkan role dan cabang.
        if ($user->role === 'Admin Cabang' || $user->isCustomerService()) {
            $query->where('cabang_id', $user->cabang_id);
        } elseif ($user->role === 'Admin Pusat' && $request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        // Filter pencarian.
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('no_referensi', 'like', "%{$search}%")
                    ->orWhere('nama_pelanggan', 'like', "%{$search}%");
            });
        }

        // Filter tanggal.
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }

        $transaksis = $query
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'admin.transaksi.pdf',
            compact('transaksis')
        )->setPaper('a4', 'landscape');

        return $pdf->download('riwayat-transaksi.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new \App\Exports\TransaksiExport($request, $this->user()),
            'riwayat-transaksi.xlsx'
        );
    }

}