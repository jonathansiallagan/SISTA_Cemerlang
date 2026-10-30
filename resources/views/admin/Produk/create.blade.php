
<x-admin-layout-stisla>
    <div class="section-header">
        <h1>Tambah Produk Baru</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item">
                <a href="{{ route('produk.index') }}">Produk &amp; Layanan</a>
            </div>
            <div class="breadcrumb-item active">Tambah Produk</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-12 col-lg-9">
                <div class="card">
                    <div class="card-header bg-primary py-2">
                        <h4 class="text-white mb-0">
                            <i class="fas fa-box mr-2"></i>
                            Form Produk, Layanan &amp; Komposisi Bahan
                        </h4>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Periksa kembali data yang diisi.</strong>
                                <ul class="mb-0 mt-2 pl-4">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('produk.store') }}" method="POST">
                            @csrf

                            <h6 class="font-weight-bold mb-3">
                                Informasi Produk
                            </h6>

                            <div class="form-group">
                                <label for="nama_produk">
                                    Nama Produk / Layanan
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_produk"
                                    name="nama_produk"
                                    class="form-control @error('nama_produk') is-invalid @enderror"
                                    value="{{ old('nama_produk') }}"
                                    placeholder="Contoh: Spanduk Flexi"
                                    maxlength="255"
                                    required
                                    autofocus
                                >
                                @error('nama_produk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="satuan">
                                    Satuan Produk
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    id="satuan"
                                    name="satuan"
                                    class="form-control @error('satuan') is-invalid @enderror"
                                    required
                                >
                                    <option value="">-- Pilih Satuan --</option>
                                    @foreach ([
                                        'pcs' => 'Pcs',
                                        'lembar' => 'Lembar',
                                        'm2' => 'Meter Persegi (m²)',
                                        'buku' => 'Buku',
                                        'unit' => 'Unit',
                                        'set' => 'Set'
                                    ] as $value => $label)
                                        <option value="{{ $value }}"
                                            {{ old('satuan') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h6 class="font-weight-bold mb-1">
                                        Komposisi Bahan Baku
                                    </h6>
                                    <small class="text-muted">
                                        Tambahkan bahan yang dibutuhkan untuk membuat produk ini.
                                    </small>
                                </div>

                                <button type="button" id="tambah-bahan"
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-plus mr-1"></i>
                                    Tambah Bahan
                                </button>
                            </div>

                            <div id="daftar-bahan">
                                @php
                                    $oldBahanIds = old('bahan_baku_id', ['']);
                                    $oldJumlah = old('jumlah_pemakaian', ['']);
                                    $oldSatuan = old('satuan_pemakaian', ['']);
                                @endphp

                                @foreach ($oldBahanIds as $i => $oldBahanId)
                                    <div class="border rounded p-3 mb-3 bahan-item">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong class="nomor-bahan">
                                                Bahan Baku {{ $i + 1 }}
                                            </strong>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger hapus-bahan">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>

                                        <div class="form-group">
                                            <label>Nama Bahan Baku</label>
                                            <select name="bahan_baku_id[]"
                                                class="form-control"
                                                required>
                                                <option value="">-- Pilih Bahan --</option>
                                                @foreach ($bahanBakus as $bahan)
                                                    <option value="{{ $bahan->id }}"
                                                        {{ (string) $oldBahanId === (string) $bahan->id ? 'selected' : '' }}>
                                                        {{ $bahan->nama_bahan }}
                                                        (Stok: {{ $bahan->stok }} {{ $bahan->satuan }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-row">
                                            <div class="col-md-6 form-group">
                                                <label>Jumlah Pemakaian</label>
                                                <input type="number"
                                                    name="jumlah_pemakaian[]"
                                                    class="form-control"
                                                    min="0.0001"
                                                    step="0.0001"
                                                    value="{{ $oldJumlah[$i] ?? '' }}"
                                                    placeholder="Contoh: 1 atau 0.03"
                                                    required>
                                            </div>

                                            <div class="col-md-6 form-group">
                                                <label>Satuan Pemakaian</label>
                                                <input type="text"
                                                    name="satuan_pemakaian[]"
                                                    class="form-control"
                                                    value="{{ $oldSatuan[$i] ?? '' }}"
                                                    placeholder="Contoh: m²/m² produk"
                                                    maxlength="30"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="alert alert-light border">
                                <i class="fas fa-info-circle text-primary mr-1"></i>
                                Jumlah pemakaian adalah kebutuhan bahan untuk satu satuan produk.
                                Contoh: 1 m² Flexi Banner per 1 m² Spanduk Flexi.
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('produk.index') }}"
                                    class="btn btn-light mr-2">
                                    <i class="fas fa-arrow-left mr-1"></i>
                                    Batal
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>
                                    Simpan Produk
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const daftarBahan = document.getElementById('daftar-bahan');
            const tombolTambah = document.getElementById('tambah-bahan');

            function perbaruiNomor() {
                daftarBahan.querySelectorAll('.bahan-item').forEach((item, index) => {
                    item.querySelector('.nomor-bahan').textContent =
                        'Bahan Baku ' + (index + 1);
                });
            }

            tombolTambah.addEventListener('click', function () {
                const itemPertama = daftarBahan.querySelector('.bahan-item');
                const itemBaru = itemPertama.cloneNode(true);

                itemBaru.querySelector('select').value = '';
                itemBaru.querySelectorAll('input').forEach(input => {
                    input.value = '';
                });

                daftarBahan.appendChild(itemBaru);
                perbaruiNomor();
            });

            daftarBahan.addEventListener('click', function (event) {
                const tombolHapus = event.target.closest('.hapus-bahan');

                if (!tombolHapus) return;

                const semuaItem = daftarBahan.querySelectorAll('.bahan-item');

                if (semuaItem.length === 1) {
                    const item = semuaItem[0];
                    item.querySelector('select').value = '';
                    item.querySelectorAll('input').forEach(input => {
                        input.value = '';
                    });
                    return;
                }

                tombolHapus.closest('.bahan-item').remove();
                perbaruiNomor();
            });
        });
    </script>
</x-admin-layout-stisla>
