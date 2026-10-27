<x-admin-layout>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">
                Jurnal Umum
            </h1>
        </div>
    </div>

    
    {{-- Filter Periode dan Cabang --}}
    <form action="{{ route('jurnal.index') }}" method="GET"
        class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

        <h2 class="mb-1 text-base font-semibold text-slate-800">
            Filter Jurnal
        </h2>
        <p class="mb-4 text-sm text-slate-500">
            Pilih periode pencatatan jurnal yang ingin ditampilkan.
        </p>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2
                    {{ Auth::user()->role === 'Admin Pusat' ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }}">

            <div>
                <label for="start_date"
                    class="mb-2 block text-sm font-medium text-slate-700">
                    Dari Tanggal
                </label>
                <input type="date"
                    id="start_date"
                    name="start_date"
                    value="{{ $startDate }}"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5
                            text-sm focus:border-blue-500 focus:outline-none
                            focus:ring-2 focus:ring-blue-100">
            </div>

            <div>
                <label for="end_date"
                    class="mb-2 block text-sm font-medium text-slate-700">
                    Sampai Tanggal
                </label>
                <input type="date"
                    id="end_date"
                    name="end_date"
                    value="{{ $endDate }}"
                    min="{{ $startDate }}"
                    required
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5
                            text-sm focus:border-blue-500 focus:outline-none
                            focus:ring-2 focus:ring-blue-100">
            </div>

            @if (Auth::user()->role === 'Admin Pusat')
                <div>
                    <label for="cabang_id"
                        class="mb-2 block text-sm font-medium text-slate-700">
                        Cabang
                    </label>
                    <select id="cabang_id"
                            name="cabang_id"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5
                                text-sm focus:border-blue-500 focus:outline-none
                                focus:ring-2 focus:ring-blue-100">
                        <option value="">Semua Cabang</option>

                        @foreach ($cabangs as $cabang)
                            <option value="{{ $cabang->id }}"
                                {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                {{ $cabang->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="flex items-end gap-2">
                <button type="submit"
                        class="inline-flex flex-1 items-center justify-center gap-2
                            rounded-lg bg-blue-700 px-4 py-2.5 text-sm
                            font-semibold text-white hover:bg-blue-800">
                    <i class="fa-solid fa-filter"></i>
                    Filter
                </button>

                <a href="{{ route('jurnal.index') }}"
                class="inline-flex items-center justify-center rounded-lg
                        border border-slate-300 px-4 py-2.5 text-sm
                        font-semibold text-slate-700 hover:bg-slate-50">
                    Reset
                </a>
            </div>
        </div>
    </form>


    @if (session('success'))
        <div class="px-4 py-3 mb-4 text-green-700 bg-green-100 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden bg-white border border-gray-300 shadow-sm rounded-xl">
        <div class="py-3 font-bold text-center text-white bg-blue-600">
            DAFTAR JURNAL
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full min-w-212.5 text-sm text-left border-collapse">
                <thead class="font-bold text-gray-700 bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 border border-gray-300 w-27.5">
                            Tanggal
                        </th>
                        <th class="px-4 py-3 border border-gray-300 w-43.75">
                            No Referensi
                        </th>
                        <th class="px-4 py-3 border border-gray-300 min-w-55">
                            Keterangan
                        </th>
                        <th class="px-4 py-3 border border-gray-300 min-w-47.5">
                            Nama Akun
                        </th>
                        <th class="px-4 py-3 border border-gray-300 w-37.5 text-right">
                            Debit
                        </th>
                        <th class="px-4 py-3 border border-gray-300 w-37.5 text-right">
                            Kredit
                        </th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($jurnals as $jurnal)
                        @php
                            $details = $jurnal->detailJurnals;

                            /*
                             * Kelompokkan detail berdasarkan keterangan detail.
                             * Jika keterangan detail kosong, gunakan keterangan
                             * header jurnal sebagai cadangan.
                             */
                            $groups = $details->groupBy(function ($detail) use ($jurnal) {
                                return $detail->keterangan
                                    ?: ($jurnal->keterangan ?: 'Tanpa Keterangan');
                            });

                            /*
                             * Urutkan kelompok agar keterangan HPP muncul
                             * setelah kelompok pendapatan/penjualan.
                             */
                            $groups = $groups->sortBy(function ($group, $keterangan) {
                                return str_starts_with(
                                    mb_strtolower(trim((string) $keterangan)),
                                    'hpp '
                                ) || str_contains(
                                    mb_strtolower((string) $keterangan),
                                    'beban pokok penjualan'
                                ) ? 1 : 0;
                            });

                            $jumlahBaris = $details->count();
                            $barisGlobal = 0;
                        @endphp

                        @foreach ($groups as $keterangan => $group)
                            @foreach ($group as $detail)
                                <tr class="{{ $barisGlobal % 2 === 0 ? 'bg-white' : 'bg-blue-50' }} hover:bg-blue-100 transition-colors">

                                    {{-- Tanggal dan referensi sekali per jurnal --}}
                                    @if ($barisGlobal === 0)
                                        <td rowspan="{{ $jumlahBaris }}"
                                            class="px-4 py-3 align-middle border border-gray-300">
                                            {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d M Y') }}
                                        </td>

                                        <td rowspan="{{ $jumlahBaris }}"
                                            class="px-4 py-3 font-medium text-blue-600 align-middle border border-gray-300 wrap-break-words">
                                            {{ $jurnal->no_referensi }}
                                        </td>
                                    @endif

                                    {{-- Keterangan sekali per kelompok --}}
                                    @if ($loop->first)
                                        <td rowspan="{{ $group->count() }}"
                                            class="px-4 py-3 font-medium text-gray-800 align-middle border border-gray-300">
                                            {{ $keterangan }}
                                        </td>
                                    @endif

                                    <td class="px-4 py-3 align-middle border border-gray-300">
                                        {{ $detail->akun->nama_akun ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-right align-middle border border-gray-300 whitespace-nowrap">
                                        {{ (float) $detail->debit > 0
                                            ? 'Rp ' . number_format((float) $detail->debit, 0, ',', '.')
                                            : '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-right align-middle border border-gray-300 whitespace-nowrap">
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
                            <td colspan="6"
                                class="px-6 py-10 text-center text-gray-500 border border-gray-300">
                                Belum ada pencatatan jurnal.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>