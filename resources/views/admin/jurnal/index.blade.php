
<x-admin-layout>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Jurnal Umum</h1>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-blue-600 text-white text-center font-bold py-3">
            DAFTAR JURNAL
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full min-w-[1050px] text-xs md:text-sm text-left border-collapse border border-gray-300">
                <thead class="bg-gray-100 text-gray-600 font-bold">
                    <tr>
                        <th class="border border-gray-300 px-2 py-3 w-[90px]">Tanggal</th>
                        <th class="border border-gray-300 px-2 py-3 w-[145px]">No Referensi</th>
                        <th class="border border-gray-300 px-2 py-3 min-w-[200px]">Keterangan</th>
                        <th class="border border-gray-300 px-2 py-3 w-[60px] text-right">Qty</th>
                        <th class="border border-gray-300 px-2 py-3 w-[70px]">Satuan</th>
                        <th class="border border-gray-300 px-2 py-3 w-[115px] text-right">Harga</th>
                        <th class="border border-gray-300 px-2 py-3 w-[125px] text-right">Total</th>
                        <th class="border border-gray-300 px-2 py-3 min-w-[160px]">Nama Akun</th>
                        <th class="border border-gray-300 px-2 py-3 w-[120px] text-right">Debit</th>
                        <th class="border border-gray-300 px-2 py-3 w-[120px] text-right">Kredit</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse ($jurnals as $jurnal)
                        @php
                            $details = $jurnal->detailJurnals;

                            // Kelompokkan detail berdasarkan keterangan.
                            // Untuk data lama yang belum memiliki keterangan detail,
                            // gunakan keterangan header jurnal sebagai cadangan.
                            $groups = $details
                                ->groupBy(function ($detail) use ($jurnal) {
                                    return $detail->keterangan
                                        ?? $jurnal->keterangan
                                        ?? 'Tanpa Keterangan';
                                })
                                // Tampilkan penjualan terlebih dahulu,
                                // lalu kelompok HPP.
                                ->sortBy(function ($group, $keterangan) {
                                    return str_starts_with(
                                        (string) $keterangan,
                                        'HPP '
                                    ) ? 1 : 0;
                                });

                            $jumlahBaris = $details->count();
                            $barisGlobal = 0;

                            $pengeluaran = $jurnal->pengeluaran;
                        @endphp

                        @foreach ($groups as $keterangan => $group)
                            @php
                                $detailUtama = $group->first(
                                    fn ($detail) =>
                                        $detail->quantity !== null
                                        || $detail->harga !== null
                                        || $detail->total_harga !== null
                                );

                                $qty = $detailUtama?->quantity
                                    ?? $pengeluaran?->qty;

                                $satuan = $detailUtama?->satuan
                                    ?? $pengeluaran?->satuan;

                                $harga = $detailUtama?->harga
                                    ?? $pengeluaran?->harga;

                                $total = $detailUtama?->total_harga
                                    ?? $pengeluaran?->total_pengeluaran;
                            @endphp

                            @foreach ($group as $detailIndex => $detail)
                                <tr class="hover:bg-blue-50 transition-colors">
                                    {{-- Tanggal dan nomor referensi digabung
                                         untuk seluruh baris satu jurnal. --}}
                                    @if ($barisGlobal === 0)
                                        <td rowspan="{{ $jumlahBaris }}"
                                            class="border border-gray-300 px-2 py-2 align-middle whitespace-normal break-words">
                                            {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d M Y') }}
                                        </td>

                                        <td rowspan="{{ $jumlahBaris }}"
                                            class="border border-gray-300 px-2 py-2 align-middle whitespace-normal break-words">
                                            {{ $jurnal->no_referensi }}
                                        </td>
                                    @endif

                                    {{-- Keterangan, qty, satuan, harga,
                                         dan total digabung per kelompok akun. --}}
                                    @if ($detailIndex === 0)
                                        <td rowspan="{{ $group->count() }}"
                                            class="border border-gray-300 px-2 py-2 align-middle whitespace-normal break-words">
                                            {{ $keterangan }}
                                        </td>

                                        <td rowspan="{{ $group->count() }}"
                                            class="border border-gray-300 px-2 py-2 align-middle text-right">
                                            {{ $qty !== null
                                                ? rtrim(rtrim(number_format((float) $qty, 2, ',', '.'), '0'), ',')
                                                : '-' }}
                                        </td>

                                        <td rowspan="{{ $group->count() }}"
                                            class="border border-gray-300 px-2 py-2 align-middle">
                                            {{ $satuan ?? '-' }}
                                        </td>

                                        <td rowspan="{{ $group->count() }}"
                                            class="border border-gray-300 px-2 py-2 align-middle text-right whitespace-nowrap">
                                            {{ $harga !== null
                                                ? 'Rp ' . number_format((float) $harga, 0, ',', '.')
                                                : '-' }}
                                        </td>

                                        <td rowspan="{{ $group->count() }}"
                                            class="border border-gray-300 px-2 py-2 align-middle text-right whitespace-nowrap">
                                            {{ $total !== null
                                                ? 'Rp ' . number_format((float) $total, 0, ',', '.')
                                                : '-' }}
                                        </td>
                                    @endif

                                    <td class="border border-gray-300 px-2 py-2 align-middle whitespace-normal break-words">
                                        {{ $detail->akun->nama_akun ?? '-' }}
                                    </td>

                                    <td class="border border-gray-300 px-2 py-2 align-middle text-right whitespace-nowrap">
                                        {{ (float) $detail->debit > 0
                                            ? 'Rp ' . number_format((float) $detail->debit, 0, ',', '.')
                                            : '-' }}
                                    </td>

                                    <td class="border border-gray-300 px-2 py-2 align-middle text-right whitespace-nowrap">
                                        {{ (float) $detail->kredit > 0
                                            ? 'Rp ' . number_format((float) $detail->kredit, 0, ',', '.')
                                            : '-' }}
                                    </td>
                                </tr>

                                @php
                                    $barisGlobal++;
                                @endphp
                            @endforeach
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
