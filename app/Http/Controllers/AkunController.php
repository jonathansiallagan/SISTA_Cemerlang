<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AkunController extends Controller
{
    /**
     * Prefix tipe akun.
     */
    private function tipeKode(string $tipe): ?string
    {
        return [
            'Aset'        => '1',
            'Kewajiban'   => '2',
            'Ekuitas'     => '3',
            'Pendapatan'  => '4',
            'Beban/Biaya' => '5',
        ][$tipe] ?? null;
    }

    /**
     * Kode awal kelompok akun.
     */
    private function kelompokKode(string $kelompok): ?int
    {
        return [
            'Aset'              => 1100,
            'Kewajiban'         => 1100,
            'Ekuitas'           => 1100,
            'Pendapatan Usaha'  => 1100,
            'HPP'               => 1100,
            'Beban Operasional' => 2100,
            'Biaya Lain-lain'   => 3100,
        ][$kelompok] ?? null;
    }

    /**
     * Aturan kelompok yang sah untuk setiap tipe akun.
     */
    private function kelompokValid(string $tipe, string $kelompok): bool
    {
        $kelompokData = [
            'Aset' => ['Aset'],
            'Kewajiban' => ['Kewajiban'],
            'Ekuitas' => ['Ekuitas'],
            'Pendapatan' => ['Pendapatan Usaha'],
            'Beban/Biaya' => [
                'HPP',
                'Beban Operasional',
                'Biaya Lain-lain',
            ],
        ];

        return in_array(
            $kelompok,
            $kelompokData[$tipe] ?? [],
            true
        );
    }

    /**
     * Membuat kode akun berikutnya untuk tipe dan kelompok tertentu.
     */
    private function kodeBerikutnya(
        string $tipe,
        string $kelompok,
        ?int $kecualiId = null
    ): ?string {
        $tipeKode = $this->tipeKode($tipe);
        $kelompokKode = $this->kelompokKode($kelompok);

        if (
            !$tipeKode ||
            $kelompokKode === null ||
            !$this->kelompokValid($tipe, $kelompok)
        ) {
            return null;
        }

        $query = Akun::where('tipe_akun', $tipe)
            ->where('kelompok_akun', $kelompok);

        if ($kecualiId !== null) {
            $query->where('id', '!=', $kecualiId);
        }

        $kodeTerakhir = $query
            ->pluck('kode_akun')
            ->map(function ($kode) {
                // Format kode akun: angka tipe + tanda "-" + angka kelompok.
                return (int) substr($kode, 2);
            })
            ->filter()
            ->max();

        $nomor = $kodeTerakhir
            ? $kodeTerakhir + 100
            : $kelompokKode;

        return $tipeKode . '-' . $nomor;
    }

    public function index()
    {
        $akuns = Akun::orderBy('kode_akun', 'asc')->get();

        return view('admin.akun.index', compact('akuns'));
    }

    public function previewKode(Request $request)
    {
        $validated = $request->validate([
            'tipe_akun' => [
                'required',
                Rule::in([
                    'Aset',
                    'Kewajiban',
                    'Ekuitas',
                    'Pendapatan',
                    'Beban/Biaya',
                ]),
            ],
            'kelompok_akun' => ['required', 'string'],
            'id' => ['nullable', 'integer', 'exists:akuns,id'],
        ]);

        $kode = $this->kodeBerikutnya(
            $validated['tipe_akun'],
            $validated['kelompok_akun'],
            isset($validated['id']) ? (int) $validated['id'] : null
        );

        return response()->json(['kode' => $kode]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_akun' => ['required', 'string', 'max:255'],
            'tipe_akun' => [
                'required',
                Rule::in([
                    'Aset',
                    'Kewajiban',
                    'Ekuitas',
                    'Pendapatan',
                    'Beban/Biaya',
                ]),
            ],
            'kelompok_akun' => ['required', 'string'],
        ], [
            'nama_akun.required' => 'Nama akun wajib diisi.',
            'tipe_akun.required' => 'Tipe akun wajib dipilih.',
            'kelompok_akun.required' => 'Kelompok akun wajib dipilih.',
        ]);

        if (!$this->kelompokValid(
            $validated['tipe_akun'],
            $validated['kelompok_akun']
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'kelompok_akun' => 'Kelompok akun tidak sesuai dengan tipe akun yang dipilih.',
                ]);
        }

        DB::transaction(function () use ($validated) {
            // Hitung ulang kode di server sebelum menyimpan.
            $kode = $this->kodeBerikutnya(
                $validated['tipe_akun'],
                $validated['kelompok_akun']
            );

            if (!$kode) {
                throw new \RuntimeException('Kode akun tidak dapat dibuat.');
            }

            Akun::create([
                'kode_akun' => $kode,
                'nama_akun' => $validated['nama_akun'],
                'tipe_akun' => $validated['tipe_akun'],
                'kelompok_akun' => $validated['kelompok_akun'],
                'status' => 'aktif',
            ]);
        });

        return redirect()
            ->route('akun.index')
            ->with('success', 'Akun berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $akun = Akun::findOrFail($id);

        return view('admin.akun.edit', compact('akun'));
    }

    public function update(Request $request, $id)
    {
        $akun = Akun::findOrFail($id);

        $validated = $request->validate([
            'nama_akun' => ['required', 'string', 'max:255'],
            'tipe_akun' => [
                'required',
                Rule::in([
                    'Aset',
                    'Kewajiban',
                    'Ekuitas',
                    'Pendapatan',
                    'Beban/Biaya',
                ]),
            ],
            'kelompok_akun' => ['required', 'string'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ], [
            'nama_akun.required' => 'Nama akun wajib diisi.',
            'tipe_akun.required' => 'Tipe akun wajib dipilih.',
            'kelompok_akun.required' => 'Kelompok akun wajib dipilih.',
            'status.required' => 'Status akun wajib dipilih.',
        ]);

        if (!$this->kelompokValid(
            $validated['tipe_akun'],
            $validated['kelompok_akun']
        )) {
            return back()
                ->withInput()
                ->withErrors([
                    'kelompok_akun' => 'Kelompok akun tidak sesuai dengan tipe akun yang dipilih.',
                ]);
        }

        $data = [
            'nama_akun' => $validated['nama_akun'],
            'tipe_akun' => $validated['tipe_akun'],
            'kelompok_akun' => $validated['kelompok_akun'],
            'status' => $validated['status'],
        ];

        // Pertahankan kode jika tipe dan kelompok tidak berubah.
        if (
            $akun->tipe_akun !== $validated['tipe_akun'] ||
            $akun->kelompok_akun !== $validated['kelompok_akun']
        ) {
            $kode = $this->kodeBerikutnya(
                $validated['tipe_akun'],
                $validated['kelompok_akun'],
                (int) $akun->id
            );

            if (!$kode) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'kelompok_akun' => 'Kode akun tidak dapat dibuat.',
                    ]);
            }

            $data['kode_akun'] = $kode;
        }

        $akun->update($data);

        return redirect()
            ->route('akun.index')
            ->with('success', 'Akun berhasil diperbarui!');
    }
}