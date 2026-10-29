<x-admin-layout-stisla>
    {{-- HEADER HALAMAN --}}
    <div
        class="mb-[30px] flex flex-wrap items-center gap-x-4 gap-y-2 rounded-[3px] bg-white px-[30px] py-5 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
        <h1 class="text-2xl font-bold text-[#34395e]">Riwayat Transaksi</h1>

        <ol class="ml-auto flex items-center text-[13px] text-[#6c757d]">
            <li>
                <a href="{{ route('transaksi.index') }}" class="hover:text-[#6777ef]">Transaksi</a>
            </li>
            <li
                class="font-semibold text-[#6777ef] before:px-2 before:font-normal before:text-[#6c757d] before:content-['/']">
                Riwayat Transaksi
            </li>
        </ol>
    </div>

    <div>

        {{-- DESKRIPSI DAN EXPORT --}}
        <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
            <p class="text-[#6c757d]">
                Riwayat transaksi dan rincian produk.
            </p>

            <div class="flex flex-wrap gap-1">
                <a href="{{ route('transaksi.export.pdf', request()->query()) }}"
                    class="inline-flex items-center rounded-[3px] bg-[#fc544b] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#fb160a]">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>

                <a href="{{ route('transaksi.export.excel', request()->query()) }}"
                    class="inline-flex items-center rounded-[3px] bg-[#54ca68] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#3fb556]">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @if (session('success'))
            <div role="alert"
                class="mb-4 flex items-center rounded-[3px] border-l-[3px] border-black/20 bg-[#54ca68] px-5 py-2 text-white">
                <span class="min-w-0">{{ session('success') }}</span>
                <button type="button" aria-label="Tutup" onclick="this.closest('[role=alert]').remove()"
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div role="alert"
                class="mb-4 flex items-center rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2 text-white">
                <span class="min-w-0">{{ session('error') }}</span>
                <button type="button" aria-label="Tutup" onclick="this.closest('[role=alert]').remove()"
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">&times;</button>
            </div>
        @endif

        {{-- FILTER TRANSAKSI: RINGKAS --}}
        <div class="mb-2 rounded-[3px] bg-white px-6 py-4 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
            <form action="{{ route('transaksi.index') }}" method="GET"
                class="grid grid-cols-1 items-end gap-x-2.5 gap-y-2 md:grid-cols-2 lg:grid-cols-4">

                {{-- TANGGAL MULAI --}}
                <div>
                    <label for="tanggal_mulai"
                        class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Tanggal Mulai
                    </label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                        value="{{ request('tanggal_mulai') }}"
                        class="h-[31px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-2 text-[13px] text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                </div>

                {{-- TANGGAL AKHIR --}}
                <div>
                    <label for="tanggal_akhir"
                        class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Tanggal Akhir
                    </label>
                    <input type="date" id="tanggal_akhir" name="tanggal_akhir"
                        value="{{ request('tanggal_akhir') }}"
                        class="h-[31px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-2 text-[13px] text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                </div>

                {{-- CABANG --}}
                @if (auth()->user()->role === 'Admin Pusat')
                    <div>
                        <label for="cabang_id"
                            class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Cabang
                        </label>
                        <select id="cabang_id" name="cabang_id"
                            class="h-[31px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-2 text-[13px] text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                            <option value="">Semua Cabang</option>

                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->id }}" @selected((string) request('cabang_id') === (string) $cabang->id)>
                                    {{ $cabang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- TOMBOL --}}
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>

                    <a href="{{ route('transaksi.index') }}"
                        class="inline-flex items-center rounded-[3px] bg-[#e3eaef] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                        <i class="fas fa-redo mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- TABEL RIWAYAT TRANSAKSI --}}
        <div class="overflow-hidden rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
            <div class="flex min-h-[70px] items-center bg-[#6777ef] px-[25px] py-2">
                <h4 class="text-base font-semibold leading-7 text-white">
                    <i class="fas fa-table mr-2"></i>
                    Data Riwayat Transaksi
                </h4>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead>
                        <tr class="text-[10px] font-bold uppercase tracking-[1.3px] text-[#666]">
                            <th class="h-[45px] whitespace-nowrap bg-black/[0.04] px-[15px] text-center">#</th>
                            <th class="h-[45px] whitespace-nowrap bg-black/[0.04] px-[15px] text-left">No Referensi</th>
                            <th class="h-[45px] whitespace-nowrap bg-black/[0.04] px-[15px] text-left">Tanggal</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Pelanggan</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Cabang</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Nama Produk</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-right">Qty</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Satuan</th>
                            <th class="h-[45px] whitespace-nowrap bg-black/[0.04] px-[15px] text-right">Total Transaksi
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
                                <tr
                                    class="border-b {{ $loop->last ? 'border-[#e3eaef]' : 'border-[#f2f2f2]' }}">
                                    @if ($loop->first)
                                        {{-- NOMOR URUT --}}
                                        <td rowspan="{{ $jumlahBaris }}"
                                            class="px-[15px] py-3 text-center align-middle text-[#6c757d]">
                                            {{ $loop->parent->iteration }}
                                        </td>

                                        {{-- NO REFERENSI --}}
                                        <td rowspan="{{ $jumlahBaris }}"
                                            class="whitespace-nowrap px-[15px] py-3 align-middle font-bold text-[#6777ef]">
                                            {{ $trx->no_referensi }}
                                        </td>

                                        {{-- TANGGAL --}}
                                        <td rowspan="{{ $jumlahBaris }}"
                                            class="whitespace-nowrap px-[15px] py-3 align-middle">
                                            {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}
                                        </td>

                                        {{-- PELANGGAN --}}
                                        <td rowspan="{{ $jumlahBaris }}" class="px-[15px] py-3 align-middle">
                                            {{ $trx->nama_pelanggan }}
                                        </td>

                                        {{-- CABANG --}}
                                        <td rowspan="{{ $jumlahBaris }}" class="px-[15px] py-3 align-middle">
                                            {{ $trx->cabang->nama ?? '-' }}
                                        </td>
                                    @endif

                                    {{-- NAMA PRODUK --}}
                                    <td class="px-[15px] py-3 align-middle">
                                        {{ $detail->produk->nama_produk ?? 'Produk tidak ditemukan' }}
                                    </td>

                                    {{-- QUANTITY --}}
                                    <td class="whitespace-nowrap px-[15px] py-3 text-right align-middle">
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

                                    {{-- SATUAN --}}
                                    <td class="whitespace-nowrap px-[15px] py-3 align-middle">
                                        {{ $detail->produk->satuan ?? '-' }}
                                    </td>

                                    {{-- TOTAL TRANSAKSI --}}
                                    @if ($loop->first)
                                        <td rowspan="{{ $jumlahBaris }}"
                                            class="whitespace-nowrap px-[15px] py-3 text-right align-middle font-bold">
                                            Rp {{ number_format((float) $trx->total_transaksi, 0, ',', '.') }}
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr class="border-b border-[#e3eaef]">
                                    <td class="px-[15px] py-3 text-center align-middle text-[#6c757d]">
                                        {{ $loop->parent->iteration }}
                                    </td>

                                    <td
                                        class="whitespace-nowrap px-[15px] py-3 align-middle font-bold text-[#6777ef]">
                                        {{ $trx->no_referensi }}
                                    </td>

                                    <td class="whitespace-nowrap px-[15px] py-3 align-middle">
                                        {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}
                                    </td>

                                    <td class="px-[15px] py-3 align-middle">
                                        {{ $trx->nama_pelanggan }}
                                    </td>

                                    <td class="px-[15px] py-3 align-middle">
                                        {{ $trx->cabang->nama ?? '-' }}
                                    </td>

                                    <td colspan="3" class="px-[15px] py-3 align-middle text-[#6c757d]">
                                        Belum ada detail produk.
                                    </td>

                                    <td
                                        class="whitespace-nowrap px-[15px] py-3 text-right align-middle font-bold">
                                        Rp {{ number_format((float) $trx->total_transaksi, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforelse
                        @empty
                            <tr>
                                <td colspan="9" class="px-[15px] py-6 text-center text-[#6c757d]">
                                    <i class="fas fa-receipt text-[2em]"></i>
                                    <p>
                                        Belum ada transaksi untuk filter yang dipilih.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-admin-layout-stisla>