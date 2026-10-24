<x-admin-layout>

    {{-- =========================
    HEADER DASHBOARD
    ========================== --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            {{ $judulDashboard }}
        </h1>
        <p class="text-gray-500 mt-1">
            {{ $subJudul }}
        </p>
    </div>

    {{-- =========================
    FILTER (INTERAKTIF)
    ========================== --}}
    <form method="GET" action="{{ route('admin.dashboard') }}" class="bg-white rounded-xl border border-gray-200 p-4 mb-5 flex flex-wrap justify-between items-center gap-3">
        <div>
            <h2 class="font-semibold text-gray-800">
                Ringkasan Aktivitas
            </h2>
            <p class="text-xs text-gray-400 mt-1">
                Diperbarui: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
            </p>
        </div>

        <div class="flex gap-2">
            <select name="cabang_id" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 focus:ring-green-500 focus:border-green-500">
                @if($isAdminPusat)
                <option value="semua" {{ $cabangId === 'semua' ? 'selected' : '' }}>Semua Cabang</option>
                @endif

                @foreach($daftarCabang as $cabang)
                <option value="{{ $cabang->id }}" {{ $cabangId == $cabang->id ? 'selected' : '' }}>
                    {{ $cabang->nama }}
                </option>
                @endforeach
            </select>

            <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600 focus:ring-green-500 focus:border-green-500">
        </div>
    </form> <!-- [PERBAIKAN 1] Penutup Form yang sebelumnya hilang -->

    {{-- =========================
    STAT CARDS
    ========================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

        {{-- OMZET --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Omzet</p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($omzet, 0, ',', '.') }}</h2>
                </div>
                <div class="w-9 h-9 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3 1.343 3 3-1.343 3-3 3m0-14V4m0 16v-2 M5 12H3m18 0h-2" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs font-semibold px-2 py-1 rounded-md {{ $persenOmzet >= 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }}">
                    {{ $persenOmzet >= 0 ? '↑' : '↓' }} {{ number_format(abs($persenOmzet), 1, ',', '.') }}%
                </span>
                <span class="text-xs text-gray-400">dari kemarin</span>
            </div>
        </div>

        {{-- TRANSAKSI --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Transaksi</p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format($jumlahTransaksi, 0, ',', '.') }}</h2>
                </div>
                <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs font-semibold px-2 py-1 rounded-md {{ $persenTransaksi >= 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }}">
                    {{ $persenTransaksi >= 0 ? '↑' : '↓' }} {{ number_format(abs($persenTransaksi), 1, ',', '.') }}%
                </span>
                <span class="text-xs text-gray-400">dari kemarin</span>
            </div>
        </div>

        {{-- HPP --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">HPP</p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($totalHpp, 0, ',', '.') }}</h2>
                </div>
                <div class="w-11 h-11 rounded-lg bg-orange-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs font-semibold px-2 py-1 rounded-md {{ $persenHpp <= 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }}">
                    {{ $persenHpp >= 0 ? '↑' : '↓' }} {{ number_format(abs($persenHpp), 1, ',', '.') }}%
                </span>
                <span class="text-xs text-gray-400">dari kemarin</span>
            </div>
        </div>

        {{-- LABA --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Laba Kotor</p>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1">Rp {{ number_format($laba, 0, ',', '.') }}</h2>
                </div>
                <div class="w-11 h-11 rounded-lg bg-green-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <span class="text-xs font-semibold px-2 py-1 rounded-md {{ $persenLaba >= 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }}">
                    {{ $persenLaba >= 0 ? '↑' : '↓' }} {{ number_format(abs($persenLaba), 1, ',', '.') }}%
                </span>
                <span class="text-xs text-gray-400">dari kemarin</span>
            </div>
        </div>
    </div>

    {{-- =========================
    CHART + PRODUK TERLARIS
    ========================== --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">

        {{-- CHART --}}
        <div class="xl:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="mb-5">
                <h2 class="font-semibold text-gray-800">Performa Omzet</h2>
                {{-- [PERBAIKAN 2] Menggunakan $namaCabang, bukan JSON object --}}
                <p class="text-xs text-gray-400">Akumulasi pendapatan – {{ $namaCabang }}</p>
            </div>
            {{-- [PERBAIKAN 5] Kanvas Chart yang hilang --}}
            <div class="relative h-64 w-full">
                <canvas id="omzetChart" data-labels='@json($tanggalChart)' data-values='@json($omzetChart)'></canvas>
            </div>
        </div>

        {{-- PRODUK TERLARIS --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <div class="mb-5">
                <h2 class="font-semibold text-gray-800">Produk Terlaris</h2>
                <p class="text-xs text-gray-400 mt-1">Berdasarkan volume penjualan</p>
            </div>

            <div class="space-y-4">
                @forelse($produkTerlaris as $index => $produk)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        {{-- [PERBAIKAN 6] Relasi yang benar untuk nama produk --}}
                        <p class="text-sm font-medium text-gray-800 truncate">{{ optional($produk->produk)->nama ?? 'Produk Dihapus' }}</p>
                        <p class="text-xs text-gray-400">{{ number_format($produk->total_terjual ?? 0, 0, ',', '.') }} terjual</p>
                    </div>
                    <div class="text-xs font-semibold text-gray-600">
                        Rp {{ number_format($produk->total_penjualan ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                @empty
                <div class="text-center py-10">
                    <p class="text-sm text-gray-400">Belum ada data produk</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- =========================
    TABLE + PANEL KANAN
    ========================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- TABLE TRANSAKSI --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
            <div class="p-5 flex justify-between">
                <div>
                    <h2 class="font-semibold text-gray-800">
                        Transaksi Terbaru – {{ $namaCabang }}
                    </h2>
                    <p class="text-xs text-gray-400">5 transaksi terakhir</p>
                </div>
                <a href="{{ route('transaksi.index') }}" class="text-xs text-green-600 font-medium hover:underline">Lihat semua →</a>
            </div>

            <div class="overflow-x-auto">
                {{-- [PERBAIKAN 3] Sinkronisasi jumlah kolom thead dan tbody --}}
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="text-left px-5 py-3 font-medium whitespace-nowrap">Waktu</th>
                            @if($isAdminPusat)
                            <th class="text-left px-5 py-3 font-medium whitespace-nowrap">Cabang</th>
                            @endif
                            <th class="text-left px-5 py-3 font-medium whitespace-nowrap">Kasir</th>
                            <th class="text-right px-5 py-3 font-medium whitespace-nowrap">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transaksiTerbaru as $t)
                        <tr class="hover:bg-gray-50">
                            <td class="px-5 py-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($t->created_at)->format('H:i') }}</td>

                            @if($isAdminPusat)
                            {{-- Mengambil nama cabang dari relasi --}}
                            <td class="px-5 py-3 whitespace-nowrap">{{ optional($t->cabang)->nama ?? '-' }}</td>
                            @endif

                            <td class="px-5 py-3 whitespace-nowrap">{{ optional($t->user)->name ?? '-' }}</td>
                            <td class="px-5 py-3 text-right font-medium text-gray-700 whitespace-nowrap">Rp {{ number_format($t->total_transaksi ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isAdminPusat ? '4' : '3' }}" class="px-5 py-10 text-center text-gray-400">
                                Tidak ada transaksi hari ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RINGKASAN JURNAL --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between p-5">
            {{-- [PERBAIKAN 4] Struktur HTML yang berantakan disusun ulang --}}
            <div>
                <div class="border-b border-gray-100 pb-4 mb-4">
                    <h2 class="font-semibold text-gray-800">Ringkasan Jurnal</h2>
                    <p class="text-xs text-gray-400 mt-1">Total debit dan kredit hari ini</p>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3 border border-gray-100">
                        <p class="text-xs text-gray-500">Total Debit</p>
                        <p class="font-semibold text-blue-600">Rp {{ number_format($totalDebit ?? 0, 0, ',', '.') }}</p>
                    </div>

                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3 border border-gray-100">
                        <p class="text-xs text-gray-500">Total Kredit</p>
                        <p class="font-semibold text-orange-600">Rp {{ number_format($totalKredit ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4">
                <a href="{{ route('jurnal.index') }}" class="block w-full text-center bg-green-50 hover:bg-green-100 text-green-700 font-medium rounded-lg py-2.5 text-sm transition-colors border border-green-200">
                    Lihat Buku Besar
                </a>
            </div>
        </div>

    </div>

    {{-- Elemen kanvas dengan data yang disematkan --}}
    <div class="relative h-64 w-full">
        <canvas id="omzetChart"
            data-labels="{{ json_encode($chartLabels) }}"
            data-values="{{ json_encode($chartData) }}">
        </canvas>
    </div>

    {{-- Panggil file script eksternal --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/dashboard-chart.js') }}"></script>

</x-admin-layout>