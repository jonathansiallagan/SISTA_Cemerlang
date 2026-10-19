<x-admin-layout>
    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <a href="{{ route('pengeluaran.index') }}"
               class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600 mb-4">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali ke Riwayat Pengeluaran
            </a>

            <h1 class="text-2xl font-bold text-gray-800">
                Tambah Pengeluaran
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Masukkan informasi pengeluaran perusahaan.
            </p>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form action="{{ route('pengeluaran.store') }}"
                  method="POST"
                  class="space-y-5">

                @csrf

                {{-- Tanggal --}}
                <div>
                    <label for="tanggal"
                           class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Pengeluaran <span class="text-red-500">*</span>
                    </label>

                    <input type="date"
                           id="tanggal"
                           name="tanggal"
                           value="{{ old('tanggal', date('Y-m-d')) }}"
                           required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                                  text-sm focus:border-blue-500 focus:ring-blue-500">

                    @error('tanggal')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cabang --}}
                <div>
                    <label for="cabang_id"
                           class="block text-sm font-medium text-gray-700 mb-2">
                        Cabang <span class="text-red-500">*</span>
                    </label>

                    <select id="cabang_id"
                            name="cabang_id"
                            required
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                                   text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Cabang</option>

                        @foreach ($cabangs as $cabang)
                            <option value="{{ $cabang->id }}"
                                {{ old('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                {{ $cabang->nama_cabang }}
                            </option>
                        @endforeach
                    </select>

                    @error('cabang_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Keterangan --}}
                <div>
                    <label for="keterangan"
                           class="block text-sm font-medium text-gray-700 mb-2">
                        Keterangan <span class="text-red-500">*</span>
                    </label>

                    <textarea id="keterangan"
                              name="keterangan"
                              rows="4"
                              required
                              placeholder="Contoh: Pembelian alat tulis kantor"
                              class="w-full rounded-lg border border-gray-300 px-4 py-3
                                     text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('keterangan') }}</textarea>

                    @error('keterangan')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Total Pengeluaran --}}
                <div>
                    <label for="total_pengeluaran"
                           class="block text-sm font-medium text-gray-700 mb-2">
                        Total Pengeluaran (Rp) <span class="text-red-500">*</span>
                    </label>

                    <input type="number"
                           id="total_pengeluaran"
                           name="total_pengeluaran"
                           value="{{ old('total_pengeluaran') }}"
                           min="1"
                           step="0.01"
                           required
                           placeholder="Contoh: 150000"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                                  text-sm focus:border-blue-500 focus:ring-blue-500">

                    @error('total_pengeluaran')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol --}}
                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4
                            border-t border-gray-100">

                    <a href="{{ route('pengeluaran.index') }}"
                       class="inline-flex justify-center items-center px-5 py-2.5
                              rounded-lg border border-gray-300 text-gray-700
                              text-sm font-medium hover:bg-gray-50">
                        Batal
                    </a>

                    <button type="submit"
                            class="inline-flex justify-center items-center gap-2 px-5 py-2.5
                                   rounded-lg bg-blue-600 text-white text-sm font-semibold
                                   hover:bg-blue-700 transition">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Simpan Pengeluaran
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-admin-layout>
