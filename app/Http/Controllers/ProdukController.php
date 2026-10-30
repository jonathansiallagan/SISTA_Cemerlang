<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\BahanBaku;
use App\Models\KomposisiProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Validation\Rule;

class ProdukController extends Controller
{
    /**
     * Menampilkan daftar produk dan bahan baku aktif.
     */
    public function index()
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => [
                'required',
                'string',
                'max:255',
                'unique:produks,nama_produk',
            ],

            'satuan' => [
                'required',
                'string',
                'max:50',
            ],

            'upah_operator' => [
                'required',
                'numeric',
                'min:0',
            ],

            'bahan_baku_id' => [
                'required',
                'array',
                'min:1',
            ],

            'bahan_baku_id.*' => [
                'required',
                'integer',
                'distinct',
                'exists:bahan_baku,id',
            ],

            'jumlah_pemakaian' => [
                'required',
                'array',
                'min:1',
            ],

            'jumlah_pemakaian.*' => [
                'required',
                'numeric',
                'min:0.0001',
            ],

            'satuan_pemakaian' => [
                'required',
                'array',
                'min:1',
            ],

            'satuan_pemakaian.*' => [
                'required',
                'string',
                'max:30',
            ],
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.unique' => 'Nama produk sudah terdaftar.',
            'nama_produk.max' => 'Nama produk maksimal 255 karakter.',

            'satuan.required' => 'Satuan produk wajib dipilih.',
            'satuan.max' => 'Satuan produk maksimal 50 karakter.',

            'upah_operator.required' => 'Upah operator wajib diisi.',
            'upah_operator.numeric' => 'Upah operator harus berupa angka.',
            'upah_operator.min' => 'Upah operator tidak boleh negatif.',

            'bahan_baku_id.required' => 'Tambahkan minimal satu bahan baku.',
            'bahan_baku_id.*.distinct' => 'Bahan baku tidak boleh dipilih berulang.',
            'bahan_baku_id.*.exists' => 'Bahan baku yang dipilih tidak ditemukan.',

            'jumlah_pemakaian.*.required' => 'Jumlah pemakaian wajib diisi.',
            'jumlah_pemakaian.*.numeric' => 'Jumlah pemakaian harus berupa angka.',
            'jumlah_pemakaian.*.min' => 'Jumlah pemakaian harus lebih dari nol.',

            'satuan_pemakaian.*.required' => 'Satuan pemakaian wajib diisi.',
        ]);

        // Pastikan jumlah setiap kelompok data komposisi sama.
        $jumlahBaris = count($validated['bahan_baku_id']);

        foreach (['jumlah_pemakaian', 'satuan_pemakaian'] as $field) {
            if (count($validated[$field]) !== $jumlahBaris) {
                return back()
                    ->withErrors([
                        $field => 'Data komposisi bahan tidak lengkap.',
                    ])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($validated) {
            $produk = Produk::create([
                'nama_produk' => $validated['nama_produk'],
                'satuan' => $validated['satuan'],
                'upah_operator' => $validated['upah_operator'],
            ]);

            foreach ($validated['bahan_baku_id'] as $i => $bahanBakuId) {
                KomposisiProduk::create([
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahanBakuId,
                    'jumlah_pemakaian' => $validated['jumlah_pemakaian'][$i],
                    'satuan_pemakaian' => $validated['satuan_pemakaian'][$i],
                ]);
            }
        });

        return redirect()
            ->route('produk.index')
            ->with('success', 'Produk dan komposisi bahan berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman edit jika route edit digunakan.
     */
    public function edit($id)
    {
        $produk = Produk::with('komposisi')->findOrFail($id);

        $bahanBakus = BahanBaku::where('status', 'aktif')
            ->orderBy('nama_bahan')
            ->get();

        return view('admin.produk.edit', compact(
            'produk',
            'bahanBakus'
        ));
    }

    /**
     * Memperbarui produk dan komposisi bahan bakunya.
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $validated = $request->validate([
            'nama_produk' => [
                'required',
                'string',
                'max:255',
                Rule::unique('produks', 'nama_produk')->ignore($produk->id),
            ],

            'satuan' => [
                'required',
                'string',
                'max:50',
            ],

            'upah_operator' => [
                'required',
                'numeric',
                'min:0',
            ],

            'bahan_baku_id' => [
                'required',
                'array',
                'min:1',
            ],

            'bahan_baku_id.*' => [
                'required',
                'integer',
                'distinct',
                'exists:bahan_baku,id',
            ],

            'jumlah_pemakaian' => [
                'required',
                'array',
                'min:1',
            ],

            'jumlah_pemakaian.*' => [
                'required',
                'numeric',
                'min:0.0001',
            ],

            'satuan_pemakaian' => [
                'required',
                'array',
                'min:1',
            ],

            'satuan_pemakaian.*' => [
                'required',
                'string',
                'max:30',
            ],
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.unique' => 'Nama produk tersebut sudah terdaftar.',
            'nama_produk.max' => 'Nama produk maksimal 255 karakter.',

            'satuan.required' => 'Satuan produk wajib dipilih.',
            'satuan.max' => 'Satuan produk maksimal 50 karakter.',

            'upah_operator.required' => 'Upah operator wajib diisi.',
            'upah_operator.numeric' => 'Upah operator harus berupa angka.',
            'upah_operator.min' => 'Upah operator tidak boleh negatif.',

            'bahan_baku_id.required' => 'Tambahkan minimal satu bahan baku.',
            'bahan_baku_id.*.distinct' => 'Bahan baku tidak boleh dipilih berulang.',
            'bahan_baku_id.*.exists' => 'Bahan baku yang dipilih tidak ditemukan.',

            'jumlah_pemakaian.*.required' => 'Jumlah pemakaian wajib diisi.',
            'jumlah_pemakaian.*.numeric' => 'Jumlah pemakaian harus berupa angka.',
            'jumlah_pemakaian.*.min' => 'Jumlah pemakaian harus lebih dari nol.',

            'satuan_pemakaian.*.required' => 'Satuan pemakaian wajib diisi.',
        ]);

        // Pastikan jumlah setiap kelompok data komposisi sama.
        $jumlahBaris = count($validated['bahan_baku_id']);

        foreach (['jumlah_pemakaian', 'satuan_pemakaian'] as $field) {
            if (count($validated[$field]) !== $jumlahBaris) {
                return back()
                    ->withErrors([
                        $field => 'Data komposisi bahan tidak lengkap.',
                    ])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($produk, $validated) {
            // Perbarui informasi produk, termasuk upah operator.
            $produk->update([
                'nama_produk' => $validated['nama_produk'],
                'satuan' => $validated['satuan'],
                'upah_operator' => $validated['upah_operator'],
            ]);

            // Ganti komposisi lama dengan komposisi yang dikirim dari form.
            $produk->komposisi()->delete();

            foreach ($validated['bahan_baku_id'] as $i => $bahanBakuId) {
                KomposisiProduk::create([
                    'produk_id' => $produk->id,
                    'bahan_baku_id' => $bahanBakuId,
                    'jumlah_pemakaian' => $validated['jumlah_pemakaian'][$i],
                    'satuan_pemakaian' => $validated['satuan_pemakaian'][$i],
                ]);
            }
        });

        return redirect()
            ->route('produk.index')
            ->with('success', 'Data produk dan komposisi bahan berhasil diperbarui.');
    }

    /**
     * Menghapus produk.
     */
    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);

        try {
            $produk->delete();
        } catch (QueryException $e) {
            return redirect()
                ->route('produk.index')
                ->with(
                    'error',
                    'Produk tidak dapat dihapus karena masih digunakan pada data transaksi atau data terkait lainnya.'
                );
        }

        return redirect()
            ->route('produk.index')
            ->with('success', 'Data produk berhasil dihapus!');
    }
}