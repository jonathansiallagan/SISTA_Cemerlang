<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Produk;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProdukController extends Controller
{
    /**
     * Kode SQLSTATE untuk pelanggaran foreign key
     * (MySQL, MariaDB, SQLite: 23000 | PostgreSQL: 23503).
     */
    private const SQLSTATE_FOREIGN_KEY = ['23000', '23503'];

    /**
     * Pesan validasi, dipakai bersama oleh tambah dan ubah produk.
     */
    private const PESAN_VALIDASI = [
        'nama_produk.required' => 'Nama produk wajib diisi.',
        'nama_produk.max' => 'Nama produk maksimal 255 karakter.',
        'nama_produk.unique' => 'Nama produk sudah terdaftar.',

        'satuan.required' => 'Satuan produk wajib dipilih.',
        'satuan.max' => 'Satuan produk maksimal 50 karakter.',

        'upah_operator.required' => 'Upah operator wajib diisi.',
        'upah_operator.numeric' => 'Upah operator harus berupa angka.',
        'upah_operator.min' => 'Upah operator tidak boleh negatif.',

        'bahan_baku_id.required' => 'Tambahkan minimal satu bahan baku.',
        'bahan_baku_id.min' => 'Tambahkan minimal satu bahan baku.',
        'bahan_baku_id.*.required' => 'Bahan baku wajib dipilih.',
        'bahan_baku_id.*.distinct' => 'Bahan baku tidak boleh dipilih berulang.',
        'bahan_baku_id.*.exists' => 'Bahan baku yang dipilih tidak ditemukan.',

        'jumlah_pemakaian.required' => 'Data komposisi bahan tidak lengkap.',
        'jumlah_pemakaian.size' => 'Data komposisi bahan tidak lengkap.',
        'jumlah_pemakaian.*.required' => 'Jumlah pemakaian wajib diisi.',
        'jumlah_pemakaian.*.numeric' => 'Jumlah pemakaian harus berupa angka.',
        'jumlah_pemakaian.*.min' => 'Jumlah pemakaian harus lebih dari nol.',

        'satuan_pemakaian.required' => 'Data komposisi bahan tidak lengkap.',
        'satuan_pemakaian.size' => 'Data komposisi bahan tidak lengkap.',
        'satuan_pemakaian.*.required' => 'Satuan pemakaian wajib diisi.',
        'satuan_pemakaian.*.max' => 'Satuan pemakaian maksimal 30 karakter.',
    ];

    /**
     * Menampilkan daftar produk beserta modal tambah dan modal edit.
     */
    public function index(): View
    {
        $produks = Produk::with('komposisi')
            ->orderBy('nama_produk')
            ->get();

        $bahanBakus = BahanBaku::where('status', 'aktif')
            ->orderBy('nama_bahan')
            ->get();

        return view('admin.produk.index', compact(
            'produks',
            'bahanBakus'
        ));
    }

    /**
     * Menyimpan produk baru beserta komposisi bahan bakunya.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validasiProduk($request);

        DB::transaction(function () use ($validated) {
            $produk = Produk::create($this->dataProduk($validated));

            $produk->komposisi()->createMany($this->dataKomposisi($validated));
        });

        return redirect()
            ->route('produk.index')
            ->with('success', 'Produk dan komposisi bahan berhasil ditambahkan.');
    }

    /**
     * Memperbarui produk dan komposisi bahan bakunya.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $produk = Produk::findOrFail($id);

        $validated = $this->validasiProduk($request, $produk);

        DB::transaction(function () use ($produk, $validated) {
            $produk->update($this->dataProduk($validated));

            // Ganti komposisi lama dengan komposisi yang dikirim dari modal edit.
            $produk->komposisi()->delete();
            $produk->komposisi()->createMany($this->dataKomposisi($validated));
        });

        return redirect()
            ->route('produk.index')
            ->with('success', 'Data produk dan komposisi bahan berhasil diperbarui.');
    }

    /**
     * Menghapus produk (Soft Delete).
     */
    public function destroy($id): RedirectResponse
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect()
            ->route('produk.index')
            ->with('success', 'Data produk berhasil dihapus secara aman!');
    }

    /**
     * Memvalidasi isian modal tambah/edit produk.
     */
    private function validasiProduk(Request $request, ?Produk $produk = null): array
    {
        $jumlahBaris = count(Arr::wrap($request->input('bahan_baku_id')));

        return $request->validate([
            'nama_produk' => [
                'required',
                'string',
                'max:255',
                Rule::unique('produks', 'nama_produk')
                    ->ignore($produk?->id)
                    ->whereNull('deleted_at'),
            ],

            'satuan' => ['required', 'string', 'max:50'],

            'upah_operator' => ['required', 'numeric', 'min:0'],

            'bahan_baku_id' => ['required', 'array', 'min:1'],
            'bahan_baku_id.*' => ['required', 'integer', 'distinct', 'exists:bahan_baku,id'],

            'jumlah_pemakaian' => ['required', 'array', 'size:' . $jumlahBaris],
            'jumlah_pemakaian.*' => ['required', 'numeric', 'min:0.0001'],

            'satuan_pemakaian' => ['required', 'array', 'size:' . $jumlahBaris],
            'satuan_pemakaian.*' => ['required', 'string', 'max:30'],
        ], self::PESAN_VALIDASI);
    }

    /**
     * Mengambil kolom milik produk dari data yang sudah tervalidasi.
     */
    private function dataProduk(array $validated): array
    {
        return Arr::only($validated, ['nama_produk', 'satuan', 'upah_operator']);
    }

    /**
     * Mengubah tiga daftar paralel dari form menjadi satu daftar baris komposisi.
     */
    private function dataKomposisi(array $validated): array
    {
        $jumlah = array_values($validated['jumlah_pemakaian']);
        $satuan = array_values($validated['satuan_pemakaian']);

        return collect(array_values($validated['bahan_baku_id']))
            ->map(fn ($bahanBakuId, $i) => [
                'bahan_baku_id' => $bahanBakuId,
                'jumlah_pemakaian' => $jumlah[$i],
                'satuan_pemakaian' => $satuan[$i],
            ])
            ->all();
    }
}