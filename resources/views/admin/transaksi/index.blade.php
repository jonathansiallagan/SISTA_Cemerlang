<x-admin-layout>
    {{-- HEADER HALAMAN DAN TOMBOL EXPORT --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                Riwayat Transaksi
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Riwayat transaksi dan rincian produk.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a
                href="{{ route('transaksi.export.pdf', request()->query()) }}"
                class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 font-medium text-white transition hover:bg-red-700"
            >
                <i class="fa-solid fa-file-pdf"></i>
                Export PDF
            </a>

            <a
                href="{{ route('transaksi.export.excel', request()->query()) }}"
                class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 font-medium text-white transition hover:bg-green-700"
            >
                <i class="fa-solid fa-file-excel"></i>
                Export Excel
            </a>
        </div>
    </div>

    {{-- NOTIFIKASI SUKSES --}}
    @if (session('success'))
        <div class="mb-4 rounded-lg border border-green-300 bg-green-100 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    {{-- NOTIFIKASI ERROR --}}
    @if (session('error'))
        <div class="mb-4 rounded-lg border border-red-300 bg-red-100 px-4 py-3 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    
    {{-- FILTER TRANSAKSI --}}
    <div class="mb-5 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <form action="{{ route('transaksi.index') }}" method="GET">
            <div class="flex flex-wrap items-end gap-3">

                {{-- TANGGAL MULAI --}}
                <div class="min-w-[150px] flex-1">
                    <label for="tanggal_mulai" class="mb-1 block text-sm font-medium text-gray-700">
                        Tanggal Mulai
                    </label>
                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        value="{{ request('tanggal_mulai') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                {{-- TANGGAL AKHIR --}}
                <div class="min-w-[150px] flex-1">
                    <label for="tanggal_akhir" class="mb-1 block text-sm font-medium text-gray-700">
                        Tanggal Akhir
                    </label>
                    <input
                        type="date"
                        id="tanggal_akhir"
                        name="tanggal_akhir"
                        value="{{ request('tanggal_akhir') }}"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>

                {{-- FILTER CABANG KHUSUS ADMIN PUSAT --}}
                @if (auth()->user()->role === 'Admin Pusat')
                    <div class="min-w-[150px] flex-1">
                        <label for="cabang_id" class="mb-1 block text-sm font-medium text-gray-700">
                            Cabang
                        </label>
                        <select
                            id="cabang_id"
                            name="cabang_id"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option value="">Semua Cabang</option>
                            @foreach ($cabangs as $cabang)
                                <option
                                    value="{{ $cabang->id }}"
                                    @selected((string) request('cabang_id') === (string) $cabang->id)
                                >
                                    {{ $cabang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- TOMBOL FILTER --}}
                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-blue-600 px-4 py-2 font-medium text-white transition hover:bg-blue-700"
                    >
                        <i class="fa-solid fa-filter"></i>
                        Filter
                    </button>

                    <a
                        href="{{ route('transaksi.index') }}"
                        class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-lg bg-gray-100 px-4 py-2 font-medium text-gray-700 transition hover:bg-gray-200"
                    >
                        <i class="fa-solid fa-rotate-left"></i>
                        Reset
                    </a>
                </div>

            </div>
        </form>
    </div>


    {{-- TABEL RIWAYAT TRANSAKSI --}}
    <div class="overflow-hidden rounded-xl border border-gray-400 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] border-collapse border border-gray-400 text-left text-sm">

                <thead class="bg-gray-100 font-bold text-gray-700">
                    <tr>
                        <th class="border border-gray-400 px-4 py-4">
                            No Referensi
                        </th>
                        <th class="border border-gray-400 px-4 py-4">
                            Tanggal
                        </th>
                        <th class="border border-gray-400 px-4 py-4">
                            Pelanggan
                        </th>
                        <th class="border border-gray-400 px-4 py-4">
                            Cabang
                        </th>
                        <th class="border border-gray-400 px-4 py-4">
                            Nama Produk
                        </th>
                        <th class="border border-gray-400 px-4 py-4 text-right">
                            Qty
                        </th>
                        <th class="border border-gray-400 px-4 py-4">
                            Satuan
                        </th>
                        <th class="border border-gray-400 px-4 py-4 text-right">
                            Harga
                        </th>
                        <th class="border border-gray-400 px-4 py-4 text-right">
                            Subtotal
                        </th>
                        <th class="border border-gray-400 px-4 py-4 text-right">
                            Total Transaksi
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($transaksis as $trx)
                        @php
                            $details = $trx->detailTransaksis;
                            $jumlahBaris = $details->count();
                        @endphp

                        @forelse ($details as $detail)
                            <tr class="transition hover:bg-blue-50">

                                {{-- INFORMASI TRANSAKSI --}}
                                @if ($loop->first)
                                    <td
                                        rowspan="{{ $jumlahBaris }}"
                                        class="border border-gray-400 px-4 py-4 align-middle font-bold text-blue-600 whitespace-nowrap"
                                    >
                                        {{ $trx->no_referensi }}
                                    </td>

                                    <td
                                        rowspan="{{ $jumlahBaris }}"
                                        class="border border-gray-400 px-4 py-4 align-middle whitespace-nowrap"
                                    >
                                        {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}
                                    </td>

                                    <td
                                        rowspan="{{ $jumlahBaris }}"
                                        class="border border-gray-400 px-4 py-4 align-middle font-medium text-gray-800"
                                    >
                                        {{ $trx->nama_pelanggan }}
                                    </td>

                                    <td
                                        rowspan="{{ $jumlahBaris }}"
                                        class="border border-gray-400 px-4 py-4 align-middle"
                                    >
                                        {{ $trx->cabang->nama ?? '-' }}
                                    </td>
                                @endif

                                {{-- NAMA PRODUK --}}
                                <td class="border border-gray-400 px-4 py-3 align-middle text-gray-800">
                                    {{ $detail->produk->nama_produk ?? 'Produk tidak ditemukan' }}
                                </td>

                                {{-- QUANTITY --}}
                                <td class="border border-gray-400 px-4 py-3 text-right align-middle whitespace-nowrap">
                                    @php
                                        $satuanProduk = strtolower(
                                            trim($detail->produk->satuan ?? '')
                                        );

                                        $meteran = $satuanProduk === 'm2'
                                            || $satuanProduk === 'meter persegi'
                                            || str_contains($satuanProduk, 'm²');

                                        $qty = $meteran && (float) $detail->luas > 0
                                            ? (float) $detail->luas
                                            : (float) $detail->jumlah;
                                    @endphp

                                    {{ rtrim(rtrim(number_format($qty, 2, ',', '.'), '0'), ',') }}
                                </td>

                                {{-- SATUAN --}}
                                <td class="border border-gray-400 px-4 py-3 align-middle whitespace-nowrap">
                                    {{ $detail->produk->satuan ?? '-' }}
                                </td>

                                {{-- HARGA --}}
                                <td class="border border-gray-400 px-4 py-3 text-right align-middle whitespace-nowrap">
                                    Rp {{ number_format((float) $detail->harga_satuan, 0, ',', '.') }}
                                </td>

                                {{-- SUBTOTAL --}}
                                <td class="border border-gray-400 px-4 py-3 text-right align-middle whitespace-nowrap">
                                    Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}
                                </td>

                                {{-- TOTAL TRANSAKSI --}}
                                @if ($loop->first)
                                    <td
                                        rowspan="{{ $jumlahBaris }}"
                                        class="border border-gray-400 bg-blue-50 px-4 py-4 text-right align-middle font-bold text-gray-800 whitespace-nowrap"
                                    >
                                        Rp {{ number_format((float) $trx->total_transaksi, 0, ',', '.') }}
                                    </td>
                                @endif

                            </tr>

                        @empty
                            {{-- TRANSAKSI TANPA DETAIL PRODUK --}}
                            <tr>
                                <td class="border border-gray-400 px-4 py-4 font-bold text-blue-600">
                                    {{ $trx->no_referensi }}
                                </td>

                                <td class="border border-gray-400 px-4 py-4 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}
                                </td>

                                <td class="border border-gray-400 px-4 py-4">
                                    {{ $trx->nama_pelanggan }}
                                </td>

                                <td class="border border-gray-400 px-4 py-4">
                                    {{ $trx->cabang->nama ?? '-' }}
                                </td>

                                <td
                                    colspan="5"
                                    class="border border-gray-400 px-4 py-4 text-gray-400"
                                >
                                    Belum ada detail produk.
                                </td>

                                <td class="border border-gray-400 px-4 py-4 text-right font-bold whitespace-nowrap">
                                    Rp {{ number_format((float) $trx->total_transaksi, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforelse

                    @empty
                        <tr>
                            <td
                                colspan="10"
                                class="border border-gray-400 px-6 py-10 text-center text-gray-400"
                            >
                                Belum ada transaksi untuk filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</x-admin-layout>
