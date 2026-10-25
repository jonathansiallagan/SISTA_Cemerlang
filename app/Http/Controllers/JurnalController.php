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
])->latest()->get();
        return view('admin.jurnal.index', compact('jurnals'));
    }

    public function create()
    {
        $akuns = Akun::where('status', 'aktif')->orderBy('kode_akun')->get();
        $cabangs = Cabang::where('status', 'aktif')->get();
        
        $today = now()->format('Ymd');
        $lastJurnal = Jurnal::whereDate('created_at', now()->today())->count();
        $no_referensi = 'JRN-' . $today . '-' . str_pad($lastJurnal + 1, 4, '0', STR_PAD_LEFT);

        return view('admin.jurnal.create', compact('akuns', 'cabangs', 'no_referensi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_referensi' => 'required|unique:jurnals,no_referensi',
            'tanggal' => 'required|date',
            'keterangan' => 'required|string',
            'cabang_id' => 'required|exists:cabangs,id',
            'akun_id' => 'required|array|min:2', // Minimal ada 2 baris (Debit & Kredit)
            'debit' => 'required|array',
            'kredit' => 'required|array',
        ]);

        // Validasi Balance
        $totalDebit = array_sum($request->debit);
        $totalKredit = array_sum($request->kredit);

        if ($totalDebit != $totalKredit) {
            return back()->with('error', 'Transaksi gagal disimpan! Total Debit (Rp '.number_format($totalDebit,0,',','.').') dan Kredit (Rp '.number_format($totalKredit,0,',','.').') tidak seimbang.')->withInput();
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

            foreach ($request->akun_id as $key => $akunId) {
                // Hanya simpan baris yang memiliki nilai debit atau kredit
                if ($request->debit[$key] > 0 || $request->kredit[$key] > 0) {
                    DetailJurnal::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id' => $akunId,
                        'debit' => $request->debit[$key] ?: 0,
                        'kredit' => $request->kredit[$key] ?: 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('jurnal.index')->with('success', 'Jurnal Umum berhasil dicatat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }
}