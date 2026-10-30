
<x-admin-layout-stisla>
    <div class="section-header">
        <h1>{{ $judulDashboard }}</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Dashboard</div>
            <div class="breadcrumb-item">SISTA</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ url()->current() }}">
                            <div class="row align-items-end">
                                <div class="form-group col-md-5 mb-2">
                                    <label>Cabang</label>
                                    <select name="cabang_id"
                                            class="form-control"
                                            onchange="this.form.submit()">
                                        @if ($isAdminPusat)
                                            <option value="semua"
                                                {{ $cabangId === null ? 'selected' : '' }}>
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

                                <div class="form-group col-md-4 mb-2">
                                    <label>Tanggal</label>
                                    <input type="date" name="tanggal"
                                           value="{{ $tanggal }}"
                                           class="form-control"
                                           onchange="this.form.submit()">
                                </div>

                                <div class="form-group col-md-3 mb-2">
                                    <a href="{{ url()->current() }}"
                                       class="btn btn-light btn-block">
                                        <i class="fas fa-sync-alt"></i> Reset Filter
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- KARTU STATISTIK --}}
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Laba Kotor</h4>
                        </div>
                        <div class="card-body">
                            Rp {{ number_format($laba, 0, ',', '.') }}
                        </div>
                        <div class="px-4 pb-3">
                            <span class="badge {{ $persenLaba < 0 ? 'badge-danger' : 'badge-success' }}">
                                {{ $persenLaba >= 0 ? '+' : '' }}{{ number_format($persenLaba, 1, ',', '.') }}%
                            </span>
                            <small class="text-muted">dari hari sebelumnya</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Omzet</h4>
                        </div>
                        <div class="card-body">
                            Rp {{ number_format($omzet, 0, ',', '.') }}
                        </div>
                        <div class="px-4 pb-3">
                            <span class="badge {{ $persenOmzet < 0 ? 'badge-danger' : 'badge-success' }}">
                                {{ $persenOmzet >= 0 ? '+' : '' }}{{ number_format($persenOmzet, 1, ',', '.') }}%
                            </span>
                            <small class="text-muted">dari hari sebelumnya</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Transaksi</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($jumlahTransaksi, 0, ',', '.') }}
                        </div>
                        <div class="px-4 pb-3">
                            <span class="badge {{ $persenTransaksi < 0 ? 'badge-danger' : 'badge-success' }}">
                                {{ $persenTransaksi >= 0 ? '+' : '' }}{{ number_format($persenTransaksi, 1, ',', '.') }}%
                            </span>
                            <small class="text-muted">dari hari sebelumnya</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRAFIK OMZET DAN PRODUK TERLARIS --}}
        <div class="row align-items-stretch">
            <div class="col-lg-8 col-md-12 d-flex">
                <div class="card w-100">
                    <div class="card-header">
                        <h4>Performa Omzet</h4>
                        <div class="card-header-action">
                            <span class="badge badge-primary">7 Hari Terakhir</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Tren pendapatan selama tujuh hari terakhir
                        </p>
                        <div style="height: 300px; position: relative;">
                            <canvas id="omzetChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-4 col-md-12 d-flex">
                <div class="card w-100">
                    <div class="card-header">
                        <h4>Produk Terlaris</h4>
                        <div class="card-header-action">
                            <span class="badge badge-primary">Hari Ini</span>
                        </div>
                    </div>

                    @php
                        $maksPenjualan = collect($produkTerlaris)->max('total_penjualan') ?: 1;
                    @endphp

                    <div class="card-body" id="top-5-scroll">
                        <ul class="list-unstyled list-unstyled-border">
                            @forelse ($produkTerlaris as $index => $produk)
                                @php
                                    $totalPenjualan = (float) ($produk->total_penjualan ?? 0);
                                    $jumlahTerjual = (float) ($produk->total_terjual ?? 0);

                                    $persenBar = min(
                                        100,
                                        ($totalPenjualan / $maksPenjualan) * 100
                                    );
                                @endphp

                                <li class="media mb-3 pb-3">
                                    <div class="mr-3 d-flex align-items-center justify-content-center"
                                        style="width: 40px; height: 40px; flex-shrink: 0;">
                                        <span class="badge badge-primary"
                                            style="font-size: 16px;">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>

                                    <div class="media-body">
                                        <div class="float-right">
                                            <div class="font-weight-600 text-muted text-small">
                                                {{ number_format($jumlahTerjual, 0, ',', '.') }} Terjual
                                            </div>
                                        </div>

                                        <div class="media-title">
                                            {{ optional($produk->produk)->nama_produk ?? 'Produk Dihapus' }}
                                        </div>

                                        <div class="mt-1">
                                            <div class="budget-price">
                                                <div class="budget-price-square bg-primary"
                                                    data-width="{{ $persenBar }}%">
                                                </div>

                                                <div class="budget-price-label">
                                                    Rp {{ number_format($totalPenjualan, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-center py-4">
                                    <i class="fas fa-box-open fa-2x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">
                                        Belum ada data produk terlaris.
                                    </p>
                                </li>
                            @endforelse
                        </ul>

                        <a href="{{ route('produk.index') }}"
                           class="btn btn-outline-primary btn-block mt-3">
                           <i class="fas fa-box-open mr-1"></i>
                           Lihat Katalog Produk
                        </a>
                    </div>
                </div>
            </div>

        </div>

        {{-- TRANSAKSI TERBARU --}}
        <div class="card">
            <div class="card-header">
                <h4>Transaksi Terbaru</h4>
                <div class="card-header-action">
                    <a href="{{ route('transaksi.index') }}" class="btn btn-primary">
                       <i class="fas fa-receipt mr-1"></i>
                       {{ number_format($jumlahTransaksi) }} Transaksi
                    </a>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-md">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                @if ($isAdminPusat)
                                    <th>Cabang</th>
                                @endif
                                <th>User</th>
                                <th class="text-right">Omzet</th>
                                <th class="text-right">HPP</th>
                                <th class="text-right">Laba</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksiTerbaru as $index => $t)
                                @php
                                    $omzetTransaksi = (float) ($t->total_transaksi ?? 0);
                                    $hppTransaksi = (float) ($t->total_hpp ?? 0);
                                    $labaTransaksi = $hppTransaksi > 0
                                        ? $omzetTransaksi - $hppTransaksi
                                        : $omzetTransaksi * 0.33;
                                @endphp

                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($t->tanggal ?? $t->created_at)->format('d/m/Y') }}</td>
                                    @if ($isAdminPusat)
                                        <td>{{ optional($t->cabang)->nama ?? '-' }}</td>
                                    @endif
                                    <td>{{ optional($t->user)->name ?? '-' }}</td>
                                    <td class="text-right">
                                        Rp {{ number_format($omzetTransaksi, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right">
                                        Rp {{ number_format($hppTransaksi, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right">
                                        <strong class="text-success">
                                            Rp {{ number_format($labaTransaksi, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $isAdminPusat ? 7 : 6 }}" class="text-center py-4">
                                        Belum ada transaksi pada tanggal ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- JURNAL TERBARU DAN RINGKASAN --}}
        <div class="row">
            <div class="col-lg-8 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Jurnal Terbaru</h4>
                        <div class="card-header-action">
                            <span class="badge badge-light">
                                {{ $jurnalTerbaru->count() }} Catatan
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        @if ($isAdminPusat)
                                            <th>Cabang</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($jurnalTerbaru as $jurnal)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') }}</td>
                                            <td>{{ $jurnal->keterangan ?? 'Jurnal akuntansi' }}</td>
                                            @if ($isAdminPusat)
                                                <td>{{ optional($jurnal->cabang)->nama ?? '-' }}</td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $isAdminPusat ? 3 : 2 }}" class="text-center py-4">
                                                Belum ada mutasi jurnal.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer text-right">
                        <a href="{{ route('jurnal.index') }}" class="btn btn-outline-primary">
                           <i class="fas fa-book-open mr-1"></i>
                           Lihat Jurnal Umum
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Ringkasan Jurnal</h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success">
                            <div class="text-small">Total Debit</div>
                            <h5 class="mb-0">Rp {{ number_format($totalDebit ?? 0, 0, ',', '.') }}</h5>
                        </div>

                        <div class="alert alert-primary">
                            <div class="text-small">Total Kredit</div>
                            <h5 class="mb-0">Rp {{ number_format($totalKredit ?? 0, 0, ',', '.') }}</h5>
                        </div>

                        <a href="{{ route('buku_besar.index') }}" class="btn btn-outline-primary btn-block">
                           <i class="fas fa-book mr-1"></i>
                           Lihat Buku Besar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik omzet --}}
    <script src="{{ asset('stisla/assets/modules/chart.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
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
                    legend: { display: false },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function (value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID', {
                                        notation: 'compact',
                                        maximumFractionDigits: 1
                                    }).format(value);
                                }
                            },
                            gridLines: { color: '#f2f2f2' }
                        }],
                        xAxes: [{
                            gridLines: { display: false }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem) {
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
