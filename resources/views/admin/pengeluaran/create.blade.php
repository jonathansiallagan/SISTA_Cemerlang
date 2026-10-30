<x-admin-layout>
    <div class="mx-auto max-w-4xl space-y-6">
        {{-- Header --}}
        <div>
            <a href="{{ route('pengeluaran.index') }}"
                class="mb-4 inline-flex items-center gap-2 text-sm text-gray-500 hover:text-blue-600">
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
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <form action="{{ route('pengeluaran.store') }}" method="POST" class="space-y-7">
                @csrf

                {{-- Tanggal --}}
                <div>
                    <label for="tanggal" class="mb-2 block text-sm font-medium text-gray-700">
                        Tanggal Pengeluaran <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('tanggal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nomor Referensi --}}
                <div>
                    <label for="no_referensi_preview" class="mb-2 block text-sm font-medium text-gray-700">
                        Nomor Referensi
                    </label>
                    <input type="text" id="no_referensi_preview" value=""
                        placeholder="Dibuat otomatis saat disimpan" readonly
                        class="w-full rounded-lg border border-gray-300 bg-gray-50 px-4 py-2.5 text-sm text-gray-600">
                    <p class="mt-1 text-xs text-gray-500">
                        Nomor referensi ditentukan otomatis berdasarkan jenis beban dan tanggal.
                    </p>
                </div>


                {{-- Cabang --}}
                <div>
                    <label for="cabang_id" class="mb-2 block text-sm font-medium text-gray-700">
                        Cabang <span class="text-red-500">*</span>
                    </label>

                    <input type="hidden" id="cabang_id" name="cabang_id"
                        value="{{ old('cabang_id', Auth::user()->cabang_id) }}">

                    <input type="text" value="{{ Auth::user()->cabang?->nama ?? 'Cabang belum diatur' }}" readonly
                        class="w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-600">

                    @error('cabang_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>


                {{-- Jenis Beban --}}
                <div>
                    <label for="akun_id" class="mb-2 block text-sm font-medium text-gray-700">
                        Jenis Beban <span class="text-red-500">*</span>
                    </label>

                    <select id="akun_id" name="akun_id" required
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih Jenis Beban</option>
                        @foreach ($akuns as $akun)
                            <option value="{{ $akun->id }}" data-kode="{{ $akun->kode_akun }}"
                                {{ old('akun_id') == $akun->id ? 'selected' : '' }}>
                                {{ $akun->kode_akun }} — {{ $akun->nama_akun }}
                            </option>
                        @endforeach
                    </select>

                    @error('akun_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Keterangan --}}
                <div>
                    <label for="keterangan" class="mb-2 block text-sm font-medium text-gray-700">
                        Keterangan <span class="text-red-500">*</span>
                    </label>
                    <textarea id="keterangan" name="keterangan" rows="3" maxlength="1000" required
                        placeholder="Masukkan keterangan pengeluaran"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Qty dan Satuan --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="qty" class="mb-2 block text-sm font-medium text-gray-700">
                            Qty <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="qty" name="qty"
                               value="{{ old('qty', '1') }}"
                               min="1" max="9999999999" step="1"
                               required
                               placeholder="Masukkan jumlah"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('qty')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="satuan" class="mb-2 block text-sm font-medium text-gray-700">
                            Satuan <span class="text-red-500">*</span>
                        </label>

                        <input type="text" id="satuan" name="satuan" value="{{ old('satuan') }}" maxlength="30"
                            required placeholder="Contoh: Orang, Unit, Pcs"
                            oninput="this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1)"
                            class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">

                        @error('satuan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                    </div>
                </div>

                {{-- Harga --}}
                <div class="mb-5">
                    <label for="harga" class="mb-2 block text-sm font-medium text-gray-700">
                        Harga per Satuan (Rp) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <span
                            class="inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 px-4 text-sm text-gray-600">
                            Rp
                        </span>
                        <input type="number" id="harga" name="harga"
                               value="{{ old('harga') }}"
                               min="1" max="9999999999999.99"
                               step="1" required
                               placeholder="Masukkan harga"
                               class="w-full min-w-0 rounded-r-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    @error('harga')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Total Pengeluaran --}}
                <div class="rounded-xl border border-blue-100 bg-blue-50 p-6">
                    <label for="total_pengeluaran" class="mb-2 block text-sm font-semibold text-gray-700">
                        Total Pengeluaran
                    </label>
                    <div class="flex items-center gap-5">
                        <span class="font-semibold text-gray-600">Rp</span>
                        <input type="text" id="total_pengeluaran" value="0" readonly
                            class="w-full rounded-lg border border-blue-200 bg-white px-4 py-3 text-xl font-bold text-blue-700">
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        Total dihitung otomatis dari Qty × Harga per Satuan.
                    </p>
                </div>

                {{-- Tombol --}}
                <div class="mt-3 flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:justify-end">
                    <a href="{{ route('pengeluaran.index') }}"
                        class="rounded-lg border border-gray-300 px-5 py-2.5 text-center text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                        <i class="fa-solid fa-floppy-disk mr-1"></i>
                        Simpan Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const qtyInput = document.getElementById('qty');
            const hargaInput = document.getElementById('harga');
            const totalInput = document.getElementById('total_pengeluaran');
            const akunSelect = document.getElementById('akun_id');
            const tanggalInput = document.getElementById('tanggal');
            const referensiInput = document.getElementById('no_referensi_preview');

            const prefixMap = {
                '5-2100': 'PAY',
                '5-2200': 'UTIL',
                '5-2300': 'TRN',
                '5-2400': 'PRO',
                '5-2500': 'ADM',
                '5-2600': 'MNT'
            };

            function updateTotal() {
                const qty = Number(qtyInput.value) || 0;
                const harga = Number(hargaInput.value) || 0;
                const hasil = qty * harga;

                totalInput.value = new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                }).format(hasil);
            }

            function updateReferensiPreview() {
                const option = akunSelect.options[akunSelect.selectedIndex];
                const prefix = option?.dataset?.kode ?
                    prefixMap[option.dataset.kode] :
                    null;

                const tanggal = tanggalInput.value;

                if (!prefix || !tanggal) {
                    referensiInput.value = '';
                    referensiInput.placeholder =
                        'Pilih jenis beban dan tanggal';
                    return;
                }

                const tanggalFormat = tanggal.replaceAll('-', '');
                referensiInput.value =
                    `${prefix}-${tanggalFormat}-AUTO`;
            }

            qtyInput.addEventListener('input', updateTotal);
            hargaInput.addEventListener('input', updateTotal);
            akunSelect.addEventListener('change', updateReferensiPreview);
            tanggalInput.addEventListener('change', updateReferensiPreview);

            updateTotal();
            updateReferensiPreview();
        });
    </script>
</x-admin-layout>
