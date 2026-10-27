
<x-cs-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Transaksi Baru</h1>
        <p class="text-sm text-gray-500 mt-1">
            Catat satu transaksi dengan satu atau beberapa produk.
        </p>
    </div>

    @if (session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4">
            <p class="font-semibold mb-1">Periksa kembali data transaksi:</p>
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transaksi.store') }}" method="POST" id="form-transaksi">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- FORM DATA TRANSAKSI --}}
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            No Referensi
                        </label>
                        <input
                            type="text"
                            name="no_referensi"
                            value="{{ old('no_referensi', $no_referensi) }}"
                            class="w-full px-4 py-2 border rounded-lg bg-gray-50"
                            readonly
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal
                        </label>
                        <input
                            type="date"
                            name="tanggal"
                            value="{{ old('tanggal', date('Y-m-d')) }}"
                            class="w-full px-4 py-2 border rounded-lg"
                            required
                        >
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nama Pelanggan
                        </label>
                        <input
                            type="text"
                            name="nama_pelanggan"
                            value="{{ old('nama_pelanggan') }}"
                            class="w-full px-4 py-2 border rounded-lg"
                            maxlength="255"
                            required
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Cabang Transaksi
                        </label>
                        <select
                            name="cabang_id"
                            class="w-full px-4 py-2 border rounded-lg"
                            required
                        >
                            @foreach ($cabangs as $cabang)
                                <option
                                    value="{{ $cabang->id }}"
                                    {{ (string) old('cabang_id', Auth::user()->cabang_id) === (string) $cabang->id ? 'selected' : '' }}
                                >
                                    {{ $cabang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="mb-5">

                {{-- JUDUL DETAIL PRODUK --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">Detail Produk</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Tambahkan semua produk dalam transaksi yang sama.
                        </p>
                    </div>

                    <button
                        type="button"
                        id="btn-tambah-produk"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition"
                    >
                        <i class="fa-solid fa-plus"></i>
                        Tambah Produk
                    </button>
                </div>

                {{-- DAFTAR BARIS PRODUK --}}
                <div id="item-list" class="space-y-4">

                    {{-- Baris produk pertama --}}
                    <div class="item-row rounded-xl border border-gray-200 p-4 bg-gray-50/50">

                        <div class="flex items-center justify-between mb-3">
                            <span class="nomor-produk text-sm font-bold text-gray-700">
                                Produk 1
                            </span>

                            <button
                                type="button"
                                class="btn-hapus-produk text-red-600 hover:text-red-800 text-sm font-medium"
                                aria-label="Hapus produk"
                            >
                                <i class="fa-solid fa-trash mr-1"></i>
                                Hapus
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                            {{-- Pilih produk --}}
                            <div class="sm:col-span-2">
                                <label class="block text-xs text-gray-500 mb-1">
                                    Produk
                                </label>

                                <select
                                    name="produk_id[]"
                                    class="produk-select w-full px-3 py-2 border rounded-lg bg-white"
                                    required
                                >
                                    <option value="">-- Pilih Produk --</option>

                                    @foreach ($produks as $produk)
                                        <option
                                            value="{{ $produk->id }}"
                                            data-harga="{{ $produk->harga_jual }}"
                                            data-hpp="{{ $produk->hpp }}"
                                            data-satuan="{{ strtolower(trim($produk->satuan)) }}"
                                        >
                                            {{ $produk->nama_produk }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Ukuran untuk produk meter persegi --}}
                            <div class="dimension-container hidden sm:col-span-2">
                                <label class="block text-xs text-gray-500 mb-1">
                                    Ukuran (meter)
                                </label>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">
                                            Tinggi / Panjang
                                        </label>
                                        <input
                                            type="number"
                                            name="tinggi[]"
                                            class="tinggi-input w-full px-3 py-2 border rounded-lg"
                                            min="1"
                                            step="1"
                                            placeholder="Contoh: 4"
                                            disabled
                                        >
                                    </div>

                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">
                                            Lebar
                                        </label>
                                        <input
                                            type="number"
                                            name="lebar[]"
                                            class="lebar-input w-full px-3 py-2 border rounded-lg"
                                            min="1"
                                            step="1"
                                            placeholder="Contoh: 3"
                                            disabled
                                        >
                                    </div>
                                </div>

                                <p class="text-xs text-gray-400 mt-2">
                                    Luas dihitung otomatis dari tinggi × lebar.
                                </p>
                            </div>

                            {{-- Qty untuk produk selain m2 --}}
                            <div class="qty-container">
                                <label class="block text-xs text-gray-500 mb-1">
                                    Kuantitas
                                </label>
                                <input
                                    type="number"
                                    name="jumlah[]"
                                    class="jumlah-input w-full px-3 py-2 border rounded-lg"
                                    value="1"
                                    min="1"
                                    step="1"
                                    required
                                >
                            </div>

                            {{-- Subtotal --}}
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">
                                    Subtotal Penjualan
                                </label>
                                <input
                                    type="text"
                                    class="subtotal-display w-full px-3 py-2 border rounded-lg bg-gray-100 font-semibold"
                                    value="Rp 0"
                                    readonly
                                >
                            </div>

                            {{-- Nilai untuk dikirim --}}
                            <input type="hidden" name="luas[]" class="luas-input" value="">

                            <input
                                type="hidden"
                                name="harga_satuan[]"
                                class="harga-satuan-input"
                                value=""
                            >

                            <input
                                type="hidden"
                                name="hpp_satuan[]"
                                class="hpp-satuan-input"
                                value=""
                            >

                            <input
                                type="hidden"
                                name="subtotal[]"
                                class="subtotal-input"
                                value="0"
                            >

                            <input
                                type="hidden"
                                name="total_hpp_item[]"
                                class="total-hpp-item-input"
                                value="0"
                            >
                        </div>

                        <div class="mt-3 flex justify-between gap-3 text-sm">
                            <span class="text-gray-500">HPP produk</span>
                            <span class="font-semibold text-gray-700 hpp-display">
                                Rp 0
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 rounded-lg bg-blue-50 text-blue-800 text-xs">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Produk bersatuan m² menggunakan perhitungan tinggi × lebar.
                    Produk bersatuan lain menggunakan kuantitas.
                </div>
            </div>

            {{-- RINGKASAN PEMBAYARAN --}}
            <div class="lg:col-span-1">
                <div class="bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-700 text-white lg:sticky lg:top-6">

                    <h3 class="font-bold text-gray-300 mb-2">
                        Total Pembayaran
                    </h3>

                    <div
                        class="text-3xl sm:text-4xl font-bold text-green-400 mb-6 break-words"
                        id="grand-total-display"
                    >
                        Rp 0
                    </div>

                    <div class="border-t border-gray-600 pt-4 mb-5 space-y-3">
                        <div class="flex justify-between gap-3 text-sm">
                            <span class="text-gray-300">Jumlah jenis produk</span>
                            <span id="jumlah-produk-display" class="font-semibold">1</span>
                        </div>

                        <div class="flex justify-between gap-3 text-sm">
                            <span class="text-gray-300">Total HPP</span>
                            <span id="grand-hpp-display" class="font-semibold">Rp 0</span>
                        </div>
                    </div>

                    <input
                        type="hidden"
                        name="total_transaksi_input"
                        id="total_transaksi_input"
                        value="0"
                    >

                    <input
                        type="hidden"
                        name="total_hpp_input"
                        id="total_hpp_input"
                        value="0"
                    >

                    <button
                        type="submit"
                        class="w-full bg-blue-600 text-white px-5 py-4 rounded-lg font-bold hover:bg-blue-700 transition"
                    >
                        <i class="fa-solid fa-check mr-2"></i>
                        Proses Transaksi
                    </button>

                    <p class="text-xs text-gray-400 mt-4 text-center">
                        Pastikan produk, ukuran, dan kuantitas sudah benar sebelum menyimpan.
                    </p>
                </div>
            </div>

        </div>
    </form>

    {{-- TEMPLATE BARIS PRODUK TAMBAHAN --}}
    <template id="item-template">
        <div class="item-row rounded-xl border border-gray-200 p-4 bg-gray-50/50">

            <div class="flex items-center justify-between mb-3">
                <span class="nomor-produk text-sm font-bold text-gray-700">
                    Produk
                </span>

                <button
                    type="button"
                    class="btn-hapus-produk text-red-600 hover:text-red-800 text-sm font-medium"
                    aria-label="Hapus produk"
                >
                    <i class="fa-solid fa-trash mr-1"></i>
                    Hapus
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                <div class="sm:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Produk</label>

                    <select
                        name="produk_id[]"
                        class="produk-select w-full px-3 py-2 border rounded-lg bg-white"
                        required
                    >
                        <option value="">-- Pilih Produk --</option>

                        @foreach ($produks as $produk)
                            <option
                                value="{{ $produk->id }}"
                                data-harga="{{ $produk->harga_jual }}"
                                data-hpp="{{ $produk->hpp }}"
                                data-satuan="{{ strtolower(trim($produk->satuan)) }}"
                            >
                                {{ $produk->nama_produk }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="dimension-container hidden sm:col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">Ukuran (meter)</label>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Tinggi / Panjang</label>
                            <input
                                type="number"
                                name="tinggi[]"
                                class="tinggi-input w-full px-3 py-2 border rounded-lg"
                                min="0.01"
                                step="0.01"
                                placeholder="Contoh: 4"
                                disabled
                            >
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Lebar</label>
                            <input
                                type="number"
                                name="lebar[]"
                                class="lebar-input w-full px-3 py-2 border rounded-lg"
                                min="0.01"
                                step="0.01"
                                placeholder="Contoh: 3"
                                disabled
                            >
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 mt-2">
                        Luas dihitung otomatis dari tinggi × lebar.
                    </p>
                </div>

                <div class="qty-container">
                    <label class="block text-xs text-gray-500 mb-1">Kuantitas</label>
                    <input
                        type="number"
                        name="jumlah[]"
                        class="jumlah-input w-full px-3 py-2 border rounded-lg"
                        value="1"
                        min="1"
                        step="1"
                        required
                    >
                </div>

                <div>
                    <label class="block text-xs text-gray-500 mb-1">Subtotal Penjualan</label>
                    <input
                        type="text"
                        class="subtotal-display w-full px-3 py-2 border rounded-lg bg-gray-100 font-semibold"
                        value="Rp 0"
                        readonly
                    >
                </div>

                <input type="hidden" name="luas[]" class="luas-input" value="">
                <input type="hidden" name="harga_satuan[]" class="harga-satuan-input" value="">
                <input type="hidden" name="hpp_satuan[]" class="hpp-satuan-input" value="">
                <input type="hidden" name="subtotal[]" class="subtotal-input" value="0">
                <input type="hidden" name="total_hpp_item[]" class="total-hpp-item-input" value="0">
            </div>

            <div class="mt-3 flex justify-between gap-3 text-sm">
                <span class="text-gray-500">HPP produk</span>
                <span class="font-semibold text-gray-700 hpp-display">Rp 0</span>
            </div>
        </div>
    </template>

    <script>
        (() => {
            const itemList = document.getElementById('item-list');
            const template = document.getElementById('item-template');
            const btnTambah = document.getElementById('btn-tambah-produk');
            const form = document.getElementById('form-transaksi');

            const rupiah = (nilai) => {
                return 'Rp ' + Number(nilai || 0).toLocaleString('id-ID', {
                    maximumFractionDigits: 2
                });
            };

            const angka = (nilai) => {
                return Number.parseFloat(nilai) || 0;
            };

            function isMeterPersegi(satuan) {
                const s = (satuan || '').toLowerCase().trim();

                return s === 'm2'
                    || s === 'm²'
                    || s === 'meter persegi'
                    || s.includes('m²');
            }

            function hitungBaris(row) {
                const select = row.querySelector('.produk-select');
                const option = select.options[select.selectedIndex];

                const qtyInput = row.querySelector('.jumlah-input');
                const tinggiInput = row.querySelector('.tinggi-input');
                const lebarInput = row.querySelector('.lebar-input');
                const luasInput = row.querySelector('.luas-input');

                const dimensionContainer = row.querySelector('.dimension-container');
                const qtyContainer = row.querySelector('.qty-container');

                const hargaInput = row.querySelector('.harga-satuan-input');
                const hppInput = row.querySelector('.hpp-satuan-input');
                const subtotalInput = row.querySelector('.subtotal-input');
                const totalHppInput = row.querySelector('.total-hpp-item-input');

                const subtotalDisplay = row.querySelector('.subtotal-display');
                const hppDisplay = row.querySelector('.hpp-display');

                if (!option || !option.value) {
                    dimensionContainer.classList.add('hidden');
                    qtyContainer.classList.remove('hidden');

                    tinggiInput.disabled = true;
                    lebarInput.disabled = true;
                    tinggiInput.required = false;
                    lebarInput.required = false;

                    luasInput.value = '';
                    hargaInput.value = '';
                    hppInput.value = '';
                    subtotalInput.value = '0';
                    totalHppInput.value = '0';

                    subtotalDisplay.value = rupiah(0);
                    hppDisplay.textContent = rupiah(0);

                    hitungTotal();
                    return;
                }

                const harga = angka(option.dataset.harga);
                const hpp = angka(option.dataset.hpp);
                const satuan = option.dataset.satuan || '';

                let multiplier = 0;

                if (isMeterPersegi(satuan)) {
                    dimensionContainer.classList.remove('hidden');
                    qtyContainer.classList.add('hidden');

                    tinggiInput.disabled = false;
                    lebarInput.disabled = false;
                    tinggiInput.required = true;
                    lebarInput.required = true;

                    const tinggi = angka(tinggiInput.value);
                    const lebar = angka(lebarInput.value);
                    const luas = tinggi * lebar;

                    luasInput.value = luas > 0 ? luas.toFixed(2) : '';
                    qtyInput.value = '1';

                    multiplier = luas;
                } else {
                    dimensionContainer.classList.add('hidden');
                    qtyContainer.classList.remove('hidden');

                    tinggiInput.disabled = true;
                    lebarInput.disabled = true;
                    tinggiInput.required = false;
                    lebarInput.required = false;

                    tinggiInput.value = '';
                    lebarInput.value = '';
                    luasInput.value = '';

                    multiplier = Math.max(0, angka(qtyInput.value));
                }

                const subtotal = Math.round((harga * multiplier + Number.EPSILON) * 100) / 100;
                const totalHpp = Math.round((hpp * multiplier + Number.EPSILON) * 100) / 100;

                hargaInput.value = harga;
                hppInput.value = hpp;
                subtotalInput.value = subtotal;
                totalHppInput.value = totalHpp;

                subtotalDisplay.value = rupiah(subtotal);
                hppDisplay.textContent = rupiah(totalHpp);

                hitungTotal();
            }

            function hitungTotal() {
                let totalPenjualan = 0;
                let totalHpp = 0;

                const rows = itemList.querySelectorAll('.item-row');

                rows.forEach((row) => {
                    totalPenjualan += angka(row.querySelector('.subtotal-input').value);
                    totalHpp += angka(row.querySelector('.total-hpp-item-input').value);
                });

                document.getElementById('total_transaksi_input').value = totalPenjualan.toFixed(2);
                document.getElementById('total_hpp_input').value = totalHpp.toFixed(2);

                document.getElementById('grand-total-display').textContent = rupiah(totalPenjualan);
                document.getElementById('grand-hpp-display').textContent = rupiah(totalHpp);
                document.getElementById('jumlah-produk-display').textContent = rows.length;
            }

            function perbaruiNomorProduk() {
                itemList.querySelectorAll('.item-row').forEach((row, index) => {
                    row.querySelector('.nomor-produk').textContent = 'Produk ' + (index + 1);

                    const btnHapus = row.querySelector('.btn-hapus-produk');
                    btnHapus.disabled = itemList.querySelectorAll('.item-row').length === 1;
                    btnHapus.classList.toggle(
                        'opacity-40',
                        btnHapus.disabled
                    );
                    btnHapus.classList.toggle(
                        'cursor-not-allowed',
                        btnHapus.disabled
                    );
                });
            }

            function tambahProduk() {
                const fragment = template.content.cloneNode(true);
                const row = fragment.querySelector('.item-row');

                itemList.appendChild(fragment);

                perbaruiNomorProduk();
                hitungBaris(row);
            }

            btnTambah.addEventListener('click', tambahProduk);

            itemList.addEventListener('change', (event) => {
                if (event.target.matches('.produk-select')) {
                    hitungBaris(event.target.closest('.item-row'));
                }
            });

            itemList.addEventListener('input', (event) => {
                if (event.target.matches('.jumlah-input, .tinggi-input, .lebar-input')) {
                    hitungBaris(event.target.closest('.item-row'));
                }
            });

            itemList.addEventListener('click', (event) => {
                const btnHapus = event.target.closest('.btn-hapus-produk');

                if (!btnHapus || btnHapus.disabled) {
                    return;
                }

                const row = btnHapus.closest('.item-row');
                row.remove();

                perbaruiNomorProduk();
                hitungTotal();
            });

            form.addEventListener('submit', (event) => {
                const rows = itemList.querySelectorAll('.item-row');

                if (rows.length === 0) {
                    event.preventDefault();
                    alert('Tambahkan minimal satu produk.');
                    return;
                }

                for (const row of rows) {
                    const select = row.querySelector('.produk-select');

                    if (!select.value) {
                        event.preventDefault();
                        alert('Pilih produk pada semua baris sebelum memproses transaksi.');
                        select.focus();
                        return;
                    }

                    const satuan = select.options[select.selectedIndex].dataset.satuan || '';

                    if (isMeterPersegi(satuan)) {
                        const tinggi = angka(row.querySelector('.tinggi-input').value);
                        const lebar = angka(row.querySelector('.lebar-input').value);

                        if (tinggi <= 0 || lebar <= 0) {
                            event.preventDefault();
                            alert('Isi tinggi/panjang dan lebar dengan nilai lebih dari 0.');
                            return;
                        }
                    } else if (angka(row.querySelector('.jumlah-input').value) < 1) {
                        event.preventDefault();
                        alert('Kuantitas produk minimal 1.');
                        return;
                    }
                }
            });

            perbaruiNomorProduk();
            hitungTotal();
        })();
    </script>
</x-cs-layout>
