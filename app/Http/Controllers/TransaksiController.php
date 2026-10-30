<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\Produk;
use App\Models\Cabang;
use App\Models\Jurnal;
use App\Models\DetailJurnal;
use App\Models\Akun;
use App\Models\BahanBaku;
use App\Models\KomposisiProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TransaksiController extends Controller
{
    /**
     * Daftar transaksi.
     */
    public function index(Request $request)
    {
        $user = $this->user();

        $query = Transaksi::with([
            'user',
            'cabang',
            'detailTransaksis.produk',
        ]);

        if ($user->role === 'Admin Cabang' || $user->isCustomerService()) {
            $query->where('cabang_id', $user->cabang_id);
        } elseif ($user->role === 'Admin Pusat' && $request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('no_referensi', 'like', "%{$search}%")
                    ->orWhere('nama_pelanggan', 'like', "%{$search}%");
            });
        }

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

        $cabangs = $user->isCustomerService()
            ? Cabang::where('id', $user->cabang_id)->get()
            : Cabang::orderBy('nama')->get();

        if ($user->isCustomerService()) {
            return view('cs.transaksi.index', compact(
                'transaksis',
                'cabangs'
            ));
        }

        return view('admin.transaksi.index', compact(
            'transaksis',
            'cabangs'
        ));
    }

    /**
     * Form transaksi baru.
     */
    
public function create()
{
    $user = $this->user();

    if (!$user->isCustomerService()) {
        abort(403, 'Hanya Customer Service yang dapat membuat transaksi.');
    }

    $produks = Produk::orderBy('nama_produk')->get();

    $cabangs = Cabang::where('id', $user->cabang_id)->get();

    $today = now()->format('Ymd');

    $lastTransaksi = Transaksi::whereDate(
        'created_at',
        now()->toDateString()
    )->count();

    $no_referensi = 'INV-' . $today . '-'
        . str_pad($lastTransaksi + 1, 4, '0', STR_PAD_LEFT);

    return view('admin.transaksi.modal-page', compact(
        'produks',
        'cabangs',
        'no_referensi'
    ));
}


    /**
     * Simpan transaksi, hitung HPP, kurangi stok, dan buat jurnal.
     */
    public function store(Request $request)
    {
        $user = $this->user();

        if (!$user->isCustomerService()) {
            abort(403, 'Hanya Customer Service yang dapat membuat transaksi.');
        }

        $validated = $request->validate([
            'no_referensi' => 'required|string|unique:transaksis,no_referensi',
            'tanggal' => 'required|date',
            'nama_pelanggan' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:20',

            'produk_id' => 'required|array|min:1',
            'produk_id.*' => 'required|integer|exists:produks,id',

            'jumlah' => 'required|array|min:1',
            'jumlah.*' => 'required|integer|min:1',

            'harga_jual' => 'required|array|min:1',
            'harga_jual.*' => 'required|numeric|min:0',

            'jasa_edit' => 'nullable|array',
            'jasa_edit.*' => 'nullable|numeric|min:0',

            'tinggi' => 'required|array|min:1',
            'tinggi.*' => 'nullable|numeric|min:0.01',

            'lebar' => 'required|array|min:1',
            'lebar.*' => 'nullable|numeric|min:0.01',
        ]);

        $jumlahBaris = count($validated['produk_id']);

        foreach (['jumlah', 'harga_jual', 'jasa_edit', 'tinggi', 'lebar'] as $field) {
            if (count($validated[$field]) !== $jumlahBaris) {
                return back()
                    ->withErrors([
                        $field => 'Data detail produk tidak lengkap.',
                    ])
                    ->withInput();
            }
        }

        try {
            DB::transaction(function () use ($validated, $user) {
                $akunKas = Akun::where('kode_akun', '1-1100')
                    ->where('status', 'aktif')
                    ->first();

                $akunPendapatan = Akun::where('kode_akun', '4-1100')
                    ->where('status', 'aktif')
                    ->first();

                $akunHpp = Akun::where('kode_akun', '5-1100')
                    ->where('status', 'aktif')
                    ->first();

                $akunPersediaan = Akun::where('kode_akun', '1-1300')
                    ->where('status', 'aktif')
                    ->first();

                if (
                    !$akunKas ||
                    !$akunPendapatan ||
                    !$akunHpp ||
                    !$akunPersediaan
                ) {
                    throw new \RuntimeException(
                        'Akun Kas, Pendapatan Penjualan, HPP, atau Persediaan '
                        . 'belum tersedia atau tidak aktif. '
                        . 'Periksa kode akun pada menu Akun.'
                    );
                }

                $transaksi = Transaksi::create([
                    'no_referensi' => $validated['no_referensi'],
                    'tanggal' => $validated['tanggal'],
                    'nama_pelanggan' => $validated['nama_pelanggan'],
                    'cabang_id' => $user->cabang_id,
                    'nomor_hp' => $validated['nomor_hp'] ?? null,
                    'user_id' => $user->id,
                    'total_transaksi' => 0,
                    'total_hpp' => 0,
                ]);

                $totalTransaksi = 0;
                $totalHppBahan = 0;
                $totalUpahOperator = 0;
                $detailRows = [];

                foreach ($validated['produk_id'] as $key => $produkId) {
                    $produk = Produk::findOrFail($produkId);

                    $jumlah = (int) $validated['jumlah'][$key];
                    $hargaFinalBarang = (float) $validated['harga_jual'][$key];
                    $jasaEdit = (float) ($validated['jasa_edit'][$key] ?? 0);

                    $satuanProduk = strtolower(trim((string) $produk->satuan));

                    $isM2 = in_array($satuanProduk, [
                        'm2',
                        'm²',
                        'meter persegi',
                    ], true);

                    $tinggi = null;
                    $lebar = null;
                    $luas = 0;

                    if ($isM2) {
                        $tinggi = (float) ($validated['tinggi'][$key] ?? 0);
                        $lebar = (float) ($validated['lebar'][$key] ?? 0);

                        if ($tinggi <= 0 || $lebar <= 0) {
                            throw new \RuntimeException(
                                "Tinggi dan lebar {$produk->nama_produk} wajib diisi."
                            );
                        }

                        // Luas total untuk seluruh jumlah barang.
                        $luas = $tinggi * $lebar * $jumlah;
                    }

                    // Harga barang dan jasa edit adalah total untuk baris pesanan.
                    $subtotal = $hargaFinalBarang + $jasaEdit;

                    $komposisis = KomposisiProduk::where('produk_id', $produk->id)
                        ->get();

                    if ($komposisis->isEmpty()) {
                        throw new \RuntimeException(
                            "Komposisi bahan untuk {$produk->nama_produk} belum diatur."
                        );
                    }

                    /*
                     * Dasar perhitungan pemakaian:
                     * - Produk m²: gunakan total luas pesanan.
                     * - Produk lainnya: gunakan jumlah barang.
                     */
                    $pengaliPemakaian = $isM2 ? $luas : $jumlah;

                    $hppBahanItem = 0;

                    foreach ($komposisis as $komposisi) {
                        $bahan = BahanBaku::whereKey($komposisi->bahan_baku_id)
                            ->lockForUpdate()
                            ->first();

                        if (!$bahan || $bahan->status !== 'aktif') {
                            throw new \RuntimeException(
                                "Bahan baku untuk {$produk->nama_produk} "
                                . 'tidak ditemukan atau tidak aktif.'
                            );
                        }

                        $jumlahPemakaian = (float) $komposisi->jumlah_pemakaian
                            * $pengaliPemakaian;

                        if ($jumlahPemakaian <= 0) {
                            continue;
                        }

                        $stokTersedia = (float) $bahan->stok;

                        if ($stokTersedia < $jumlahPemakaian) {
                            throw new \RuntimeException(
                                "Stok {$bahan->nama_bahan} tidak mencukupi. "
                                . "Stok tersedia: {$stokTersedia} {$bahan->satuan}, "
                                . "dibutuhkan: {$jumlahPemakaian} "
                                . "{$komposisi->satuan_pemakaian}."
                            );
                        }

                        $hargaRataRata = (float) $bahan->harga_rata_rata;
                        $biayaBahan = $jumlahPemakaian * $hargaRataRata;

                        $hppBahanItem += $biayaBahan;

                        // Kurangi stok sesuai komposisi.
                        $bahan->stok = $stokTersedia - $jumlahPemakaian;
                        $bahan->save();
                    }

                    /*
                     * UPAH OPERATOR:
                     * Tarif disimpan pada master produk sebagai upah per satuan.
                     *
                     * Produk m²:
                     * upah per m² x total luas pesanan.
                     *
                     * Produk lainnya:
                     * upah per satuan x jumlah barang.
                     */
                    $upahPerSatuan = (float) ($produk->upah_operator ?? 0);

                    $upahItem = $upahPerSatuan * $pengaliPemakaian;

                    // HPP produk = biaya bahan baku + total upah operator.
                    $hppItem = $hppBahanItem + $upahItem;

                    $detailRows[] = [
                        'transaksi_id' => $transaksi->id,
                        'produk_id' => $produk->id,
                        'jumlah' => $jumlah,
                        'tinggi' => $tinggi,
                        'lebar' => $lebar,
                        'luas' => $isM2 ? $luas : null,

                        // Harga final seluruh pesanan pada baris ini.
                        'harga_satuan' => $hargaFinalBarang,

                        // HPP rata-rata per barang.
                        'hpp_satuan' => $hppItem / max($jumlah, 1),

                        // Harga barang ditambah jasa edit.
                        'subtotal' => $subtotal,

                        // Total HPP bahan dan upah operator pada baris ini.
                        'total_hpp' => $hppItem,
                    ];

                    $totalTransaksi += $subtotal;
                    $totalHppBahan += $hppBahanItem;
                    $totalUpahOperator += $upahItem;
                }

                $totalHpp = $totalHppBahan + $totalUpahOperator;

                foreach ($detailRows as $detailRow) {
                    DetailTransaksi::create($detailRow);
                }

                $transaksi->update([
                    'total_transaksi' => $totalTransaksi,
                    'total_hpp' => $totalHpp,
                ]);

                // Buat jurnal penjualan.
                $jurnal = Jurnal::create([
                    'no_referensi' => $transaksi->no_referensi,
                    'tanggal' => $transaksi->tanggal,
                    'keterangan' => 'Penjualan produk cetak dan jasa edit',
                    'cabang_id' => $transaksi->cabang_id,
                    'user_id' => $user->id,
                ]);

                // Debit Kas sebesar pembayaran pelanggan.
                DetailJurnal::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $akunKas->id,
                    'keterangan' => 'Pendapatan penjualan',
                    'debit' => $totalTransaksi,
                    'kredit' => 0,
                ]);

                // Kredit Pendapatan: harga barang + jasa edit.
                DetailJurnal::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $akunPendapatan->id,
                    'keterangan' => 'Pendapatan penjualan',
                    'debit' => 0,
                    'kredit' => $totalTransaksi,
                ]);

                // Debit HPP seluruh baris produk.
                DetailJurnal::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $akunHpp->id,
                    'keterangan' => 'Pengakuan harga pokok produksi',
                    'debit' => $totalHpp,
                    'kredit' => 0,
                ]);

                // Kredit Persediaan sebesar nilai bahan yang dipakai.
                if ($totalHppBahan > 0) {
                    DetailJurnal::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id' => $akunPersediaan->id,
                        'keterangan' => 'Pengakuan harga pokok produksi',
                        'debit' => 0,
                        'kredit' => $totalHppBahan,
                    ]);
                }

                /*
                 * Kredit Kas untuk upah operator.
                 * Asumsi: upah operator dibayarkan langsung saat transaksi.
                 */
                if ($totalUpahOperator > 0) {
                    DetailJurnal::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id' => $akunKas->id,
                        'keterangan' => 'Pengakuan harga pokok produksi',
                        'debit' => 0,
                        'kredit' => $totalUpahOperator,
                    ]);
                }
            });

            return redirect()
                ->route('transaksi.index')
                ->with('success', 'Transaksi, HPP, stok, dan jurnal berhasil disimpan!');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Transaksi gagal disimpan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Ekspor PDF.
     */
    public function exportPdf(Request $request)
    {
        $user = $this->user();

        $query = Transaksi::with([
            'user',
            'cabang',
            'detailTransaksis.produk',
        ]);

        if ($user->role === 'Admin Cabang' || $user->isCustomerService()) {
            $query->where('cabang_id', $user->cabang_id);
        } elseif ($user->role === 'Admin Pusat' && $request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('no_referensi', 'like', "%{$search}%")
                    ->orWhere('nama_pelanggan', 'like', "%{$search}%");
            });
        }

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

    public function formModal()
    {
        $user = $this->user();

        if (!$user->isCustomerService()) {
            abort(403, 'Hanya Customer Service yang dapat membuat transaksi.');
        }

        $produks = Produk::orderBy('nama_produk')->get();

        $cabangs = Cabang::where('id', $user->cabang_id)->get();

        $today = now()->format('Ymd');

        $lastTransaksi = Transaksi::whereDate(
            'created_at',
            now()->toDateString()
        )->count();

        $no_referensi = 'INV-' . $today . '-'
            . str_pad($lastTransaksi + 1, 4, '0', STR_PAD_LEFT);

        return view(
            'admin.transaksi.partials.form',
            compact('produks', 'cabangs', 'no_referensi')
        );
    }

    /**
     * Ekspor Excel.
     */
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new \App\Exports\TransaksiExport($request, $this->user()),
            'riwayat-transaksi.xlsx'
        );
    }
}