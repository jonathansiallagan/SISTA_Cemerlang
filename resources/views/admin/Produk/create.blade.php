<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Tambah Produk Baru</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form action="{{ route('produk.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk / Layanan</label>
                <input type="text" name="nama_produk" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        Satuan
    </label>

    <select
        name="satuan"
        class="w-full px-4 py-2 border rounded-lg bg-white"
        required
    >
        <option value="">-- Pilih Satuan --</option>
        <option value="pcs">Pcs</option>
        <option value="lembar">Lembar</option>
        <option value="m2">Meter Persegi (m²)</option>
    </select>
</div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual (Rp)</label>
                    <input type="number" name="harga_jual" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Pokok Penjualan / HPP (Rp)</label>
                    <input type="number" name="hpp" class="w-full px-4 py-2 border rounded-lg" required>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium">Simpan Produk</button>
                <a href="{{ route('produk.index') }}" class="bg-gray-100 text-gray-600 px-6 py-2 rounded-lg font-medium">Batal</a>
            </div>
        </form>
    </div>
</x-admin-layout>