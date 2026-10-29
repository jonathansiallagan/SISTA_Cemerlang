
<x-cs-layout>
    <div class="max-w-[1600px] mx-auto space-y-6 pb-8">

        {{-- HEADER --}}
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                Transaksi Baru
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Input pesanan pelanggan, harga produk, dan jasa edit.
            </p>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800">
                <p class="font-bold mb-2">Periksa kembali input kamu:</p>
                <ul class="list-disc pl-5 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('transaksi.store') }}"
              method="POST"
              id="form-transaksi">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                {{-- =========================================
                     KOLOM KIRI: INFORMASI DAN INPUT PRODUK
                ========================================== --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- INFORMASI TRANSAKSI --}}
                    <section class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-blue-100 text-blue-700 font-bold">
                                1
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-gray-800">
                                    Informasi Transaksi
                                </h2>
                                <p class="text-sm text-gray-500">
                                    Informasi dasar pesanan pelanggan.
                                </p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    No. Referensi
                                </label>
                                <input type="text"
                                       name="no_referensi"
                                       value="{{ old('no_referensi', $no_referensi) }}"
                                       readonly
                                       class="w-full border border-gray-300 rounded-xl px-4 py-3 bg-gray-100 text-gray-600">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Tanggal <span class="text-red-500">*</span>
                                </label>
                                <input type="date"
                                       name="tanggal"
                                       value="{{ old('tanggal', date('Y-m-d')) }}"
                                       required
                                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Nama Pelanggan <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       name="nama_pelanggan"
                                       value="{{ old('nama_pelanggan') }}"
                                       maxlength="255"
                                       placeholder="Masukkan nama pelanggan"
                                       required
                                       class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Cabang <span class="text-red-500">*</span>
                                </label>
                                <select name="cabang_id"
                                        required
                                        class="w-full border border-gray-300 rounded-xl px-4 py-3 bg-white focus:ring-2 focus:ring-blue-500">
                                    @foreach ($cabangs as $cabang)
                                        <option value="{{ $cabang->id }}"
                                            {{ (string) old('cabang_id', auth()->user()->cabang_id) === (string) $cabang->id ? 'selected' : '' }}>
                                            {{ $cabang->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </section>

                    {{-- DETAIL PRODUK --}}
                    <section>
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 font-bold">
                                    2
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-800">
                                        Detail Produk
                                    </h2>
                                    <p class="text-sm text-gray-500">
                                        Tambahkan produk sesuai pesanan.
                                    </p>
                                </div>
                            </div>

                            <button type="button"
                                id="tambah-produk"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-3 rounded-xl transition">
                            <i class="fa-solid fa-plus mr-1"></i>
                            Tambah Produk
                        </button>
                        </div>

                        {{-- BARIS PRODUK DITAMBAHKAN OLEH JAVASCRIPT --}}
                        <div id="daftar-produk" class="space-y-4"></div>

                        <button type="button"
                                id="tambah-produk-bawah"
                                class="w-full mt-4 border-2 border-dashed border-blue-300 hover:border-blue-500 hover:bg-blue-50 text-blue-700 font-semibold rounded-xl p-4 transition">
                            <i class="fa-solid fa-plus mr-1"></i>
                            Tambah Produk Lain
                            <span class="block text-xs font-normal mt-1">
                                Produk baru akan muncul di paling atas.
                            </span>
                        </button>
                    </section>
                </div>

                {{-- =========================================
                     KOLOM KANAN: RINGKASAN STICKY
                ========================================== --}}
                <aside class="lg:col-span-1 lg:sticky lg:top-5">
                    <section class="bg-gray-900 text-white rounded-2xl shadow-lg overflow-hidden">

                        <div class="p-5">
                            <div class="flex items-center gap-3 mb-5">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                                    💰
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold">
                                        Ringkasan Transaksi
                                    </h2>
                                    <p class="text-xs text-gray-400">
                                        Perhitungan langsung
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <p class="text-sm text-gray-400">
                                        Jumlah Baris Produk
                                    </p>
                                    <p id="jumlah-baris"
                                       class="text-xl font-bold mt-1">1 produk</p>
                                </div>

                                <div class="border-t border-gray-700 pt-4">
                                    <p class="text-sm text-gray-400">
                                        Total Harga Barang
                                    </p>
                                    <p id="total-barang"
                                       class="text-xl font-bold mt-1">Rp0</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-400">
                                        Total Jasa Edit
                                    </p>
                                    <p id="total-edit"
                                       class="text-xl font-bold mt-1">Rp0</p>
                                </div>

                                <div class="border-t border-gray-700 pt-4">
                                    <p class="text-sm text-gray-400">
                                        Total yang Dibayar Pelanggan
                                    </p>
                                    <p id="total-pendapatan"
                                       class="text-3xl font-extrabold text-green-400 mt-2 break-words">
                                        Rp0
                                    </p>
                                </div>
                            </div>

                            <button type="submit"
                                    id="tombol-proses"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-3 rounded-xl mt-5 transition">
                                <i class="fa-solid fa-cash-register mr-2"></i>
                                Proses Transaksi
                            </button>

                            <a href="{{ route('transaksi.index') }}"
                               class="block text-center bg-white/10 hover:bg-white/20 font-semibold px-4 py-3 rounded-xl mt-3 transition">
                                Batal
                            </a>
                        </div>
                    </section>
                </aside>

            </div>
        </form>
    </div>

    {{-- =========================================
         TEMPLATE BARIS PRODUK
    ========================================== --}}
    <template id="template-produk">
        <div class="produk-row bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="flex items-center justify-between gap-3 bg-gray-50 border-b border-gray-200 px-4 py-4">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="nomor-produk flex-shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-blue-600 text-white font-bold">
                        1
                    </span>
                    <div class="min-w-0">
                        <h3 class="judul-produk font-bold text-gray-800 truncate">
                            Produk 1
                        </h3>
                        <p class="status-produk text-xs text-gray-500">
                            Silakan pilih produk
                        </p>
                    </div>
                </div>

                <button type="button"
                        class="hapus-produk flex-shrink-0 text-sm font-semibold text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg">
                    <i class="fa-solid fa-trash mr-1"></i>
                    Hapus
                </button>
            </div>

            <div class="p-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Produk <span class="text-red-500">*</span>
                        </label>

                        <select name="produk_id[]"
                                class="produk-select w-full border border-gray-300 rounded-xl px-4 py-3 bg-white focus:ring-2 focus:ring-blue-500"
                                required>
                            <option value="">-- Pilih produk --</option>
                            @foreach ($produks as $produk)
                                <option value="{{ $produk->id }}"
                                        data-satuan="{{ strtolower(trim($produk->satuan)) }}">
                                    {{ $produk->nama_produk }} ({{ $produk->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="ukuran-container hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tinggi / Panjang (meter)
                        </label>
                        <input type="number"
                               name="tinggi[]"
                               step="0.01"
                               min="0.01"
                               placeholder="Contoh: 1"
                               class="tinggi w-full border border-gray-300 rounded-xl px-4 py-3">
                    </div>

                    <div class="ukuran-container hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Lebar (meter)
                        </label>
                        <input type="number"
                               name="lebar[]"
                               step="0.01"
                               min="0.01"
                               placeholder="Contoh: 2"
                               class="lebar w-full border border-gray-300 rounded-xl px-4 py-3">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Jumlah Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="jumlah[]"
                               value="1"
                               min="1"
                               step="1"
                               class="jumlah w-full border border-gray-300 rounded-xl px-4 py-3"
                               required>
                        <p class="text-xs text-gray-500 mt-1">
                            Banyaknya barang yang dipesan.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Harga Barang (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="harga_jual[]"
                               min="0"
                               step="1"
                               placeholder="Masukkan harga barang"
                               class="harga-jual w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500"
                               required>
                        <p class="text-xs text-gray-500 mt-1">
                            Harga total untuk baris pesanan ini.
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Jasa Edit (Rp)
                        </label>
                        <input type="number"
                               name="jasa_edit[]"
                               min="0"
                               step="1"
                               placeholder="Masukkan biaya edit (opsional)"
                               class="jasa-edit w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500"
                               >
                        <p class="text-xs text-gray-500 mt-1">
                            Kosongkan jika tidak ada jasa edit.
                        </p>
                    </div>
                </div>

                {{-- RINGKASAN PER PRODUK --}}
                <div class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 font-bold mb-3">
                        Ringkasan Produk Ini
                    </p>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Luas Total</p>
                            <p class="luas-display font-bold text-gray-800 mt-1">-</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Harga Barang</p>
                            <p class="harga-display font-semibold text-gray-800 mt-1">Rp0</p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500">Jasa Edit</p>
                            <p class="edit-display font-semibold text-gray-800 mt-1">Rp0</p>
                        </div>

                        <div class="border-l border-gray-200 pl-3">
                            <p class="text-xs text-gray-500">Subtotal</p>
                            <p class="subtotal-total font-bold text-blue-700 text-lg mt-1">Rp0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
        (() => {
            const list = document.getElementById('daftar-produk');
            const template = document.getElementById('template-produk');
            const form = document.getElementById('form-transaksi');
            const tombolProses = document.getElementById('tombol-proses');

            const angka = input => Number.parseFloat(input?.value) || 0;

            const rupiah = nilai => 'Rp' +
                Number(nilai || 0).toLocaleString('id-ID', {
                    maximumFractionDigits: 0
                });

            function isM2(satuan) {
                return ['m2', 'm²', 'meter persegi'].includes(
                    (satuan || '').toLowerCase().trim()
                );
            }

            function hitungBaris(row) {
                const select = row.querySelector('.produk-select');
                const option = select.options[select.selectedIndex];
                const satuan = option?.dataset.satuan || '';
                const dipilih = Boolean(select.value);
                const berbasisLuas = dipilih && isM2(satuan);

                row.querySelectorAll('.ukuran-container').forEach(el => {
                    el.classList.toggle('hidden', !berbasisLuas);
                });

                const tinggi = row.querySelector('.tinggi');
                const lebar = row.querySelector('.lebar');

                tinggi.required = berbasisLuas;
                lebar.required = berbasisLuas;

                if (!berbasisLuas) {
                    tinggi.value = '';
                    lebar.value = '';
                }

                const jumlah = Math.max(1, angka(row.querySelector('.jumlah')));
                const luas = berbasisLuas
                    ? angka(tinggi) * angka(lebar) * jumlah
                    : 0;

                const harga = angka(row.querySelector('.harga-jual'));
                const edit = angka(row.querySelector('.jasa-edit'));

                row.querySelector('.judul-produk').textContent =
                    dipilih ? option.textContent.trim() : 'Produk';

                row.querySelector('.status-produk').textContent =
                    !dipilih
                        ? 'Silakan pilih produk'
                        : (berbasisLuas
                            ? 'Perhitungan berdasarkan luas m²'
                            : 'Perhitungan berdasarkan jumlah barang');

                row.querySelector('.luas-display').textContent =
                    berbasisLuas
                        ? luas.toLocaleString('id-ID', {
                            maximumFractionDigits: 2
                        }) + ' m²'
                        : '-';

                row.querySelector('.harga-display').textContent = rupiah(harga);
                row.querySelector('.edit-display').textContent = rupiah(edit);
                row.querySelector('.subtotal-total').textContent =
                    rupiah(harga + edit);

                hitungTotal();
            }

            function hitungTotal() {
                let totalBarang = 0;
                let totalEdit = 0;
                let jumlahBaris = 0;

                list.querySelectorAll('.produk-row').forEach(row => {
                    if (!row.querySelector('.produk-select').value) return;

                    jumlahBaris++;
                    totalBarang += angka(row.querySelector('.harga-jual'));
                    totalEdit += angka(row.querySelector('.jasa-edit'));
                });

                document.getElementById('jumlah-baris').textContent =
                    jumlahBaris + (jumlahBaris === 1 ? ' produk' : ' produk');

                document.getElementById('total-barang').textContent =
                    rupiah(totalBarang);

                document.getElementById('total-edit').textContent =
                    rupiah(totalEdit);

                document.getElementById('total-pendapatan').textContent =
                    rupiah(totalBarang + totalEdit);
            }

            function perbaruiNomor() {
                const rows = list.querySelectorAll('.produk-row');

                rows.forEach((row, index) => {
                    row.querySelector('.nomor-produk').textContent = index + 1;

                    const tombolHapus = row.querySelector('.hapus-produk');
                    tombolHapus.disabled = rows.length === 1;
                    tombolHapus.classList.toggle(
                        'opacity-40',
                        tombolHapus.disabled
                    );
                    tombolHapus.classList.toggle(
                        'cursor-not-allowed',
                        tombolHapus.disabled
                    );
                });
            }

            function tambahProduk() {
                const fragment = template.content.cloneNode(true);
                const rowBaru = fragment.querySelector('.produk-row');

                // Produk terbaru selalu muncul di atas.
                list.prepend(fragment);

                perbaruiNomor();
                hitungBaris(rowBaru);

                rowBaru.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                setTimeout(() => {
                    rowBaru.querySelector('.produk-select').focus();
                }, 250);
            }

            document.getElementById('tambah-produk')
                .addEventListener('click', tambahProduk);

            document.getElementById('tambah-produk-bawah')
                .addEventListener('click', tambahProduk);

            list.addEventListener('change', event => {
                const row = event.target.closest('.produk-row');
                if (row) hitungBaris(row);
            });

            list.addEventListener('input', event => {
                if (event.target.matches(
                    '.jumlah, .tinggi, .lebar, .harga-jual, .jasa-edit'
                )) {
                    hitungBaris(event.target.closest('.produk-row'));
                }
            });

            list.addEventListener('click', event => {
                const tombol = event.target.closest('.hapus-produk');

                if (!tombol || tombol.disabled) return;

                tombol.closest('.produk-row').remove();

                perbaruiNomor();
                hitungTotal();
            });

            form.addEventListener('submit', event => {
                const rows = list.querySelectorAll('.produk-row');

                if (rows.length === 0) {
                    event.preventDefault();
                    alert('Tambahkan minimal satu produk.');
                    return;
                }

                for (const row of rows) {
                    const select = row.querySelector('.produk-select');

                    if (!select.value) {
                        event.preventDefault();
                        alert('Pilih produk pada semua baris.');
                        select.focus();
                        return;
                    }

                    const option = select.options[select.selectedIndex];

                    if (isM2(option.dataset.satuan)) {
                        if (
                            angka(row.querySelector('.tinggi')) <= 0 ||
                            angka(row.querySelector('.lebar')) <= 0
                        ) {
                            event.preventDefault();
                            alert('Isi tinggi dan lebar produk m² dengan nilai lebih dari 0.');
                            row.querySelector('.tinggi').focus();
                            return;
                        }
                    }
                }

                // Input harga boleh kosong saat mengetik.
                // Sebelum dikirim, kosongkan menjadi 0 agar validasi Laravel lolos.
                list.querySelectorAll('.harga-jual, .jasa-edit').forEach(input => {
                    if (input.value.trim() === '') {
                        input.value = '0';
                    }
                });

                tombolProses.disabled = true;
                tombolProses.textContent = 'Memproses transaksi...';
                tombolProses.classList.add('opacity-70', 'cursor-wait');
            });

            // Buat satu baris produk awal.
            tambahProduk();

        })();
    </script>
</x-cs-layout>
