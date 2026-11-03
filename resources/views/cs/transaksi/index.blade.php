<x-cs-layout>

    <div class="w-full space-y-6">

        {{-- ================= HEADER HALAMAN ================= --}}
        <div
            class="rounded-2xl border-l-4 border-[#041C88] bg-white dark:bg-[#111827] border-y border-r border-transparent dark:border-gray-800 p-6 shadow-sm transition-colors duration-300">
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                        Riwayat Transaksi
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Kelola dan lihat rincian transaksi penjualan pelanggan.
                    </p>
                </div>

                <div
                    class="flex items-center gap-2 text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 px-3 py-1.5 rounded-lg">
                    <span class="text-[#041C88] dark:text-blue-400">Transaksi</span>
                    <span>/</span>
                    <span>Riwayat Transaksi</span>
                </div>
            </div>
        </div>

        {{-- ================= KONTEN HALAMAN ================= --}}
        <div class="space-y-4">

            {{-- TOMBOL EXPORT --}}
            <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <p class="text-sm font-medium text-[#6c757d] dark:text-gray-400">
                    Daftar riwayat transaksi dan rincian item.
                </p>

                <div class="flex flex-wrap gap-2.5">
                    <a href="{{ route('transaksi.export.pdf', request()->query()) }}"
                        class="inline-flex items-center rounded-xl bg-[#fc544b] hover:bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-red-500/20 transition hover:-translate-y-0.5">
                        <i class="fas fa-file-pdf mr-2"></i>
                        Export PDF
                    </a>

                    <a href="{{ route('transaksi.export.excel', request()->query()) }}"
                        class="inline-flex items-center rounded-xl bg-[#47c363] hover:bg-green-600 px-4 py-2 text-xs font-bold text-white shadow-md shadow-green-500/20 transition hover:-translate-y-0.5">
                        <i class="fas fa-file-excel mr-2"></i>
                        Export Excel
                    </a>
                </div>
            </div>

            {{-- NOTIFIKASI SUKSES --}}
            @if (session('success'))
                <div class="flex items-center justify-between gap-3 rounded-xl border border-emerald-200 dark:border-emerald-900/50 bg-emerald-50 dark:bg-emerald-950/20 px-4 py-3 text-sm font-medium text-emerald-800 dark:text-emerald-300 shadow-sm"
                    data-alert>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-500 text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" data-close-alert
                        class="text-lg font-bold leading-none text-emerald-600 dark:text-emerald-400 hover:text-emerald-900">
                        &times;
                    </button>
                </div>
            @endif

            {{-- NOTIFIKASI ERROR --}}
            @if (session('error'))
                <div class="flex items-center justify-between gap-3 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50 dark:bg-rose-950/20 px-4 py-3 text-sm font-medium text-rose-800 dark:text-rose-300 shadow-sm"
                    data-alert>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-rose-500 text-base"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" data-close-alert
                        class="text-lg font-bold leading-none text-rose-600 dark:text-rose-400 hover:text-rose-900">
                        &times;
                    </button>
                </div>
            @endif

            {{-- ================= FILTER TRANSAKSI ================= --}}
            <div
                class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#111827] p-6 shadow-sm transition-colors duration-300">
                <form action="{{ route('transaksi.index') }}" method="GET">
                    <div class="grid grid-cols-1 items-end gap-4 md:grid-cols-2 lg:grid-cols-4">

                        {{-- TANGGAL MULAI --}}
                        <div>
                            <label for="tanggal_mulai"
                                class="mb-2 block text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                Tanggal Mulai
                            </label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                                value="{{ request('tanggal_mulai') }}"
                                class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-800 dark:text-gray-100 outline-none transition focus:border-[#041C88] dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30">
                        </div>

                        {{-- TANGGAL AKHIR --}}
                        <div>
                            <label for="tanggal_akhir"
                                class="mb-2 block text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                Tanggal Akhir
                            </label>
                            <input type="date" id="tanggal_akhir" name="tanggal_akhir"
                                value="{{ request('tanggal_akhir') }}"
                                class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-800 dark:text-gray-100 outline-none transition focus:border-[#041C88] dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30">
                        </div>

                        {{-- FILTER CABANG ADMIN PUSAT --}}
                        @if (auth()->user()->role === 'Admin Pusat')
                            <div>
                                <label for="cabang_id"
                                    class="mb-2 block text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                                    Cabang
                                </label>
                                <select id="cabang_id" name="cabang_id"
                                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3.5 py-2.5 text-xs text-gray-800 dark:text-gray-100 outline-none transition focus:border-[#041C88] dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30">
                                    <option value="">Semua Cabang</option>
                                    @foreach ($cabangs as $cabang)
                                        <option value="{{ $cabang->id }}" @selected((string) request('cabang_id') === (string) $cabang->id)>
                                            {{ $cabang->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- TOMBOL FILTER DAN RESET --}}
                        <div class="flex items-center gap-2">
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-[#041C88] hover:bg-[#122bad] dark:bg-blue-600 dark:hover:bg-blue-700 px-4 py-2.5 text-xs font-bold text-white shadow-md transition hover:-translate-y-0.5">
                                <i class="fas fa-filter mr-2"></i>
                                Filter
                            </button>

                            <a href="{{ route('transaksi.index') }}"
                                class="inline-flex items-center justify-center rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 shadow-sm transition hover:bg-gray-200 dark:hover:bg-gray-700">
                                <i class="fas fa-redo mr-2"></i>
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- ================= TABEL RIWAYAT TRANSAKSI ================= --}}
            <div
                class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-[#111827] shadow-sm transition-colors duration-300">

                {{-- HEADER TABEL --}}
                <div class="flex items-center gap-3 bg-[#041C88] dark:bg-[#0f172a] px-6 py-4 text-white">
                    <i class="fas fa-table text-sm"></i>
                    <h4 class="text-sm font-extrabold tracking-wide">
                        Data Riwayat Transaksi
                    </h4>
                </div>

                {{-- ISI TABEL --}}
                <div class="w-full overflow-x-auto">
                    <table class="w-full min-w-[1150px] border-collapse text-xs">

                        {{-- HEADER KOLOM --}}
                        <thead
                            class="bg-gray-100 dark:bg-gray-800/70 text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-800">
                            <tr>
                                <th
                                    class="whitespace-nowrap border-r border-gray-200 dark:border-gray-800 px-4 py-3.5 text-center font-bold">
                                    #</th>
                                <th
                                    class="whitespace-nowrap border-r border-gray-200 dark:border-gray-800 px-4 py-3.5 text-left font-bold">
                                    No Referensi</th>
                                <th
                                    class="whitespace-nowrap border-r border-gray-200 dark:border-gray-800 px-4 py-3.5 text-left font-bold">
                                    Tanggal</th>
                                <th
                                    class="border-r border-gray-200 dark:border-gray-800 px-4 py-3.5 text-left font-bold">
                                    Pelanggan</th>
                                <th
                                    class="border-r border-gray-200 dark:border-gray-800 px-4 py-3.5 text-left font-bold">
                                    Nomor HP</th>
                                <th
                                    class="border-r border-gray-200 dark:border-gray-800 px-4 py-3.5 text-left font-bold">
                                    Cabang</th>
                                <th
                                    class="border-r border-gray-200 dark:border-gray-800 px-4 py-3.5 text-left font-bold">
                                    Nama Produk</th>
                                <th
                                    class="whitespace-nowrap border-r border-gray-200 dark:border-gray-800 px-4 py-3.5 text-center font-bold">
                                    Qty</th>
                                <th
                                    class="border-r border-gray-200 dark:border-gray-800 px-4 py-3.5 text-left font-bold">
                                    Satuan</th>
                                <th class="whitespace-nowrap px-4 py-3.5 text-right font-bold">Total Transaksi</th>
                            </tr>
                        </thead>

                        {{-- DATA TRANSAKSI --}}
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-700 dark:text-gray-300">

                            @forelse ($transaksis as $trx)

                                @php
                                    $details = $trx->detailTransaksis;
                                    $jumlahBaris = $details->count();
                                    $nomorTransaksi = $loop->iteration;
                                @endphp

                                @forelse ($details as $detail)
                                    <tr class="transition-colors hover:bg-blue-50/50 dark:hover:bg-gray-800/50">

                                        @if ($loop->first)
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="border-r border-gray-200 dark:border-gray-800 px-4 py-4 text-center align-middle font-medium text-gray-500 dark:text-gray-400">
                                                {{ $nomorTransaksi }}
                                            </td>

                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="whitespace-nowrap border-r border-gray-200 dark:border-gray-800 px-4 py-4 align-middle font-bold text-[#041C88] dark:text-blue-400">
                                                {{ $trx->no_referensi }}
                                            </td>

                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="whitespace-nowrap border-r border-gray-200 dark:border-gray-800 px-4 py-4 align-middle font-medium text-gray-600 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}
                                            </td>

                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="border-r border-gray-200 dark:border-gray-800 px-4 py-4 align-middle font-bold text-gray-800 dark:text-gray-200">
                                                {{ $trx->nama_pelanggan }}
                                            </td>

                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="border-r border-gray-200 dark:border-gray-800 px-4 py-4 align-middle text-gray-600 dark:text-gray-400">
                                                {{ $trx->nomor_hp ?? '-' }}
                                            </td>

                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="border-r border-gray-200 dark:border-gray-800 px-4 py-4 align-middle text-gray-600 dark:text-gray-400">
                                                {{ $trx->cabang->nama ?? '-' }}
                                            </td>
                                        @endif

                                        <td
                                            class="border-r border-gray-200 dark:border-gray-800 px-4 py-4 align-middle font-medium text-gray-800 dark:text-gray-200">
                                            {{ $detail->produk->nama_produk ?? 'Produk tidak ditemukan' }}
                                        </td>

                                        <td
                                            class="whitespace-nowrap border-r border-gray-200 dark:border-gray-800 px-4 py-4 text-center align-middle font-bold text-gray-800 dark:text-gray-200">
                                            @php
                                                $satuanProduk = strtolower(trim($detail->produk->satuan ?? ''));
                                                $meteran =
                                                    $satuanProduk === 'm2' ||
                                                    $satuanProduk === 'meter persegi' ||
                                                    str_contains($satuanProduk, 'm²');
                                                $qty =
                                                    $meteran && (float) $detail->luas > 0
                                                        ? (float) $detail->luas
                                                        : (float) $detail->jumlah;
                                            @endphp
                                            {{ rtrim(rtrim(number_format($qty, 2, ',', '.'), '0'), ',') }}
                                        </td>

                                        <td
                                            class="whitespace-nowrap border-r border-gray-200 dark:border-gray-800 px-4 py-4 align-middle text-gray-600 dark:text-gray-400">
                                            {{ $detail->produk->satuan ?? '-' }}
                                        </td>

                                        @if ($loop->first)
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="whitespace-nowrap px-4 py-4 text-right align-middle font-extrabold text-gray-900 dark:text-white">
                                                Rp {{ number_format((float) $trx->total_transaksi, 0, ',', '.') }}
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr class="hover:bg-blue-50/50 dark:hover:bg-gray-800/50">
                                        <td
                                            class="border-r border-gray-200 dark:border-gray-800 px-4 py-4 text-center font-medium">
                                            {{ $nomorTransaksi }}</td>
                                        <td
                                            class="whitespace-nowrap border-r border-gray-200 dark:border-gray-800 px-4 py-4 font-bold text-[#041C88] dark:text-blue-400">
                                            {{ $trx->no_referensi }}</td>
                                        <td
                                            class="whitespace-nowrap border-r border-gray-200 dark:border-gray-800 px-4 py-4 text-gray-600 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}</td>
                                        <td
                                            class="border-r border-gray-200 dark:border-gray-800 px-4 py-4 font-bold text-gray-800 dark:text-gray-200">
                                            {{ $trx->nama_pelanggan }}</td>
                                        <td
                                            class="border-r border-gray-200 dark:border-gray-800 px-4 py-4 text-gray-600 dark:text-gray-400">
                                            {{ $trx->nomor_hp ?? '-' }}</td>
                                        <td
                                            class="border-r border-gray-200 dark:border-gray-800 px-4 py-4 text-gray-600 dark:text-gray-400">
                                            {{ $trx->cabang->nama ?? '-' }}</td>
                                        <td colspan="3"
                                            class="border-r border-gray-200 dark:border-gray-800 px-4 py-4 text-gray-400">
                                            Belum ada detail produk.</td>
                                        <td
                                            class="whitespace-nowrap px-4 py-4 text-right font-extrabold text-gray-900 dark:text-white">
                                            Rp {{ number_format((float) $trx->total_transaksi, 0, ',', '.') }}</td>
                                    </tr>
                                @endforelse

                            @empty
                                <tr>
                                    <td colspan="10" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i
                                                class="fas fa-receipt mb-3 text-3xl text-gray-300 dark:text-gray-600"></i>
                                            <p class="font-bold text-gray-600 dark:text-gray-300">Belum ada transaksi
                                            </p>
                                            <p class="mt-1 text-xs text-gray-400">Belum ada transaksi untuk filter yang
                                                dipilih.</p>
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

    <script>
        document.addEventListener('click', function(event) {
            const tombol = event.target.closest('[data-close-alert]');
            if (!tombol) return;
            const alert = tombol.closest('[data-alert]');
            if (alert) alert.remove();
        });
    </script>

</x-cs-layout>
