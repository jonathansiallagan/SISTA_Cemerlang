@php
    // Data satu akun dari controller
    $akun = $item['akun'];
    $sn = $akun->saldo_normal;

    // Format rupiah
    $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

<div class="mb-6 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

    {{-- Header akun --}}
    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-gray-100 bg-gray-50/70 p-5">
        <div>
            <h3 class="text-lg font-bold text-gray-800">
                {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
            </h3>

            <p class="mt-1 text-sm text-gray-500">
                Periode:
                {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }}
                s/d
                {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
            </p>
        </div>

        <div class="flex gap-6 text-right">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                    Tipe Akun
                </span>
                <div class="mt-1 font-bold text-blue-600">
                    {{ $akun->tipe_akun }}
                </div>
            </div>

            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">
                    Saldo Normal
                </span>
                <div class="mt-1 font-bold text-blue-600">
                    {{ $sn }}
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel buku besar --}}
    <div class="w-full overflow-x-auto">
        <table class="w-full min-w-[650px] table-fixed text-sm text-left">

            <thead class="border-b border-gray-200 bg-gray-50 text-gray-500">
                <tr>
                    <th class="w-[13%] px-4 py-3 font-bold">Tanggal</th>
                    <th class="w-[19%] px-4 py-3 font-bold">No Referensi</th>
                    <th class="w-[28%] px-4 py-3 font-bold">Keterangan</th>
                    <th class="w-[13%] px-4 py-3 text-right font-bold">Debit</th>
                    <th class="w-[13%] px-4 py-3 text-right font-bold">Kredit</th>
                    <th class="w-[14%] bg-gray-100 px-4 py-3 text-right font-bold">Saldo</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">

                {{-- Saldo awal --}}
                <tr class="bg-orange-50/30">
                    <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-700">
                        Saldo Awal:
                    </td>

                    <td class="px-4 py-3"></td>
                    <td class="px-4 py-3"></td>

                    <td class="whitespace-nowrap bg-orange-50 px-4 py-3 text-right font-bold text-gray-800">
                        {{ $rp($item['saldoAwal']) }}
                        <span class="text-xs text-gray-400">
                            ({{ $item['sisiAwal'] }})
                        </span>
                    </td>
                </tr>

                {{-- Mutasi transaksi --}}
                @forelse ($item['baris'] as $detail)
                    <tr class="transition hover:bg-blue-50/50">

                        <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                            {{ \Carbon\Carbon::parse($detail->jurnal->tanggal)->format('d M Y') }}
                        </td>

                        <td class="break-words px-4 py-3">
                            <span class="font-medium text-blue-600">
                                {{ $detail->jurnal->no_referensi }}
                            </span>
                        </td>

                        <td class="break-words px-4 py-3 text-gray-700">
                            {{ $detail->keterangan_buku_besar ?: '—' }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-right text-gray-700">
                            {{ (float) $detail->debit > 0 ? $rp($detail->debit) : '—' }}
                        </td>

                        <td class="whitespace-nowrap px-4 py-3 text-right text-gray-700">
                            {{ (float) $detail->kredit > 0 ? $rp($detail->kredit) : '—' }}
                        </td>

                        <td class="whitespace-nowrap bg-gray-50 px-4 py-3 text-right font-bold text-gray-800">
                            {{ $rp($detail->saldo) }}
                            <span class="text-xs text-gray-400">
                                ({{ $detail->sisi_saldo }})
                            </span>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            Tidak ada mutasi transaksi pada periode ini.
                        </td>
                    </tr>
                @endforelse

            </tbody>

            {{-- Total mutasi dan saldo akhir --}}
            <tfoot class="border-t-2 border-gray-200 bg-gray-50 font-bold text-gray-800">
                <tr>
                    <td colspan="3" class="px-4 py-4 text-right">
                        Total Mutasi / Saldo Akhir:
                    </td>

                    <td class="whitespace-nowrap px-4 py-4 text-right">
                        {{ $rp($item['totalDebit']) }}
                    </td>

                    <td class="whitespace-nowrap px-4 py-4 text-right">
                        {{ $rp($item['totalKredit']) }}
                    </td>

                    <td class="whitespace-nowrap bg-gray-100 px-4 py-4 text-right">
                        {{ $rp($item['saldoAkhir']) }}
                        <span class="text-xs text-gray-400">
                            ({{ $item['sisiAkhir'] }})
                        </span>
                    </td>
                </tr>
            </tfoot>

        </table>
    </div>
</div>