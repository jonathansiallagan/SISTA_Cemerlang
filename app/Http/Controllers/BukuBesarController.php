<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\DetailJurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BukuBesarExport;
use Maatwebsite\Excel\Facades\Excel;

class BukuBesarController extends Controller
{
    /**
     * Batasi akses Buku Besar.
     */
    private function batasiAkses(): void
    {
        $user = Auth::user();

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
    }

    /**
     * Halaman utama Buku Besar.
     */
    public function index(Request $request)
    {
        $this->batasiAkses();

        return view(
            'admin.buku_besar.index',
            $this->siapkanLaporan($request)
        );
    }

    /**
     * Export PDF.
     */
    public function cetakPdf(Request $request)
    {
        $this->batasiAkses();

        $data = $this->siapkanLaporan($request);

        $pdf = Pdf::loadView(
            'admin.buku_besar.pdf',
            $data
        )->setPaper('a4', 'landscape');

        return $pdf->download(
            $this->namaFile($data, 'pdf')
        );
    }

    /**
     * Export Excel.
     */
    public function cetakExcel(Request $request)
    {
        $this->batasiAkses();

        $data = $this->siapkanLaporan($request);

        return Excel::download(
            new BukuBesarExport($data),
            $this->namaFile($data, 'xlsx')
        );
    }

    /**
     * Membuat nama file export.
     */
    private function namaFile(array $data, string $ekstensi): string
    {
        return 'buku-besar_'
            . $data['start_date']
            . '_sd_'
            . $data['end_date']
            . '.'
            . $ekstensi;
    }

    /**
     * Menyiapkan data laporan.
     */
    private function siapkanLaporan(Request $request): array
    {
        $request->validate([
            'akun_id' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'cabang_id' => 'nullable|integer|exists:cabangs,id',
        ]);

        $akuns = Akun::orderBy('kode_akun')->get();

        // Ambil daftar cabang aktif untuk dropdown Admin Pusat.
        $cabangs = Cabang::where('status', 'aktif')
            ->orderBy('nama')
            ->get();

        // Filter akun dan periode.
        $akun_id = $request->input('akun_id') ?: 'semua';
        $start_date = $request->input('start_date') ?: date('Y-m-01');
        $end_date = $request->input('end_date') ?: date('Y-m-t');

        $semuaAkun = $akun_id === 'semua';

        if ($semuaAkun) {
            $daftarAkun = $akuns;
        } else {
            $akunTerpilih = $akuns->firstWhere('id', (int) $akun_id);

            abort_if(!$akunTerpilih, 404);

            $daftarAkun = collect([$akunTerpilih]);
        }

        $user = Auth::user();

        // Admin Cabang hanya melihat cabangnya sendiri.
        // Admin Pusat dapat memilih cabang atau melihat semua cabang.
        $cabangId = $user->role === 'Admin Cabang'
            ? (int) $user->cabang_id
            : (
                $user->role === 'Admin Pusat' && $request->filled('cabang_id')
                    ? (int) $request->input('cabang_id')
                    : null
            );

        // Susun laporan berdasarkan akun, periode, dan cabang.
        $laporan = $this->susunLaporan(
            $daftarAkun,
            $start_date,
            $end_date,
            $cabangId
        );

        // Jika semua akun dipilih, tampilkan akun yang memiliki saldo
        // awal atau transaksi dalam periode laporan.
        if ($semuaAkun) {
            $laporan = $laporan->filter(function ($item) {
                return abs($item['saldoAwal']) > 0
                    || $item['baris']->isNotEmpty();
            })->values();
        }

        // Total debit dan kredit.
        $grandDebit = (float) $laporan->sum('totalDebit');
        $grandKredit = (float) $laporan->sum('totalKredit');

        // Total saldo awal dengan memperhitungkan sisi normal saldo.
        $grandSaldoAwal = (float) $laporan->sum(
            fn ($item) => $item['sisiAwal'] === 'Debit'
                ? $item['saldoAwal']
                : -$item['saldoAwal']
        );

        // Total saldo akhir.
        $grandSaldoAkhir = $grandSaldoAwal
            + $grandDebit
            - $grandKredit;

        return [
            'akuns' => $akuns,
            'cabangs' => $cabangs,
            'laporan' => $laporan,
            'semuaAkun' => $semuaAkun,
            'grandTotal' => (float) $laporan->sum(
                fn ($item) => $item['totalNilai'] ?? 0
            ),
            'grandDebit' => $grandDebit,
            'grandKredit' => $grandKredit,
            'grandSaldoAwal' => $grandSaldoAwal,
            'grandSaldoAkhir' => $grandSaldoAkhir,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'akun_id' => $akun_id,
            'cabang_id' => $cabangId,
        ];
    }

    /**
     * Mengubah saldo menjadi angka positif dan sisi saldo.
     */
    private function sisiSaldo(
        float $saldo,
        bool $isDebitNormal
    ): array {
        $saldo = round($saldo, 2);

        $sisiNormal = $isDebitNormal ? 'Debit' : 'Kredit';
        $sisiLawan = $isDebitNormal ? 'Kredit' : 'Debit';

        return [
            'nilai' => abs($saldo),
            'sisi' => $saldo >= 0 ? $sisiNormal : $sisiLawan,
        ];
    }

    /**
     * Menentukan keterangan akun lawan untuk Buku Besar.
     */
    private function keteranganAkunLawan(
        DetailJurnal $detail,
        $semuaDetailJurnal
    ): string {
        $kodeAkun = $detail->akun?->kode_akun;

        $pasanganKhusus = match ($kodeAkun) {
            '1-1100' => ['4-1100'],
            '4-1100' => ['1-1100'],
            '5-1100' => ['1-1300'],
            '1-1300' => ['5-1100'],
            default => null,
        };

        if ($pasanganKhusus !== null) {
            $namaPasangan = $semuaDetailJurnal
                ->filter(function ($item) use ($pasanganKhusus, $detail) {
                    return $item->id !== $detail->id
                        && in_array(
                            $item->akun?->kode_akun,
                            $pasanganKhusus,
                            true
                        );
                })
                ->pluck('akun.nama_akun')
                ->filter()
                ->unique()
                ->values()
                ->implode(', ');

            if ($namaPasangan !== '') {
                return $namaPasangan;
            }
        }

        $debit = (float) $detail->debit;
        $kredit = (float) $detail->kredit;

        $akunLawan = $semuaDetailJurnal
            ->filter(function ($item) use ($detail, $debit, $kredit) {
                if ($item->id === $detail->id) {
                    return false;
                }

                if ($debit > 0) {
                    return (float) $item->kredit > 0;
                }

                if ($kredit > 0) {
                    return (float) $item->debit > 0;
                }

                return false;
            })
            ->pluck('akun.nama_akun')
            ->filter()
            ->unique()
            ->values()
            ->implode(', ');

        return $akunLawan
            ?: ($detail->jurnal?->keterangan ?? '-');
    }

    /**
     * Menyusun laporan Buku Besar untuk satu atau banyak akun.
     */
    private function susunLaporan(
        $daftarAkun,
        $start_date,
        $end_date,
        ?int $cabangId = null
    ) {
        $akunIds = $daftarAkun->pluck('id');

        // 1. Saldo sebelum periode laporan.
        $sebelumnya = DetailJurnal::select('detail_jurnals.akun_id')
            ->selectRaw(
                'SUM(detail_jurnals.debit) as total_debit,
                 SUM(detail_jurnals.kredit) as total_kredit'
            )
            ->join(
                'jurnals',
                'detail_jurnals.jurnal_id',
                '=',
                'jurnals.id'
            )
            ->whereIn('detail_jurnals.akun_id', $akunIds)
            ->where('jurnals.tanggal', '<', $start_date)
            ->when(
                $cabangId !== null,
                fn ($query) => $query->where(
                    'jurnals.cabang_id',
                    $cabangId
                )
            )
            ->groupBy('detail_jurnals.akun_id')
            ->get()
            ->keyBy('akun_id');

        // 2. Ambil mutasi dalam periode laporan.
        $mutasiSemua = DetailJurnal::with(['jurnal', 'akun'])
            ->select('detail_jurnals.*')
            ->join(
                'jurnals',
                'detail_jurnals.jurnal_id',
                '=',
                'jurnals.id'
            )
            ->whereIn('detail_jurnals.akun_id', $akunIds)
            ->whereBetween(
                'jurnals.tanggal',
                [$start_date, $end_date]
            )
            ->when(
                $cabangId !== null,
                fn ($query) => $query->where(
                    'jurnals.cabang_id',
                    $cabangId
                )
            )
            ->orderBy('jurnals.tanggal')
            ->orderBy('jurnals.id')
            ->orderBy('detail_jurnals.id')
            ->get()
            ->groupBy('akun_id');

        // 3. Detail jurnal periode untuk menentukan akun lawan.
        $detailPeriode = DetailJurnal::with(['akun', 'jurnal'])
            ->whereHas('jurnal', function ($query) use (
                $start_date,
                $end_date,
                $cabangId
            ) {
                $query->whereBetween(
                    'tanggal',
                    [$start_date, $end_date]
                );

                if ($cabangId !== null) {
                    $query->where('cabang_id', $cabangId);
                }
            })
            ->get()
            ->groupBy('jurnal_id');

        // 4. Hitung saldo awal, mutasi, dan saldo akhir per akun.
        return $daftarAkun->map(function ($akun) use (
            $sebelumnya,
            $mutasiSemua,
            $detailPeriode
        ) {
            $isDebitNormal = $akun->saldo_normal === 'Debit';

            $awal = $sebelumnya->get($akun->id);

            $debitAwal = (float) ($awal->total_debit ?? 0);
            $kreditAwal = (float) ($awal->total_kredit ?? 0);

            $saldo = $isDebitNormal
                ? $debitAwal - $kreditAwal
                : $kreditAwal - $debitAwal;

            $awalTampil = $this->sisiSaldo($saldo, $isDebitNormal);

            $mutasi = $mutasiSemua->get($akun->id, collect());

            $baris = $mutasi->map(function ($detail) use (
                &$saldo,
                $isDebitNormal,
                $detailPeriode
            ) {
                $debit = (float) $detail->debit;
                $kredit = (float) $detail->kredit;

                $saldo += $isDebitNormal
                    ? $debit - $kredit
                    : $kredit - $debit;

                $tampil = $this->sisiSaldo($saldo, $isDebitNormal);

                $detail->saldo = $tampil['nilai'];
                $detail->sisi_saldo = $tampil['sisi'];

                $semuaDetailJurnal = $detailPeriode->get(
                    $detail->jurnal_id,
                    collect()
                );

                $detail->keterangan_buku_besar =
                    $this->keteranganAkunLawan(
                        $detail,
                        $semuaDetailJurnal
                    );

                $detail->total_tampil = max($debit, $kredit);

                return $detail;
            });

            $akhirTampil = $this->sisiSaldo($saldo, $isDebitNormal);

            return [
                'akun' => $akun,
                'saldoAwal' => $awalTampil['nilai'],
                'sisiAwal' => $awalTampil['sisi'],
                'baris' => $baris,
                'saldoAkhir' => $akhirTampil['nilai'],
                'sisiAkhir' => $akhirTampil['sisi'],
                'totalNilai' => (float) $baris->sum('total_tampil'),
                'totalDebit' => (float) $mutasi->sum('debit'),
                'totalKredit' => (float) $mutasi->sum('kredit'),
            ];
        });
    }
}