<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\DetailJurnal;
use App\Models\Jurnal;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PengeluaranController extends Controller
{
    private function kodePrefix(Akun $akun): string
    {
        return match ($akun->kode_akun) {
            '5-2100' => 'PAY',
            '5-2200' => 'UTIL',
            '5-2300' => 'TRN',
            '5-2400' => 'PRO',
            '5-2500' => 'ADM',
            '5-2600' => 'MNT',
            default => throw ValidationException::withMessages([
                'akun_id' => 'Akun beban yang dipilih tidak valid.',
            ]),
        };
    }

    public function index()
    {
        $user = Auth::user();

        $query = Pengeluaran::with(['cabang', 'user', 'akun'])
            ->latest('tanggal')
            ->latest('id');

        if ($user->role === 'Admin Cabang') {
            $query->where('cabang_id', $user->cabang_id);
        }

        $pengeluarans = $query->get();

        return view('admin.pengeluaran.index', compact('pengeluarans'));
    }

    public function create()
    {
        $user = Auth::user();

        $cabangs = Cabang::where('status', 'aktif')
            ->when(
                $user->role === 'Admin Cabang',
                fn ($query) => $query->where('id', $user->cabang_id)
            )
            ->get();

        $akuns = Akun::where('status', 'aktif')
            ->where('tipe_akun', 'Beban/Biaya')
            ->where('kelompok_akun', 'Beban Operasional')
            ->whereIn('kode_akun', [
                '5-2100',
                '5-2200',
                '5-2300',
                '5-2400',
                '5-2500',
                '5-2600',
            ])
            ->orderBy('kode_akun')
            ->get();

        return view(
            'admin.pengeluaran.create',
            compact('cabangs', 'akuns')
        );
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'tanggal' => ['required', 'date'],
            'cabang_id' => ['required', 'exists:cabangs,id'],
            'akun_id' => ['required', 'exists:akuns,id'],
            'keterangan' => ['required', 'string', 'max:1000'],
            'qty' => ['required', 'numeric', 'gt:0', 'max:9999999999'],
            'satuan' => ['required', 'in:Orang,Unit,Pcs'],
            'harga' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
        ]);

        // Admin Cabang hanya boleh mencatat pengeluaran cabangnya sendiri.
        if ($user->role === 'Admin Cabang') {
            $data['cabang_id'] = $user->cabang_id;
        }

        $akun = Akun::where('status', 'aktif')
            ->where('tipe_akun', 'Beban/Biaya')
            ->where('kelompok_akun', 'Beban Operasional')
            ->whereIn('kode_akun', [
                '5-2100',
                '5-2200',
                '5-2300',
                '5-2400',
                '5-2500',
                '5-2600',
            ])
            ->findOrFail($data['akun_id']);

        // Akun Kas berdasarkan kode akun, bukan ID yang diasumsikan.
        $akunKas = Akun::where('kode_akun', '1-1100')
            ->where('status', 'aktif')
            ->first();

        if (!$akunKas) {
            throw ValidationException::withMessages([
                'akun_id' => 'Akun Kas berkode 1-1100 belum tersedia atau tidak aktif.',
            ]);
        }

        $prefix = $this->kodePrefix($akun);
        $tanggalReferensi = str_replace('-', '', $data['tanggal']);

        // Total dihitung ulang di server agar tidak bisa dimanipulasi.
        $qty = (float) $data['qty'];
        $harga = (float) $data['harga'];
        $total = round($qty * $harga, 2);

        if (!is_finite($total) || $total <= 0 || $total > 9999999999999.99) {
            throw ValidationException::withMessages([
                'harga' => 'Total pengeluaran tidak valid atau melebihi batas.',
            ]);
        }

        DB::transaction(function () use (
            $data,
            $user,
            $akun,
            $akunKas,
            $prefix,
            $tanggalReferensi,
            $qty,
            $harga,
            $total
        ) {
            // Referensi mengikuti jenis beban dan tanggal pengeluaran.
            $referensiTerakhir = Jurnal::where(
                'no_referensi',
                'like',
                $prefix . '-' . $tanggalReferensi . '-%'
            )
                ->get()
                ->map(fn ($item) => (int) substr($item->no_referensi, -4))
                ->max() ?? 0;

            $noReferensi = sprintf(
                '%s-%s-%04d',
                $prefix,
                $tanggalReferensi,
                $referensiTerakhir + 1
            );

            // Simpan data pengeluaran.
            Pengeluaran::create([
                'no_referensi' => $noReferensi,
                'tanggal' => $data['tanggal'],
                'cabang_id' => $data['cabang_id'],
                'akun_id' => $akun->id,
                'keterangan' => $data['keterangan'],
                'qty' => $qty,
                'satuan' => $data['satuan'],
                'harga' => $harga,
                'total_pengeluaran' => $total,
                'user_id' => $user->id,
            ]);

            // Buat jurnal otomatis dengan nomor referensi yang sama.
            $jurnal = Jurnal::create([
                'no_referensi' => $noReferensi,
                'tanggal' => $data['tanggal'],
                'keterangan' => $data['keterangan'],
                'cabang_id' => $data['cabang_id'],
                'user_id' => $user->id,
            ]);

            // Debit: akun beban yang dipilih.
            DetailJurnal::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akun->id,
                'debit' => $total,
                'kredit' => 0,
            ]);

            // Kredit: Kas.
            DetailJurnal::create([
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akunKas->id,
                'debit' => 0,
                'kredit' => $total,
            ]);
        });

        return redirect()
            ->route('pengeluaran.index')
            ->with(
                'success',
                'Pengeluaran berhasil disimpan dan jurnal otomatis berhasil dibuat.'
            );
    }
}
