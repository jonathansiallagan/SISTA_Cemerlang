<x-admin-layout>
    <div class="w-full">

        {{-- Header --}}
        <div class="mb-6 flex items-center justify-between gap-4">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                Riwayat Pengeluaran
            </h1>

            <a href="{{ route('pengeluaran.create') }}"
               class="inline-flex shrink-0 items-center gap-2 rounded-lg
                      bg-blue-700 px-5 py-3 text-sm font-semibold text-white
                      shadow-sm hover:bg-blue-800 transition-colors">
                <i class="fa-solid fa-plus"></i>
                Tambah Pengeluaran
            </a>
        </div>

        
    {{-- Filter Periode dan Cabang --}}
    <form action="{{ route('pengeluaran.index') }}" method="GET"
        class="mb-6 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

        <div class="mb-4">
            <h2 class="text-base font-semibold text-slate-800">
                Filter Pengeluaran
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                Pilih periode untuk melihat riwayat pengeluaran.
            </p>
        </div>

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

                <a href="{{ route('pengeluaran.index') }}"
                class="inline-flex items-center justify-center gap-2
                        rounded-lg border border-slate-300 px-4 py-2.5
                        text-sm font-semibold text-slate-700
                        hover:bg-slate-50">
                    Reset
                </a>
            </div>

        </div>
    </form>


        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="mb-5 rounded-lg border border-green-200 bg-green-50
                        px-4 py-3 text-sm text-green-700">
                <i class="fa-solid fa-circle-check mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabel --}}
        <div class="w-full overflow-hidden rounded-2xl border border-slate-200
                    bg-white shadow-sm">

            <div class="w-full overflow-x-auto">
                <table class="w-full min-w-[800px] table-fixed border-collapse text-left text-sm">

                    <thead class="border-b border-slate-200 bg-white">
                        <tr class="text-xs font-semibold tracking-wide text-slate-500">
                            <th class="w-[17%] px-6 py-5">
                                No Referensi
                            </th>
                            <th class="w-[13%] px-5 py-5">
                                Tanggal
                            </th>
                            <th class="w-[17%] px-5 py-5">
                                Cabang
                            </th>
                            <th class="w-[35%] px-5 py-5">
                                Keterangan
                            </th>
                            <th class="w-[18%] px-6 py-5 text-right">
                                Total Pengeluaran
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pengeluarans as $pengeluaran)
                            <tr class="transition-colors hover:bg-slate-50">

                                <td class="break-words px-6 py-5 align-middle
                                           font-semibold text-blue-600">
                                    {{ $pengeluaran->no_referensi }}
                                </td>

                                <td class="whitespace-nowrap px-5 py-5
                                           align-middle text-slate-600">
                                    {{ $pengeluaran->tanggal->format('d M Y') }}
                                </td>

                                <td class="break-words px-5 py-5 align-middle
                                           text-slate-700">
                                    {{ $pengeluaran->cabang?->nama ?? '-' }}
                                </td>

                                <td class="px-5 py-5 align-middle text-slate-600">
                                    <div class="truncate"
                                         title="{{ $pengeluaran->keterangan }}">
                                        {{ $pengeluaran->keterangan }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-5 text-right
                                           align-middle font-bold text-slate-900">
                                    Rp {{ number_format(
                                        (float) $pengeluaran->total_pengeluaran,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-14 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="mb-4 flex h-14 w-14 items-center
                                                    justify-center rounded-full bg-slate-100">
                                            <i class="fa-solid fa-receipt text-2xl text-slate-400"></i>
                                        </div>

                                        <p class="font-semibold text-slate-700">
                                            Belum ada data pengeluaran
                                        </p>

                                        <p class="mt-2 text-sm text-slate-500">
                                            Klik tombol Tambah Pengeluaran untuk mencatat pengeluaran baru.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        {{-- Jumlah data --}}
        <p class="mt-4 text-sm text-slate-500">
            Menampilkan {{ $pengeluarans->count() }} data pengeluaran
        </p>

    </div>
</x-admin-layout>
