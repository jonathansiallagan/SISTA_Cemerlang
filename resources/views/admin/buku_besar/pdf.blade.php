<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Buku Besar</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 9px;
            color: #222;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .periode {
            text-align: center;
            margin-bottom: 20px;
        }

        .akun {
            font-weight: bold;
            font-size: 11px;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid #999;
            padding: 5px;
        }

        th {
            background-color: #eee;
            text-align: center;
        }

        .angka {
            text-align: right;
        }

        .total {
            font-weight: bold;
            background-color: #f5f5f5;
        }
    </style>
</head>

<body>
    <h2>LAPORAN BUKU BESAR</h2>

    <p class="periode">
        Periode: {{ $start_date }} s/d {{ $end_date }}
    </p>

    @forelse ($laporan as $item)
        <div class="akun">
            {{ $item['akun']->kode_akun }}
            - {{ $item['akun']->nama_akun }}
        </div>

        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>No. Referensi</th>
                    <th>Keterangan</th>
                    <th>Debit</th>
                    <th>Kredit</th>
                    <th>Saldo</th>
                    <th>Sisi Saldo</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td colspan="3"><strong>Saldo Awal</strong></td>
                    <td></td>
                    <td></td>
                    <td class="angka">
                        {{ number_format($item['saldoAwal'], 2, ',', '.') }}
                    </td>
                    <td>{{ $item['sisiAwal'] }}</td>
                </tr>

                @foreach ($item['baris'] as $detail)
                    <tr>
                        <td>
                            {{ $detail->jurnal?->tanggal }}
                        </td>

                        <td>
                            {{ $detail->jurnal?->no_referensi }}
                        </td>

                        <td>
                            {{ $detail->jurnal?->keterangan }}
                        </td>

                        <td class="angka">
                            {{ number_format((float) $detail->debit, 2, ',', '.') }}
                        </td>

                        <td class="angka">
                            {{ number_format((float) $detail->kredit, 2, ',', '.') }}
                        </td>

                        <td class="angka">
                            {{ number_format((float) $detail->saldo, 2, ',', '.') }}
                        </td>

                        <td>{{ $detail->sisi_saldo }}</td>
                    </tr>
                @endforeach

                <tr class="total">
                    <td colspan="3">Total Mutasi / Saldo Akhir</td>

                    <td class="angka">
                        {{ number_format($item['totalDebit'], 2, ',', '.') }}
                    </td>

                    <td class="angka">
                        {{ number_format($item['totalKredit'], 2, ',', '.') }}
                    </td>

                    <td class="angka">
                        {{ number_format($item['saldoAkhir'], 2, ',', '.') }}
                    </td>

                    <td>{{ $item['sisiAkhir'] }}</td>
                </tr>
            </tbody>
        </table>
    @empty
        <p>Tidak ada data buku besar pada periode yang dipilih.</p>
    @endforelse

    <table>
        <tr class="total">
            <td>Total Debit Keseluruhan</td>
            <td class="angka">
                {{ number_format($grandDebit, 2, ',', '.') }}
            </td>

            <td>Total Kredit Keseluruhan</td>
            <td class="angka">
                {{ number_format($grandKredit, 2, ',', '.') }}
            </td>
        </tr>
    </table>
</body>
</html>
