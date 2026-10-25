<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use App\Models\DetailJurnal;
use App\Models\Akun;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    public function index()
    {
        $jurnals = Jurnal::with([
            'user',
            'cabang',
            'detailJurnals.akun',
            'transaksi.detailTransaksis.produk',
        ])->latest('tanggal')->latest('id')->get();

        return view('admin.jurnal.index', compact('jurnals'));
    }

    public function create()
    {
        $akuns = Akun::where('status', 'aktif')->orderBy('kode_akun')->get();
        $cabangs = Cabang::where('status', 'aktif')->get();
        $no_referensi = $this->buatNomor('JRN');

        return view('admin.jurnal.create', compact('akuns', 'cabangs', 'no_referensi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_referensi' => 'required|unique:jurnals,no_referensi',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'cabang_id' => 'required|exists:cabangs,id',
            'akun_id' => 'required|array|min:2',
            'akun_id.*' => 'required|exists:akuns,id',
            'debit' => 'required|array',
            'debit.*' => 'nullable|numeric|min:0',
            'kredit' => 'required|array',
            'kredit.*' => 'nullable|numeric|min:0',
        ], [
            'akun_id.min' => 'Jurnal minimal terdiri dari 2 baris akun.',
            'akun_id.*.required' => 'Ada baris yang akunnya belum dipilih.',
            'akun_id.*.exists' => 'Akun yang dipilih tidak ditemukan.',
            'debit.*.numeric' => 'Nilai debit harus berupa angka.',
            'debit.*.min' => 'Nilai debit tidak boleh negatif.',
            'kredit.*.numeric' => 'Nilai kredit harus berupa angka.',
            'kredit.*.min' => 'Nilai kredit tidak boleh negatif.',
        ]);

        $baris = [];
        foreach ($request->akun_id as $key => $akunId) {
            $debit = round((float) ($request->debit[$key] ?? 0), 2);
            $kredit = round((float) ($request->kredit[$key] ?? 0), 2);

            if ($debit <= 0 && $kredit <= 0) {
                continue;
            }

            if ($debit > 0 && $kredit > 0) {
                return back()
                    ->with('error', 'Satu baris akun tidak boleh diisi debit dan kredit sekaligus.')
                    ->withInput();
            }

            $baris[] = ['akun_id' => $akunId, 'debit' => $debit, 'kredit' => $kredit];
        }

        if (count($baris) < 2) {
            return back()
                ->with('error', 'Jurnal harus memiliki minimal 2 baris akun yang terisi nilainya.')
                ->withInput();
        }

        $totalDebit = round(array_sum(array_column($baris, 'debit')), 2);
        $totalKredit = round(array_sum(array_column($baris, 'kredit')), 2);

        if (abs($totalDebit - $totalKredit) > 0.001) {
            return back()
                ->with('error', 'Jurnal gagal disimpan! Total Debit (Rp ' . number_format($totalDebit, 0, ',', '.') . ') dan Kredit (Rp ' . number_format($totalKredit, 0, ',', '.') . ') tidak seimbang.')
                ->withInput();
        }

        if ($totalDebit <= 0) {
            return back()->with('error', 'Total transaksi jurnal tidak boleh 0.')->withInput();
        }

        DB::beginTransaction();
        try {
            $jurnal = Jurnal::create([
                'no_referensi' => $request->no_referensi,
                'tanggal' => $request->tanggal,
                'keterangan' => $request->keterangan,
                'cabang_id' => $request->cabang_id,
                'user_id' => Auth::id(),
            ]);

            foreach ($baris as $item) {
                DetailJurnal::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $item['akun_id'],
                    'debit' => $item['debit'],
                    'kredit' => $item['kredit'],
                ]);
            }

            DB::commit();
            return redirect()->route('jurnal.index')->with('success', 'Jurnal Umum berhasil dicatat!');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            $pesan = config('app.debug')
                ? 'Terjadi kesalahan sistem: ' . $e->getMessage()
                : 'Terjadi kesalahan sistem saat menyimpan jurnal.';

            return back()->with('error', $pesan)->withInput();
        }
    }

    // Contoh hasil: JRN-20261025-0001 (hanya menghitung nomor berawalan JRN)
    private function buatNomor(string $prefix): string
    {
        $awal = $prefix . '-' . now()->format('Ymd') . '-';

        $terakhir = Jurnal::where('no_referensi', 'like', $awal . '%')
            ->orderByDesc('no_referensi')
            ->value('no_referensi');

        $urut = $terakhir ? ((int) substr($terakhir, -4)) + 1 : 1;

        return $awal . str_pad($urut, 4, '0', STR_PAD_LEFT);
    }
}