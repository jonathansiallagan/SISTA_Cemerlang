<x-admin-layout>
    <div class="space-y-6">

        <!-- HEADER DASHBOARD -->
        <section class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">
                    {{ $judulDashboard }}
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Pantau seluruh aktivitas transaksi, laba, dan performa percetakan CV Cemerlang.
                </p>
            </div>

            <!-- FILTER CABANG DAN TANGGAL -->
            <form method="GET" action="{{ url()->current() }}"
                class="flex flex-wrap items-center gap-3">

                <select name="cabang_id"
                    onchange="this.form.submit()"
                    class="min-w-[150px] rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">

                    @if($isAdminPusat)
                    <option value="semua"
                        {{ $cabangId === null ? 'selected' : '' }}>
                        Semua Cabang
                    </option>
                    @endif

                    @foreach($daftarCabang as $cabang)
                    <option value="{{ $cabang->id }}"
                        {{ (string) $cabangId === (string) $cabang->id ? 'selected' : '' }}>
                        {{ $cabang->nama }}
                    </option>
                    @endforeach
                </select>

                <input type="date"
                    name="tanggal"
                    value="{{ $tanggal }}"
                    onchange="this.form.submit()"
                    class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </form>
        </section>


        <!-- KARTU STATISTIK -->
        <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

            <!-- LABA KOTOR (POSISI PERTAMA) -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-600">
                            Laba Kotor
                        </span>
                        <h2 class="mt-4 text-xl font-extrabold text-emerald-600">
                            Rp {{ number_format($laba, 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-lg shadow-emerald-500/20">
                        <i class="fa-solid fa-arrow-trend-up text-lg"></i>
                    </div>
                </div>
                <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                    <span class="rounded-md px-2 py-1 text-xs font-bold {{ $persenLaba < 0 ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }}">
                        {{ $persenLaba >= 0 ? '↗ +' : '↘ ' }}{{ number_format(abs($persenLaba), 1, ',', '.') }}%
                    </span>
                    <span class="text-[11px] text-slate-400">dari hari sebelumnya</span>
                </div>
            </div>

            <!-- OMZET (POSISI KEDUA) -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="rounded-lg bg-blue-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-blue-600">
                            Omzet
                        </span>
                        <h2 class="mt-4 text-xl font-extrabold text-slate-900">
                            Rp {{ number_format($omzet, 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-500 text-white shadow-lg shadow-blue-500/20">
                        <i class="fa-solid fa-chart-line text-lg"></i>
                    </div>
                </div>
                <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                    <span class="rounded-md px-2 py-1 text-xs font-bold {{ $persenOmzet < 0 ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }}">
                        {{ $persenOmzet >= 0 ? '↗ +' : '↘ ' }}{{ number_format(abs($persenOmzet), 1, ',', '.') }}%
                    </span>
                    <span class="text-[11px] text-slate-400">dari hari sebelumnya</span>
                </div>
            </div>

            <!-- TRANSAKSI (POSISI KETIGA) -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="rounded-lg bg-purple-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-purple-600">
                            Transaksi
                        </span>
                        <h2 class="mt-4 text-xl font-extrabold text-slate-900">
                            {{ number_format($jumlahTransaksi, 0, ',', '.') }}
                        </h2>
                    </div>
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-600 text-white shadow-lg shadow-violet-500/20">
                        <i class="fa-solid fa-receipt text-lg"></i>
                    </div>
                </div>
                <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
                    <span class="rounded-md px-2 py-1 text-xs font-bold {{ $persenTransaksi < 0 ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }}">
                        {{ $persenTransaksi >= 0 ? '↗ +' : '↘ ' }}{{ number_format(abs($persenTransaksi), 1, ',', '.') }}%
                    </span>
                    <span class="text-[11px] text-slate-400">dari hari sebelumnya</span>
                </div>
            </div>

        </section>


        <!-- GRAFIK DAN PRODUK TERLARIS -->
        <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">

            <!-- GRAFIK OMZET -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm md:p-6 xl:col-span-2">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Performa Omzet</h2>
                        <p class="mt-1 text-xs text-slate-400">
                            Tren pendapatan selama 7 hari terakhir
                        </p>
                    </div>
                    <span class="rounded-full border border-blue-100 bg-blue-50 px-3 py-1.5 text-[11px] font-semibold text-blue-600">
                        7 Hari Terakhir
                    </span>
                </div>

                <div class="mt-5 flex flex-wrap items-center gap-3">
                    <span class="flex items-center gap-2 text-xs font-medium text-slate-600">
                        <span class="h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                        Omzet Masuk
                    </span>
                    <span class="rounded-full bg-blue-600 px-3 py-1 text-[11px] font-semibold text-white">
                        Puncak: Rp {{ number_format(collect($omzetChart)->max() ?? 0, 0, ',', '.') }}
                    </span>
                </div>

                <div class="relative mt-5 h-64 w-full">
                    <canvas id="omzetChart"></canvas>
                </div>
            </div>

            <!-- PRODUK TERLARIS -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm md:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Produk Terlaris</h2>
                        <p class="mt-1 text-xs text-slate-400">
                            Produk dengan penjualan tertinggi
                        </p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-50 text-amber-500">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse($produkTerlaris as $index => $produk)
                    @php
                    $maksPenjualan = collect($produkTerlaris)->max('total_terjual') ?? 0;
                    $persenBar = $maksPenjualan > 0
                    ? min(100, (($produk->total_terjual ?? 0) / $maksPenjualan) * 100)
                    : 0;
                    @endphp

                    <div class="rounded-xl border border-amber-100 bg-amber-50/30 p-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-500 text-sm font-bold text-white">
                                {{ $index + 1 }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-bold text-slate-800">
                                    {{ optional($produk->produk)->nama ?? 'Produk Dihapus' }}
                                </p>
                                <p class="mt-1 text-[11px] text-slate-500">
                                    {{ number_format($produk->total_terjual ?? 0, 0, ',', '.') }} terjual
                                </p>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between gap-2">
                            <span class="text-[10px] font-semibold text-emerald-600">
                                {{ $index === 0 ? 'Produk Teratas' : 'Peringkat ' . ($index + 1) }}
                            </span>
                            <span class="text-xs font-bold text-slate-700">
                                Rp {{ number_format($produk->total_penjualan ?? 0, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-rose-500"
                                style="width: {{ $persenBar }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="rounded-xl border border-dashed border-slate-200 px-4 py-10 text-center">
                        <i class="fa-regular fa-clock text-xl text-slate-300"></i>
                        <p class="mt-3 text-xs font-semibold text-slate-600">
                            Belum ada data produk
                        </p>
                        <p class="mt-1 text-[11px] text-slate-400">
                            Data akan tampil setelah transaksi tersedia.
                        </p>
                    </div>
                    @endforelse
                </div>

                <div class="mt-5 border-t border-slate-100 pt-4">
                    <a href="{{ route('produk.index') }}"
                        class="block text-center text-xs font-semibold text-blue-600 hover:text-blue-800">
                        Lihat Katalog Produk →
                    </a>
                </div>
            </div>
        </section>

        <!-- TRANSAKSI TERBARU -->
        <section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <div class="flex flex-col gap-3 p-5 md:flex-row md:items-center md:justify-between md:px-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Transaksi Terbaru</h2>
                    <p class="mt-1 text-xs text-slate-400">
                        Daftar transaksi pada {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                    </p>
                </div>

                <a href="{{ route('transaksi.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-full border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-100">
                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                    {{ $jumlahTransaksi }} Transaksi
                </a>
            </div>

            <div class="overflow-x-auto px-5 pb-5 md:px-6">
                <table class="w-full min-w-[720px] text-left text-xs">
                    <thead>
                        <tr class="border-y border-slate-100 bg-slate-50 text-[10px] uppercase tracking-wide text-slate-400">
                            <th class="px-3 py-4 font-bold">#</th>
                            <th class="px-3 py-4 font-bold">Tanggal</th>
                            @if($isAdminPusat)
                            <th class="px-3 py-4 font-bold">Cabang</th>
                            @endif
                            <th class="px-3 py-4 font-bold">User</th>
                            <th class="px-3 py-4 text-right font-bold">Omzet</th>
                            <th class="px-3 py-4 text-right font-bold">HPP</th>
                            <th class="px-3 py-4 text-right font-bold">Laba</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($transaksiTerbaru as $index => $t)
                        @php
                        $omzetTransaksi = (float) ($t->total_transaksi ?? 0);
                        $hppTransaksi = (float) ($t->total_hpp ?? 0);
                        $labaTransaksi = $hppTransaksi > 0
                        ? $omzetTransaksi - $hppTransaksi
                        : $omzetTransaksi * 0.33;
                        @endphp

                        <tr class="transition hover:bg-blue-50/40">
                            <td class="px-3 py-4">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-50 font-bold text-blue-600">
                                    {{ $index + 1 }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-3 py-4 text-slate-600">
                                <i class="fa-regular fa-calendar mr-1 text-slate-400"></i>
                                {{ \Carbon\Carbon::parse($t->tanggal ?? $t->created_at)->format('d/m/Y') }}
                            </td>

                            @if($isAdminPusat)
                            <td class="whitespace-nowrap px-3 py-4">
                                <span class="rounded-lg border border-blue-100 bg-blue-50 px-2 py-1 font-medium text-blue-600">
                                    {{ optional($t->cabang)->nama ?? '-' }}
                                </span>
                            </td>
                            @endif

                            <td class="whitespace-nowrap px-3 py-4 font-semibold text-slate-700">
                                <i class="fa-regular fa-user mr-1 text-slate-400"></i>
                                {{ optional($t->user)->name ?? '-' }}
                            </td>

                            <td class="whitespace-nowrap px-3 py-4 text-right font-bold text-slate-800">
                                Rp {{ number_format($omzetTransaksi, 0, ',', '.') }}
                            </td>

                            <td class="whitespace-nowrap px-3 py-4 text-right">
                                <span class="rounded-md border border-amber-200 bg-amber-50 px-2 py-1 font-semibold text-amber-700">
                                    Rp {{ number_format($hppTransaksi, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap px-3 py-4 text-right">
                                <span class="rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 font-bold text-emerald-700">
                                    Rp {{ number_format($labaTransaksi, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isAdminPusat ? 7 : 6 }}"
                                class="px-4 py-10 text-center text-slate-400">
                                <i class="fa-regular fa-folder-open mb-2 text-2xl"></i>
                                <p>Belum ada transaksi pada tanggal ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- JURNAL TERBARU DAN RINGKASAN JURNAL -->
        <section class="grid grid-cols-1 gap-5 xl:grid-cols-3">

            <!-- JURNAL TERBARU -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm md:p-6 xl:col-span-2">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Jurnal Terbaru</h2>
                        <p class="mt-1 text-xs text-slate-400">Jurnal akuntansi terbaru</p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-semibold text-slate-500">
                        {{ $jurnalTerbaru->count() }} Catatan
                    </span>
                </div>

                <div class="mt-5 overflow-x-auto">
                    <table class="w-full min-w-[400px] text-left text-xs">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] text-slate-400">
                                <th class="rounded-l-lg px-3 py-3 font-bold">Tanggal</th>
                                <th class="px-3 py-3 font-bold">Keterangan</th>
                                @if($isAdminPusat)
                                <th class="rounded-r-lg px-3 py-3 font-bold">Cabang</th>
                                @endif
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse($jurnalTerbaru as $jurnal)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-3 py-3 text-slate-600">
                                    {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}
                                </td>
                                <td class="px-3 py-3 font-medium text-slate-700">
                                    {{ $jurnal->keterangan ?? 'Jurnal akuntansi' }}
                                </td>
                                @if($isAdminPusat)
                                <td class="whitespace-nowrap px-3 py-3 text-slate-600">
                                    {{ optional($jurnal->cabang)->nama ?? '-' }}
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $isAdminPusat ? 3 : 2 }}" class="py-8">
                                    <div class="rounded-xl border border-dashed border-slate-200 px-4 py-8 text-center">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-500">
                                            <i class="fa-solid fa-book-open text-xl"></i>
                                        </div>
                                        <p class="mt-3 text-xs font-semibold text-slate-700">
                                            Belum ada mutasi jurnal
                                        </p>
                                        <p class="mt-1 text-[11px] text-slate-400">
                                            Jurnal akan ditampilkan ketika data tersedia.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 border-t border-slate-100 pt-4">
                    <a href="{{ route('jurnal.index') }}"
                        class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                        Lihat Jurnal Umum →
                    </a>
                </div>
            </div>

            <!-- RINGKASAN JURNAL -->
            <div class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Ringkasan Jurnal</h2>
                        <p class="mt-1 text-xs text-slate-400">Total debit dan kredit</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-between gap-3 rounded-xl border border-emerald-100 bg-emerald-50/40 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500 text-white">
                            <i class="fa-solid fa-arrow-down"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase text-slate-500">Total Debit</p>
                            <p class="mt-1 text-base font-extrabold text-slate-900">
                                Rp {{ number_format($totalDebit ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <span class="rounded-md border border-emerald-200 bg-white px-2 py-1 text-[10px] font-semibold text-emerald-600">
                        Debit
                    </span>
                </div>

                <div class="mt-3 flex items-center justify-between gap-3 rounded-xl border border-blue-100 bg-blue-50/40 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white">
                            <i class="fa-solid fa-arrow-up"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase text-slate-500">Total Kredit</p>
                            <p class="mt-1 text-base font-extrabold text-slate-900">
                                Rp {{ number_format($totalKredit ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                    <span class="rounded-md border border-blue-200 bg-white px-2 py-1 text-[10px] font-semibold text-blue-600">
                        Kredit
                    </span>
                </div>

                <div class="mt-5 border-t border-slate-100 pt-4">
                    <a href="{{ route('buku_besar.index') }}"
                        class="block text-center text-xs font-semibold text-blue-600 hover:text-blue-800">
                        Lihat Buku Besar →
                    </a>
                </div>
            </div>

        </section>
    </div>

    <!-- CHART.JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('omzetChart');

            if (!canvas || typeof Chart === 'undefined') return;

            const labels = @json($tanggalChart);
            const values = @json($omzetChart);
            const ctx = canvas.getContext('2d');

            const gradient = ctx.createLinearGradient(0, 0, 0, 280);
            gradient.addColorStop(0, 'rgba(37, 99, 235, 0.22)');
            gradient.addColorStop(1, 'rgba(37, 99, 235, 0.00)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Omzet',
                        data: values,
                        borderColor: '#2563eb',
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#172554',
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Omzet: Rp ' +
                                        new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            border: {
                                display: false
                            },
                            grid: {
                                color: '#edf2f7'
                            },
                            ticks: {
                                color: '#94a3b8',
                                font: {
                                    size: 10
                                },
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                        notation: 'compact',
                                        maximumFractionDigits: 1
                                    }).format(value);
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

</x-admin-layout>