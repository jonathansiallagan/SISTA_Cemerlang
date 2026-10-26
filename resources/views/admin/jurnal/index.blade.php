<x-admin-layout>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">
                Jurnal Umum
            </h1>
        </div>
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
                    @foreach ($jurnal->detailJurnals as $index => $detail)
                        <tr class="hover:bg-gray-50 transition
                            {{ $index === 0 || $index === 2 ? 'border-t-2 border-gray-200' : '' }}">

                            {{-- Tanggal: tampil pada baris Kas dan HPP --}}
                            <td class="border border-gray-300 px-2 py-3 whitespace-nowrap">
                                @if ($index === 0 || $index === 2)
                                    {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d M Y') }}
                                @endif
                            </td>

                            {{-- Referensi: tampil pada baris Kas dan HPP --}}
                            <td class="border border-gray-300 px-2 py-3 whitespace-nowrap">
                                @if ($index === 0 || $index === 2)
                                    {{ $jurnal->no_referensi }}
                                @endif
                            </td>

                            {{-- Keterangan --}}
                            <td class="border border-gray-300 px-2 py-3 align-middle">
                                @if ($index === 0 || $index === 2)
                                    @php
                                        $detailTransaksi = $jurnal->transaksi?->detailTransaksis;
                                    @endphp

                                    @if ($detailTransaksi && $detailTransaksi->isNotEmpty())
                                        Penjualan
                                        @foreach ($detailTransaksi as $item)
                                            {{ $item->produk->nama_produk ?? 'Produk' }}
                                            {{ $item->luas > 0 ? $item->luas . ' m²' : $item->jumlah . ' ' . ($item->produk->satuan ?? '') }}
                                            @if (!$loop->last), @endif
                                        @endforeach
                                    @else
                                        {{ $jurnal->keterangan ?? '-' }}
                                    @endif
                                @endif
                            </td>

                          
                            {{-- Quantity --}}
                            <td class="border border-gray-300 px-3 py-3 text-right align-middle">
                                @if ($index === 0 && $jurnal->pengeluaran)
                                    {{ rtrim(rtrim(number_format((float) $jurnal->pengeluaran->qty, 2, ',', '.'), '0'), ',') }}
                                @elseif ($index === 0 && $jurnal->transaksi)
                                    -
                                @else
                                    -
                                @endif
                            </td>

                            {{-- Satuan --}}
                            <td class="border border-gray-300 px-2 py-3">
                                {{ $index === 0 ? ($jurnal->pengeluaran?->satuan ?? '-') : '-' }}
                            </td>

                            {{-- Harga --}}
                            <td class="border border-gray-300 px-2 py-3 text-right whitespace-nowrap">
                                @if ($index === 0 && $jurnal->pengeluaran)
                                    Rp {{ number_format((float) $jurnal->pengeluaran->harga, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>

                            {{-- Total --}}
                            <td class="border border-gray-300 px-2 py-3 text-right whitespace-nowrap">
                                @if ($index === 0 && $jurnal->pengeluaran)
                                    Rp {{ number_format((float) $jurnal->pengeluaran->total_pengeluaran, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>


                            {{-- Nama Akun tanpa kode --}}
                            <td class="border border-gray-300 px-2 py-3 text-left align-middle text-gray-800">
                                {{ $detail->akun->nama_akun ?? '-' }}
                            </td>

                            {{-- Debit --}}
                            <td class="border border-gray-300 px-2 py-3 text-right whitespace-nowrap">
                                {{ $detail->debit > 0
                                    ? 'Rp ' . number_format($detail->debit, 0, ',', '.')
                                    : '-' }}
                            </td>

                            {{-- Kredit --}}
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