<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BukuBesarExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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
            'Kuantitas',
            'Satuan',
            'Harga',
            'Total',
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

            $rows->push([
                $akun->kode_akun,
                $akun->nama_akun,
                '',
                '',
                'Saldo Awal',
                '',
                '',
                '',
                '',
                '',
                '',
                $item['saldoAwal'],
                $item['sisiAwal'],
            ]);

            foreach ($item['baris'] as $detail) {
                $jurnal = $detail->jurnal;

                $rows->push([
                    $akun->kode_akun,
                    $akun->nama_akun,
                    $jurnal?->tanggal,
                    $jurnal?->no_referensi,
                    $jurnal?->keterangan,
                    $detail->quantity,
                    $detail->satuan,
                    $detail->harga,
                    $detail->total_harga ?? max(
                        (float) $detail->debit,
                        (float) $detail->kredit
                    ),
                    (float) $detail->debit,
                    (float) $detail->kredit,
                    (float) $detail->saldo,
                    $detail->sisi_saldo,
                ]);
            }

            $rows->push([
                $akun->kode_akun,
                $akun->nama_akun,
                '',
                '',
                'Total Mutasi / Saldo Akhir',
                '',
                '',
                '',
                $item['totalNilai'] ?? 0,
                $item['totalDebit'],
                $item['totalKredit'],
                $item['saldoAkhir'],
                $item['sisiAkhir'],
            ]);

            $rows->push([]);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);
        $sheet->getStyle('A1:M1')->getAlignment()
            ->setHorizontal('center');

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:M1');

        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        foreach (['H', 'I', 'J', 'K', 'L'] as $column) {
            $sheet->getStyle($column . ':' . $column)
                ->getNumberFormat()
                ->setFormatCode('#,##0.00');
        }

        return [];
    }
}