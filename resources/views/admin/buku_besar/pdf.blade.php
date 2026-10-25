@php
    // Helper tampilan (hanya format, tanpa perhitungan)
    $rp  = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
    $qty = fn($n) => rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Buku Besar</title>
    <style>
        @page { margin: 28px 30px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 8px; color: #1f2937; }
        h1 { font-size: 16px; margin: 0 0 2px; }
        h2 { font-size: 11px; margin: 0 0 2px; }
        .muted { color: #6b7280; }
        .kop { margin-bottom: 14px; }
        .blok { margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #d1d5db; padding: 4px 5px; }
        thead th { background: #f3f4f6; font-weight: bold; text-align: left; }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }
        .r { text-align: right; }
        .nw { white-space: nowrap; }
        .awal td { background: #fff7ed; font-weight: bold; }
        .total td { background: #f3f4f6; font-weight: bold; }
        .saldo { background: #f9fafb; font-weight: bold; }
        .kosong { text-align: center; color: #9ca3af; padding: 10px; }
    </style>
</head>
<body>

    <div class="kop">
        <h1>Buku Besar</h1>
        <div class="muted">
            Periode: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }}
            s/d {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
            &nbsp;|&nbsp; Dicetak: {{ now()->format('d M Y H:i') }}
        </div>
    </div>

    @forelse($laporan as $item)
        @php
            $akun = $item['akun'];
            $sn   = $akun->saldo_normal;
        @endphp

        <div class="blok">
            <h2>{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</h2>
            <div class="muted" style="margin-bottom: 4px;">
                Tipe Akun: {{ $akun->tipe_akun }} &nbsp;|&nbsp; Saldo Normal: {{ $sn }}
            </div>

            <table>
                <thead>
                    <tr>
                        <th style="width: 8%;">Tanggal</th>
                        <th style="width: 10%;">No Referensi</th>
                        <th style="width: 18%;">Keterangan</th>
                        <th class="r" style="width: 7%;">Kuantitas</th>
                        <th style="width: 6%;">Satuan</th>
                        <th class="r" style="width: 10%;">Harga</th>
                        <th class="r" style="width: 10%;">Total</th>
                        <th class="r" style="width: 10%;">Debit</th>
                        <th class="r" style="width: 10%;">Kredit</th>
                        <th class="r" style="width: 11%;">Saldo Normal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="awal">
                        <td colspan="7" class="r">Saldo Awal :</td>
                        <td></td>
                        <td></td>
                        <td class="r nw">{{ $rp($item['saldoAwal']) }} ({{ $item['sisiAwal'] }})</td>
                    </tr>

                    @forelse($item['baris'] as $detail)
                        <tr>
                            <td class="nw">{{ \Carbon\Carbon::parse($detail->jurnal->tanggal)->format('d M Y') }}</td>
                            <td class="nw">{{ $detail->jurnal->no_referensi }}</td>
                            <td>{{ $detail->jurnal->keterangan }}</td>
                            <td class="px-4 py-4 text-right">{{ $detail->quantity !== null ? $qty($detail->quantity) : '-' }}</td>
                            <td>{{ $detail->satuan ?: '-' }}</td>
                            <td class="r nw">{{ $detail->harga !== null ? $rp($detail->harga) : '-' }}</td>
                            <td class="r nw">{{ $rp($detail->total_tampil) }}</td>
                            <td class="r nw">{{ $detail->debit > 0 ? $rp($detail->debit) : '-' }}</td>
                            <td class="r nw">{{ $detail->kredit > 0 ? $rp($detail->kredit) : '-' }}</td>
                            <td class="r nw saldo">{{ $rp($detail->saldo) }} ({{ $detail->sisi_saldo }})</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="kosong">Tidak ada mutasi transaksi pada periode ini.</td>
                        </tr>
                    @endforelse

                    <tr class="total">
                        <td colspan="6" class="r">Total Mutasi / Saldo Akhir :</td>
                        <td class="r nw">{{ $rp($item['totalNilai']) }}</td>
                        <td class="r nw">{{ $rp($item['totalDebit']) }}</td>
                        <td class="r nw">{{ $rp($item['totalKredit']) }}</td>
                        <td class="r nw">{{ $rp($item['saldoAkhir']) }} ({{ $item['sisiAkhir'] }})</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @empty
        <p class="kosong">Tidak ada akun yang memiliki saldo atau mutasi pada periode ini.</p>
    @endforelse

    {{-- Ringkasan (hanya untuk mode Semua Akun) --}}
    @if($semuaAkun && $laporan->isNotEmpty())
        <div class="blok">
            <h2>Ringkasan Semua Akun</h2>
            <table style="width: 60%;">
                <tr>
                    <th>Total</th>
                    <th>Total Debit</th>
                    <th>Total Kredit</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td class="nw">{{ $rp($grandTotal) }}</td>
                    <td class="nw">{{ $rp($grandDebit) }}</td>
                    <td class="nw">{{ $rp($grandKredit) }}</td>
                    <td>{{ round($grandDebit, 2) === round($grandKredit, 2) ? 'Seimbang' : 'Tidak Seimbang' }}</td>
                </tr>
            </table>
        </div>
    @endif

</body>
</html>