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
    /**
     * Memastikan hanya Admin Pusat dan Admin Cabang
     * yang dapat mengakses Jurnal Umum.
     */
    private function authorizeJurnal(): void
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['Admin Pusat', 'Admin Cabang'], true)) {
            throw new AuthorizationException(
                'Kamu tidak memiliki akses ke menu Jurnal Umum.'
            );
        }

        if ($user->role === 'Admin Cabang' && !$user->cabang_id) {
            throw new AuthorizationException(
                'Akun Admin Cabang belum terhubung dengan cabang.'
            );
        }
    }

    /**
     * Daftar jurnal.
     * Admin Pusat melihat semua cabang.
     * Admin Cabang hanya melihat jurnal cabangnya sendiri.
     */
    
    public function index(Request $request)
    {
        $user = Auth::user();

        // Hanya Admin Pusat dan Admin Cabang yang boleh mengakses.
        abort_unless(
            $user && in_array(
                $user->role,
                ['Admin Pusat', 'Admin Cabang'],
                true
            ),
            403
        );

        if ($user->role === 'Admin Cabang' && !$user->cabang_id) {
            abort(403, 'Akun belum terhubung dengan cabang.');
        }

        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'cabang_id' => [
                'nullable',
                'integer',
                'exists:cabangs,id',
            ],
        ]);

        // Default periode bulan berjalan.
        $startDate = $request->input(
            'start_date',
            now()->startOfMonth()->toDateString()
        );

        $endDate = $request->input(
            'end_date',
            now()->endOfMonth()->toDateString()
        );

        $query = Jurnal::with([
            'user',
            'cabang',
            'detailJurnals.akun',
            'transaksi.detailTransaksis.produk',
        ])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        // Admin Cabang hanya melihat jurnal cabangnya sendiri.
        if ($user->role === 'Admin Cabang') {
            $query->where('cabang_id', $user->cabang_id);
        }

        // Admin Pusat dapat memilih satu cabang atau semua cabang.
        if (
            $user->role === 'Admin Pusat'
            && $request->filled('cabang_id')
        ) {
            $query->where('cabang_id', $request->input('cabang_id'));
        }

        $jurnals = $query
            ->latest('tanggal')
            ->latest('id')
            ->get();

        // Data dropdown hanya dikirim untuk Admin Pusat.
        $cabangs = $user->role === 'Admin Pusat'
            ? Cabang::where('status', 'aktif')
                ->orderBy('nama')
                ->get()
            : collect();

        return view('admin.jurnal.index', compact(
            'jurnals',
            'cabangs',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Form tambah jurnal.
     */
    public function create()
    {
        $this->authorizeJurnal();

        $user = Auth::user();

        $akuns = Akun::where('status', 'aktif')
            ->orderBy('kode_akun')
            ->get();

        $cabangQuery = Cabang::where('status', 'aktif');

        if ($user->role === 'Admin Cabang') {
            $cabangQuery->where('id', $user->cabang_id);
        }

        $cabangs = $cabangQuery->get();

        $today = now()->format('Ymd');

        $lastJurnal = Jurnal::whereDate('created_at', now()->toDateString())
            ->count();

        $no_referensi = 'JRN-' . $today . '-' .
            str_pad($lastJurnal + 1, 4, '0', STR_PAD_LEFT);

        return view(
            'admin.jurnal.create',
            compact('akuns', 'cabangs', 'no_referensi')
        );
    }

    /**
     * Simpan jurnal beserta detail debit dan kredit.
     */
    public function store(Request $request)
    {
        $this->authorizeJurnal();

        $user = Auth::user();

        $rules = [
            'no_referensi' => [
                'required',
                'string',
                'max:100',
                'unique:jurnals,no_referensi',
            ],
            'tanggal' => ['required', 'date'],
            'keterangan' => ['required', 'string', 'max:1000'],
            'akun_id' => ['required', 'array', 'min:2'],
            'akun_id.*' => ['required', 'integer', 'distinct', 'exists:akuns,id'],
            'debit' => ['required', 'array', 'min:2'],
            'debit.*' => ['required', 'numeric', 'min:0'],
            'kredit' => ['required', 'array', 'min:2'],
            'kredit.*' => ['required', 'numeric', 'min:0'],
        ];

        if ($user->role === 'Admin Pusat') {
            $rules['cabang_id'] = [
                'required',
                'integer',
                'exists:cabangs,id',
            ];
        }

        $data = $request->validate($rules);

        // Pastikan jumlah baris akun, debit, dan kredit sesuai.
        if (
            count($data['akun_id']) !== count($data['debit']) ||
            count($data['akun_id']) !== count($data['kredit'])
        ) {
            return back()
                ->with('error', 'Jumlah baris akun, debit, dan kredit tidak sesuai.')
                ->withInput();
        }

        // Admin Cabang selalu menggunakan cabangnya sendiri.
        if ($user->role === 'Admin Cabang') {
            $data['cabang_id'] = $user->cabang_id;
        }

        // Pastikan cabang yang dipilih aktif.
        $cabangAktif = Cabang::where('id', $data['cabang_id'])
            ->where('status', 'aktif')
            ->exists();

        if (!$cabangAktif) {
            return back()
                ->with('error', 'Cabang tidak ditemukan atau tidak aktif.')
                ->withInput();
        }

        // Pastikan setiap akun yang dipilih aktif.
        $jumlahAkunAktif = Akun::whereIn('id', $data['akun_id'])
            ->where('status', 'aktif')
            ->count();

        if ($jumlahAkunAktif !== count(array_unique($data['akun_id']))) {
            return back()
                ->with('error', 'Terdapat akun yang tidak aktif atau tidak valid.')
                ->withInput();
        }

        // Validasi debit dan kredit.
        $totalDebit = 0;
        $totalKredit = 0;

        foreach ($data['akun_id'] as $key => $akunId) {
            $debit = (float) $data['debit'][$key];
            $kredit = (float) $data['kredit'][$key];

            // Satu baris tidak boleh memiliki debit dan kredit sekaligus.
            if ($debit > 0 && $kredit > 0) {
                return back()
                    ->with('error', 'Satu baris jurnal tidak boleh memiliki debit dan kredit sekaligus.')
                    ->withInput();
            }

            $totalDebit += $debit;
            $totalKredit += $kredit;
        }

        if (round($totalDebit, 2) !== round($totalKredit, 2)) {
            return back()
                ->with(
                    'error',
                    'Jurnal gagal disimpan! Total Debit (Rp ' .
                    number_format($totalDebit, 0, ',', '.') .
                    ') dan Kredit (Rp ' .
                    number_format($totalKredit, 0, ',', '.') .
                    ') tidak seimbang.'
                )
                ->withInput();
        }

        if ($totalDebit <= 0) {
            return back()
                ->with('error', 'Total transaksi jurnal tidak boleh 0.')
                ->withInput();
        }

        try {
            DB::transaction(function () use ($data, $user) {
                $jurnal = Jurnal::create([
                    'no_referensi' => $data['no_referensi'],
                    'tanggal' => $data['tanggal'],
                    'keterangan' => $data['keterangan'],
                    'cabang_id' => $data['cabang_id'],
                    'user_id' => $user->id,
                ]);

                foreach ($data['akun_id'] as $key => $akunId) {
                    $debit = (float) $data['debit'][$key];
                    $kredit = (float) $data['kredit'][$key];

                    // Lewati baris yang nilai debit dan kreditnya sama-sama nol.
                    if ($debit == 0 && $kredit == 0) {
                        continue;
                    }

                    DetailJurnal::create([
                        'jurnal_id' => $jurnal->id,
                        'akun_id' => $akunId,
                        'debit' => $debit,
                        'kredit' => $kredit,
                    ]);
                }
            });

            return redirect()
                ->route('jurnal.index')
                ->with('success', 'Jurnal Umum berhasil dicatat!');

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', 'Terjadi kesalahan sistem saat menyimpan jurnal.')
                ->withInput();
        }
    }
}
