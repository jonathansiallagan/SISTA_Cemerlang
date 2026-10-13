<x-admin-layout>

    {{-- HEADER --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard Customer Service</h1>
        <p class="text-gray-500 mt-1">
            Ringkasan aktivitas {{ $namaCabang }} pada
            {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}.
        </p>
    </div>

    {{-- FILTER TANGGAL --}}
    <form method="GET" action="{{ route('cs') }}"
          class="bg-white rounded-xl border border-gray-200 p-4 mb-5 flex flex-wrap justify-between items-center gap-3">
        <div>
            <h2 class="font-semibold text-gray-800">Halo, {{ $user->name }}</h2>
            <p class="text-xs text-gray-400 mt-1">Cabang: {{ $namaCabang }}</p>
        </div>
        <input type="date" name="tanggal" value="{{ $tanggal }}" onchange="this.form.submit()"
               class="border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600">
    </form>

    {{-- STATISTIK --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500">Transaksi saya hari ini</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ $statistik['transaksi_saya'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500">Omzet saya</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">Rp {{ number_format($statistik['omzet_saya'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500">Transaksi cabang</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">{{ $statistik['transaksi_cabang'] }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <p class="text-xs text-gray-500">Omzet cabang</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">Rp {{ number_format($statistik['omzet_cabang'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- AKSI CEPAT --}}
    <div class="mb-5">
        <a href="{{ route('transaksi.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition">
            <i class="fa-solid fa-plus"></i> Buat Transaksi Baru
        </a>
    </div>

    {{-- TRANSAKSI TERBARU --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Transaksi terbaru cabang</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-500 uppercase border-b border-gray-200">
                    <tr>
                        <th class="py-2 pr-4">No. Invoice</th>
                        <th class="py-2 pr-4">Pelanggan</th>
                        <th class="py-2 pr-4">Dicatat oleh</th>
                        <th class="py-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksiTerbaru as $t)
                        <tr class="border-b border-gray-100">
                            <td class="py-2 pr-4 font-medium">{{ $t->no_invoice }}</td>
                            <td class="py-2 pr-4">{{ $t->nama_pelanggan }}</td>
                            <td class="py-2 pr-4">{{ $t->user->name ?? '-' }}</td>
                            <td class="py-2 text-right">Rp {{ number_format($t->total_transaksi, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-400">Belum ada transaksi pada tanggal ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-admin-layout>