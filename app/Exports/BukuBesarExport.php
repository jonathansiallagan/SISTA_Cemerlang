<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BukuBesarExport implements
    FromCollection,
    WithHeadings,
    WithTitle,
    WithStyles
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Buku Besar';
    }

    public function headings(): array
    {
        return [
            'Kode Akun',
            'Nama Akun',
            'Tanggal',
            'No Referensi',
            'Keterangan',
            'Debit',
            'Kredit',
            'Saldo',
            'Sisi Saldo',
        ];
    }

    public function collection(): Collection
    {
        $rows = collect();

        foreach ($this->data['laporan'] as $item) {
            $akun = $item['akun'];

            // Saldo awal
            $rows->push([
                $akun->kode_akun,
                $akun->nama_akun,
                '',
                '',
                'Saldo Awal',
                0,
                0,
                (float) $item['saldoAwal'],
                $item['sisiAwal'],
            ]);

            // Mutasi transaksi
            foreach ($item['baris'] as $detail) {
                $jurnal = $detail->jurnal;

                $rows->push([
                    $akun->kode_akun,
                    $akun->nama_akun,
                    $jurnal?->tanggal,
                    $jurnal?->no_referensi,
                    $detail->keterangan_buku_besar
                        ?: ($jurnal?->keterangan ?? '-'),
                    (float) $detail->debit,
                    (float) $detail->kredit,
                    (float) $detail->saldo,
                    $detail->sisi_saldo,
                ]);
            }

            // Total mutasi dan saldo akhir
            $rows->push([
                $akun->kode_akun,
                $akun->nama_akun,
                '',
                '',
                'Total Mutasi / Saldo Akhir',
                (float) $item['totalDebit'],
                (float) $item['totalKredit'],
                (float) $item['saldoAkhir'],
                $item['sisiAkhir'],
            ]);

            // Baris kosong sebagai pemisah antar akun
            $rows->push([]);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        // Format header
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $sheet->getStyle('A1:I1')
            ->getAlignment()
            ->setHorizontal('center');

        // Bekukan baris judul dan aktifkan filter
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:I1');

        // Sesuaikan lebar kolom
        foreach (range('A', 'I') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Format angka untuk Debit, Kredit, dan Saldo
        foreach (['F', 'G', 'H'] as $column) {
            $sheet->getStyle($column . ':' . $column)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
        }

        return [];
    }
}