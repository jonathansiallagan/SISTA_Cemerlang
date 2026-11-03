
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        p {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #777;
            padding: 6px;
        }

        th {
            background-color: #e5e7eb;
        }

        .right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h2>CV Cemerlang</h2>
    <p>Riwayat Transaksi Penjualan</p>

    <table>
        <thead>
            <tr>
                <th>No. Referensi</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Cabang</th>
                <th>Nama Produk</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Harga</th>
                <th>Subtotal</th>
                <th>Total Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksis as $trx)
                @forelse ($trx->detailTransaksis as $detail)
                    <tr>
                        <td>{{ $trx->no_referensi }}</td>
                        <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d-m-Y') }}</td>
                        <td>{{ $trx->nama_pelanggan }}</td>
                        <td>{{ $trx->cabang->nama ?? '-' }}</td>
                        <td>{{ $detail->produk->nama_produk ?? 'Produk tidak ditemukan' }}</td>
                        <td class="right">
                            @php
                                $satuan = strtolower(trim($detail->produk->satuan ?? ''));
                                $meteran = $satuan === 'm2'
                                    || $satuan === 'meter persegi'
                                    || str_contains($satuan, 'm²');

                                $qty = $meteran && (float) $detail->luas > 0
                                    ? (float) $detail->luas
                                    : (float) $detail->jumlah;
                            @endphp
                            {{ rtrim(rtrim(number_format($qty, 2, ',', '.'), '0'), ',') }}
                        </td>
                        <td>{{ $detail->produk->satuan ?? '-' }}</td>
                        <td class="right">
                            Rp {{ number_format((float) $detail->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td class="right">
                            Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}
                        </td>
                        <td class="right">
                            Rp {{ number_format((float) $trx->total_transaksi, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td>{{ $trx->no_referensi }}</td>
                        <td>{{ \Carbon\Carbon::parse($trx->tanggal)->format('d-m-Y') }}</td>
                        <td>{{ $trx->nama_pelanggan }}</td>
                        <td>{{ $trx->cabang->nama ?? '-' }}</td>
                        <td colspan="5">Belum ada detail produk</td>
                        <td class="right">
                            Rp {{ number_format((float) $trx->total_transaksi, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforelse
            @empty
                <tr>
                    <td colspan="10" style="text-align: center;">
                        Tidak ada transaksi untuk filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
