@php
    // Data satu akun dari controller
    $akun = $item['akun'];
    $sn = $akun->saldo_normal;

    // Format rupiah
    $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

<div class="card mb-3">

    {{-- HEADER AKUN --}}
    <div class="card-header bg-primary d-flex flex-wrap align-items-center justify-content-between py-3">
        <div class="mb-2 mb-md-0">
            <h6 class="font-weight-bold text-white mb-1">
                {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
            </h6>

            <small class="text-white">
                Periode:
                {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }}
                s/d
                {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
            </small>
        </div>

        <div class="d-flex flex-wrap">
            <div class="mr-4 mb-1">
                <small class="text-white d-block">Tipe Akun</small>
                <span class="font-weight-bold text-white">
                    {{ $akun->tipe_akun }}
                </span>
            </div>

            <div class="mb-1">
                <small class="text-white d-block">Saldo Normal</small>
                <span class="font-weight-bold text-white">
                    {{ $sn }}
                </span>
            </div>
        </div>
    </div>

    {{-- TABEL BUKU BESAR --}}
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sista">

                <thead>
                    <tr>
                        <th class="text-center">#</th>
                        <th>Tanggal</th>
                        <th>No Referensi</th>
                        <th>Keterangan</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Kredit</th>
                        <th class="text-right">Saldo</th>
                    </tr>
                </thead>

                <tbody>

                    {{-- SALDO AWAL --}}
                    <tr class="font-weight-bold">
                        <td colspan="4" class="text-right align-middle">
                            Saldo Awal:
                        </td>

                        <td></td>
                        <td></td>

                        <td class="text-right align-middle text-nowrap">
                            {{ $rp($item['saldoAwal']) }}
                            <small class="text-muted">
                                ({{ $item['sisiAwal'] }})
                            </small>
                        </td>
                    </tr>

                    {{-- MUTASI TRANSAKSI --}}
                    @forelse ($item['baris'] as $detail)
                        <tr>
                            <td class="text-center text-muted align-middle">
                                {{ $loop->iteration }}
                            </td>

                            <td class="align-middle text-nowrap">
                                {{ \Carbon\Carbon::parse($detail->jurnal->tanggal)->format('d M Y') }}
                            </td>

                            <td class="align-middle">
                                <span class="font-weight-bold text-primary">
                                    {{ $detail->jurnal->no_referensi }}
                                </span>
                            </td>

                            <td class="align-middle">
                                {{ $detail->keterangan_buku_besar ?: '—' }}
                            </td>

                            <td class="text-right align-middle text-nowrap">
                                {{ (float) $detail->debit > 0 ? $rp($detail->debit) : '—' }}
                            </td>

                            <td class="text-right align-middle text-nowrap">
                                {{ (float) $detail->kredit > 0 ? $rp($detail->kredit) : '—' }}
                            </td>

                            <td class="text-right align-middle text-nowrap font-weight-bold">
                                {{ $rp($detail->saldo) }}
                                <small class="text-muted">
                                    ({{ $detail->sisi_saldo }})
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-file-alt fa-2x mb-2"></i>
                                <p class="mb-0">
                                    Tidak ada mutasi transaksi pada periode ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse

                </tbody>

                {{-- TOTAL MUTASI DAN SALDO AKHIR --}}
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="4" class="text-right align-middle">
                            Total Mutasi / Saldo Akhir:
                        </td>

                        <td class="text-right align-middle text-nowrap">
                            {{ $rp($item['totalDebit']) }}
                        </td>

                        <td class="text-right align-middle text-nowrap">
                            {{ $rp($item['totalKredit']) }}
                        </td>

                        <td class="text-right align-middle text-nowrap">
                            {{ $rp($item['saldoAkhir']) }}
                            <small class="text-muted">
                                ({{ $item['sisiAkhir'] }})
                            </small>
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>
    </div>

</div>