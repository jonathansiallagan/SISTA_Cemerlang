<x-admin-layout>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">
                Data Produk & Layanan
            </h1>
        </div>
        <a href="{{ route('produk.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Produk
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 font-bold border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4">Nama Produk/Layanan</th>
                    <th class="px-6 py-4">Satuan</th>
                    <th class="px-6 py-4 text-right">Harga Jual</th>
                    <th class="px-6 py-4 text-right">HPP</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($produks as $produk)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-gray-800">{{ $produk->nama_produk }}</td>
                    <td class="px-6 py-4">{{ $produk->satuan }}</td>
                    <td class="px-6 py-4 text-right font-bold text-green-600">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-right text-gray-600">Rp {{ number_format($produk->hpp, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center flex justify-center gap-2">
                        <a href="{{ route('produk.edit', $produk->id) }}" class="text-blue-500 hover:text-blue-700"><i class="fa-solid fa-pen-to-square"></i></a>
                        <form action="{{ route('produk.destroy', $produk->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada data produk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>