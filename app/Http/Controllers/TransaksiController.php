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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index()
    {
        // Mengambil data transaksi beserta relasi user dan cabangnya
        $transaksis = Transaksi::with(['user', 'cabang'])->latest()->get();
        return view('admin.transaksi.index', compact('transaksis'));
    }

    public function create()
    {
        $produks = Produk::all();
        $cabangs = Cabang::where('status', 'aktif')->get();
        
        // Pembuatan Nomor Referensi Otomatis
        $today = now()->format('Ymd');
        $lastTransaksi = Transaksi::whereDate('created_at', now()->today())->count();
        $no_referensi = 'INV-' . $today . '-' . str_pad($lastTransaksi + 1, 4, '0', STR_PAD_LEFT);

        return view('admin.transaksi.create', compact('produks', 'cabangs', 'no_referensi'));
    }

    public function store(Request $request)
    {
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

        DB::beginTransaction();

        try {
            // Cari akun yang diperlukan untuk jurnal otomatis.
            $akunKas = Akun::where('nama_akun', 'Kas')
                ->where('status', 'aktif')
                ->first();

            $akunPendapatan = Akun::where('nama_akun', 'Pendapatan Penjualan')
                ->where('status', 'aktif')
                ->first();

            $akunHpp = Akun::where('nama_akun', 'Harga Pokok Produksi')
                ->where('status', 'aktif')
                ->first();

            $akunPersediaan = Akun::where('nama_akun', 'Persediaan')
                ->where('status', 'aktif')
                ->first();

            if (!$akunKas || !$akunPendapatan || !$akunHpp || !$akunPersediaan) {
                throw new \RuntimeException(
                    'Akun Kas, Pendapatan Penjualan, Harga Pokok Produksi, atau Persediaan belum tersedia atau tidak aktif.'
                );
            }

            // 1. Simpan transaksi induk.
            $transaksi = Transaksi::create([
                'no_referensi' => $request->no_referensi,
                'tanggal' => $request->tanggal,
                'nama_pelanggan' => $request->nama_pelanggan,
                'cabang_id' => $request->cabang_id,
                'user_id' => Auth::id(),
                'total_transaksi' => 0,
                'total_hpp' => 0,
            ]);

            $totalTransaksi = 0;
            $totalHpp = 0;
            $rincianJurnal = [];

            // 2. Simpan detail produk dan siapkan rincian jurnal.
            foreach ($request->produk_id as $key => $produkId) {
                $produk = Produk::findOrFail($produkId);

                $jumlah = (float) $request->jumlah[$key];
                $luas = (float) ($request->luas[$key] ?? 0);

                $harga = (float) $request->harga_satuan[$key];
                $hpp = (float) $request->hpp_satuan[$key];

                $subtotal = (float) $request->subtotal[$key];
                $totalHppItem = (float) $request->total_hpp_item[$key];

                // Untuk produk m2 gunakan luas sebagai quantity.
                $satuan = strtolower(trim($produk->satuan));

                $quantity = (
                    $satuan === 'm2' ||
                    $satuan === 'meter persegi' ||
                    str_contains($satuan, 'm²')
                ) ? $luas : $jumlah;

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

                $totalTransaksi += $subtotal;
                $totalHpp += $totalHppItem;

                $rincianJurnal[] = [
                    'quantity' => $quantity,
                    'satuan' => $produk->satuan,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                    'hpp' => $hpp,
                    'total_hpp' => $totalHppItem,
                ];
            }

            // 3. Perbarui total transaksi dari detail yang disimpan.
            $transaksi->update([
                'total_transaksi' => $totalTransaksi,
                'total_hpp' => $totalHpp,
            ]);

            // 4. Buat satu header jurnal untuk transaksi ini.
            $jurnal = Jurnal::create([
                'no_referensi' => $transaksi->no_referensi,
                'tanggal' => $transaksi->tanggal,
                'keterangan' => 'Penjualan - ' . $transaksi->nama_pelanggan,
                'cabang_id' => $transaksi->cabang_id,
                'user_id' => Auth::id(),
            ]);

            // 5. Buat empat baris jurnal untuk setiap produk:
            // Kas (Debit), Pendapatan (Kredit),
            // HPP (Debit), Persediaan (Kredit).
            foreach ($rincianJurnal as $rincian) {
                // A. Pendapatan: Debit Kas.
                DetailJurnal::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $akunKas->id,
                    'quantity' => $rincian['quantity'],
                    'satuan' => $rincian['satuan'],
                    'harga' => $rincian['harga'],
                    'total_harga' => $rincian['subtotal'],
                    'debit' => $rincian['subtotal'],
                    'kredit' => 0,
                ]);

                // B. Pendapatan: Kredit Pendapatan Penjualan.
                DetailJurnal::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $akunPendapatan->id,
                    'quantity' => $rincian['quantity'],
                    'satuan' => $rincian['satuan'],
                    'harga' => $rincian['harga'],
                    'total_harga' => $rincian['subtotal'],
                    'debit' => 0,
                    'kredit' => $rincian['subtotal'],
                ]);

                // C. HPP: Debit Harga Pokok Produksi.
                DetailJurnal::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $akunHpp->id,
                    'quantity' => $rincian['quantity'],
                    'satuan' => $rincian['satuan'],
                    'harga' => $rincian['hpp'],
                    'total_harga' => $rincian['total_hpp'],
                    'debit' => $rincian['total_hpp'],
                    'kredit' => 0,
                ]);

                // D. HPP: Kredit Persediaan.
                DetailJurnal::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $akunPersediaan->id,
                    'quantity' => $rincian['quantity'],
                    'satuan' => $rincian['satuan'],
                    'harga' => $rincian['hpp'],
                    'total_harga' => $rincian['total_hpp'],
                    'debit' => 0,
                    'kredit' => $rincian['total_hpp'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('transaksi.index')
                ->with('success', 'Transaksi dan jurnal otomatis berhasil disimpan!');

        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            return back()
                ->with('error', 'Transaksi gagal disimpan. Periksa konfigurasi akun dan struktur database.')
                ->withInput();
        }
    }
}