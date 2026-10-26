<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\DetailJurnal;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\BukuBesarExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BukuBesarController extends Controller
{
    /**
     * Halaman utama Buku Besar.
     */
    public function index(Request $request)
    {
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
        ]);

        $akuns = Akun::orderBy('kode_akun')->get();

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

        $laporan = $this->susunLaporan(
            $daftarAkun,
            $start_date,
            $end_date
        );

        if ($semuaAkun) {
            $laporan = $laporan->filter(function ($item) {
                return abs($item['saldoAwal']) > 0
                    || $item['baris']->isNotEmpty();
            })->values();
        }

        $grandDebit = (float) $laporan->sum('totalDebit');
        $grandKredit = (float) $laporan->sum('totalKredit');

        $grandSaldoAwal = (float) $laporan->sum(
            fn ($item) => $item['sisiAwal'] === 'Debit'
                ? $item['saldoAwal']
                : -$item['saldoAwal']
        );

        $grandSaldoAkhir = $grandSaldoAwal
            + $grandDebit
            - $grandKredit;

        return [
            'akuns' => $akuns,
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
     *
     * Penjualan:
     * Kas <-> Pendapatan Penjualan
     *
     * HPP:
     * Harga Pokok Produksi <-> Persediaan
     *
     * Beban operasional:
     * Mengambil akun lawan yang benar-benar tercatat,
     * misalnya Kas atau Bank.
     */
    private function keteranganAkunLawan(
        DetailJurnal $detail,
        $semuaDetailJurnal
    ): string {
        $kodeAkun = $detail->akun?->kode_akun;

        // Pasangan khusus agar akun penjualan dan HPP
        // tidak tercampur meskipun berada di jurnal yang sama.
        $pasanganKhusus = match ($kodeAkun) {
            '1-1100' => ['4-1100'], // Kas -> Pendapatan Penjualan
            '4-1100' => ['1-1100'], // Pendapatan Penjualan -> Kas
            '5-1100' => ['1-1300'], // HPP -> Persediaan
            '1-1300' => ['5-1100'], // Persediaan -> HPP
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

        // Untuk beban operasional dan jurnal lainnya,
        // pilih akun lawan yang memiliki mutasi berpasangan.
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
        $end_date
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
            ->groupBy('detail_jurnals.akun_id')
            ->get()
            ->keyBy('akun_id');

        // 2. Ambil semua mutasi dalam periode.
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
            ->orderBy('jurnals.tanggal')
            ->orderBy('jurnals.id')
            ->orderBy('detail_jurnals.id')
            ->get()
            ->groupBy('akun_id');

        // 3. Ambil semua detail jurnal pada periode yang sama,
        // termasuk akun lawan yang tidak sedang ditampilkan.
        $detailPeriode = DetailJurnal::with(['akun', 'jurnal'])
            ->whereHas('jurnal', function ($query) use ($start_date, $end_date) {
                $query->whereBetween('tanggal', [$start_date, $end_date]);
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

                $detail->total_tampil = $detail->total_harga !== null
                    ? (float) $detail->total_harga
                    : max($debit, $kredit);

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