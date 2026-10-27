<?php

namespace App\Exports;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;
    protected $user;
    protected $nomor = 0;

    public function __construct(Request $request, $user)
    {
        $this->request = $request;
        $this->user = $user;
    }

    public function collection(): \Illuminate\Support\Collection
    {
        $query = Transaksi::with([
            'cabang',
            'detailTransaksis.produk',
        ]);

        // Customer Service hanya dapat mengekspor cabangnya sendiri.
        if ($this->user->isCustomerService()) {
            $query->where('cabang_id', $this->user->cabang_id);
        } elseif (
            $this->user->role !== 'Admin Pusat'
        ) {
            // Admin Cabang juga hanya dapat mengekspor cabangnya sendiri.
            $query->where('cabang_id', $this->user->cabang_id);
        } elseif ($this->request->filled('cabang_id')) {
            // Hanya Admin Pusat yang dapat memilih cabang.
            $query->where('cabang_id', $this->request->cabang_id);
        }

        if ($this->request->filled('search')) {
            $search = $this->request->search;

            $query->where(function ($q) use ($search) {
                $q->where('no_referensi', 'like', "%{$search}%")
                    ->orWhere('nama_pelanggan', 'like', "%{$search}%");
            });
        }

        if ($this->request->filled('tanggal_mulai')) {
            $query->whereDate(
                'tanggal',
                '>=',
                $this->request->tanggal_mulai
            );
        }

        if ($this->request->filled('tanggal_akhir')) {
            $query->whereDate(
                'tanggal',
                '<=',
                $this->request->tanggal_akhir
            );
        }

        return $query
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No Referensi',
            'Tanggal',
            'Pelanggan',
            'Cabang',
            'Nama Produk',
            'Qty',
            'Satuan',
            'Harga',
            'Subtotal',
            'Total Transaksi',
        ];
    }

    public function map($transaksi): array
    {
        $baris = [];

        foreach ($transaksi->detailTransaksis as $detail) {
            $satuan = strtolower(trim($detail->produk->satuan ?? ''));

            $meteran = $satuan === 'm2'
                || $satuan === 'meter persegi'
                || str_contains($satuan, 'm²');

            $qty = $meteran && (float) $detail->luas > 0
                ? (float) $detail->luas
                : (float) $detail->jumlah;

            $baris[] = [
                ++$this->nomor,
                $transaksi->no_referensi,
                \Carbon\Carbon::parse($transaksi->tanggal)->format('d-m-Y'),
                $transaksi->nama_pelanggan,
                $transaksi->cabang->nama ?? '-',
                $detail->produk->nama_produk ?? 'Produk tidak ditemukan',
                $qty,
                $detail->produk->satuan ?? '-',
                (float) $detail->harga_satuan,
                (float) $detail->subtotal,
                (float) $transaksi->total_transaksi,
            ];
        }

        // Tetap tampilkan transaksi meskipun belum memiliki detail produk.
        if (empty($baris)) {
            $baris[] = [
                ++$this->nomor,
                $transaksi->no_referensi,
                \Carbon\Carbon::parse($transaksi->tanggal)->format('d-m-Y'),
                $transaksi->nama_pelanggan,
                $transaksi->cabang->nama ?? '-',
                'Belum ada detail produk',
                0,
                '-',
                0,
                0,
                (float) $transaksi->total_transaksi,
            ];
        }

        return $baris;
    }
}
