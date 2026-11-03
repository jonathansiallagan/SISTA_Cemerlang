<x-cs-layout :fullwidth="true">

    {{-- LATAR BELAKANG PERCETAKAN DENGAN OVERLAY DARK MODE --}}
    <div class="flex min-h-[calc(100vh-134px)] items-center justify-center px-4 py-10"
        style="background-image: linear-gradient(rgba(15, 23, 42, 0.55), rgba(15, 23, 42, 0.55)), url('{{ asset('images/percetakan.jpg') }}'); background-position: center; background-size: cover; background-repeat: no-repeat;">

        {{-- PANEL TRANSAKSI TERBARU --}}
        <div
            class="w-full max-w-[1060px] overflow-hidden rounded-2xl border border-white/30 dark:border-gray-800 bg-white/95 dark:bg-[#111827]/95 shadow-2xl backdrop-blur-xl transition-colors duration-300">

            {{-- ================= HEADER PANEL ================= --}}
            <div
                class="flex items-center justify-between gap-3 bg-gradient-to-r from-[#041C88] to-[#122bad] dark:from-[#0f172a] dark:to-[#1e293b] px-6 py-5 sm:px-8">

                <div class="flex min-w-0 items-center gap-4">
                    {{-- ICON --}}
                    <div
                        class="flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-xl bg-white/15 text-lg text-white shadow-inner">
                        <i class="fas fa-receipt"></i>
                    </div>

                    {{-- JUDUL --}}
                    <div>
                        <h5 class="m-0 text-lg font-extrabold leading-tight text-white tracking-tight">
                            Transaksi Terbaru
                        </h5>

                        @if ($namaCabang !== 'Belum ditentukan')
                            <span class="mt-0.5 block text-xs text-white/80 font-medium">
                                {{ $namaCabang }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- TANGGAL --}}
                <div
                    class="shrink-0 whitespace-nowrap rounded-full bg-white/15 px-4 py-1.5 text-xs font-bold text-white border border-white/10">
                    <i class="far fa-calendar-alt mr-1.5"></i>
                    {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                </div>
            </div>

            {{-- ================= ISI PANEL ================= --}}
            <div class="w-full px-4 pb-3 pt-2 sm:px-8">

                {{-- TABEL TRANSAKSI --}}
                <div class="w-full overflow-x-auto">
                    <table class="w-full min-w-full table-auto border-collapse text-sm">

                        {{-- HEADER TABEL --}}
                        <thead>
                            <tr class="text-left border-b border-gray-200 dark:border-gray-800">
                                <th
                                    class="whitespace-nowrap px-3 py-4 text-center text-[11px] font-bold uppercase tracking-wider text-[#98a6ad] dark:text-gray-400">
                                    #
                                </th>
                                <th
                                    class="whitespace-nowrap px-3 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-[#98a6ad] dark:text-gray-400">
                                    No. Referensi
                                </th>
                                <th
                                    class="whitespace-nowrap px-3 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-[#98a6ad] dark:text-gray-400">
                                    Pelanggan
                                </th>
                                <th
                                    class="whitespace-nowrap px-3 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-[#98a6ad] dark:text-gray-400">
                                    Diinput Oleh
                                </th>
                                <th
                                    class="whitespace-nowrap px-3 py-4 text-right text-[11px] font-bold uppercase tracking-wider text-[#98a6ad] dark:text-gray-400">
                                    Total
                                </th>
                            </tr>
                        </thead>

                        {{-- DATA TRANSAKSI --}}
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse ($transaksiTerbaru as $index => $t)
                                <tr class="transition-colors duration-150 hover:bg-[#f6f7ff] dark:hover:bg-gray-800/50">

                                    <td
                                        class="whitespace-nowrap px-3 py-4 text-center align-middle text-[#34395e] dark:text-gray-300 font-medium">
                                        {{ $index + 1 }}
                                    </td>

                                    <td
                                        class="whitespace-nowrap px-3 py-4 align-middle font-bold text-[#041C88] dark:text-blue-400">
                                        {{ $t->no_referensi ?? '-' }}
                                    </td>

                                    <td
                                        class="whitespace-nowrap px-3 py-4 align-middle text-[#34395e] dark:text-gray-200 font-medium">
                                        {{ $t->nama_pelanggan ?? '-' }}
                                    </td>

                                    <td
                                        class="whitespace-nowrap px-3 py-4 align-middle text-[#6c757d] dark:text-gray-400">
                                        {{ optional($t->user)->name ?? '-' }}
                                    </td>

                                    <td
                                        class="whitespace-nowrap px-3 py-4 text-right align-middle font-extrabold text-[#34395e] dark:text-gray-100">
                                        Rp {{ number_format((float) $t->total_transaksi, 0, ',', '.') }}
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-10 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <i class="fas fa-inbox text-4xl text-gray-300 dark:text-gray-600"></i>
                                            <strong class="text-[15px] font-bold text-[#34395e] dark:text-gray-300">
                                                Belum ada transaksi
                                            </strong>
                                            <span class="text-xs text-[#98a6ad] dark:text-gray-400">
                                                Transaksi yang dibuat pada tanggal ini akan tampil di sini.
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ================= FOOTER PANEL ================= --}}
            <div
                class="flex items-center justify-between gap-3 border-t border-[#eceff7] dark:border-gray-800 bg-[#f6f7fb]/90 dark:bg-gray-900/90 px-6 py-4 sm:px-8">

                <span class="text-xs font-semibold text-[#98a6ad] dark:text-gray-400">
                    Menampilkan {{ $transaksiTerbaru->count() }} transaksi terakhir
                </span>

                <div class="flex items-center gap-2.5">
                    {{-- RIWAYAT TRANSAKSI --}}
                    <a href="{{ route('transaksi.index') }}"
                        class="inline-flex items-center rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 shadow-sm transition hover:bg-gray-50 dark:hover:bg-gray-700">
                        <i class="fas fa-history mr-1.5"></i>
                        Riwayat Transaksi
                    </a>

                    {{-- TRANSAKSI BARU --}}
                    <a href="{{ route('transaksi.create') }}"
                        class="inline-flex items-center rounded-xl bg-[#041C88] hover:bg-[#122bad] dark:bg-blue-600 dark:hover:bg-blue-700 px-4 py-2 text-xs font-bold text-white shadow-md transition hover:-translate-y-0.5">
                        <i class="fas fa-plus mr-1.5"></i>
                        Transaksi Baru
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-cs-layout>
