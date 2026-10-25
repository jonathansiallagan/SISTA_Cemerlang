<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\DetailJurnal;
use App\Models\DetailTransaksi;
use App\Models\Jurnal;
use App\Models\Produk;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksis = Transaksi::with(['user', 'cabang'])->latest()->get();
        return view('admin.transaksi.index', compact('transaksis'));
    }

    public function create()
    {
        $produks = Produk::all();
        $cabangs = Cabang::where('status', 'aktif')->get();
        $no_referensi = $this->buatNomor('INV');

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
            'jumlah.*' => 'required|integer|min:1',
            'panjang.*' => 'nullable|numeric|min:0',
            'lebar.*' => 'nullable|numeric|min:0',
        ], [
            'produk_id.required' => 'Pilih minimal satu produk.',
            'produk_id.*.required' => 'Ada baris produk yang belum dipilih.',
            'produk_id.*.exists' => 'Produk yang dipilih tidak ditemukan.',
            'jumlah.*.integer' => 'Qty harus berupa bilangan bulat.',
            'jumlah.*.min' => 'Qty minimal 1.',
        ]);

        // Akun yang dibutuhkan untuk jurnal otomatis
        $akunKas = $this->cariAkun('Kas');
        $akunPendapatan = $this->cariAkun('Pendapatan Penjualan');
        $akunHpp = $this->cariAkun('Harga Pokok Produksi');
        $akunPersediaan = $this->cariAkun('Persediaan');

        if (!$akunKas || !$akunPendapatan || !$akunHpp || !$akunPersediaan) {
            return back()
                ->with('error', 'Akun Kas, Pendapatan Penjualan, Harga Pokok Produksi, atau Persediaan belum tersedia / tidak aktif. Jalankan AkunSeeder atau tambahkan akun tersebut di menu Akun.')
                ->withInput();
        }

        // Semua nilai dihitung di server (harga & HPP dari tabel produk)
        $produkMap = Produk::whereIn('id', $request->produk_id)->get()->keyBy('id');
        $items = [];

        foreach ($request->produk_id as $key => $produkId) {
            $produk = $produkMap[$produkId];
            $satuan = strtolower(trim($produk->satuan));
            $adalahM2 = $satuan === 'm2' || $satuan === 'meter persegi' || str_contains($satuan, 'm²');

            $jumlah = (int) ($request->jumlah[$key] ?? 1);
            $panjang = null;
            $lebar = null;
            $luas = null;

            if ($adalahM2) {
                $panjang = (float) ($request->panjang[$key] ?? 0);
                $lebar = (float) ($request->lebar[$key] ?? 0);

                if ($panjang <= 0 || $lebar <= 0) {
                    throw ValidationException::withMessages([
                        'produk_id' => 'Panjang dan lebar wajib diisi untuk produk "' . $produk->nama_produk . '".',
                    ]);
                }

                $luas = round($panjang * $lebar, 2);
                $jumlah = 1;
                $pengali = $luas;
            } else {
                $pengali = $jumlah;
            }

            $harga = (float) $produk->harga_jual;
            $hpp = (float) $produk->hpp;

            $items[] = [
                'produk' => $produk,
                'jumlah' => $jumlah,
                'panjang' => $panjang,
                'lebar' => $lebar,
                'luas' => $luas,
                'quantity' => $pengali,
                'harga' => $harga,
                'hpp' => $hpp,
                'subtotal' => round($harga * $pengali, 2),
                'total_hpp' => round($hpp * $pengali, 2),
            ];
        }

        $totalTransaksi = array_sum(array_column($items, 'subtotal'));
        $totalHpp = array_sum(array_column($items, 'total_hpp'));

        DB::beginTransaction();

        try {
            $transaksi = Transaksi::create([
                'no_referensi' => $request->no_referensi,
                'tanggal' => $request->tanggal,
                'nama_pelanggan' => $request->nama_pelanggan,
                'cabang_id' => $request->cabang_id,
                'user_id' => Auth::id(),
                'total_transaksi' => $totalTransaksi,
                'total_hpp' => $totalHpp,
            ]);

            $jurnal = Jurnal::create([
                'no_referensi' => $transaksi->no_referensi,
                'tanggal' => $transaksi->tanggal,
                'keterangan' => 'Penjualan - ' . $transaksi->nama_pelanggan,
                'cabang_id' => $transaksi->cabang_id,
                'user_id' => Auth::id(),
            ]);

            foreach ($items as $item) {
                // Kolom `tinggi` dipakai menyimpan PANJANG (form: panjang x lebar)
                DetailTransaksi::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk']->id,
                    'jumlah' => $item['jumlah'],
                    'lebar' => $item['lebar'],
                    'tinggi' => $item['panjang'],
                    'luas' => $item['luas'],
                    'harga_satuan' => $item['harga'],
                    'hpp_satuan' => $item['hpp'],
                    'subtotal' => $item['subtotal'],
                    'total_hpp' => $item['total_hpp'],
                ]);

                // 4 baris jurnal per produk: Kas(D), Pendapatan(K), HPP(D), Persediaan(K)
                $baris = [
                    [$akunKas, $item['harga'], $item['subtotal'], 'debit'],
                    [$akunPendapatan, $item['harga'], $item['subtotal'], 'kredit'],
                    [$akunHpp, $item['hpp'], $item['total_hpp'], 'debit'],
                    [$akunPersediaan, $item['hpp'], $item['total_hpp'], 'kredit'],
                ];

                foreach ($baris as [$akun, $harga, $total, $sisi]) {
                    DetailJurnal::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id' => $akun->id,
                        'quantity' => $item['quantity'],
                        'satuan' => $item['produk']->satuan,
                        'harga' => $harga,
                        'total_harga' => $total,
                        'debit' => $sisi === 'debit' ? $total : 0,
                        'kredit' => $sisi === 'kredit' ? $total : 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('transaksi.index')
                ->with('success', 'Transaksi dan jurnal otomatis berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            $pesan = config('app.debug')
                ? 'Transaksi gagal disimpan: ' . $e->getMessage()
                : 'Transaksi gagal disimpan. Periksa konfigurasi akun dan struktur database.';

            return back()->with('error', $pesan)->withInput();
        }
    }

    private function cariAkun(string $nama): ?Akun
    {
        return Akun::where('nama_akun', $nama)->where('status', 'aktif')->first();
    }

    // Contoh hasil: INV-20261025-0001
    private function buatNomor(string $prefix): string
    {
        $awal = $prefix . '-' . now()->format('Ymd') . '-';

        $terakhir = DB::table('transaksis')
            ->where('no_referensi', 'like', $awal . '%')
            ->orderByDesc('no_referensi')
            ->value('no_referensi');

        $urut = $terakhir ? ((int) substr($terakhir, -4)) + 1 : 1;

        return $awal . str_pad($urut, 4, '0', STR_PAD_LEFT);
    }
}