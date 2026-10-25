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
     * Halaman utama buku besar.
     */
    public function index(Request $request)
    {
        $data = $this->siapkanLaporan($request);

        return view('admin.buku_besar.index', $data);
    }

    /**
     * Export buku besar ke PDF.
     */
    public function cetakPdf(Request $request)
    {
        if (!class_exists(Pdf::class)) {
            return back()->with(
                'error',
                'Fitur PDF belum aktif. Jalankan composer require barryvdh/laravel-dompdf'
            );
        }

        $data = $this->siapkanLaporan($request);

        $pdf = Pdf::loadView(
            'admin.buku_besar.pdf',
            $data
        )->setPaper('a4', 'landscape');

        return $pdf->download(
            $this->namaFile($data, 'pdf')
        );
    }

    
    public function cetakExcel(Request $request)
    {
        $data = $this->siapkanLaporan($request);

        $namaFile = 'buku-besar_'
            . $data['start_date']
            . '_sd_'
            . $data['end_date']
            . '.xlsx';

        return Excel::download(
            new BukuBesarExport($data),
            $namaFile
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
     * Menyiapkan data untuk halaman, PDF, dan CSV.
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

    $start_date = $request->input('start_date')
        ?: date('Y-m-01');

    $end_date = $request->input('end_date')
        ?: date('Y-m-t');

    $semuaAkun = $akun_id === 'semua';

    if ($semuaAkun) {
        $daftarAkun = $akuns;
    } else {
        $akunTerpilih = $akuns->firstWhere(
            'id',
            (int) $akun_id
        );

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

    // Total mutasi pada periode yang dipilih.
    $grandDebit = (float) $laporan->sum('totalDebit');
    $grandKredit = (float) $laporan->sum('totalKredit');

    // Saldo awal bersih:
    // Debit positif, Kredit negatif.
    $grandSaldoAwal = (float) $laporan->sum(
        fn ($item) => $item['sisiAwal'] === 'Debit'
            ? $item['saldoAwal']
            : -$item['saldoAwal']
    );

    // Saldo akhir bersih dihitung dari saldo awal bersih
    // ditambah mutasi debit dikurangi mutasi kredit.
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
     * Mengubah saldo menjadi angka positif dan label sisi saldo.
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
     * Menyusun laporan buku besar untuk satu atau banyak akun.
     */
    private function susunLaporan(
        $daftarAkun,
        $start_date,
        $end_date
    ) {
        $akunIds = $daftarAkun->pluck('id');

        // 1. Menghitung saldo sebelum periode awal.
        $sebelumnya = DetailJurnal::select(
                'detail_jurnals.akun_id'
            )
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

        // 2. Mengambil mutasi jurnal pada periode laporan.
        $mutasiSemua = DetailJurnal::with('jurnal')
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

        // 3. Menghitung saldo awal, mutasi, dan saldo akhir.
        return $daftarAkun->map(
            function ($akun) use ($sebelumnya, $mutasiSemua) {
                $isDebitNormal = $akun->saldo_normal === 'Debit';

                $awal = $sebelumnya->get($akun->id);

                $debitAwal = (float) ($awal->total_debit ?? 0);
                $kreditAwal = (float) ($awal->total_kredit ?? 0);

                $saldo = $isDebitNormal
                    ? $debitAwal - $kreditAwal
                    : $kreditAwal - $debitAwal;

                $awalTampil = $this->sisiSaldo(
                    $saldo,
                    $isDebitNormal
                );

                $mutasi = $mutasiSemua->get(
                    $akun->id,
                    collect()
                );

                $baris = $mutasi->map(
                    function ($detail) use (&$saldo, $isDebitNormal) {
                        $debit = (float) $detail->debit;
                        $kredit = (float) $detail->kredit;

                        $saldo += $isDebitNormal
                            ? $debit - $kredit
                            : $kredit - $debit;

                        $tampil = $this->sisiSaldo(
                            $saldo,
                            $isDebitNormal
                        );

                        $detail->saldo = $tampil['nilai'];
                        $detail->sisi_saldo = $tampil['sisi'];

                        // Nilai total untuk export, jika kolom tersedia.
                        $detail->total_tampil =
                            $detail->total_harga !== null
                                ? (float) $detail->total_harga
                                : max($debit, $kredit);

                        return $detail;
                    }
                );

                $akhirTampil = $this->sisiSaldo(
                    $saldo,
                    $isDebitNormal
                );

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
            }
        );
    }
}