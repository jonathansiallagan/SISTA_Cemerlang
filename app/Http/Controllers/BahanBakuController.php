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

    /**
     * Menampilkan daftar bahan baku.
     */
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

    /**
     * Menyimpan bahan baku baru.
     */
    public function store(Request $request)
    {
        // Membersihkan spasi di awal dan akhir input.
        $request->merge([
            'nama_bahan' => trim($request->nama_bahan ?? ''),
            'spesifikasi' => $request->filled('spesifikasi')
                ? trim($request->spesifikasi)
                : null,
        ]);

        $validated = $request->validate([
            'nama_bahan' => [
                'required',
                'string',
                'max:150',
                Rule::unique('bahan_baku', 'nama_bahan'),
            ],

            'spesifikasi' => [
                'nullable',
                'string',
                'max:150',
            ],

            'satuan' => [
                'required',
                'string',
                'max:30',
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
            'nama_bahan.max' => 'Nama bahan maksimal 150 karakter.',
            'nama_bahan.unique' => 'Nama bahan baku sudah terdaftar. Silakan gunakan nama lain.',

            'spesifikasi.max' => 'Spesifikasi maksimal 150 karakter.',

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
            report($e);

            // Menangani konflik UNIQUE pada database.
            if ($e->getCode() === '23000') {
                return redirect()
                    ->route('bahan-baku.index')
                    ->withInput($request->except('_token'))
                    ->withErrors([
                        'nama_bahan' => 'Nama atau kode bahan baku sudah terdaftar. Periksa kembali data yang dimasukkan.',
                    ]);
            }

            return redirect()
                ->route('bahan-baku.index')
                ->withInput($request->except('_token'))
                ->with(
                    'error',
                    'Terjadi kesalahan database. Data bahan baku gagal disimpan.'
                );
        }

        return redirect()
            ->route('bahan-baku.index')
            ->with(
                'success',
                'Data bahan baku berhasil ditambahkan.'
            );
    }

    /**
     * Memperbarui data bahan baku.
     */
    public function update(Request $request, $id)
    {
        $bahanBaku = BahanBaku::findOrFail($id);

        // Membersihkan spasi di awal dan akhir input.
        $request->merge([
            'nama_bahan' => trim($request->nama_bahan ?? ''),
            'spesifikasi' => $request->filled('spesifikasi')
                ? trim($request->spesifikasi)
                : null,
        ]);

        $validated = $request->validate([
            'kode_bahan' => [
                'required',
                'string',
                'max:30',
                Rule::unique('bahan_baku', 'kode_bahan')
                    ->ignore($bahanBaku->id),
            ],

            'nama_bahan' => [
                'required',
                'string',
                'max:150',
                Rule::unique('bahan_baku', 'nama_bahan')
                    ->ignore($bahanBaku->id),
            ],

            'spesifikasi' => [
                'nullable',
                'string',
                'max:150',
            ],

            'satuan' => [
                'required',
                'string',
                'max:30',
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
            'nama_bahan.max' => 'Nama bahan maksimal 150 karakter.',
            'nama_bahan.unique' => 'Nama bahan baku sudah digunakan. Silakan gunakan nama lain.',

            'spesifikasi.max' => 'Spesifikasi maksimal 150 karakter.',

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

    /**
     * Menghapus bahan baku.
     */
    public function destroy($id)
    {
        $bahanBaku = BahanBaku::findOrFail($id);

        try {
            $bahanBaku->delete();

        } catch (QueryException $e) {
            report($e);

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