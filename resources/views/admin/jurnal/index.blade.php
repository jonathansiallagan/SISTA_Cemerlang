<x-admin-layout>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Jurnal Umum</h1>
        </div>
        <a href="{{ route('jurnal.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium"><i class="fa-solid fa-plus"></i> Tambah Jurnal Manual</a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-blue-600 text-white text-center font-bold py-3 border border-blue-600">
            DAFTAR JURNAL
        </div>
        <div class="w-full">
            <table class="w-full table-fixed text-sm text-left border-collapse border border-gray-300">
                <thead class="bg-gray-100 text-gray-600 font-bold">
                    <tr>
                        <th class="border border-gray-300 px-3 py-4 w-[10%]">Tanggal</th>
                        <th class="border border-gray-300 px-3 py-4 w-[12%]">No Referensi</th>
                        <th class="border border-gray-300 px-3 py-4 w-[20%]">Keterangan</th>
                        <th class="border border-gray-300 px-3 py-4 w-[6%] text-right">Qty</th>
                        <th class="border border-gray-300 px-3 py-4 w-[8%]">Satuan</th>
                        <th class="border border-gray-300 px-3 py-4 w-[9%] text-right">Harga</th>
                        <th class="border border-gray-300 px-3 py-4 w-[9%] text-right">Total</th>
                        <th class="border border-gray-300 px-3 py-4 w-[11%]">Nama Akun</th>
                        <th class="border border-gray-300 px-3 py-4 w-[8%] text-right">Debit</th>
                        <th class="border border-gray-300 px-3 py-4 w-[7%] text-right">Kredit</th>
                    </tr>
                </thead>

            <tbody class="divide-y divide-gray-100">
                @forelse ($jurnals as $jurnal)
                    @php
                        // Jurnal otomatis dari penjualan: tiap produk = 4 baris
                        // (Kas/Pendapatan, lalu HPP/Persediaan). Jurnal manual: header di baris pertama saja.
                        $detailTransaksi = $jurnal->transaksi?->detailTransaksis;
                        $jurnalOtomatis = $detailTransaksi && $detailTransaksi->isNotEmpty();
                    @endphp

                    @foreach ($jurnal->detailJurnals as $index => $detail)
                        @php
                            $awalBlok = $jurnalOtomatis ? ($index % 2 === 0) : ($index === 0);
                            $itemTrx = $jurnalOtomatis ? $detailTransaksi->values()->get(intdiv($index, 4)) : null;
                        @endphp
                        <tr class="hover:bg-gray-50 transition {{ $awalBlok ? 'border-t-2 border-gray-200' : '' }}">

                            <td class="border border-gray-300 px-2 py-3 whitespace-nowrap">
                                @if ($awalBlok)
                                    {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d M Y') }}
                                @endif
                            </td>

                            <td class="border border-gray-300 px-2 py-3 whitespace-nowrap">
                                @if ($awalBlok)
                                    {{ $jurnal->no_referensi }}
                                @endif
                            </td>

                            <td class="border border-gray-300 px-2 py-3 align-middle">
                                @if ($awalBlok)
                                    @if ($itemTrx)
                                        {{ $index % 4 < 2 ? 'Penjualan' : 'HPP' }}
                                        {{ $itemTrx->produk->nama_produk ?? 'Produk' }}
                                        {{ $itemTrx->luas > 0 ? $itemTrx->luas . ' m²' : $itemTrx->jumlah . ' ' . ($itemTrx->produk->satuan ?? '') }}
                                    @else
                                        {{ $jurnal->keterangan ?? '-' }}
                                    @endif
                                @endif
                            </td>

                            <td class="border border-gray-300 px-3 py-3 pl-5 text-right align-middle">
                                {{ $detail->quantity !== null
                                    ? rtrim(rtrim(number_format($detail->quantity, 2, ',', '.'), '0'), ',')
                                    : '-' }}
                            </td>

                            <td class="border border-gray-300 px-2 py-3">
                                {{ $detail->satuan ?? '-' }}
                            </td>

                            <td class="border border-gray-300 px-2 py-3 text-right whitespace-nowrap">
                                {{ $detail->harga !== null
                                    ? 'Rp ' . number_format($detail->harga, 0, ',', '.')
                                    : '-' }}
                            </td>

                            <td class="border border-gray-300 px-2 py-3 text-right whitespace-nowrap">
                                {{ $detail->total_harga !== null
                                    ? 'Rp ' . number_format($detail->total_harga, 0, ',', '.')
                                    : '-' }}
                            </td>

                            <td class="border border-gray-300 px-2 py-3 text-left align-middle text-gray-800">
                                {{ $detail->akun->nama_akun ?? '-' }}
                            </td>

                            <td class="border border-gray-300 px-2 py-3 text-right whitespace-nowrap">
                                {{ $detail->debit > 0
                                    ? 'Rp ' . number_format($detail->debit, 0, ',', '.')
                                    : '-' }}
                            </td>

                            <td class="border border-gray-300 px-2 py-3 text-right whitespace-nowrap">
                                {{ $detail->kredit > 0
                                    ? 'Rp ' . number_format($detail->kredit, 0, ',', '.')
                                    : '-' }}
                            </td>

                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-8 text-center text-gray-400">
                            Belum ada pencatatan jurnal.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>