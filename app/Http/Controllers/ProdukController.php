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
    public function index()
    {
        $produks = Produk::orderBy('nama_produk')->get();

        $bahanBakus = BahanBaku::where('status', 'aktif')
            ->orderBy('nama_bahan')
            ->get();

        return view('admin.produk.index', compact(
            'produks',
            'bahanBakus'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_produk' => [
                'required',
                'string',
                'max:255',
                'unique:produks,nama_produk',
            ],
            'satuan' => 'required|string|max:30',

            'bahan_baku_id' => 'required|array|min:1',
            'bahan_baku_id.*' => [
                'required',
                'integer',
                'distinct',
                'exists:bahan_baku,id',
            ],

            'jumlah_pemakaian' => 'required|array|min:1',
            'jumlah_pemakaian.*' => 'required|numeric|min:0.0001',

            'satuan_pemakaian' => 'required|array|min:1',
            'satuan_pemakaian.*' => 'required|string|max:30',
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.unique' => 'Nama produk sudah terdaftar.',
            'satuan.required' => 'Satuan produk wajib dipilih.',
            'bahan_baku_id.required' => 'Tambahkan minimal satu bahan baku.',
            'bahan_baku_id.*.distinct' => 'Bahan baku tidak boleh dipilih berulang.',
            'jumlah_pemakaian.*.required' => 'Jumlah pemakaian wajib diisi.',
            'satuan_pemakaian.*.required' => 'Satuan pemakaian wajib diisi.',
        ]);

        // Pastikan jumlah detail setiap array sama.
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

    public function edit($id)
    {
        $produk = Produk::findOrFail($id);

        return view('admin.produk.edit', compact('produk'));
    }

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
        ], [
            'nama_produk.required' => 'Nama produk wajib diisi.',
            'nama_produk.unique' => 'Nama produk tersebut sudah terdaftar.',
            'nama_produk.max' => 'Nama produk maksimal 255 karakter.',
            'satuan.required' => 'Satuan wajib dipilih atau diisi.',
            'satuan.max' => 'Satuan maksimal 50 karakter.',
        ]);

        $produk->update($validated);

        return redirect()
            ->route('produk.index')
            ->with('success', 'Data produk berhasil diperbarui!');
    }

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