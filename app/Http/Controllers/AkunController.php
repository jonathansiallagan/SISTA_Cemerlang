<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    private function tipeKode($tipe)
    {
        return [
            'Aset'        => '1',
            'Kewajiban'   => '2',
            'Ekuitas'     => '3',
            'Pendapatan'  => '4',
            'Beban/Biaya' => '5',
        ][$tipe] ?? null;
    }

    private function kelompokKode($kelompok)
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

    public function index()
    {
        $akuns = Akun::orderBy('kode_akun', 'asc')->get();

        return view('admin.akun.index', compact('akuns'));
    }

    public function create()
    {
        return view('admin.akun.create');
    }

    public function previewKode(Request $request)
    {
        $validated = $request->validate([
            'tipe_akun' => 'required|string',
            'kelompok_akun' => 'required|string',
        ]);

        $tipeKode = $this->tipeKode($validated['tipe_akun']);

        $kelompokKode = $this->kelompokKode($validated['kelompok_akun']);

        if (!$tipeKode || !$kelompokKode) {
            return response()->json([
                'kode' => null
            ]);
        }

        $query = Akun::where(
            'tipe_akun',
            $validated['tipe_akun']
        )
        ->where(
            'kelompok_akun',
            $validated['kelompok_akun']
        );

        // Kalau sedang edit, jangan hitung akun yang sedang diedit
        if ($request->filled('id')) {
            $query->where('id', '!=', $request->id);
        }

        $akunTerakhir = $query
            ->orderBy('kode_akun', 'desc')
            ->first();

        if (!$akunTerakhir) {

            $nomorKode = $kelompokKode;

        } else {

            $nomorTerakhir = (int) substr(
                $akunTerakhir->kode_akun,
                2
            );

            $nomorKode = $nomorTerakhir + 100;
        }

        $kodeAkun = $tipeKode . '-' . $nomorKode;

        return response()->json([
            'kode' => $kodeAkun
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_akun'     => 'required|string|max:255',
            'tipe_akun'     => 'required|string',
            'kelompok_akun' => 'required|string',
        ]);

        $tipeKode = $this->tipeKode(
            $validated['tipe_akun']
        );

        $kelompokKode = $this->kelompokKode(
            $validated['kelompok_akun']
        );

        if (!$tipeKode || !$kelompokKode) {
            return back()
                ->withInput()
                ->withErrors([
                    'kelompok_akun' => 'Tipe atau kelompok akun tidak valid.'
                ]);
        }

        $akunDalamKelompok = Akun::where(
            'tipe_akun',
            $validated['tipe_akun']
        )
        ->where(
            'kelompok_akun',
            $validated['kelompok_akun']
        )
        ->get();

        if ($akunDalamKelompok->isEmpty()) {

            // Akun pertama
            $nomorKode = $kelompokKode;

        } else {

            $nomorTerakhir = $akunDalamKelompok->max(function ($akun) {

                return (int) substr(
                    $akun->kode_akun,
                    2
                );
            });

            $nomorKode = $nomorTerakhir + 100;
        }

        $kodeAkun =
            $tipeKode . '-' . $nomorKode;

        Akun::create([
            'kode_akun'     => $kodeAkun,
            'nama_akun'     => $validated['nama_akun'],
            'tipe_akun'     => $validated['tipe_akun'],
            'kelompok_akun' => $validated['kelompok_akun'],
            'status'        => 'aktif',
        ]);


        return redirect()
            ->route('akun.index')
            ->with(
                'success',
                'Akun berhasil ditambahkan!'
            );
    }

    public function edit($id)
    {
        $akun = Akun::findOrFail($id);

        return view(
            'admin.akun.edit',
            compact('akun')
        );
    }

    public function update(Request $request, $id)
    {
        $akun = Akun::findOrFail($id);


        $validated = $request->validate([
            'nama_akun'     => 'required|string|max:255',
            'tipe_akun'     => 'required|string',
            'kelompok_akun' => 'required|string',
            'status'        => 'required|in:aktif,nonaktif',
        ]);

        if (
            $akun->tipe_akun !== $validated['tipe_akun'] ||
            $akun->kelompok_akun !== $validated['kelompok_akun']
        ) {

            $tipeKode = $this->tipeKode(
                $validated['tipe_akun']
            );

            $kelompokKode = $this->kelompokKode(
                $validated['kelompok_akun']
            );


            if (!$tipeKode || !$kelompokKode) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'kelompok_akun' =>
                            'Tipe atau kelompok akun tidak valid.'
                    ]);
            }


            $prefix =
                $tipeKode . '-' . $kelompokKode;


            $akunTerakhir = Akun::where(
                'kode_akun',
                'like',
                $prefix . '%'
            )
            ->where('id', '!=', $akun->id)
            ->orderBy('kode_akun', 'desc')
            ->first();


            if ($akunTerakhir) {

                $nomorTerakhir = (int) substr(
                    $akunTerakhir->kode_akun,
                    -4
                );

                $nomorBaru = $nomorTerakhir + 100;

            } else {

                $nomorBaru = 100;
            }


            $validated['kode_akun'] =
                $prefix .
                str_pad(
                    $nomorBaru,
                    2,
                    '0',
                    STR_PAD_LEFT
                );

        } else {

            $validated['kode_akun'] =
                $akun->kode_akun;
        }


        $akun->update($validated);


        return redirect()
            ->route('akun.index')
            ->with(
                'success',
                'Akun berhasil diperbarui!'
            );
    }

    public function destroy($id)
    {
        $akun = Akun::findOrFail($id);

        $akun->delete();

        return redirect()
            ->route('akun.index')
            ->with(
                'success',
                'Akun berhasil dihapus!'
            );
    }
}