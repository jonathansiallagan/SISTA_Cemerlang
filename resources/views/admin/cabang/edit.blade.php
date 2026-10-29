
<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Edit Cabang</h1>
        <p class="text-gray-500 mt-1">
            Perbarui informasi cabang percetakan dengan lengkap.
        </p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form action="{{ route('cabang.update', $cabang->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nama Cabang --}}
            <div class="mb-4">
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Cabang
                </label>

                <input
                    type="text"
                    id="nama"
                    name="nama"
                    value="{{ old('nama', $cabang->nama) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Contoh: Cabang Arengka"
                    required
                >

                @error('nama')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nomor HP --}}
            <div class="mb-4">
                <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-1">
                    Nomor HP/Telepon
                </label>

                <input
                    type="text"
                    id="no_hp"
                    name="no_hp"
                    value="{{ old('no_hp', $cabang->no_hp) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Contoh: 081234567890"
                    required
                >

                @error('no_hp')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="mb-6">
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">
                    Alamat Lengkap
                </label>

                <textarea
                    id="alamat"
                    name="alamat"
                    rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Masukkan alamat lengkap cabang"
                    required
                >{{ old('alamat', $cabang->alamat) }}</textarea>

                @error('alamat')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex gap-3">
                <button
                    type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition font-medium"
                >
                    <i class="fa-solid fa-floppy-disk mr-2"></i>
                    Simpan Perubahan
                </button>

                <a
                    href="{{ route('cabang.index') }}"
                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition font-medium"
                >
                    <i class="fa-solid fa-xmark mr-2"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</x-admin-layout>
