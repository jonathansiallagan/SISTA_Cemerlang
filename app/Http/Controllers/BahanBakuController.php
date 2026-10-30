<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class BahanBakuController extends Controller
{
    /**
     * Menghasilkan kode bahan berikutnya, misalnya BB001, BB002.
     * Dibuat kompatibel dengan SQLite dan MySQL.
     */
    private function generateKodeBahan(): string
    {
        $nomorTerakhir = BahanBaku::query()
            ->pluck('kode_bahan')
            ->filter(function ($kode) {
                return is_string($kode)
                    && preg_match('/^BB\d+$/', $kode);
            })
            ->map(function ($kode) {
                return (int) substr($kode, 2);
            })
            ->max();

        $nomorBaru = ((int) $nomorTerakhir) + 1;

        return 'BB' . str_pad(
            (string) $nomorBaru,
            3,
            '0',
            STR_PAD_LEFT
        );
    }

    public function index()
    {
        $bahanBakus = BahanBaku::orderBy('nama_bahan')->get();

        // Kode awal untuk ditampilkan di modal.
        $kodeBahan = $this->generateKodeBahan();

        return view(
            'admin.bahan_baku.index',
            compact('bahanBakus', 'kodeBahan')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_bahan' => [
                'required',
                'string',
                'max:255',
            ],
            'spesifikasi' => [
                'nullable',
                'string',
                'max:255',
            ],
            'satuan' => [
                'required',
                'string',
                'max:50',
                Rule::in([
                    'lembar',
                    'rim',
                    'pcs',
                    'roll',
                    'meter',
                    'm2',
                    'kg',
                    'liter',
                    'botol',
                    'pak',
                ]),
            ],
            'status' => [
                'required',
                Rule::in(['aktif', 'nonaktif']),
            ],
        ], [
            'nama_bahan.required' => 'Nama bahan wajib diisi.',
            'nama_bahan.max' => 'Nama bahan maksimal 255 karakter.',
            'spesifikasi.max' => 'Spesifikasi maksimal 255 karakter.',
            'satuan.required' => 'Satuan wajib dipilih.',
            'satuan.in' => 'Satuan yang dipilih tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                // Kode dibuat ulang di server saat menyimpan.
                $validated['kode_bahan'] = $this->generateKodeBahan();

                // Bahan baru belum memiliki transaksi pembelian.
                $validated['stok'] = 0;
                $validated['harga_rata_rata'] = 0;

                BahanBaku::create($validated);
            });
        } catch (QueryException $e) {
            // Misalnya terjadi konflik kode ketika ada penyimpanan bersamaan.
            report($e);

            return redirect()
                ->route('bahan-baku.index')
                ->withInput($request->except('_token'))
                ->with(
                    'error',
                    'Bahan baku gagal disimpan. Silakan coba kembali.'
                );
        }

        return redirect()
            ->route('bahan-baku.index')
            ->with(
                'success',
                'Data bahan baku berhasil ditambahkan.'
            );
    }

    public function update(Request $request, $id)
    {
        $bahanBaku = BahanBaku::findOrFail($id);

        $validated = $request->validate([
            'kode_bahan' => [
                'required',
                'string',
                'max:50',
                Rule::unique('bahan_baku', 'kode_bahan')
                    ->ignore($bahanBaku->id),
            ],
            'nama_bahan' => [
                'required',
                'string',
                'max:255',
            ],
            'spesifikasi' => [
                'nullable',
                'string',
                'max:255',
            ],
            'satuan' => [
                'required',
                'string',
                'max:50',
                Rule::in([
                    'lembar',
                    'rim',
                    'pcs',
                    'roll',
                    'meter',
                    'm2',
                    'kg',
                    'liter',
                    'botol',
                    'pak',
                ]),
            ],
            'status' => [
                'required',
                Rule::in(['aktif', 'nonaktif']),
            ],
        ], [
            'kode_bahan.required' => 'Kode bahan wajib diisi.',
            'kode_bahan.unique' => 'Kode bahan sudah digunakan.',
            'nama_bahan.required' => 'Nama bahan wajib diisi.',
            'satuan.required' => 'Satuan wajib dipilih.',
            'satuan.in' => 'Satuan yang dipilih tidak valid.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ]);

        // Stok dan harga rata-rata tidak diubah melalui form master.
        $bahanBaku->update($validated);

        return redirect()
            ->route('bahan-baku.index')
            ->with(
                'success',
                'Data bahan baku berhasil diperbarui.'
            );
    }

    public function destroy($id)
    {
        $bahanBaku = BahanBaku::findOrFail($id);

        try {
            $bahanBaku->delete();
        } catch (QueryException $e) {
            return redirect()
                ->route('bahan-baku.index')
                ->with(
                    'error',
                    'Bahan baku tidak dapat dihapus karena masih digunakan pada transaksi terkait.'
                );
        }

        return redirect()
            ->route('bahan-baku.index')
            ->with(
                'success',
                'Data bahan baku berhasil dihapus.'
            );
    }
}