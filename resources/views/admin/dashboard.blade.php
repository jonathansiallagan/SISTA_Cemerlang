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
    <form method="GET" action="{{ route('admin.dashboard') }}"
        class="bg-white rounded-xl border border-gray-200 p-4 mb-5
                flex flex-wrap justify-between items-center gap-3">

        <div>
            <h2 class="font-semibold text-gray-800">
                Ringkasan aktivitas seluruh cabang
            </h2>
            <p class="text-xs text-gray-400 mt-1">
                Diperbarui: {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
            </p>
        </div>

        <div class="flex gap-2">
            <select name="cabang_id" class="...">
                @if($isAdminPusat)
                <option value="semua" {{ $cabangId === 'semua' ? 'selected' : '' }}>Semua Cabang</option>
                @endif

                @foreach($daftarCabang as $cabang)
                <!-- Ganti 'nama' dengan nama kolom yang sesuai di tabel cabangs milikmu (misal: nama_cabang) -->
                <option value="{{ $cabang->id }}" {{ $cabangId == $cabang->id ? 'selected' : '' }}>
                    {{ $cabang->nama }}
                </option>
                @endforeach
            </select>

            <input type="date"
                name="tanggal"
                value="{{ $tanggal }}"
                onchange="this.form.submit()"
                class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600">
        </div>
    </form>


    {{-- =========================
        STATISTIK
    ========================== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">

        {{-- Omzet --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs text-gray-500">Omzet seluruh cabang</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-2">
                        Rp {{ number_format($omzet / 1000000, 1, ',', '.') }} jt
                    </h3>
                </div>
                <div class="w-9 h-9 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3
                                3 1.343 3 3-1.343 3-3 3m0-14V4m0 16v-2
                                M5 12H3m18 0h-2" />
                    </svg>
                </div>
            </div>
            <span class="inline-block mt-3 text-xs text-green-600 bg-green-50 px-2 py-1 rounded">
                ↑ 12,4%
            </span>
        </div>

        {{-- Transaksi --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex justify-between">
                <div>
                    <p class="text-xs text-gray-500">Transaksi hari ini</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-2">
                        {{ number_format($jumlahTransaksi) }}
                    </h3>
                </div>
                <div class="w-9 h-9 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6M7 4h10
                                a2 2 0 012 2v12a2 2 0 01-2 2H7
                                a2 2 0 01-2-2V6a2 2 0 012-2z" />
                    </svg>
                </div>
            </div>
            <span class="inline-block mt-3 text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded">
                ↑ 8,5%
            </span>
        </div>

        {{-- Laba --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex justify-between">
                <div>
                    <p class="text-xs text-gray-500">Laba bersih</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-2">
                        Rp {{ number_format($laba / 1000000, 1, ',', '.') }} jt
                    </h3>
                </div>
                <div class="w-9 h-9 bg-green-100 text-green-600 rounded-lg flex items-center justify-center">
                    ↗
                </div>
            </div>
            <span class="inline-block mt-3 text-xs text-green-600 bg-green-50 px-2 py-1 rounded">
                ↑ 9,6%
            </span>
        </div>

        {{-- Cabang --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex justify-between">
                <div>
                    <p class="text-xs text-gray-500">Cabang aktif</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-2">24 / 25</h3>
                </div>
                <div class="w-9 h-9 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center">
                    🏢
                </div>
            </div>
            <span class="inline-block mt-3 text-xs text-orange-600 bg-orange-50 px-2 py-1 rounded">
                1 offline
            </span>
        </div>
    </div>


    {{-- =========================
        GRAFIK
    ========================== --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5 mb-5">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="font-semibold text-gray-800">
                    Performa Pendapatan Keseluruhan Perusahaan
                </h2>
                <p class="text-xs text-gray-400">
                    Akumulasi pendapatan
                    @if($cabang && $cabang !== 'Semua Cabang')
                    – {{ $cabang }}
                    @endif
                </p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-400">Total hari ini</p>
                <p class="font-bold text-gray-700">
                    Rp {{ number_format($omzet, 0, ',', '.') }}
                </p>
                <span class="text-xs text-green-600">+12,4% dari kemarin</span>
            </div>
        </div>

        <div class="h-64">
            <canvas id="pusatChart"></canvas>
        </div>
    </div>


    {{-- =========================
        TABLE + PANEL
    ========================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- TABLE TRANSAKSI --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="p-5 flex justify-between">
                <div>
                    <h2 class="font-semibold text-gray-800">
                        Transaksi Terbaru
                        @if($cabang && $cabang !== 'Semua Cabang')
                        – {{ $cabang }}
                        @else
                        Seluruh Cabang
                        @endif
                    </h2>
                    <p class="text-xs text-gray-400">5 transaksi terakhir</p>
                </div>
                <a href="{{ route('transaksi.index') }}" class="text-xs text-green-600 font-medium">
                    Lihat semua →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs">
                        <tr>
                            <th class="text-left px-5 py-3">Waktu</th>
                            <th class="text-left px-5 py-3">Cabang</th>
                            <th class="text-left px-5 py-3">Pelanggan</th>
                            <th class="text-left px-5 py-3">Layanan</th>
                            <th class="text-right px-5 py-3">Nilai</th>
                            <th class="text-center px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transaksiTerbaru as $t)
                        <tr>
                            <td class="px-5 py-3">
                                {{ \Carbon\Carbon::parse($t->created_at)->format('H:i') }}
                            </td>
                            <td class="px-5 py-3">{{ $t->cabang ?? '-' }}</td>
                            <td class="px-5 py-3">{{ $t->nama_pelanggan ?? $t->pelanggan ?? '-' }}</td>
                            <td class="px-5 py-3">{{ $t->nama_layanan ?? $t->layanan ?? '-' }}</td>
                            <td class="px-5 py-3 text-right">
                                Rp {{ number_format($t->nominal ?? $t->total ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                @php
                                $status = $t->status ?? 'Pending';
                                $statusClass = match($status) {
                                'Selesai' => 'bg-green-50 text-green-600',
                                'Diproses' => 'bg-blue-50 text-blue-600',
                                'Pending' => 'bg-orange-50 text-orange-600',
                                default => 'bg-gray-50 text-gray-600',
                                };
                                @endphp
                                <span class="{{ $statusClass }} px-2 py-1 rounded-full text-xs">
                                    {{ $status }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-gray-400">
                                Tidak ada transaksi pada tanggal / cabang ini
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PANEL PUSAT --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex justify-between mb-4">
                <div>
                    <h2 class="font-semibold text-gray-800">Kelola Pusat</h2>
                    <p class="text-xs text-gray-400">Pintasan</p>
                </div>
            </div>

            <div class="space-y-3">
                <a href="{{ route('produk.index') }}"
                    class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 rounded-lg p-3">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Produk & Harga</p>
                        <p class="text-xs text-gray-400">Kelola daftar produk</p>
                    </div>
                    <span>›</span>
                </a>

                <a href="{{ route('user.index') }}"
                    class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 rounded-lg p-3">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Pengguna</p>
                        <p class="text-xs text-gray-400">Kelola akun pengguna</p>
                    </div>
                    <span>›</span>
                </a>

                <a href="{{ route('cabang.index') }}"
                    class="flex items-center justify-between bg-gray-50 hover:bg-gray-100 rounded-lg p-3">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Data Cabang</p>
                        <p class="text-xs text-gray-400">Kelola seluruh cabang</p>
                    </div>
                    <span>›</span>
                </a>

                <a href="#"
                    class="block w-full text-center bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg py-3 text-sm">
                    Lihat laporan laba rugi
                </a>
            </div>
        </div>
    </div>


    {{-- =========================
        CHART JS
    ========================== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const pusatCtx = document.getElementById('pusatChart');

        new Chart(pusatCtx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($chartData),
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    borderColor: '#22c55e',
                    backgroundColor: 'rgba(34, 197, 94, 0.15)',
                    pointBackgroundColor: '#22c55e',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value + ' jt';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>

</x-admin-layout>