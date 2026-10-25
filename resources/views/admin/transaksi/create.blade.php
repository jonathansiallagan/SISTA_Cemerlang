<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Transaksi Baru</h1>
    </div>

    @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5 text-sm">
                @foreach($errors->all() as $pesan)
                    <li>{{ $pesan }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $barisLama = old('produk_id', ['']);
    @endphp

    <form action="{{ route('transaksi.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

            <!-- Form Kiri -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No Referensi</label>
                        <input type="text" name="no_referensi" value="{{ old('no_referensi', $no_referensi) }}" class="w-full px-4 py-2 border rounded-lg bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}" class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cabang Transaksi</label>
                        <select name="cabang_id" class="w-full px-4 py-2 border rounded-lg" required>
                            @foreach($cabangs as $cabang)
                                <option value="{{ $cabang->id }}" {{ (string) old('cabang_id', Auth::user()->cabang_id) === (string) $cabang->id ? 'selected' : '' }}>{{ $cabang->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="mb-4">
                <h3 class="font-bold text-gray-800 mb-4">Detail Produk</h3>

                <div id="item-list">
                    @foreach($barisLama as $i => $produkTerpilih)
                    <div class="item-row flex flex-wrap items-end gap-3 mb-3 pb-3 border-b border-gray-100">

                        <!-- Produk -->
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs text-gray-500 mb-1">Produk</label>
                            <select name="produk_id[]" class="w-full px-3 py-2 border rounded-lg produk-select" required onchange="calculateRow(this)">
                                <option value="">- Pilih Produk -</option>
                                @foreach($produks as $produk)
                                    <option
                                        value="{{ $produk->id }}"
                                        data-harga="{{ $produk->harga_jual }}"
                                        data-satuan="{{ strtolower($produk->satuan) }}"
                                        {{ (string) $produkTerpilih === (string) $produk->id ? 'selected' : '' }}
                                    >
                                        {{ $produk->nama_produk }} ({{ $produk->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Ukuran (hanya produk m2) -->
                        <div class="dimension-container hidden">
                            <label class="block text-xs text-gray-500 mb-1">Ukuran (meter)</label>
                            <div class="flex gap-2">
                                <div class="w-28">
                                    <input type="number" step="0.01" min="0" name="panjang[]" value="{{ old('panjang.' . $i) }}"
                                           class="w-full px-3 py-2 border rounded-lg panjang-input" placeholder="Panjang"
                                           oninput="calculateRow(this)">
                                </div>
                                <div class="w-28">
                                    <input type="number" step="0.01" min="0" name="lebar[]" value="{{ old('lebar.' . $i) }}"
                                           class="w-full px-3 py-2 border rounded-lg lebar-input" placeholder="Lebar"
                                           oninput="calculateRow(this)">
                                </div>
                            </div>
                            <div class="text-[11px] text-gray-400 mt-1 luas-text"></div>
                        </div>

                        <!-- Qty (pcs / lembar) -->
                        <div class="qty-container w-24">
                            <label class="block text-xs text-gray-500 mb-1">Qty</label>
                            <input type="number" name="jumlah[]" value="{{ old('jumlah.' . $i, 1) }}" min="1" step="1"
                                   class="w-full px-3 py-2 border rounded-lg jumlah-input"
                                   oninput="calculateRow(this)">
                        </div>

                        <!-- Subtotal (tampilan saja, nilai asli dihitung server) -->
                        <div class="w-40">
                            <label class="block text-xs text-gray-500 mb-1">Subtotal</label>
                            <input type="text" class="w-full px-3 py-2 border rounded-lg bg-gray-50 font-bold subtotal-display" readonly>
                        </div>

                        <button type="button" class="text-red-500 hover:text-red-700 pb-2" onclick="removeRow(this)" title="Hapus baris">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    @endforeach
                </div>

                <button type="button" onclick="addRow()" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                    <i class="fa-solid fa-plus"></i> Tambah Produk
                </button>
            </div>

            <!-- Form Kanan -->
            <div class="bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-700 text-white">
                <h3 class="font-bold text-gray-300 mb-4">Total Pembayaran</h3>
                <div class="text-4xl font-bold text-green-400 mb-6" id="grand-total-display">Rp 0</div>

                <button type="submit" class="w-full bg-blue-600 text-white px-6 py-4 rounded-lg font-bold text-lg hover:bg-blue-700 transition">
                    Proses Transaksi
                </button>
                <p class="text-xs text-gray-400 mt-4 text-center">* Panjang &amp; lebar hanya diisi jika satuan produk adalah meter persegi (m2).</p>
            </div>

        </div>
    </form>

    <script>
        function isM2(satuan) {
            return satuan === 'm2' || satuan === 'meter persegi' || satuan.includes('m²');
        }

        function calculateRow(element) {
            const row = element.closest('.item-row');
            const select = row.querySelector('.produk-select');
            const option = select.options[select.selectedIndex];

            const dimension = row.querySelector('.dimension-container');
            const qtyBox = row.querySelector('.qty-container');
            const panjangInput = row.querySelector('.panjang-input');
            const lebarInput = row.querySelector('.lebar-input');
            const qtyInput = row.querySelector('.jumlah-input');
            const luasText = row.querySelector('.luas-text');
            const display = row.querySelector('.subtotal-display');

            if (!option || !option.value) {
                dimension.classList.add('hidden');
                qtyBox.classList.remove('hidden');
                luasText.textContent = '';
                display.value = '';
                row.dataset.subtotal = 0;
                calculateGrandTotal();
                return;
            }

            const harga = parseFloat(option.getAttribute('data-harga')) || 0;
            const satuan = (option.getAttribute('data-satuan') || '').toLowerCase().trim();
            let pengali = 0;

            if (isM2(satuan)) {
                dimension.classList.remove('hidden');
                qtyBox.classList.add('hidden');

                const panjang = parseFloat(panjangInput.value) || 0;
                const lebar = parseFloat(lebarInput.value) || 0;
                const luas = Math.round(panjang * lebar * 100) / 100;

                luasText.textContent = luas > 0 ? 'Luas: ' + luas.toLocaleString('id-ID') + ' m²' : '';
                qtyInput.value = 1;
                pengali = luas;
            } else {
                dimension.classList.add('hidden');
                qtyBox.classList.remove('hidden');

                panjangInput.value = '';
                lebarInput.value = '';
                luasText.textContent = '';
                pengali = parseFloat(qtyInput.value) || 0;
            }

            const subtotal = harga * pengali;
            row.dataset.subtotal = subtotal;
            display.value = 'Rp ' + subtotal.toLocaleString('id-ID');

            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(function (row) {
                total += parseFloat(row.dataset.subtotal) || 0;
            });
            document.getElementById('grand-total-display').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        function addRow() {
            const list = document.getElementById('item-list');
            const baru = list.querySelector('.item-row').cloneNode(true);

            baru.querySelector('.produk-select').value = '';
            baru.querySelector('.panjang-input').value = '';
            baru.querySelector('.lebar-input').value = '';
            baru.querySelector('.jumlah-input').value = 1;

            list.appendChild(baru);
            calculateRow(baru.querySelector('.produk-select'));
        }

        function removeRow(btn) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length > 1) {
                btn.closest('.item-row').remove();
                calculateGrandTotal();
            } else {
                alert('Minimal harus ada 1 produk dalam transaksi.');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.item-row').forEach(function (row) {
                calculateRow(row.querySelector('.produk-select'));
            });
        });
    </script>
</x-admin-layout>