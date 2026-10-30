
<x-cs-layout>

    <div class="w-full">

        {{-- ================= HEADER HALAMAN ================= --}}
        <div class="-mx-5 -mt-8 mb-8 border-t-2 border-[#6777ef] bg-white px-8 py-6 md:-mx-8">

            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">

                <h1 class="text-2xl font-semibold tracking-tight text-gray-800">
                    Riwayat Transaksi
                </h1>

                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <span class="font-medium text-[#6777ef]">Transaksi</span>
                    <span>/</span>
                    <span>Riwayat Transaksi</span>
                </div>

            </div>

        </div>


        {{-- ================= KONTEN HALAMAN ================= --}}
        <div class="space-y-4">

            {{-- DESKRIPSI DAN TOMBOL EXPORT --}}
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">

                <p class="text-sm text-gray-400">
                    Riwayat transaksi dan rincian produk.
                </p>

                <div class="flex flex-wrap gap-2">

                    <a href="{{ route('transaksi.export.pdf', request()->query()) }}"
                       class="inline-flex items-center rounded-md bg-[#fc544b] px-3 py-2 text-xs font-semibold text-white shadow-md transition hover:bg-red-600">

                        <i class="fas fa-file-pdf mr-2"></i>
                        Export PDF

                    </a>

                    <a href="{{ route('transaksi.export.excel', request()->query()) }}"
                       class="inline-flex items-center rounded-md bg-[#47c363] px-3 py-2 text-xs font-semibold text-white shadow-md transition hover:bg-green-600">

                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel

                    </a>

                </div>

            </div>


            {{-- NOTIFIKASI SUKSES --}}
            @if (session('success'))

                <div class="flex items-center justify-between gap-3 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700"
                     data-alert>

                    <div>
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('success') }}
                    </div>

                    <button type="button"
                            data-close-alert
                            class="text-lg font-bold leading-none">
                        &times;
                    </button>

                </div>

            @endif


            {{-- NOTIFIKASI ERROR --}}
            @if (session('error'))

                <div class="flex items-center justify-between gap-3 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                     data-alert>

                    <div>
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>

                    <button type="button"
                            data-close-alert
                            class="text-lg font-bold leading-none">
                        &times;
                    </button>

                </div>

            @endif


            {{-- ================= FILTER TRANSAKSI ================= --}}
            <div class="rounded-md border border-gray-100 bg-white p-6 shadow-sm">

                <form action="{{ route('transaksi.index') }}" method="GET">

                    <div class="grid grid-cols-1 items-end gap-3 md:grid-cols-2 lg:grid-cols-4">

                        {{-- TANGGAL MULAI --}}
                        <div>

                            <label for="tanggal_mulai"
                                   class="mb-1.5 block text-sm font-medium text-gray-700">
                                Tanggal Mulai
                            </label>

                            <input type="date"
                                   id="tanggal_mulai"
                                   name="tanggal_mulai"
                                   value="{{ request('tanggal_mulai') }}"
                                   class="w-full rounded-md border border-[#dce0ff] bg-white px-3 py-2 text-sm text-gray-700 outline-none transition focus:border-[#6777ef] focus:ring-2 focus:ring-blue-100">

                        </div>


                        {{-- TANGGAL AKHIR --}}
                        <div>

                            <label for="tanggal_akhir"
                                   class="mb-1.5 block text-sm font-medium text-gray-700">
                                Tanggal Akhir
                            </label>

                            <input type="date"
                                   id="tanggal_akhir"
                                   name="tanggal_akhir"
                                   value="{{ request('tanggal_akhir') }}"
                                   class="w-full rounded-md border border-[#dce0ff] bg-white px-3 py-2 text-sm text-gray-700 outline-none transition focus:border-[#6777ef] focus:ring-2 focus:ring-blue-100">

                        </div>


                        {{-- FILTER CABANG ADMIN PUSAT --}}
                        @if (auth()->user()->role === 'Admin Pusat')

                            <div>

                                <label for="cabang_id"
                                       class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Cabang
                                </label>

                                <select id="cabang_id"
                                        name="cabang_id"
                                        class="w-full rounded-md border border-[#dce0ff] bg-white px-3 py-2 text-sm text-gray-700 outline-none transition focus:border-[#6777ef] focus:ring-2 focus:ring-blue-100">

                                    <option value="">Semua Cabang</option>

                                    @foreach ($cabangs as $cabang)

                                        <option value="{{ $cabang->id }}"
                                            @selected((string) request('cabang_id') === (string) $cabang->id)>
                                            {{ $cabang->nama }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        @endif


                        {{-- TOMBOL FILTER DAN RESET --}}
                        <div class="flex items-center gap-2">

                            <button type="submit"
                                    class="inline-flex items-center rounded-md bg-[#6777ef] px-3 py-2 text-xs font-semibold text-white shadow-md transition hover:bg-[#5665d8]">

                                <i class="fas fa-filter mr-2"></i>
                                Filter

                            </button>

                            <a href="{{ route('transaksi.index') }}"
                               class="inline-flex items-center rounded-md bg-gray-100 px-3 py-2 text-xs font-semibold text-gray-700 shadow-sm transition hover:bg-gray-200">

                                <i class="fas fa-redo mr-2"></i>
                                Reset

                            </a>

                        </div>

                    </div>

                </form>

            </div>


            {{-- ================= TABEL RIWAYAT TRANSAKSI ================= --}}
            <div class="overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm">

                {{-- HEADER TABEL --}}
                <div class="flex items-center gap-3 bg-[#6777ef] px-7 py-6 text-white">

                    <i class="fas fa-table"></i>

                    <h4 class="text-base font-semibold">
                        Data Riwayat Transaksi
                    </h4>

                </div>


                {{-- ISI TABEL --}}
                <div class="w-full overflow-x-auto">

                    <table class="w-full min-w-[1150px] border-collapse text-sm">

                        {{-- HEADER KOLOM --}}
                        <thead class="bg-[#f3f3f3]">

                            <tr class="text-gray-600">

                                <th class="whitespace-nowrap border border-gray-300 px-4 py-4 text-center font-medium">
                                    #
                                </th>

                                <th class="whitespace-nowrap border border-gray-300 px-4 py-4 text-left font-medium">
                                    No Referensi
                                </th>

                                <th class="whitespace-nowrap border border-gray-300 px-4 py-4 text-left font-medium">
                                    Tanggal
                                </th>

                                <th class="border border-gray-300 px-4 py-4 text-left font-medium">
                                    Pelanggan
                                </th>

                                <th class="border border-gray-300 px-4 py-4 text-left font-medium">
                                    Nomor HP
                                </th>

                                <th class="border border-gray-300 px-4 py-4 text-left font-medium">
                                    Cabang
                                </th>

                                <th class="border border-gray-300 px-4 py-4 text-left font-medium">
                                    Nama Produk
                                </th>

                                <th class="whitespace-nowrap border border-gray-300 px-4 py-4 text-center font-medium">
                                    Qty
                                </th>

                                <th class="border border-gray-300 px-4 py-4 text-left font-medium">
                                    Satuan
                                </th>

                                <th class="whitespace-nowrap border border-gray-300 px-4 py-4 text-right font-medium">
                                    Total Transaksi
                                </th>

                            </tr>

                        </thead>


                        {{-- DATA TRANSAKSI --}}
                        <tbody>

                            @forelse ($transaksis as $trx)

                                @php
                                    $details = $trx->detailTransaksis;
                                    $jumlahBaris = $details->count();
                                    $nomorTransaksi = $loop->iteration;
                                @endphp


                                @forelse ($details as $detail)

                                    <tr class="transition hover:bg-blue-50">

                                        @if ($loop->first)

                                            {{-- NOMOR URUT --}}
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="border border-gray-300 px-4 py-4 text-center align-middle text-gray-500">

                                                {{ $nomorTransaksi }}

                                            </td>


                                            {{-- NO REFERENSI --}}
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="whitespace-nowrap border border-gray-300 px-4 py-4 align-middle font-semibold text-[#6777ef]">

                                                {{ $trx->no_referensi }}

                                            </td>


                                            {{-- TANGGAL --}}
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="whitespace-nowrap border border-gray-300 px-4 py-4 align-middle text-gray-600">

                                                {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}

                                            </td>


                                            {{-- PELANGGAN --}}
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="border border-gray-300 px-4 py-4 align-middle font-medium text-gray-700">

                                                {{ $trx->nama_pelanggan }}

                                            </td>


                                            {{-- NOMOR HP --}}
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="border border-gray-300 px-4 py-4 align-middle text-gray-600">

                                                {{ $trx->nomor_hp ?? '-' }}

                                            </td>


                                            {{-- CABANG --}}
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="border border-gray-300 px-4 py-4 align-middle text-gray-600">

                                                {{ $trx->cabang->nama ?? '-' }}

                                            </td>

                                        @endif


                                        {{-- NAMA PRODUK --}}
                                        <td class="border border-gray-300 px-4 py-4 align-middle text-gray-700">

                                            {{ $detail->produk->nama_produk ?? 'Produk tidak ditemukan' }}

                                        </td>


                                        {{-- QUANTITY --}}
                                        <td class="whitespace-nowrap border border-gray-300 px-4 py-4 text-center align-middle text-gray-700">

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
                                        <td class="whitespace-nowrap border border-gray-300 px-4 py-4 align-middle text-gray-600">

                                            {{ $detail->produk->satuan ?? '-' }}

                                        </td>


                                        {{-- TOTAL TRANSAKSI --}}
                                        @if ($loop->first)

                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="whitespace-nowrap border border-gray-300 px-4 py-4 text-right align-middle font-bold text-gray-700">

                                                Rp {{ number_format((float) $trx->total_transaksi, 0, ',', '.') }}

                                            </td>

                                        @endif

                                    </tr>

                                @empty

                                    {{-- TRANSAKSI TANPA DETAIL PRODUK --}}
                                    <tr class="hover:bg-blue-50">

                                        <td class="border border-gray-300 px-4 py-4 text-center text-gray-500">
                                            {{ $nomorTransaksi }}
                                        </td>

                                        <td class="whitespace-nowrap border border-gray-300 px-4 py-4 font-semibold text-[#6777ef]">
                                            {{ $trx->no_referensi }}
                                        </td>

                                        <td class="whitespace-nowrap border border-gray-300 px-4 py-4 text-gray-600">
                                            {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}
                                        </td>

                                        <td class="border border-gray-300 px-4 py-4">
                                            {{ $trx->nama_pelanggan }}
                                        </td>

                                        <td class="border border-gray-300 px-4 py-4">
                                            {{ $trx->nomor_hp ?? '-' }}
                                        </td>

                                        <td class="border border-gray-300 px-4 py-4">
                                            {{ $trx->cabang->nama ?? '-' }}
                                        </td>

                                        <td colspan="3"
                                            class="border border-gray-300 px-4 py-4 text-gray-400">

                                            Belum ada detail produk.

                                        </td>

                                        <td class="whitespace-nowrap border border-gray-300 px-4 py-4 text-right font-bold">

                                            Rp {{ number_format((float) $trx->total_transaksi, 0, ',', '.') }}

                                        </td>

                                    </tr>

                                @endforelse


                            @empty

                                {{-- DATA KOSONG --}}
                                <tr>

                                    <td colspan="10"
                                        class="border border-gray-300 px-4 py-12 text-center">

                                        <div class="flex flex-col items-center">

                                            <i class="fas fa-receipt mb-3 text-3xl text-gray-300"></i>

                                            <p class="font-semibold text-gray-600">
                                                Belum ada transaksi
                                            </p>

                                            <p class="mt-1 text-xs text-gray-400">
                                                Belum ada transaksi untuk filter yang dipilih.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>


    {{-- ================= SCRIPT NOTIFIKASI ================= --}}
    <script>
        document.addEventListener('click', function (event) {

            const tombol = event.target.closest('[data-close-alert]');

            if (!tombol) return;

            const alert = tombol.closest('[data-alert]');

            if (alert) {
                alert.remove();
            }

        });
    </script>

</x-cs-layout>
