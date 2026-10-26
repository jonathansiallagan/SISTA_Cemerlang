<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Transaksi Baru</h1>
    </div>

    @if (session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <form action="{{ route('transaksi.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Form Kiri (Data Pelanggan) -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No Referensi</label>
                        <input type="text" name="no_referensi" value="{{ $no_referensi }}"
                            class="w-full px-4 py-2 border rounded-lg bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" class="w-full px-4 py-2 border rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cabang Transaksi</label>
                        <select name="cabang_id" class="w-full px-4 py-2 border rounded-lg" required>
                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->id }}"
                                    {{ Auth::user()->cabang_id == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="mb-4">
                <h3 class="font-bold text-gray-800 mb-4">Detail Produk</h3>

                <div id="item-list">
                    <div class="item-row grid grid-cols-[2.5fr_2fr_0.8fr_1.2fr] gap-3 mb-3 items-end">

                        <!-- Produk -->
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">
                                Produk
                            </label>

                            <select name="produk_id[]" class="w-full px-3 py-2 border rounded-lg produk-select" required
                                onchange="calculateRow(this)">
                                <option value="">- Pilih Produk -</option>

                                @foreach ($produks as $produk)
                                    <option value="{{ $produk->id }}" data-harga="{{ $produk->harga_jual }}"
                                        data-hpp="{{ $produk->hpp }}" data-satuan="{{ strtolower($produk->satuan) }}">
                                        {{ $produk->nama_produk }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Ukuran -->
                        <div class="dimension-container hidden">
                            <label class="block text-xs text-gray-500 mb-1">
                                Ukuran (meter)
                            </label>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] text-gray-400 mb-1">
                                        Panjang
                                    </label>

                                    <input type="number" step="0.01" min="0" name="panjang[]"
                                        class="w-full px-3 py-2 border rounded-lg panjang-input" placeholder="0.00"
                                        oninput="calculateRow(this)">
                                </div>

                                <div>
                                    <label class="block text-[11px] text-gray-400 mb-1">
                                        Lebar
                                    </label>

                                    <input type="number" step="0.01" min="0" name="lebar[]"
                                        class="w-full px-3 py-2 border rounded-lg lebar-input" placeholder="0.00"
                                        oninput="calculateRow(this)">
                                </div>
                            </div>

                            <input type="hidden" name="luas[]" class="luas-input">
                        </div>

                        <!-- Qty -->
                        <div class="qty-container">
                            <label class="block text-xs text-gray-500 mb-1">
                                Qty
                            </label>

                            <input type="number" name="jumlah[]"
                                class="w-full px-3 py-2 border rounded-lg jumlah-input" value="1" min="1"
                                oninput="calculateRow(this)">
                        </div>

                        <!-- Subtotal -->
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">
                                Subtotal
                            </label>

                            <input type="text"
                                class="w-full px-3 py-2 border rounded-lg bg-gray-50 font-bold subtotal-display"
                                readonly>

                            <input type="hidden" name="harga_satuan[]" class="harga-satuan-input">

                            <input type="hidden" name="hpp_satuan[]" class="hpp-satuan-input">

                            <input type="hidden" name="subtotal[]" class="subtotal-input">

                            <input type="hidden" name="total_hpp_item[]" class="total-hpp-item-input">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Kanan (Total & Pembayaran) -->
            <div class="bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-700 text-white">
                <h3 class="font-bold text-gray-300 mb-4">Total Pembayaran</h3>
                <div class="text-4xl font-bold text-green-400 mb-6" id="grand-total-display">Rp 0</div>

                <input type="hidden" name="total_transaksi_input" id="total_transaksi_input">
                <input type="hidden" name="total_hpp_input" id="total_hpp_input">

                <button type="submit"
                    class="w-full bg-blue-600 text-white px-6 py-4 rounded-lg font-bold text-lg hover:bg-blue-700 transition">
                    Proses Transaksi
                </button>
                <p class="text-xs text-gray-400 mt-4 text-center">* P/L hanya diisi jika satuan produk adalah
                    meter (m2).</p>
            </div>
        </div>
    </form>

    <script>
        function calculateRow(element) {

            // Ambil baris produk
            let row = element.closest('.item-row');

            // Ambil select produk
            let select = row.querySelector('.produk-select');

            // Ambil produk yang dipilih
            let option = select.options[select.selectedIndex];

            // Kalau belum memilih produk
            if (!option || !option.value) {
                return;
            }


            // =========================================================
            // DATA PRODUK
            // =========================================================

            let harga = parseFloat(option.getAttribute('data-harga')) || 0;

            let hpp = parseFloat(option.getAttribute('data-hpp')) || 0;

            let satuan = (option.getAttribute('data-satuan') || '').toLowerCase().trim();


            // =========================================================
            // ELEMENT INPUT
            // =========================================================

            let qtyInput = row.querySelector('.jumlah-input');

            let panjangInput = row.querySelector('.panjang-input');
            let lebarInput = row.querySelector('.lebar-input');

            let luasInput = row.querySelector('.luas-input');

            let dimensionContainer = row.querySelector('.dimension-container');

            let qtyContainer = row.querySelector('.qty-container');


            // =========================================================
            // MULTIPLIER
            // =========================================================

            let multiplier = 0;


            // =========================================================
            // JIKA SATUAN = METER PERSEGI
            // =========================================================

            if (
                satuan === 'm2' ||
                satuan === 'meter persegi' ||
                satuan.includes('m²')
            ) {

                // Tampilkan panjang & lebar
                dimensionContainer.classList.remove('hidden');

                // Sembunyikan quantity
                qtyContainer.classList.add('hidden');


                // Ambil panjang
                let panjang = parseFloat(panjangInput.value) || 0;


                // Ambil lebar
                let lebar = parseFloat(lebarInput.value) || 0;


                // Hitung luas
                let luas = panjang * lebar;


                // Simpan luas
                luasInput.value = luas;


                // Untuk m2, yang dikalikan adalah luas
                multiplier = luas;


                // Quantity tidak digunakan untuk m2
                qtyInput.value = 1;

            }


            // =========================================================
            // JIKA SATUAN = PCS / LEMBAR
            // =========================================================
            else {

                // Sembunyikan panjang & lebar
                dimensionContainer.classList.add('hidden');

                // Tampilkan quantity
                qtyContainer.classList.remove('hidden');


                // Kosongkan ukuran
                panjangInput.value = '';
                lebarInput.value = '';
                luasInput.value = '';


                // Ambil quantity
                let qty = parseFloat(qtyInput.value) || 0;


                // Untuk pcs / lembar
                multiplier = qty;
            }


            // =========================================================
            // HITUNG HARGA
            // =========================================================

            let subtotal = harga * multiplier;


            // =========================================================
            // HITUNG HPP
            // =========================================================

            let totalHpp = hpp * multiplier;


            // =========================================================
            // SIMPAN KE INPUT HIDDEN
            // =========================================================

            row.querySelector('.harga-satuan-input').value = harga;

            row.querySelector('.hpp-satuan-input').value = hpp;

            row.querySelector('.subtotal-input').value = subtotal;

            row.querySelector('.total-hpp-item-input').value = totalHpp;


            // =========================================================
            // TAMPILKAN SUBTOTAL
            // =========================================================

            row.querySelector('.subtotal-display').value = 'Rp ' + subtotal.toLocaleString('id-ID');


            // =========================================================
            // HITUNG TOTAL TRANSAKSI
            // =========================================================

            calculateGrandTotal();
        }


        // =============================================================
        // TOTAL SEMUA PRODUK
        // =============================================================

        function calculateGrandTotal() {

            let subtotals = document.querySelectorAll('.subtotal-input');

            let hpps = document.querySelectorAll('.total-hpp-item-input');


            let grandTotal = 0;

            let grandHpp = 0;


            // Hitung total penjualan
            subtotals.forEach(function(item) {

                grandTotal += parseFloat(item.value) || 0;

            });


            // Hitung total HPP
            hpps.forEach(function(item) {

                grandHpp += parseFloat(item.value) || 0;

            });


            // Simpan total transaksi
            document.getElementById('total_transaksi_input').value = grandTotal;


            // Simpan total HPP
            document.getElementById('total_hpp_input').value = grandHpp;


            // Tampilkan total
            document.getElementById('grand-total-display').innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        }
    </script>
</x-admin-layout>