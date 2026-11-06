<x-admin-layout-stisla>

    @php
        $adalahPusat = Auth::user()->role === 'Admin Pusat';

        // Lebar kolom di layar besar. Ditulis lengkap agar terbaca oleh Tailwind.
        $kolom = $adalahPusat ? 'lg:col-span-3' : 'lg:col-span-4';
    @endphp

    {{-- HEADER HALAMAN --}}
    <div
        class="mb-[30px] flex flex-wrap items-center gap-x-4 gap-y-2 rounded-[3px] bg-white px-[30px] py-5 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
        <h1 class="text-2xl font-bold text-[#34395e]">Jurnal Umum</h1>

        <ol class="ml-auto flex items-center text-[13px] text-[#6c757d]">
            <li class="font-semibold text-[#041C88]">Jurnal Umum</li>
        </ol>
    </div>

    <div>
        {{-- FILTER PERIODE DAN CABANG --}}
        <x-filter-date-range :action="route('jurnal.index')" :startDate="$startDate" :endDate="$endDate" :cabangs="$cabangs ?? null"
            :showCabang="true" />

        {{-- NOTIFIKASI SUKSES --}}
        @if (session('success'))
            <div role="alert"
                class="mb-4 flex items-center rounded-[3px] border-l-[3px] border-black/20 bg-[#54ca68] px-5 py-2 text-white">
                <i class="fas fa-check-circle mr-1"></i>
                <span class="min-w-0">{{ session('success') }}</span>

                <button type="button" aria-label="Tutup" onclick="this.closest('[role=alert]').remove()"
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- TABEL JURNAL UMUM --}}
        <x-card-table title="Data Jurnal Umum" icon="fa-receipt">

            {{-- HEADER TABEL --}}
            <x-slot:head>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-center">#</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-left">Tanggal</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-left">No Referensi</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-left">Keterangan</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-left">Nama Akun</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-right">Debit</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-right">Kredit</th>
            </x-slot:head>

            {{-- ISI TABEL --}}
            @forelse ($jurnals as $jurnal)
                @php
                    $nomorJurnal = $loop->iteration;
                    $details = $jurnal->detailJurnals;

                    /*
                     * Kelompokkan detail berdasarkan keterangan detail.
                     * Jika keterangan detail kosong, gunakan keterangan
                     * header jurnal sebagai cadangan.
                     */
                    $groups = $details->groupBy(function ($detail) use ($jurnal) {
                        return $detail->keterangan ?: ($jurnal->keterangan ?: 'Tanpa Keterangan');
                    });

                    /*
                     * Urutkan kelompok agar keterangan HPP muncul
                     * setelah kelompok pendapatan/penjualan.
                     */
                    $groups = $groups->sortBy(function ($group, $keterangan) {
                        return str_starts_with(mb_strtolower(trim((string) $keterangan)), 'hpp ') ||
                            str_contains(mb_strtolower((string) $keterangan), 'beban pokok penjualan')
                            ? 1
                            : 0;
                    });

                    $jumlahBaris = $details->count();
                    $barisGlobal = 0;
                @endphp

                @foreach ($groups as $keterangan => $group)
                    @foreach ($group as $detail)
                        <tr
                            class="border-b {{ $barisGlobal === $jumlahBaris - 1 ? 'border-[#e3eaef]' : 'border-[#f2f2f2]' }}">

                            {{-- NOMOR, TANGGAL DAN REFERENSI SEKALI PER JURNAL --}}
                            @if ($barisGlobal === 0)
                                <td rowspan="{{ $jumlahBaris }}"
                                    class="px-[15px] py-3 text-center align-middle text-[#6c757d]">
                                    {{ $nomorJurnal }}
                                </td>

                                <td rowspan="{{ $jumlahBaris }}" class="whitespace-nowrap px-[15px] py-3 align-middle">
                                    {{ \Carbon\Carbon::parse($jurnal->tanggal)->translatedFormat('d F Y') }}
                                </td>

                                <td rowspan="{{ $jumlahBaris }}"
                                    class="px-[15px] py-3 align-middle font-bold text-[#041C88]">
                                    {{ $jurnal->no_referensi }}
                                </td>
                            @endif

                            {{-- KETERANGAN SEKALI PER KELOMPOK --}}
                            @if ($loop->first)
                                <td rowspan="{{ $group->count() }}" class="px-[15px] py-3 align-middle font-bold">
                                    {{ $keterangan }}
                                </td>
                            @endif

                            {{-- NAMA AKUN --}}
                            <td class="px-[15px] py-3 align-middle">
                                {{ $detail->akun->nama_akun ?? '-' }}
                            </td>

                            {{-- DEBIT --}}
                            <td class="whitespace-nowrap px-[15px] py-3 text-right align-middle">
                                {{ (float) $detail->debit > 0 ? 'Rp ' . number_format((float) $detail->debit, 0, ',', '.') : '-' }}
                            </td>

                            {{-- KREDIT --}}
                            <td class="whitespace-nowrap px-[15px] py-3 text-right align-middle">
                                {{ (float) $detail->kredit > 0 ? 'Rp ' . number_format((float) $detail->kredit, 0, ',', '.') : '-' }}
                            </td>
                        </tr>

                        @php
                            $barisGlobal++;
                        @endphp
                    @endforeach
                @endforeach
            @empty
                <tr>
                    <td colspan="7" class="px-[15px] py-6 text-center text-[#6c757d]">
                        <i class="fas fa-book-open text-[2em]"></i>

                        <p>
                            Belum ada pencatatan jurnal.
                        </p>
                    </td>
                </tr>
            @endforelse
        </x-card-table>
    </div>
</x-admin-layout-stisla>
