
<div class="w-full">

    {{-- JUDUL FORM --}}
    <div class="mb-5">
        <h4 class="text-lg font-bold text-gray-800">
            Form Transaksi Baru
        </h4>

        <p class="mt-1 text-sm text-gray-500">
            Isi informasi pelanggan, lalu tambahkan produk yang dipesan.
        </p>
    </div>

    <form action="{{ route('transaksi.store') }}"
          method="POST"
          id="form-transaksi-modal">

        @csrf

        {{-- INFORMASI TRANSAKSI --}}
        <div class="mb-7 grid grid-cols-1 gap-4 md:grid-cols-2">

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                    No. Referensi
                </label>

                <input type="text"
                       name="no_referensi"
                       value="{{ $no_referensi }}"
                       readonly
                       class="w-full rounded-lg border border-gray-300 bg-gray-100 px-3 py-2.5 text-sm text-gray-600 outline-none">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                    Tanggal
                </label>

                <input type="date"
                       name="tanggal"
                       value="{{ date('Y-m-d') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                    Nama Pelanggan
                </label>

                <input type="text"
                       name="nama_pelanggan"
                       maxlength="255"
                       required
                       placeholder="Nama pelanggan"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <div>
                <label class="mb-1.5 block text-sm font-semibold text-gray-700">
                    Nomor HP Pelanggan
                </label>

                <input type="tel"
                       name="nomor_hp"
                       maxlength="20"
                       inputmode="tel"
                       value="{{ old('nomor_hp') }}"
                       placeholder="Contoh: 081234567890"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

        </div>

        {{-- DETAIL PRODUK --}}
        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">

            <div>
                <h5 class="font-bold text-gray-800">
                    Daftar Produk
                </h5>

                <p class="mt-1 text-xs text-gray-500">
                    Tambahkan produk sesuai pesanan pelanggan.
                </p>
            </div>

            <button type="button"
                    id="modal-tambah-produk"
                    class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                <i class="fas fa-plus"></i>
                Tambah Produk
            </button>

        </div>

        {{-- TABEL PRODUK --}}
        <div class="w-full overflow-x-auto rounded-xl border border-gray-200">

            <table class="w-full min-w-[1000px] border-collapse text-sm">

                <thead class="bg-gray-100 text-gray-700">

                    <tr>
                        <th class="p-3 text-center font-semibold">No.</th>
                        <th class="p-3 text-left font-semibold">Produk</th>
                        <th class="p-3 text-left font-semibold">Ukuran (m)</th>
                        <th class="p-3 text-center font-semibold">Jumlah</th>
                        <th class="p-3 text-right font-semibold">Harga Barang (Rp)</th>
                        <th class="p-3 text-right font-semibold">Jasa Edit (Rp)</th>
                        <th class="p-3 text-right font-semibold">Subtotal</th>
                        <th class="p-3 text-center font-semibold">Aksi</th>
                    </tr>

                </thead>

                <tbody id="modal-daftar-produk"></tbody>

            </table>

        </div>

        {{-- TEMPLATE PRODUK --}}
        <template id="modal-template-produk">

            <tr class="modal-produk-row border-t border-gray-200 align-top hover:bg-gray-50">

                <td class="modal-nomor p-3 text-center text-gray-600">
                    1
                </td>

                {{-- PRODUK --}}
                <td class="p-3">

                    <select name="produk_id[]"
                            class="modal-produk-select w-full min-w-[180px] rounded-lg border border-gray-300 px-2 py-2 text-sm outline-none focus:border-blue-500"
                            required>

                        <option value="">-- Pilih produk --</option>

                        @foreach ($produks as $produk)
                            <option value="{{ $produk->id }}"
                                    data-satuan="{{ strtolower(trim($produk->satuan)) }}">
                                {{ $produk->nama_produk }} ({{ $produk->satuan }})
                            </option>
                        @endforeach

                    </select>

                    <p class="modal-status mt-1 text-xs text-gray-500">
                        Pilih produk.
                    </p>

                </td>

                {{-- UKURAN --}}
                <td class="p-3">

                    <div class="modal-ukuran hidden space-y-2">

                        <input type="number"
                               name="tinggi[]"
                               min="0.01"
                               step="0.01"
                               placeholder="Tinggi/panjang"
                               class="modal-tinggi w-full rounded-lg border border-gray-300 px-2 py-2 text-sm outline-none focus:border-blue-500">

                        <input type="number"
                               name="lebar[]"
                               min="0.01"
                               step="0.01"
                               placeholder="Lebar"
                               class="modal-lebar w-full rounded-lg border border-gray-300 px-2 py-2 text-sm outline-none focus:border-blue-500">

                        <p class="modal-luas text-xs font-medium text-blue-700"></p>

                    </div>

                    <span class="modal-tanpa-ukuran text-xs text-gray-400">
                        Tidak perlu
                    </span>

                </td>

                {{-- JUMLAH --}}
                <td class="p-3">

                    <input type="number"
                           name="jumlah[]"
                           min="1"
                           step="1"
                           value="1"
                           required
                           class="modal-jumlah w-20 rounded-lg border border-gray-300 px-2 py-2 text-center text-sm outline-none focus:border-blue-500">

                </td>

                {{-- HARGA BARANG --}}
                <td class="p-3">

                    <input type="number"
                           name="harga_jual[]"
                           min="0"
                           step="1"
                           value=""
                           placeholder="Rp"
                           required
                           class="modal-harga w-full min-w-[120px] rounded-lg border border-gray-300 px-2 py-2 text-right text-sm outline-none focus:border-blue-500">

                </td>

                {{-- JASA EDIT --}}
                <td class="p-3">

                    <input type="number"
                           name="jasa_edit[]"
                           min="0"
                           step="1"
                           value=""
                           placeholder="Rp"
                           class="modal-edit w-full min-w-[120px] rounded-lg border border-gray-300 px-2 py-2 text-right text-sm outline-none focus:border-blue-500">

                    <p class="mt-1 text-xs text-gray-500">
                        Opsional. Kosongkan jika tidak ada biaya jasa edit.
                    </p>

                </td>

                {{-- SUBTOTAL --}}
                <td class="p-3 text-right">

                    <span class="modal-subtotal whitespace-nowrap font-bold text-gray-800">
                        Rp0
                    </span>

                </td>

                {{-- AKSI --}}
                <td class="p-3 text-center">

                    <button type="button"
                            class="modal-hapus-produk rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 transition hover:bg-red-50">

                        <i class="fas fa-trash-alt mr-1"></i>
                        Hapus

                    </button>

                </td>

            </tr>

        </template>

        {{-- RINGKASAN --}}
        <div class="mt-5 grid grid-cols-1 gap-3 md:grid-cols-3">

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">

                <p class="text-xs font-medium text-gray-500">
                    Total Harga Barang
                </p>

                <p id="modal-total-barang"
                   class="mt-1 text-xl font-bold text-gray-800">
                    Rp0
                </p>

            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">

                <p class="text-xs font-medium text-gray-500">
                    Total Jasa Edit
                </p>

                <p id="modal-total-edit"
                   class="mt-1 text-xl font-bold text-gray-800">
                    Rp0
                </p>

            </div>

            <div class="rounded-xl border border-green-200 bg-green-50 p-4">

                <p class="text-xs font-medium text-green-700">
                    Total Pembayaran
                </p>

                <p id="modal-total-pendapatan"
                   class="mt-1 text-2xl font-bold text-green-700">
                    Rp0
                </p>

            </div>

        </div>

        {{-- TOMBOL FORM --}}
        <div class="mt-6 flex flex-col justify-end gap-3 border-t border-gray-200 pt-5 sm:flex-row">

            <button type="button"
                    id="modal-tombol-batal"
                    class="rounded-lg border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-100">

                <i class="fas fa-times mr-1"></i>
                Batal

            </button>

            <button type="submit"
                    id="modal-tombol-proses"
                    class="rounded-lg bg-blue-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-700">

                <i class="fas fa-save mr-1"></i>
                Simpan Transaksi

            </button>

        </div>

    </form>

</div>
