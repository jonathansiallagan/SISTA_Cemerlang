<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BahanBakuController extends Controller
{
    /**
     * Mengambil ID cabang pengguna yang sedang login.
     */
    private function cabangId(): int
    {
        $cabangId = Auth::user()?->cabang_id;

        abort_if(
            !$cabangId,
            403,
            'Akun pengguna belum terhubung dengan cabang.'
        );

        return (int) $cabangId;
    }

    /**
     * Query bahan baku spesifik untuk cabang ini.
     */
    private function queryBahanBaku()
    {
        return BahanBaku::where('cabang_id', $this->cabangId());
    }

    /**
     * Menghasilkan kode bahan berikutnya, misalnya BB001, BB002 (Unik per cabang).
     */
    private function generateKodeBahan(): string
    {
        $nomorTerakhir = $this->queryBahanBaku()
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
        // Hanya panggil bahan baku milik cabang ini
        $bahanBakus = $this->queryBahanBaku()->orderBy('nama_bahan')->get();

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
        $cabangId = $this->cabangId();

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
                // Unik berdasarkan cabang
                Rule::unique('bahan_baku', 'nama_bahan')
                    ->where('cabang_id', $cabangId),
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
        ], [
            'nama_bahan.required' => 'Nama bahan wajib diisi.',
            'nama_bahan.max' => 'Nama bahan maksimal 150 karakter.',
            'nama_bahan.unique' => 'Nama bahan baku sudah terdaftar di cabang ini. Silakan gunakan nama lain.',
            'spesifikasi.max' => 'Spesifikasi maksimal 150 karakter.',
            'satuan.required' => 'Satuan wajib dipilih.',
            'satuan.in' => 'Satuan yang dipilih tidak valid.',
        ]);

        try {
            DB::transaction(function () use ($validated, $cabangId) {
                // Set cabang id dan kode
                $validated['cabang_id'] = $cabangId;
                $validated['kode_bahan'] = $this->generateKodeBahan();

                // Bahan baru belum memiliki transaksi pembelian.
                $validated['stok'] = 0;
                $validated['harga_rata_rata'] = 0;

                BahanBaku::create($validated);
            });
        } catch (QueryException $e) {
            report($e);

            if ($e->getCode() === '23000') {
                return redirect()
                    ->route('bahan-baku.index')
                    ->withInput($request->except('_token'))
                    ->withErrors([
                        'nama_bahan' => 'Nama atau kode bahan baku sudah terdaftar di cabang ini.',
                    ]);
            }

            return redirect()
                ->route('bahan-baku.index')
                ->withInput($request->except('_token'))
                ->with('error', 'Terjadi kesalahan database. Data gagal disimpan.');
        }

        return redirect()
            ->route('bahan-baku.index')
            ->with('success', 'Data bahan baku berhasil ditambahkan.');
    }

    /**
     * Memperbarui data bahan baku.
     */
    public function update(Request $request, $id)
    {
        $cabangId = $this->cabangId();

        // Memastikan bahan yang diupdate benar-benar milik cabang ini
        $bahanBaku = $this->queryBahanBaku()->findOrFail($id);

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
                    ->where('cabang_id', $cabangId)
                    ->ignore($bahanBaku->id),
            ],

            'nama_bahan' => [
                'required',
                'string',
                'max:150',
                Rule::unique('bahan_baku', 'nama_bahan')
                    ->where('cabang_id', $cabangId)
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
        ], [
            // ... (Pesan validasi sama seperti sebelumnya) ...
        ]);

        $bahanBaku->update($validated);

        return redirect()
            ->route('bahan-baku.index')
            ->with('success', 'Data bahan baku berhasil diperbarui.');
    }

    /**
     * Menghapus bahan baku.
     */
    public function destroy($id)
    {
        // Memastikan bahan yang dihapus benar-benar milik cabang ini
        $bahanBaku = $this->queryBahanBaku()->findOrFail($id);

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
            ->with('success', 'Data bahan baku berhasil dihapus.');
    }
}
