<x-admin-layout>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Riwayat Transaksi</h1>
        </div>
        <a href="{{ route('transaksi.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
            <i class="fa-solid fa-plus"></i> Transaksi Baru
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 font-bold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">No Invoice</th>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Pelanggan</th>
                    <th class="px-6 py-4">Cabang</th>
                    <th class="px-6 py-4 text-right">Total Transaksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($transaksis as $trx)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-blue-600">{{ $trx->no_invoice }}</td>
                    <td class="px-6 py-4">{{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}</td>
                    <td class="px-6 py-4 font-bold">{{ $trx->nama_pelanggan }}</td>
                    <td class="px-6 py-4">{{ $trx->cabang->nama ?? '-' }}</td>
                    <td class="px-6 py-4 text-right font-bold text-gray-800">Rp {{ number_format($trx->total_transaksi, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>