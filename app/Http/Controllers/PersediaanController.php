<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\PembelianBahan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PersediaanController extends Controller
{
    /**
     * Menampilkan daftar persediaan bahan baku.
     */
    public function index()
    {
        $bahanBakus = BahanBaku::orderBy('nama_bahan')->get();

        $totalJenisBahan = $bahanBakus->count();

        $totalJenisAktif = $bahanBakus
            ->where('status', 'aktif')
            ->count();

        $totalStok = $bahanBakus->sum('stok');

        $totalNilaiPersediaan = $bahanBakus->sum(
            fn ($bahan) => (float) $bahan->stok
                * (float) $bahan->harga_rata_rata
        );

        return view('admin.persediaan.index', compact(
            'bahanBakus',
            'totalJenisBahan',
            'totalJenisAktif',
            'totalStok',
            'totalNilaiPersediaan'
        ));
    }

    /**
     * Menyimpan transaksi pembelian dan memperbarui persediaan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pembelian' => [
                'required',
                'date',
            ],

            'nama_pemasok' => [
                'nullable',
                'string',
                'max:255',
            ],

            'keterangan' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.bahan_baku_id' => [
                'required',
                'integer',
                'distinct',
                'exists:bahan_baku,id',
            ],

            'items.*.jumlah' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'items.*.harga_satuan' => [
                'required',
                'numeric',
                'gte:0',
            ],
        ], [
            'tanggal_pembelian.required' =>
                'Tanggal pembelian wajib diisi.',

            'items.required' =>
                'Tambahkan minimal satu bahan baku.',

            'items.min' =>
                'Tambahkan minimal satu bahan baku.',

            'items.*.bahan_baku_id.required' =>
                'Pilih bahan baku.',

            'items.*.bahan_baku_id.distinct' =>
                'Bahan yang sama tidak boleh dimasukkan dua kali. Gabungkan jumlahnya dalam satu baris.',

            'items.*.bahan_baku_id.exists' =>
                'Bahan baku yang dipilih tidak ditemukan.',

            'items.*.jumlah.required' =>
                'Jumlah pembelian wajib diisi.',

            'items.*.jumlah.gt' =>
                'Jumlah pembelian harus lebih dari nol.',

            'items.*.harga_satuan.required' =>
                'Harga satuan wajib diisi.',

            'items.*.harga_satuan.gte' =>
                'Harga beli tidak boleh negatif.',
        ]);

        // Hitung total pembelian di sisi server.
        $totalHarga = 0;

        foreach ($validated['items'] as $item) {
            $totalHarga += round(
                (float) $item['jumlah']
                    * (float) $item['harga_satuan'],
                2
            );
        }

        DB::transaction(function () use ($validated, $totalHarga) {
            $pembelian = PembelianBahan::create([
                'no_pembelian' => 'PB-'
                    . now()->format('YmdHis')
                    . '-'
                    . strtoupper(Str::random(4)),

                'tanggal_pembelian' =>
                    $validated['tanggal_pembelian'],

                'nama_pemasok' =>
                    $validated['nama_pemasok'] ?? null,

                'keterangan' =>
                    $validated['keterangan'] ?? null,

                'total_harga' => $totalHarga,
            ]);

            foreach ($validated['items'] as $item) {
                $bahan = BahanBaku::whereKey(
                    $item['bahan_baku_id']
                )
                    ->lockForUpdate()
                    ->first();

                if (!$bahan || $bahan->status !== 'aktif') {
                    throw ValidationException::withMessages([
                        'items' =>
                            'Bahan baku tidak ditemukan atau sudah nonaktif.',
                    ]);
                }

                $stokLama = (float) $bahan->stok;
                $hargaLama = (float) $bahan->harga_rata_rata;

                $jumlahBeli = (float) $item['jumlah'];
                $hargaBeli = (float) $item['harga_satuan'];

                $stokBaru = $stokLama + $jumlahBeli;

                // Hitung harga rata-rata tertimbang.
                $hargaRataRataBaru = $stokBaru > 0
                    ? (
                        ($stokLama * $hargaLama)
                        + ($jumlahBeli * $hargaBeli)
                    ) / $stokBaru
                    : 0;

                $subtotal = round(
                    $jumlahBeli * $hargaBeli,
                    2
                );

                // Simpan detail pembelian.
                $pembelian->detail()->create([
                    'bahan_baku_id' => $bahan->id,
                    'jumlah' => $jumlahBeli,
                    'harga_satuan' => $hargaBeli,
                    'total_harga' => $subtotal,
                ]);

                // Perbarui stok dan harga rata-rata bahan.
                $bahan->update([
                    'stok' => $stokBaru,
                    'harga_rata_rata' =>
                        round($hargaRataRataBaru, 2),
                ]);
            }
        });

        return redirect()
            ->route('persediaan.index')
            ->with(
                'success',
                'Pembelian berhasil disimpan. Stok dan harga rata-rata telah diperbarui.'
            );
    }
}