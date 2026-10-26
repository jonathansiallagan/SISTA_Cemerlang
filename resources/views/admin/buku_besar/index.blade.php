<x-admin-layout>
    <!-- HEADER & TOMBOL EXPORT -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">

        <!-- Judul -->
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">
                Buku Besar
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Laporan mutasi dan saldo per akun perkiraan.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @php
                $paramCetak = [
                    'akun_id' => $akun_id ?: 'semua',
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                ];
            @endphp

            {{-- Tombol Export PDF --}}
            <a href="{{ route('buku_besar.pdf', $paramCetak) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition-colors shadow-sm">
                <i class="fa-solid fa-file-pdf"></i>
                Export PDF
            </a>

            {{-- Tombol Export Excel --}}
            <a href="{{ route('buku_besar.excel', $paramCetak) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 transition-colors shadow-sm">
                <i class="fa-solid fa-file-excel"></i>
                Export Excel
            </a>
        </div>
    </div>

    <!-- FORM FILTER -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('buku_besar.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Akun</label>

                <select name="akun_id" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500">

                    <option value="semua" {{ ($akun_id ?? 'semua') === 'semua' ? 'selected' : '' }}>
                        Semua Akun
                    </option>

                    @foreach ($akuns as $akun)
                        <option value="{{ $akun->id }}"
                            {{ (string) ($akun_id ?? 'semua') === (string) $akun->id ? 'selected' : '' }}>
                            {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                        </option>
                    @endforeach
                </select>

            </div>
            <div class="w-full md:w-1/4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $start_date }}"
                    class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="w-full md:w-1/4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $end_date }}"
                    class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="w-full md:w-auto">
                <button type="submit"
                    class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition flex items-center gap-2 justify-center">
                    <i class="fa-solid fa-filter"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- HASIL LAPORAN -->
    @if ($laporan->isNotEmpty())

        {{-- Satu tabel untuk setiap akun --}}
        @foreach ($laporan as $item)
            @include('admin.buku_besar.tabel', [
                'item' => $item,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ])
        @endforeach



        {{-- Ringkasan laporan buku besar --}}
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

            <h3 class="mb-4 text-lg font-bold text-gray-800">
                Ringkasan Buku Besar
            </h3>

            @php
                // Saldo awal bersih: Debit positif, Kredit negatif.
                $totalSaldoAwal = $laporan->sum(function ($item) {
                    return $item['sisiAwal'] === 'Debit' ? $item['saldoAwal'] : -$item['saldoAwal'];
                });

                // Saldo akhir bersih: Debit positif, Kredit negatif.
                $totalSaldoAkhir = $laporan->sum(function ($item) {
                    return $item['sisiAkhir'] === 'Debit' ? $item['saldoAkhir'] : -$item['saldoAkhir'];
                });

                $seimbang = round($grandDebit, 2) === round($grandKredit, 2);
            @endphp

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                {{-- Total Debit --}}
                <div class="rounded-lg border border-gray-200 p-3">
                    <p class="text-sm text-gray-500">Total Debit</p>
                    <p class="mt-1 text-lg font-semibold text-gray-800">
                        Rp {{ number_format($grandDebit, 2, ',', '.') }}
                    </p>
                </div>

                {{-- Saldo Awal --}}
                <div class="rounded-lg border border-gray-200 p-3">
                    <p class="text-sm text-gray-500">Saldo Awal Bersih</p>
                    <p class="mt-1 text-lg font-semibold text-gray-800">
                        Rp {{ number_format(abs($totalSaldoAwal), 2, ',', '.') }}
                        <span class="text-sm text-gray-500">
                            ({{ $totalSaldoAwal < 0 ? 'Kredit' : 'Debit' }})
                        </span>
                    </p>
                </div>

                {{-- Total Kredit --}}
                <div class="rounded-lg border border-gray-200 p-3">
                    <p class="text-sm text-gray-500">Total Kredit</p>
                    <p class="mt-1 text-lg font-semibold text-gray-800">
                        Rp {{ number_format($grandKredit, 2, ',', '.') }}
                    </p>
                </div>

                {{-- Saldo Akhir --}}
                <div class="rounded-lg border border-gray-200 p-3">
                    <p class="text-sm text-gray-500">Saldo Akhir Bersih</p>
                    <p class="mt-1 text-lg font-semibold text-gray-800">
                        Rp {{ number_format(abs($totalSaldoAkhir), 2, ',', '.') }}
                        <span class="text-sm text-gray-500">
                            ({{ $totalSaldoAkhir < 0 ? 'Kredit' : 'Debit' }})
                        </span>
                    </p>
                </div>

            </div>

            {{-- Status keseimbangan jurnal --}}
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3
        rounded-lg bg-gray-50 p-4">

                <div>
                    <p class="text-sm text-gray-500">
                        Status Keseimbangan Jurnal
                    </p>
                    <p class="text-xs text-gray-500">
                        Perbandingan total debit dan total kredit
                    </p>
                </div>

                @if ($seimbang)
                    <span
                        class="rounded-full bg-green-100 px-3 py-1
                text-sm font-semibold text-green-700">
                        Seimbang
                    </span>
                @else
                    <span class="rounded-full bg-red-100 px-3 py-1
                text-sm font-semibold text-red-700">
                        Tidak Seimbang
                    </span>
                @endif

            </div>

        </div>
    @elseif($akun_id)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400">
            Tidak ada akun yang memiliki saldo atau mutasi pada periode ini.
        </div>
    @endif
</x-admin-layout>
