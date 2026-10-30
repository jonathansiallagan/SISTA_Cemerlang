
<x-cs-layout :fullwidth="true">

    {{-- LATAR BELAKANG: FOTO PERCETAKAN --}}
    <div class="flex min-h-[calc(100vh-134px)] items-center justify-center px-5 py-10"
         style="background-image: linear-gradient(rgba(15, 23, 42, 0.45), rgba(15, 23, 42, 0.45)), url('{{ asset('images/percetakan.jpg') }}'); background-position: center; background-size: cover; background-repeat: no-repeat;">

        {{-- PANEL TRANSAKSI TERBARU --}}
        <div class="w-full max-w-[1060px] overflow-hidden rounded-[20px] border border-white/50 bg-white/95 shadow-2xl backdrop-blur-xl">

            {{-- ================= HEADER PANEL ================= --}}
            <div class="flex items-center justify-between gap-3 bg-gradient-to-r from-[#6777ef] to-[#8191f7] px-8 py-6">

                <div class="flex min-w-0 items-center gap-4">

                    {{-- ICON --}}
                    <div class="flex h-[50px] w-[50px] shrink-0 items-center justify-center rounded-xl bg-white/20 text-lg text-white">
                        <i class="fas fa-receipt"></i>
                    </div>

                    {{-- JUDUL --}}
                    <div>
                        <h5 class="m-0 text-lg font-bold leading-tight text-white">
                            Transaksi Terbaru
                        </h5>

                        @if ($namaCabang !== 'Belum ditentukan')
                            <span class="mt-1 block text-xs text-white/80">
                                {{ $namaCabang }}
                            </span>
                        @endif
                    </div>

                </div>

                {{-- TANGGAL --}}
                <div class="shrink-0 whitespace-nowrap rounded-full bg-white/20 px-4 py-2 text-xs font-bold text-white">

                    <i class="far fa-calendar-alt mr-1"></i>

                    {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}

                </div>

            </div>


            {{-- ================= ISI PANEL ================= --}}
            <div class="w-full px-5 pb-3 pt-2 sm:px-8">

                {{-- TABEL TRANSAKSI --}}
                <div class="w-full overflow-x-auto">

                    <table class="w-full min-w-full table-auto border-collapse text-sm">

                        {{-- HEADER TABEL --}}
                        <thead>

                            <tr class="text-left">

                                <th class="whitespace-nowrap border-b border-gray-200 px-3 py-4 text-center text-[11px] font-bold uppercase tracking-wider text-[#98a6ad]">
                                    #
                                </th>

                                <th class="whitespace-nowrap border-b border-gray-200 px-3 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-[#98a6ad]">
                                    No. Referensi
                                </th>

                                <th class="whitespace-nowrap border-b border-gray-200 px-3 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-[#98a6ad]">
                                    Pelanggan
                                </th>

                                <th class="whitespace-nowrap border-b border-gray-200 px-3 py-4 text-left text-[11px] font-bold uppercase tracking-wider text-[#98a6ad]">
                                    Diinput Oleh
                                </th>

                                <th class="whitespace-nowrap border-b border-gray-200 px-3 py-4 text-right text-[11px] font-bold uppercase tracking-wider text-[#98a6ad]">
                                    Total
                                </th>

                            </tr>

                        </thead>


                        {{-- DATA TRANSAKSI --}}
                        <tbody>

                            @forelse ($transaksiTerbaru as $index => $t)

                                <tr class="transition-colors duration-200 hover:bg-[#f6f7ff]">

                                    <td class="whitespace-nowrap border-b border-gray-100 px-3 py-5 text-center align-middle text-[#34395e]">
                                        {{ $index + 1 }}
                                    </td>

                                    <td class="whitespace-nowrap border-b border-gray-100 px-3 py-5 align-middle text-[#34395e]">
                                        {{ $t->no_referensi ?? '-' }}
                                    </td>

                                    <td class="whitespace-nowrap border-b border-gray-100 px-3 py-5 align-middle text-[#34395e]">
                                        {{ $t->nama_pelanggan ?? '-' }}
                                    </td>

                                    <td class="whitespace-nowrap border-b border-gray-100 px-3 py-5 align-middle text-[#34395e]">
                                        {{ optional($t->user)->name ?? '-' }}
                                    </td>

                                    <td class="whitespace-nowrap border-b border-gray-100 px-3 py-5 text-right align-middle font-bold text-[#34395e]">

                                        Rp {{ number_format((float) $t->total_transaksi, 0, ',', '.') }}

                                    </td>

                                </tr>

                            @empty

                                {{-- JIKA BELUM ADA TRANSAKSI --}}
                                <tr>

                                    <td colspan="5" class="px-3 py-10 text-center">

                                        <div class="flex flex-col items-center gap-2">

                                            <i class="fas fa-inbox text-4xl text-gray-400"></i>

                                            <strong class="text-[15px] font-semibold text-[#34395e]">
                                                Belum ada transaksi
                                            </strong>

                                            <span class="text-xs text-[#98a6ad]">
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
            <div class="flex items-center justify-between gap-3 border-t border-[#eceff7] bg-[#f6f7fb]/90 px-8 py-4">

                {{-- JUMLAH TRANSAKSI --}}
                <span class="text-xs text-[#98a6ad]">

                    Menampilkan {{ $transaksiTerbaru->count() }} transaksi terakhir

                </span>


                {{-- TOMBOL AKSI --}}
                <div class="flex items-center gap-3">

                    {{-- RIWAYAT TRANSAKSI --}}
                    <a href="{{ route('transaksi.index') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-full border border-[#d9deef] bg-white px-5 py-2.5 text-[13px] font-bold text-[#34395e] no-underline transition duration-200 hover:-translate-y-0.5 hover:border-[#6777ef] hover:text-[#6777ef]">

                        <i class="fas fa-history"></i>

                        Riwayat Transaksi

                    </a>


                    {{-- TRANSAKSI BARU --}}
                    <a href="{{ route('transaksi.create') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-full bg-[#6777ef] px-5 py-2.5 text-[13px] font-bold text-white no-underline shadow-lg shadow-indigo-200 transition duration-200 hover:-translate-y-0.5 hover:bg-[#5565e0]">

                        <i class="fas fa-plus"></i>

                        Transaksi Baru

                    </a>

                </div>

            </div>

        </div>

    </div>


    {{-- ================= RESPONSIVE MOBILE ================= --}}
    <div class="hidden"></div>

</x-cs-layout>
