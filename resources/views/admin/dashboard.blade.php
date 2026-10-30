<x-admin-layout-stisla>
    {{-- HEADER HALAMAN --}}
    <div
        class="mb-[30px] flex flex-wrap items-center gap-x-4 gap-y-2 rounded-[3px] bg-white px-[30px] py-5 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
        <h1 class="text-2xl font-bold text-[#34395e]">{{ $judulDashboard }}</h1>

        <ol class="ml-auto flex items-center text-[13px] text-[#6c757d]">
            <li class="font-semibold text-[#6777ef]">Dashboard</li>
            <li class="before:px-2 before:content-['/']">SISTA</li>
        </ol>
    </div>

    <div>
        {{-- FILTER --}}
        <div class="mb-[30px] rounded-[3px] bg-white p-[25px] shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
            <form method="GET" action="{{ url()->current() }}"
                class="grid grid-cols-1 items-end gap-x-[30px] gap-y-4 md:grid-cols-12">

                <div class="md:col-span-5">
                    <label class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">Cabang</label>
                    <select name="cabang_id" onchange="this.form.submit()"
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                        @if ($isAdminPusat)
                            <option value="semua" {{ $cabangId === null ? 'selected' : '' }}>
                                Semua Cabang
                            </option>
                        @endif

                        @foreach ($daftarCabang as $cabang)
                            <option value="{{ $cabang->id }}"
                                {{ (string) $cabangId === (string) $cabang->id ? 'selected' : '' }}>
                                {{ $cabang->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-4">
                    <label class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                </div>

                <div class="md:col-span-3">
                    <a href="{{ url()->current() }}"
                        class="flex h-[42px] w-full items-center justify-center gap-1.5 rounded-[3px] bg-[#e3eaef] text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                        <i class="fas fa-sync-alt"></i> Reset Filter
                    </a>
                </div>
            </form>
        </div>

        {{-- KARTU STATISTIK --}}
        <div class="mb-[30px] grid grid-cols-1 gap-[30px] md:grid-cols-2 lg:grid-cols-3">

            {{-- Laba Kotor --}}
            <div class="flex items-center rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
                <div
                    class="m-2.5 flex h-20 w-20 shrink-0 items-center justify-center rounded-[3px] bg-[#54ca68] text-[30px] text-white">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="min-w-0 flex-1 py-2.5 pr-5">
                    <h4 class="text-[13px] font-semibold uppercase tracking-[0.5px] text-[#98a6ad]">Laba Kotor</h4>
                    <div class="text-xl font-bold text-[#34395e]">
                        Rp {{ number_format($laba, 0, ',', '.') }}
                    </div>
                    <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1">
                        <span
                            class="rounded-full px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white {{ $persenLaba < 0 ? 'bg-[#fc544b]' : 'bg-[#54ca68]' }}">
                            {{ $persenLaba >= 0 ? '+' : '' }}{{ number_format($persenLaba, 1, ',', '.') }}%
                        </span>
                        <small class="text-xs text-[#6c757d]">dari hari sebelumnya</small>
                    </div>
                </div>
            </div>

            {{-- Omzet --}}
            <div class="flex items-center rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
                <div
                    class="m-2.5 flex h-20 w-20 shrink-0 items-center justify-center rounded-[3px] bg-[#6777ef] text-[30px] text-white">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="min-w-0 flex-1 py-2.5 pr-5">
                    <h4 class="text-[13px] font-semibold uppercase tracking-[0.5px] text-[#98a6ad]">Omzet</h4>
                    <div class="text-xl font-bold text-[#34395e]">
                        Rp {{ number_format($omzet, 0, ',', '.') }}
                    </div>
                    <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1">
                        <span
                            class="rounded-full px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white {{ $persenOmzet < 0 ? 'bg-[#fc544b]' : 'bg-[#54ca68]' }}">
                            {{ $persenOmzet >= 0 ? '+' : '' }}{{ number_format($persenOmzet, 1, ',', '.') }}%
                        </span>
                        <small class="text-xs text-[#6c757d]">dari hari sebelumnya</small>
                    </div>
                </div>
            </div>

            {{-- Transaksi --}}
            <div class="flex items-center rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
                <div
                    class="m-2.5 flex h-20 w-20 shrink-0 items-center justify-center rounded-[3px] bg-[#ffa426] text-[30px] text-white">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="min-w-0 flex-1 py-2.5 pr-5">
                    <h4 class="text-[13px] font-semibold uppercase tracking-[0.5px] text-[#98a6ad]">Transaksi</h4>
                    <div class="text-xl font-bold text-[#34395e]">
                        {{ number_format($jumlahTransaksi, 0, ',', '.') }}
                    </div>
                    <div class="mt-1.5 flex flex-wrap items-center gap-x-2 gap-y-1">
                        <span
                            class="rounded-full px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white {{ $persenTransaksi < 0 ? 'bg-[#fc544b]' : 'bg-[#54ca68]' }}">
                            {{ $persenTransaksi >= 0 ? '+' : '' }}{{ number_format($persenTransaksi, 1, ',', '.') }}%
                        </span>
                        <small class="text-xs text-[#6c757d]">dari hari sebelumnya</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRAFIK OMZET DAN PRODUK TERLARIS --}}
        <div class="mb-[30px] grid grid-cols-1 gap-[30px] lg:grid-cols-12">

            {{-- Performa Omzet --}}
            <div class="flex min-w-0 flex-col rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)] lg:col-span-8">
                <div class="flex min-h-[70px] items-center border-b border-[#f9f9f9] px-[25px] py-1">
                    <h4 class="pr-2.5 text-base font-semibold leading-7 text-[#6777ef]">Performa Omzet</h4>
                    <div class="ml-auto">
                        <span
                            class="rounded-full bg-[#6777ef] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">
                            7 Hari Terakhir
                        </span>
                    </div>
                </div>
                <div class="flex-1 p-[25px]">
                    <p class="mb-4 text-[#6c757d]">
                        Tren pendapatan selama tujuh hari terakhir
                    </p>
                    <div class="relative h-[300px] w-full">
                        <canvas id="omzetChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Produk Terlaris --}}
            <div class="flex min-w-0 flex-col rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)] lg:col-span-4">
                <div class="flex min-h-[70px] items-center border-b border-[#f9f9f9] px-[25px] py-1">
                    <h4 class="pr-2.5 text-base font-semibold leading-7 text-[#6777ef]">Produk Terlaris</h4>
                    <div class="ml-auto">
                        <span
                            class="rounded-full bg-[#6777ef] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">
                            Hari Ini
                        </span>
                    </div>
                </div>

                @php
                    $maksPenjualan = collect($produkTerlaris)->max('total_penjualan') ?: 1;
                @endphp

                <div class="flex min-h-0 flex-1 flex-col p-[25px]">
                    <div id="top-5-scroll" class="max-h-[315px] min-h-0 flex-1 overflow-y-auto pr-1">
                        <ul>
                            @forelse ($produkTerlaris as $index => $produk)
                                @php
                                    $totalPenjualan = (float) ($produk->total_penjualan ?? 0);
                                    $jumlahTerjual = (float) ($produk->total_terjual ?? 0);

                                    $persenBar = min(100, ($totalPenjualan / $maksPenjualan) * 100);
                                @endphp

                                <li
                                    class="mb-3 flex border-b border-[#f2f2f2] pb-3 last:mb-0 last:border-b-0 last:pb-0">
                                    <div class="mr-3 flex h-10 w-10 shrink-0 items-center justify-center">
                                        <span
                                            class="rounded-full bg-[#6777ef] px-2.5 py-[3px] text-base font-semibold text-white">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="font-semibold text-[#34395e]">
                                                {{ optional($produk->produk)->nama_produk ?? 'Produk Dihapus' }}
                                            </div>
                                            <div class="shrink-0 text-xs font-semibold text-[#6c757d]">
                                                {{ number_format($jumlahTerjual, 0, ',', '.') }} Terjual
                                            </div>
                                        </div>

                                        <div class="mt-2 flex items-center">
                                            <div class="h-2 shrink rounded-[3px] bg-[#6777ef]"
                                                style="width: {{ $persenBar }}%"></div>

                                            <div
                                                class="ml-2.5 shrink-0 whitespace-nowrap text-xs font-semibold opacity-60">
                                                Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="py-6 text-center">
                                    <i class="fas fa-box-open mb-3 text-[2em] text-[#6c757d]"></i>
                                    <p class="text-[#6c757d]">
                                        Belum ada data produk terlaris.
                                    </p>
                                </li>
                            @endforelse
                        </ul>
                        
                        <a href="{{ route('produk.index') }}"
                           class="mt-3 flex w-full items-center justify-center rounded-[3px] border border-[#6777ef] px-4 py-2 text-[13px] font-semibold tracking-[0.5px] text-[#6777ef] transition-colors hover:bg-[#6777ef] hover:text-white">
                           <i class="fas fa-box-open mr-2"></i>
                           Lihat Katalog Produk
                        </a>
                    
                    </div>

                    <a href="{{ route('produk.index') }}"
                        class="mt-4 flex h-[38px] w-full items-center justify-center rounded-[3px] border border-[#6777ef] text-xs font-semibold tracking-[0.5px] text-[#6777ef] transition-colors hover:bg-[#6777ef] hover:text-white">
                        Lihat Katalog Produk
                    </a>
                </div>
            </div>
        </div>

        {{-- TRANSAKSI TERBARU --}}
        <div class="mb-[30px] rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
            <div class="flex min-h-[70px] items-center border-b border-[#f9f9f9] px-[25px] py-1">
                <h4 class="pr-2.5 text-base font-semibold leading-7 text-[#6777ef]">Transaksi Terbaru</h4>
                <div class="ml-auto">
                    <a href="{{ route('transaksi.index') }}"
                        class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
                        {{ number_format($jumlahTransaksi) }} Transaksi
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap text-left text-[13px]">
                    <thead>
                        <tr class="text-[10px] font-bold uppercase tracking-[1.3px] text-[#666]">
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">#</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Tanggal</th>
                            @if ($isAdminPusat)
                                <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Cabang</th>
                            @endif
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">User</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-right">Omzet</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-right">HPP</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-right">Laba</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transaksiTerbaru as $index => $t)
                            @php
                                $omzetTransaksi = (float) ($t->total_transaksi ?? 0);
                                $hppTransaksi = (float) ($t->total_hpp ?? 0);
                                $labaTransaksi =
                                    $hppTransaksi > 0 ? $omzetTransaksi - $hppTransaksi : $omzetTransaksi * 0.33;
                            @endphp

                            <tr class="odd:bg-black/[0.025]">
                                <td class="px-[15px] py-3.5">{{ $index + 1 }}</td>
                                <td class="px-[15px] py-3.5">
                                    {{ \Carbon\Carbon::parse($t->tanggal ?? $t->created_at)->format('d/m/Y') }}
                                </td>
                                @if ($isAdminPusat)
                                    <td class="px-[15px] py-3.5">{{ optional($t->cabang)->nama ?? '-' }}</td>
                                @endif
                                <td class="px-[15px] py-3.5">{{ optional($t->user)->name ?? '-' }}</td>
                                <td class="px-[15px] py-3.5 text-right">
                                    Rp {{ number_format($omzetTransaksi, 0, ',', '.') }}
                                </td>
                                <td class="px-[15px] py-3.5 text-right">
                                    Rp {{ number_format($hppTransaksi, 0, ',', '.') }}
                                </td>
                                <td class="px-[15px] py-3.5 text-right">
                                    <strong class="font-bold text-[#54ca68]">
                                        Rp {{ number_format($labaTransaksi, 0, ',', '.') }}
                                    </strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isAdminPusat ? 7 : 6 }}" class="px-[15px] py-6 text-center">
                                    Belum ada transaksi pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- JURNAL TERBARU DAN RINGKASAN --}}
        <div class="grid grid-cols-1 items-start gap-[30px] lg:grid-cols-12">

            {{-- Jurnal Terbaru --}}
            <div class="min-w-0 rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)] lg:col-span-8">
                <div class="flex min-h-[70px] items-center border-b border-[#f9f9f9] px-[25px] py-1">
                    <h4 class="pr-2.5 text-base font-semibold leading-7 text-[#6777ef]">Jurnal Terbaru</h4>
                    <div class="ml-auto">
                        <span
                            class="rounded-full bg-[#e3eaef] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-[#191d21]">
                            {{ $jurnalTerbaru->count() }} Catatan
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap text-left text-[13px]">
                        <thead>
                            <tr class="text-[10px] font-bold uppercase tracking-[1.3px] text-[#666]">
                                <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Tanggal</th>
                                <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Keterangan</th>
                                @if ($isAdminPusat)
                                    <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Cabang</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jurnalTerbaru as $jurnal)
                                <tr class="odd:bg-black/[0.025]">
                                    <td class="px-[15px] py-3.5">
                                        {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}
                                    </td>
                                    <td class="px-[15px] py-3.5">{{ $jurnal->keterangan ?? 'Jurnal akuntansi' }}</td>
                                    @if ($isAdminPusat)
                                        <td class="px-[15px] py-3.5">{{ optional($jurnal->cabang)->nama ?? '-' }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isAdminPusat ? 3 : 2 }}" class="px-[15px] py-6 text-center">
                                        Belum ada mutasi jurnal.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-black/5 px-[25px] py-4 text-right">
                    <a href="{{ route('jurnal.index') }}"
                        class="inline-flex items-center rounded-[3px] border border-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#6777ef] transition-colors hover:bg-[#6777ef] hover:text-white">
                        Lihat Jurnal Umum
                    </a>
                </div>
            </div>

            {{-- Ringkasan Jurnal --}}
            <div class="min-w-0 rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)] lg:col-span-4">
                <div class="flex min-h-[70px] items-center border-b border-[#f9f9f9] px-[25px] py-1">
                    <h4 class="pr-2.5 text-base font-semibold leading-7 text-[#6777ef]">Ringkasan Jurnal</h4>
                </div>

                <div class="p-[25px]">
                    <div class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#54ca68] px-5 py-4 text-white">
                        <div class="text-xs">Total Debit</div>
                        <h5 class="text-base font-semibold">Rp {{ number_format($totalDebit ?? 0, 0, ',', '.') }}</h5>
                    </div>

                    <div class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#6777ef] px-5 py-4 text-white">
                        <div class="text-xs">Total Kredit</div>
                        <h5 class="text-base font-semibold">Rp {{ number_format($totalKredit ?? 0, 0, ',', '.') }}</h5>
                    </div>

                    <a href="{{ route('buku_besar.index') }}"
                        class="flex h-[38px] w-full items-center justify-center rounded-[3px] border border-[#6777ef] text-xs font-semibold tracking-[0.5px] text-[#6777ef] transition-colors hover:bg-[#6777ef] hover:text-white">
                        Lihat Buku Besar
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik omzet --}}
    <script src="{{ asset('stisla/assets/modules/chart.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('omzetChart');

            if (!canvas || typeof Chart === 'undefined') {
                return;
            }

            const labels = {{ \Illuminate\Support\Js::from($tanggalChart) }};
            const values = {{ \Illuminate\Support\Js::from($omzetChart) }};

            new Chart(canvas.getContext('2d'), {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Omzet',
                        data: values,
                        borderColor: '#6777ef',
                        backgroundColor: 'rgba(103, 119, 239, 0.12)',
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#6777ef',
                        fill: true,
                        lineTension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                        notation: 'compact',
                                        maximumFractionDigits: 1
                                    }).format(value);
                                }
                            },
                            gridLines: {
                                color: '#f2f2f2'
                            }
                        }],
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return 'Omzet: Rp ' +
                                    new Intl.NumberFormat('id-ID').format(tooltipItem.yLabel);
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-admin-layout-stisla>