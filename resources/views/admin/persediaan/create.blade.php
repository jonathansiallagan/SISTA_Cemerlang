
<x-admin-layout-stisla>
    <div class="section-header">
        <h1>Tambah Pembelian Bahan Baku</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item">Persediaan</div>
            <div class="breadcrumb-item">
                <a href="{{ route('persediaan.index') }}">Persediaan</a>
            </div>
            <div class="breadcrumb-item active">Pembelian</div>
        </div>
    </div>

    <div class="section-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Pembelian belum dapat disimpan.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($bahanBakus->isEmpty())
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Belum ada bahan baku aktif.
                <a href="{{ route('bahan-baku.create') }}" class="alert-link">
                    Daftarkan bahan baku terlebih dahulu.
                </a>
            </div>
        @endif

        <form action="{{ route('persediaan.store') }}" method="POST" id="formPembelian">
            @csrf

            <div class="card">
                <div class="card-header bg-primary py-2">
                    <h4 class="text-white mb-0">
                        <i class="fas fa-shopping-cart mr-2"></i>Informasi Pembelian
                    </h4>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tanggal_pembelian">
                                    Tanggal Pembelian <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="date"
                                    name="tanggal_pembelian"
                                    id="tanggal_pembelian"
                                    class="form-control @error('tanggal_pembelian') is-invalid @enderror"
                                    value="{{ old('tanggal_pembelian', now()->toDateString()) }}"
                                    required
                                >
                                @error('tanggal_pembelian')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_pemasok">Nama Pemasok</label>
                                <input
                                    type="text"
                                    name="nama_pemasok"
                                    id="nama_pemasok"
                                    class="form-control"
                                    value="{{ old('nama_pemasok') }}"
                                    placeholder="Nama toko atau pemasok"
                                    maxlength="255"
                                >
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group mb-0">
                                <label for="keterangan">Keterangan</label>
                                <textarea
                                    name="keterangan"
                                    id="keterangan"
                                    class="form-control"
                                    rows="2"
                                    placeholder="Catatan tambahan (opsional)"
                                >{{ old('keterangan') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                    <h4 class="mb-0">Detail Bahan yang Dibeli</h4>
                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        id="btnTambahBaris"
                        {{ $bahanBakus->isEmpty() ? 'disabled' : '' }}
                    >
                        <i class="fas fa-plus mr-1"></i>Tambah Baris
                    </button>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sista mb-0" id="tabelPembelian">
                            <thead>
                                <tr>
                                    <th style="min-width: 220px;">Bahan Baku</th>
                                    <th style="min-width: 110px;">Jumlah</th>
                                    <th style="min-width: 160px;">Harga Satuan (Rp)</th>
                                    <th style="min-width: 150px;" class="text-right">Subtotal (Rp)</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="daftarItem">
                                @php
                                    $oldItems = old('items', [
                                        ['bahan_baku_id' => '', 'jumlah' => '', 'harga_satuan' => '']
                                    ]);
                                @endphp

                                @foreach ($oldItems as $i => $oldItem)
                                    <tr class="baris-item">
                                        <td>
                                            <select
                                                name="items[{{ $i }}][bahan_baku_id]"
                                                class="form-control pilih-bahan"
                                                required
                                            >
                                                <option value="">-- Pilih Bahan --</option>
                                                @foreach ($bahanBakus as $bahan)
                                                    <option
                                                        value="{{ $bahan->id }}"
                                                        data-satuan="{{ $bahan->satuan }}"
                                                        @selected((string) ($oldItem['bahan_baku_id'] ?? '') === (string) $bahan->id)
                                                    >
                                                        {{ $bahan->kode_bahan }} - {{ $bahan->nama_bahan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted label-satuan">Satuan: -</small>
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                name="items[{{ $i }}][jumlah]"
                                                class="form-control input-jumlah"
                                                value="{{ $oldItem['jumlah'] ?? '' }}"
                                                min="0.001"
                                                step="0.001"
                                                placeholder="0"
                                                required
                                            >
                                        </td>

                                        <td>
                                            <input
                                                type="number"
                                                name="items[{{ $i }}][harga_satuan]"
                                                class="form-control input-harga"
                                                value="{{ $oldItem['harga_satuan'] ?? '' }}"
                                                min="0"
                                                step="0.01"
                                                placeholder="0"
                                                required
                                            >
                                        </td>

                                        <td class="text-right align-middle">
                                            <span class="subtotal">Rp 0,00</span>
                                        </td>

                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-danger btn-sm btn-hapus" title="Hapus baris">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row justify-content-end">
                        <div class="col-md-5 col-lg-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>Total Pembelian</strong>
                                <h5 class="text-primary mb-0" id="totalPembelian">Rp 0,00</h5>
                            </div>
                            <small class="text-muted">
                                Total dihitung dari jumlah dikali harga satuan setiap bahan.
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <a href="{{ route('persediaan.index') }}" class="btn btn-secondary mr-2">
                    <i class="fas fa-arrow-left mr-1"></i>Kembali
                </a>
                <button
                    type="submit"
                    class="btn btn-primary"
                    {{ $bahanBakus->isEmpty() ? 'disabled' : '' }}
                >
                    <i class="fas fa-save mr-1"></i>Simpan Pembelian
                </button>
            </div>
        </form>
    </div>

    
    @php
        $dataBahanOptions = $bahanBakus->map(function ($bahan) {
            return [
                'id' => $bahan->id,
                'label' => $bahan->kode_bahan . ' - ' . $bahan->nama_bahan,
                'satuan' => $bahan->satuan,
            ];
        })->values()->all();
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const daftarItem = document.getElementById('daftarItem');
            const btnTambah = document.getElementById('btnTambahBaris');
            const totalPembelian = document.getElementById('totalPembelian');

            // Data bahan baku dari Laravel.
            const bahanOptions = @json($dataBahanOptions);

            // Format angka ke Rupiah.
            const rupiah = angka => new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(angka);

            // Memperbarui nama input agar indeks selalu berurutan.
            function perbaruiNomorInput() {
                daftarItem.querySelectorAll('.baris-item').forEach((baris, index) => {
                    baris.querySelector('.pilih-bahan').name =
                        `items[${index}][bahan_baku_id]`;

                    baris.querySelector('.input-jumlah').name =
                        `items[${index}][jumlah]`;

                    baris.querySelector('.input-harga').name =
                        `items[${index}][harga_satuan]`;
                });
            }

            // Menghitung subtotal setiap baris dan total pembelian.
            function hitungTotal() {
                let total = 0;

                daftarItem.querySelectorAll('.baris-item').forEach(baris => {
                    const inputJumlah = baris.querySelector('.input-jumlah');
                    const inputHarga = baris.querySelector('.input-harga');

                    const jumlah = parseFloat(inputJumlah.value) || 0;
                    const harga = parseFloat(inputHarga.value) || 0;
                    const subtotal = jumlah * harga;

                    baris.querySelector('.subtotal').textContent = rupiah(subtotal);
                    total += subtotal;

                    const pilihan = baris.querySelector('.pilih-bahan');
                    const option = pilihan.options[pilihan.selectedIndex];

                    baris.querySelector('.label-satuan').textContent =
                        option && option.dataset.satuan
                            ? `Satuan: ${option.dataset.satuan}`
                            : 'Satuan: -';
                });

                totalPembelian.textContent = rupiah(total);
            }

            // Membuat baris bahan baku baru.
            function buatBaris() {
                const tr = document.createElement('tr');
                tr.className = 'baris-item';

                // Kolom bahan baku.
                const tdBahan = document.createElement('td');
                const select = document.createElement('select');

                select.className = 'form-control pilih-bahan';
                select.required = true;

                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = '-- Pilih Bahan --';
                select.appendChild(defaultOption);

                bahanOptions.forEach(bahan => {
                    const option = document.createElement('option');

                    option.value = bahan.id;
                    option.textContent = bahan.label;
                    option.dataset.satuan = bahan.satuan;

                    select.appendChild(option);
                });

                const satuan = document.createElement('small');
                satuan.className = 'text-muted label-satuan';
                satuan.textContent = 'Satuan: -';

                tdBahan.append(select, satuan);

                // Kolom jumlah.
                const tdJumlah = document.createElement('td');
                const inputJumlah = document.createElement('input');

                inputJumlah.type = 'number';
                inputJumlah.className = 'form-control input-jumlah';
                inputJumlah.min = '0.001';
                inputJumlah.step = '0.001';
                inputJumlah.placeholder = '0';
                inputJumlah.required = true;

                tdJumlah.appendChild(inputJumlah);

                // Kolom harga satuan.
                const tdHarga = document.createElement('td');
                const inputHarga = document.createElement('input');

                inputHarga.type = 'number';
                inputHarga.className = 'form-control input-harga';
                inputHarga.min = '0';
                inputHarga.step = '0.01';
                inputHarga.placeholder = '0';
                inputHarga.required = true;

                tdHarga.appendChild(inputHarga);

                // Kolom subtotal.
                const tdSubtotal = document.createElement('td');
                tdSubtotal.className = 'text-right align-middle';

                const subtotal = document.createElement('span');
                subtotal.className = 'subtotal';
                subtotal.textContent = rupiah(0);

                tdSubtotal.appendChild(subtotal);

                // Kolom aksi.
                const tdAksi = document.createElement('td');
                tdAksi.className = 'text-center align-middle';

                const hapus = document.createElement('button');
                hapus.type = 'button';
                hapus.className = 'btn btn-danger btn-sm btn-hapus';
                hapus.title = 'Hapus baris';
                hapus.innerHTML = '<i class="fas fa-trash"></i>';

                tdAksi.appendChild(hapus);

                // Gabungkan seluruh kolom ke baris.
                tr.append(tdBahan, tdJumlah, tdHarga, tdSubtotal, tdAksi);
                daftarItem.appendChild(tr);

                perbaruiNomorInput();
                hitungTotal();
            }

            // Tombol tambah baris.
            if (btnTambah) {
                btnTambah.addEventListener('click', buatBaris);
            }

            // Hitung ulang ketika jumlah atau harga berubah.
            daftarItem.addEventListener('input', function (event) {
                if (
                    event.target.classList.contains('input-jumlah') ||
                    event.target.classList.contains('input-harga')
                ) {
                    hitungTotal();
                }
            });

            // Tampilkan satuan ketika bahan dipilih.
            daftarItem.addEventListener('change', function (event) {
                if (event.target.classList.contains('pilih-bahan')) {
                    hitungTotal();
                }
            });

            // Hapus baris bahan.
            daftarItem.addEventListener('click', function (event) {
                const tombol = event.target.closest('.btn-hapus');

                if (!tombol) {
                    return;
                }

                const baris = tombol.closest('.baris-item');
                const semuaBaris = daftarItem.querySelectorAll('.baris-item');

                if (semuaBaris.length > 1) {
                    baris.remove();
                } else {
                    // Sisakan satu baris kosong agar form tetap bisa digunakan.
                    baris.querySelector('.pilih-bahan').value = '';
                    baris.querySelector('.input-jumlah').value = '';
                    baris.querySelector('.input-harga').value = '';
                }

                perbaruiNomorInput();
                hitungTotal();
            });

            // Inisialisasi awal termasuk data lama setelah validasi gagal.
            perbaruiNomorInput();
            hitungTotal();
        });
    </script>

</x-admin-layout-stisla>
