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
    /**
     * Memastikan hanya Admin Pusat dan Admin Cabang
     * yang dapat mengakses fitur pengeluaran.
     */
    private function authorizePengeluaran(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && in_array(
                $user->role,
                ['Admin Pusat', 'Admin Cabang'],
                true
            ),
            403,
            'Anda tidak memiliki akses ke fitur pengeluaran.'
        );

        if ($user->role === 'Admin Cabang' && !$user->cabang_id) {
            abort(403, 'Akun Anda belum terhubung ke cabang.');
        }
    }

    /**
     * Menentukan cabang yang boleh digunakan.
     */
    private function cabangAktifQuery()
    {
        $user = Auth::user();

        $query = Cabang::where('status', 'aktif');

        if ($user->role === 'Admin Cabang') {
            $query->where('id', $user->cabang_id);
        }

        return $query;
    }

    /**
     * Menentukan prefix referensi berdasarkan akun beban.
     */
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

    /**
     * Daftar pengeluaran.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'cabang_id' => ['nullable', 'integer', 'exists:cabangs,id'],
        ]);

        // Default periode: bulan berjalan.
        $startDate = $request->input(
            'start_date',
            now()->startOfMonth()->toDateString()
        );

        $endDate = $request->input(
            'end_date',
            now()->endOfMonth()->toDateString()
        );

        $query = Pengeluaran::with(['cabang', 'user', 'akun'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->latest('tanggal')
            ->latest('id');

        // Admin Cabang hanya bisa melihat pengeluaran cabangnya sendiri.
        if ($user->role === 'Admin Cabang') {
            $query->where('cabang_id', $user->cabang_id);
        }

        // Hanya Admin Pusat yang dapat memfilter cabang.
        if (
            $user->role === 'Admin Pusat'
            && $request->filled('cabang_id')
        ) {
            $query->where('cabang_id', $request->input('cabang_id'));
        }

        $pengeluarans = $query->get();

        // Dropdown cabang hanya dibutuhkan Admin Pusat.
        $cabangs = $user->role === 'Admin Pusat'
            ? Cabang::where('status', 'aktif')->orderBy('nama')->get()
            : collect();

        return view('admin.pengeluaran.index', compact(
            'pengeluarans',
            'cabangs',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Form tambah pengeluaran.
     */
    public function create()
    {
        $this->authorizePengeluaran();

        $user = Auth::user();

        // Admin Pusat dapat memilih semua cabang aktif.
        // Admin Cabang hanya mendapatkan cabangnya sendiri.
        $cabangs = $this->cabangAktifQuery()
            ->orderBy('nama')
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

    /**
     * Menyimpan pengeluaran beserta jurnal otomatis.
     */
    public function store(Request $request)
    {
        $this->authorizePengeluaran();

        $user = Auth::user();

        $rules = [
            'tanggal' => ['required', 'date'],
            'akun_id' => ['required', 'integer', 'exists:akuns,id'],
            'keterangan' => ['required', 'string', 'max:1000'],
            'qty' => [
                'required',
                'numeric',
                'gt:0',
                'max:9999999999',
            ],
            'satuan' => ['required', 'in:Orang,Unit,Pcs'],
            'harga' => [
                'required',
                'numeric',
                'gt:0',
                'max:9999999999999.99',
            ],
        ];

        // Cabang wajib dipilih oleh Admin Pusat.
        if ($user->role === 'Admin Pusat') {
            $rules['cabang_id'] = [
                'required',
                'exists:cabangs,id',
            ];
        }

        $data = $request->validate($rules);

        // Tentukan cabang berdasarkan role.
        if ($user->role === 'Admin Cabang') {
            $data['cabang_id'] = $user->cabang_id;
        }

        // Pastikan cabang yang dipilih Admin Pusat aktif.
        $cabang = Cabang::where('status', 'aktif')
            ->find($data['cabang_id']);

        if (!$cabang) {
            throw ValidationException::withMessages([
                'cabang_id' => 'Cabang tidak ditemukan atau tidak aktif.',
            ]);
        }

        // Validasi akun beban yang diperbolehkan.
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

        // Cari akun Kas berdasarkan kode akun.
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

        // Hitung total di server.
        $qty = (float) $data['qty'];
        $harga = (float) $data['harga'];
        $total = round($qty * $harga, 2);

        if (
            !is_finite($total)
            || $total <= 0
            || $total > 9999999999999.99
        ) {
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
            // Buat nomor referensi berdasarkan jenis beban dan tanggal.
            $referensiTerakhir = Jurnal::where(
                'no_referensi',
                'like',
                $prefix . '-' . $tanggalReferensi . '-%'
            )
                ->lockForUpdate()
                ->get()
                ->map(
                    fn ($item) => (int) substr(
                        $item->no_referensi,
                        -4
                    )
                )
                ->max() ?? 0;

            $noReferensi = sprintf(
                '%s-%s-%04d',
                $prefix,
                $tanggalReferensi,
                $referensiTerakhir + 1
            );

            // Simpan pengeluaran.
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

            // Buat jurnal otomatis untuk cabang yang sama.
            $jurnal = Jurnal::create([
                'no_referensi' => $noReferensi,
                'tanggal' => $data['tanggal'],
                'keterangan' => $data['keterangan'],
                'cabang_id' => $data['cabang_id'],
                'user_id' => $user->id,
            ]);

            // Informasi detail pengeluaran pada kedua baris jurnal.
            $detailTransaksi = [
                'quantity' => $qty,
                'satuan' => $data['satuan'],
                'harga' => $harga,
                'total_harga' => $total,
            ];

            // Debit: akun beban yang dipilih.
            DetailJurnal::create(array_merge(
                $detailTransaksi,
                [
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $akun->id,
                    'debit' => $total,
                    'kredit' => 0,
                ]
            ));

            // Kredit: Kas.
            DetailJurnal::create(array_merge(
                $detailTransaksi,
                [
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $akunKas->id,
                    'debit' => 0,
                    'kredit' => $total,
                ]
            ));
        });

        return redirect()
            ->route('pengeluaran.index')
            ->with(
                'success',
                'Pengeluaran berhasil disimpan dan jurnal otomatis berhasil dibuat.'
            );
    }
}