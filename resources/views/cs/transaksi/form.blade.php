<div class="w-full">

    {{-- JUDUL FORM --}}
    <div class="mb-6">
        <h4 class="text-lg font-bold text-gray-900 dark:text-white">
            Form Transaksi Baru
        </h4>

        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Isi informasi pelanggan, lalu tambahkan produk yang dipesan.
        </p>
    </div>

    <form action="{{ route('transaksi.store') }}" method="POST" id="form-transaksi-modal">
        @csrf
        {{-- INFORMASI TRANSAKSI --}}
        <div class="mb-8 grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                    No. Referensi
                </label>

                <input type="text" name="no_referensi" value="{{ $no_referensi }}" readonly
                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800/80 px-4 py-2.5 text-xs font-bold text-gray-600 dark:text-gray-400 outline-none cursor-not-allowed">
            </div>

            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                    Tanggal
                </label>

                <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required
                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-xs text-gray-800 dark:text-gray-100 outline-none transition focus:border-[#041C88] dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30">
            </div>

            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                    Nama Pelanggan
                </label>

                <input type="text" name="nama_pelanggan" maxlength="255" required placeholder="Nama pelanggan"
                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-xs text-gray-800 dark:text-gray-100 outline-none transition focus:border-[#041C88] dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30">
            </div>

            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wide text-gray-700 dark:text-gray-300">
                    Nomor HP Pelanggan
                </label>

                <input type="tel" name="nomor_hp" maxlength="20" inputmode="tel" value="{{ old('nomor_hp') }}"
                    placeholder="Contoh: 081234567890"
                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-4 py-2.5 text-xs text-gray-800 dark:text-gray-100 outline-none transition focus:border-[#041C88] dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30">
            </div>
        </div>

        {{-- DETAIL PRODUK HEADER --}}
        <div
            class="mb-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 dark:border-gray-800 pt-6">
            <div>
                <h5 class="font-bold text-gray-900 dark:text-white text-base">
                    Daftar Produk
                </h5>
                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                    Tambahkan rincian produk yang dipesan.
                </p>
            </div>

            <button type="button" id="modal-tambah-produk"
                class="inline-flex items-center rounded-xl bg-[#041C88] hover:bg-[#122bad] dark:bg-blue-600 dark:hover:bg-blue-700 px-4 py-2 text-xs font-bold text-white shadow-md transition hover:-translate-y-0.5">
                <i class="fas fa-plus mr-1.5"></i>
                Tambah Produk
            </button>
        </div>

        {{-- TABEL PRODUK --}}
        <div class="w-full overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
            <table class="w-full min-w-[1000px] border-collapse text-xs">
                <thead
                    class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-800">

                    <tr>
                        <th class="p-3.5 text-center font-bold">No.</th>
                        <th class="p-3.5 text-left font-bold">Produk</th>
                        <th class="p-3.5 text-left font-bold">Ukuran (m)</th>
                        <th class="p-3.5 text-center font-bold">Jumlah</th>
                        <th class="p-3.5 text-right font-bold">Harga Barang (Rp)</th>
                        <th class="p-3.5 text-right font-bold">Jasa Edit (Rp)</th>
                        <th class="p-3.5 text-right font-bold">Subtotal</th>
                        <th class="p-3.5 text-center font-bold">Aksi</th>
                    </tr>

                </thead>
                <tbody id="modal-daftar-produk"
                    class="divide-y divide-gray-200 dark:divide-gray-800 text-gray-800 dark:text-gray-200"></tbody>
            </table>
        </div>

        {{-- TEMPLATE PRODUK --}}
        <template id="modal-template-produk">
            <tr class="modal-produk-row align-top transition-colors hover:bg-gray-50/70 dark:hover:bg-gray-800/40">
                <td class="modal-nomor p-3.5 text-center font-bold text-gray-500 dark:text-gray-400">
                    1
                </td>

                {{-- PRODUK --}}
                <td class="p-3.5">
                    <select name="produk_id[]"
                        class="modal-produk-select w-full min-w-[180px] rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-xs outline-none focus:border-[#041C88] dark:focus:border-blue-500"
                        required>

                        <option value="">-- Pilih produk --</option>

                        @foreach ($produks as $produk)
                            <option value="{{ $produk->id }}" data-satuan="{{ strtolower(trim($produk->satuan)) }}">
                                {{ $produk->nama_produk }} ({{ $produk->satuan }})
                            </option>
                        @endforeach
                    </select>

                    <p class="modal-status mt-1.5 text-[11px] text-gray-500 dark:text-gray-400">
                        Pilih produk.
                    </p>
                </td>

                {{-- UKURAN --}}
                <td class="p-3.5">
                    <div class="modal-ukuran hidden space-y-2">
                        <input type="number" name="tinggi[]" min="0.01" step="0.01" placeholder="Tinggi/panjang"
                            class="modal-tinggi w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-xs outline-none focus:border-[#041C88] dark:focus:border-blue-500">

                        <input type="number" name="lebar[]" min="0.01" step="0.01" placeholder="Lebar"
                            class="modal-lebar w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-xs outline-none focus:border-[#041C88] dark:focus:border-blue-500">

                        <p class="modal-luas text-[11px] font-bold text-[#041C88] dark:text-blue-400"></p>
                    </div>

                    <span class="modal-tanpa-ukuran text-[11px] text-gray-400 dark:text-gray-500">
                        Tidak perlu
                    </span>
                </td>

                {{-- JUMLAH --}}
                <td class="p-3.5">
                    <input type="number" name="jumlah[]" min="1" step="1" value="1" required
                        class="modal-jumlah w-20 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-center text-xs outline-none focus:border-[#041C88] dark:focus:border-blue-500">
                </td>

                {{-- HARGA BARANG --}}
                <td class="p-3.5">
                    <input type="number" name="harga_jual[]" min="0" step="1" value=""
                        placeholder="Rp" required
                        class="modal-harga w-full min-w-[120px] rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-right text-xs outline-none focus:border-[#041C88] dark:focus:border-blue-500">
                </td>

                {{-- JASA EDIT --}}
                <td class="p-3.5">
                    <input type="number" name="jasa_edit[]" min="0" step="1" value=""
                        placeholder="Rp"
                        class="modal-edit w-full min-w-[120px] rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 px-3 py-2 text-right text-xs outline-none focus:border-[#041C88] dark:focus:border-blue-500">

                    <p class="mt-1 text-[10px] text-gray-400 dark:text-gray-500">
                        Opsional
                    </p>
                </td>

                {{-- SUBTOTAL --}}
                <td class="p-3.5 text-right">
                    <span class="modal-subtotal whitespace-nowrap font-extrabold text-gray-900 dark:text-white">
                        Rp0
                    </span>
                </td>

                {{-- AKSI --}}
                <td class="p-3.5 text-center">
                    <button type="button"
                        class="modal-hapus-produk inline-flex items-center rounded-xl bg-[#fc544b] hover:bg-red-600 px-3 py-2 text-xs font-bold text-white transition hover:shadow-md">
                        <i class="fas fa-trash-alt mr-1"></i>
                        Hapus
                    </button>
                </td>
            </tr>
        </template>

        {{-- RINGKASAN HARGA --}}
        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60 p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Total Harga Barang
                </p>
                <p id="modal-total-barang" class="mt-1 text-xl font-extrabold text-gray-900 dark:text-white">
                    Rp0
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60 p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Total Jasa Edit
                </p>
                <p id="modal-total-edit" class="mt-1 text-xl font-extrabold text-gray-900 dark:text-white">
                    Rp0
                </p>
            </div>

            <div
                class="rounded-2xl border border-emerald-200 dark:border-emerald-900/40 bg-emerald-50 dark:bg-emerald-950/30 p-4">
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                    Total Pembayaran
                </p>
                <p id="modal-total-pendapatan"
                    class="mt-1 text-2xl font-extrabold text-emerald-700 dark:text-emerald-400">
                    Rp0
                </p>
            </div>
        </div>

        {{-- TOMBOL FORM --}}
        <div
            class="mt-8 flex flex-col justify-end gap-3 border-t border-gray-100 dark:border-gray-800 pt-6 sm:flex-row">
            <button type="button" id="modal-tombol-batal"
                class="inline-flex items-center justify-center rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 shadow-sm transition hover:bg-gray-50 dark:hover:bg-gray-700">
                <i class="fas fa-times mr-1.5"></i>
                Batal
            </button>

            <button type="button" id="modal-tombol-proses"
                class="inline-flex items-center justify-center rounded-xl bg-[#47c363] hover:bg-green-600 px-6 py-2.5 text-xs font-bold text-white shadow-lg shadow-green-500/20 transition hover:-translate-y-0.5"
                data-confirm-form="form-transaksi-modal" data-confirm-theme="success"
                data-confirm-title="Simpan Transaksi"
                data-confirm-message="Apakah Anda yakin ingin menyimpan transaksi ini? Pastikan seluruh data pelanggan, rincian produk, dan nominal harga telah terisi dengan benar."
                data-confirm-action="Ya, Simpan Transaksi">
                <i class="fas fa-save mr-1.5"></i>
                Simpan Transaksi
            </button>
        </div>
    </form>
</div>
