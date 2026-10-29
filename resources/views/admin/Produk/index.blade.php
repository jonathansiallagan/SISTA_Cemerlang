<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div class="section-header">
        <h1>Data Produk &amp; Layanan</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Produk &amp; Layanan</div>
        </div>
    </div>

    <div class="section-body">

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <p class="text-muted mb-2">
                Kelola daftar produk dan layanan percetakan CV Cemerlang.
            </p>

            <button type="button"
                    class="btn btn-primary btn-sm mb-2"
                    data-toggle="modal"
                    data-target="#modalTambahProduk">
                <i class="fas fa-plus mr-1"></i>
                Tambah Produk
            </button>
        </div>

        {{-- NOTIFIKASI --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 mb-3"
                 role="alert">
                <i class="fas fa-check-circle mr-1"></i>
                {{ session('success') }}

                <button type="button" class="close"
                        data-dismiss="alert" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show py-2 mb-3"
                 role="alert">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ session('error') }}

                <button type="button" class="close"
                        data-dismiss="alert" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- TABEL PRODUK: TAMPILAN DIPERTAHANKAN --}}
        <div class="card">
            <div class="card-header bg-primary py-2">
                <h4 class="text-white mb-0">
                    <i class="fas fa-box mr-2"></i>
                    Daftar Produk &amp; Layanan
                </h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sista">
                        <thead>
                            <tr>
                                <th width="7%" class="text-center">#</th>
                                <th>Nama Produk/Layanan</th>
                                <th width="20%">Satuan</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($produks as $produk)
                                <tr>
                                    {{-- NOMOR URUT --}}
                                    <td class="text-center align-middle text-muted">
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- NAMA PRODUK --}}
                                    <td class="align-middle font-weight-bold">
                                        {{ $produk->nama_produk }}
                                    </td>

                                    {{-- SATUAN --}}
                                    <td class="align-middle">
                                        {{ $produk->satuan }}
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center align-middle text-nowrap">
                                        <button type="button"
                                                class="btn btn-primary btn-sm btn-edit-produk"
                                                title="Edit Produk"
                                                data-id="{{ $produk->id }}"
                                                data-url="{{ route('produk.update', $produk->id) }}"
                                                data-nama="{{ $produk->nama_produk }}"
                                                data-satuan="{{ $produk->satuan }}"
                                                data-upah="{{ $produk->upah_operator ?? 0 }}"
                                                data-komposisi="{{ $produk->komposisi->map(fn ($item) => [
                                                    'bahan_baku_id' => $item->bahan_baku_id,
                                                    'jumlah_pemakaian' => $item->jumlah_pemakaian,
                                                    'satuan_pemakaian' => $item->satuan_pemakaian,
                                                ])->values()->toJson(JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <form action="{{ route('produk.destroy', $produk->id) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-danger btn-sm"
                                                    title="Hapus Produk">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-box-open fa-2x mb-2"></i>
                                        <p class="mb-0">
                                            Belum ada data produk.
                                        </p>
                                        <small>Silakan tambahkan produk atau layanan terlebih dahulu.</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer text-muted">
                <small>
                    Total produk dan layanan:
                    <strong>{{ $produks->count() }}</strong>
                </small>
            </div>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- MODAL TAMBAH PRODUK --}}
    {{-- ====================================================== --}}
    <div class="modal fade"
         id="modalTambahProduk"
         tabindex="-1"
         role="dialog"
         aria-labelledby="judulModalTambahProduk"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <div>
                        <h5 class="modal-title text-white" id="judulModalTambahProduk">
                            <i class="fas fa-box mr-2"></i>
                            Tambah Produk &amp; Layanan
                        </h5>
                        <small>Isi informasi produk dan komposisi bahan baku.</small>
                    </div>

                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal"
                            aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form action="{{ route('produk.store') }}"
                      method="POST"
                      id="formTambahProduk">
                    @csrf

                    <div class="modal-body">

                        {{-- INFORMASI PRODUK --}}
                        <h6 class="font-weight-bold text-primary mb-3">
                            Informasi Produk
                        </h6>

                        <div class="form-group">
                            <label for="modal_nama_produk">
                                Nama Produk / Layanan
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   id="modal_nama_produk"
                                   name="nama_produk"
                                   class="form-control @error('nama_produk') is-invalid @enderror"
                                   value="{{ old('nama_produk') }}"
                                   maxlength="255"
                                   placeholder="Contoh: Spanduk Flexi"
                                   required>

                            @error('nama_produk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="modal_satuan_produk">
                                Satuan Produk
                                <span class="text-danger">*</span>
                            </label>

                            <select id="modal_satuan_produk"
                                    name="satuan"
                                    class="form-control @error('satuan') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Satuan --</option>

                                @foreach ([
                                    'pcs' => 'Pcs',
                                    'lembar' => 'Lembar',
                                    'm2' => 'Meter Persegi (m²)',
                                    'buku' => 'Buku',
                                    'unit' => 'Unit',
                                    'set' => 'Set',
                                ] as $value => $label)
                                    <option value="{{ $value }}"
                                        @selected(old('satuan') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            @error('satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- UPAH OPERATOR --}}
                        <div class="form-group">
                            <label for="modal_upah_operator">
                                Upah Operator
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>

                                <input type="number"
                                       id="modal_upah_operator"
                                       name="upah_operator"
                                       class="form-control @error('upah_operator') is-invalid @enderror"
                                       value="{{ old('upah_operator', 0) }}"
                                       min="0"
                                       step="1"
                                       placeholder="Contoh: 5000"
                                       required>
                            </div>

                            @error('upah_operator')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror

                            <small class="form-text text-muted">
                                Masukkan upah operator untuk satu satuan produk.
                            </small>
                        </div>

                        <hr>

                        {{-- KOMPOSISI BAHAN BAKU --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-primary mb-1">
                                    Komposisi Bahan Baku
                                </h6>
                                <small class="text-muted">
                                    Tambahkan bahan yang diperlukan untuk satu satuan produk.
                                </small>
                            </div>

                            <button type="button"
                                    id="modalTambahBarisBahan"
                                    class="btn btn-success btn-sm">
                                <i class="fas fa-plus mr-1"></i> Tambah Bahan
                            </button>
                        </div>

                        <div id="modalDaftarBahan">
                            @php
                                $oldBahanIds = old('bahan_baku_id', ['']);
                                $oldJumlah = old('jumlah_pemakaian', ['']);
                                $oldSatuan = old('satuan_pemakaian', ['']);
                            @endphp

                            @foreach ($oldBahanIds as $i => $oldBahanId)
                                <div class="border rounded p-3 mb-3 modal-bahan-item">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong class="modal-nomor-bahan">
                                            Bahan Baku {{ $i + 1 }}
                                        </strong>

                                        <button type="button"
                                                class="btn btn-outline-danger btn-sm modal-hapus-bahan">
                                            <i class="fas fa-trash mr-1"></i> Hapus
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
                                                    @selected((string) $oldBahanId === (string) $bahan->id)>
                                                    {{ $bahan->nama_bahan }}
                                                    (Stok: {{ $bahan->stok }} {{ $bahan->satuan }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-row">
                                        <div class="form-group col-md-6">
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

                                        <div class="form-group col-md-6">
                                            <label>Satuan Pemakaian</label>

                                            <input type="text"
                                                   name="satuan_pemakaian[]"
                                                   class="form-control"
                                                   maxlength="30"
                                                   value="{{ $oldSatuan[$i] ?? '' }}"
                                                   placeholder="Contoh: m²"
                                                   required>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if ($errors->has('bahan_baku_id') ||
                             $errors->has('jumlah_pemakaian') ||
                             $errors->has('satuan_pemakaian'))
                            <div class="alert alert-danger">
                                Periksa kembali data komposisi bahan baku.
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif

                        <div class="alert alert-light border mb-0">
                            <i class="fas fa-info-circle text-primary mr-1"></i>
                            Jumlah pemakaian adalah kebutuhan bahan untuk satu satuan produk.
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button"
                                class="btn btn-light"
                                data-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit"
                                id="modalSimpanProduk"
                                class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i> Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ====================================================== --}}
    {{-- MODAL EDIT PRODUK --}}
    {{-- ====================================================== --}}
    <div class="modal fade"
         id="modalEditProduk"
         tabindex="-1"
         role="dialog"
         aria-labelledby="judulModalEditProduk"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <div>
                        <h5 class="modal-title text-white" id="judulModalEditProduk">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Produk &amp; Layanan
                        </h5>
                        <small>Ubah informasi produk dan komposisi bahan baku.</small>
                    </div>

                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal"
                            aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formEditProduk" method="POST" action="#">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="edit_produk_id" id="edit_produk_id">

                    <div class="modal-body">

                        {{-- INFORMASI PRODUK --}}
                        <h6 class="font-weight-bold text-primary mb-3">
                            Informasi Produk
                        </h6>

                        <div class="form-group">
                            <label for="edit_nama_produk">
                                Nama Produk / Layanan
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   name="nama_produk"
                                   id="edit_nama_produk"
                                   class="form-control"
                                   maxlength="255"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="edit_satuan_produk">
                                Satuan Produk
                                <span class="text-danger">*</span>
                            </label>

                            <select name="satuan"
                                    id="edit_satuan_produk"
                                    class="form-control"
                                    required>
                                <option value="">-- Pilih Satuan --</option>
                                <option value="pcs">Pcs</option>
                                <option value="lembar">Lembar</option>
                                <option value="m2">Meter Persegi (m²)</option>
                                <option value="buku">Buku</option>
                                <option value="unit">Unit</option>
                                <option value="set">Set</option>
                            </select>
                        </div>

                        {{-- UPAH OPERATOR --}}
                        <div class="form-group">
                            <label for="edit_upah_operator">
                                Upah Operator
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>

                                <input type="number"
                                       name="upah_operator"
                                       id="edit_upah_operator"
                                       class="form-control"
                                       min="0"
                                       step="1"
                                       placeholder="Contoh: 5000"
                                       required>
                            </div>

                            <small class="form-text text-muted">
                                Upah operator untuk satu satuan produk.
                            </small>
                        </div>

                        <hr>

                        {{-- KOMPOSISI BAHAN BAKU --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="font-weight-bold text-primary mb-1">
                                    Komposisi Bahan Baku
                                </h6>
                                <small class="text-muted">
                                    Atur kebutuhan bahan untuk satu satuan produk.
                                </small>
                            </div>

                            <button type="button"
                                    id="editTambahBarisBahan"
                                    class="btn btn-success btn-sm">
                                <i class="fas fa-plus mr-1"></i>
                                Tambah Bahan
                            </button>
                        </div>

                        <div id="editDaftarBahan">
                            {{-- Baris bahan baku diisi melalui JavaScript --}}
                        </div>

                        {{-- TEMPLATE BARIS BAHAN BAKU --}}
                        <template id="templateEditBahan">
                            <div class="border rounded p-3 mb-3 edit-bahan-item">

                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong class="edit-nomor-bahan">
                                        Bahan Baku 1
                                    </strong>

                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm edit-hapus-bahan">
                                        <i class="fas fa-trash mr-1"></i>
                                        Hapus
                                    </button>
                                </div>

                                <div class="form-group">
                                    <label>Nama Bahan Baku</label>

                                    <select name="bahan_baku_id[]"
                                            class="form-control edit-pilih-bahan"
                                            required>
                                        <option value="">-- Pilih Bahan --</option>

                                        @foreach ($bahanBakus as $bahan)
                                            <option value="{{ $bahan->id }}"
                                                    data-satuan="{{ $bahan->satuan }}">
                                                {{ $bahan->nama_bahan }}
                                                (Stok: {{ $bahan->stok }} {{ $bahan->satuan }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label>Jumlah Pemakaian</label>

                                        <input type="number"
                                               name="jumlah_pemakaian[]"
                                               class="form-control"
                                               min="0.0001"
                                               step="0.0001"
                                               placeholder="Contoh: 1 atau 0.03"
                                               required>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Satuan Pemakaian</label>

                                        <input type="text"
                                               name="satuan_pemakaian[]"
                                               class="form-control"
                                               maxlength="30"
                                               placeholder="Contoh: m²"
                                               required>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="alert alert-light border mb-0">
                            <i class="fas fa-info-circle text-primary mr-1"></i>
                            Jumlah pemakaian adalah kebutuhan bahan untuk satu satuan produk.
                        </div>
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>
                            Batal
                        </button>

                        <button type="submit"
                                id="btnUpdateProduk"
                                class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // =========================================
            // MODAL TAMBAH PRODUK
            // =========================================

            const modalTambah = document.getElementById('modalTambahProduk');
            const daftarTambah = document.getElementById('modalDaftarBahan');
            const tombolTambahBahan = document.getElementById('modalTambahBarisBahan');
            const formTambah = document.getElementById('formTambahProduk');
            const tombolSimpanTambah = document.getElementById('modalSimpanProduk');

            if (modalTambah) {
                document.body.appendChild(modalTambah);
            }

            function nomorBahanTambah() {
                if (!daftarTambah) return;

                daftarTambah.querySelectorAll('.modal-bahan-item').forEach((item, index) => {
                    const nomor = item.querySelector('.modal-nomor-bahan');

                    if (nomor) {
                        nomor.textContent = 'Bahan Baku ' + (index + 1);
                    }
                });
            }

            if (daftarTambah && tombolTambahBahan) {
                tombolTambahBahan.addEventListener('click', function () {
                    const pertama = daftarTambah.querySelector('.modal-bahan-item');

                    if (!pertama) return;

                    const baru = pertama.cloneNode(true);

                    baru.querySelectorAll('select, input').forEach(input => {
                        input.value = '';
                    });

                    daftarTambah.appendChild(baru);
                    nomorBahanTambah();
                });

                daftarTambah.addEventListener('click', function (event) {
                    const tombol = event.target.closest('.modal-hapus-bahan');

                    if (!tombol) return;

                    const item = tombol.closest('.modal-bahan-item');
                    const semua = daftarTambah.querySelectorAll('.modal-bahan-item');

                    if (semua.length === 1) {
                        item.querySelectorAll('select, input').forEach(input => {
                            input.value = '';
                        });

                        return;
                    }

                    item.remove();
                    nomorBahanTambah();
                });
            }

            if (formTambah && tombolSimpanTambah) {
                formTambah.addEventListener('submit', function () {
                    tombolSimpanTambah.disabled = true;
                    tombolSimpanTambah.innerHTML =
                        '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                });
            }

            // =========================================
            // MODAL EDIT PRODUK
            // =========================================

            const modalEdit = document.getElementById('modalEditProduk');
            const formEdit = document.getElementById('formEditProduk');
            const daftarEdit = document.getElementById('editDaftarBahan');
            const templateEdit = document.getElementById('templateEditBahan');
            const tombolTambahEdit = document.getElementById('editTambahBarisBahan');
            const tombolSimpanEdit = document.getElementById('btnUpdateProduk');

            if (modalEdit) {
                document.body.appendChild(modalEdit);
            }

            function tambahBarisEdit(data = {}) {
                if (!daftarEdit || !templateEdit) return;

                const fragmen = templateEdit.content.cloneNode(true);
                const item = fragmen.querySelector('.edit-bahan-item');

                const pilihBahan = item.querySelector('[name="bahan_baku_id[]"]');
                const jumlah = item.querySelector('[name="jumlah_pemakaian[]"]');
                const satuan = item.querySelector('[name="satuan_pemakaian[]"]');

                pilihBahan.value = data.bahan_baku_id ?? '';
                jumlah.value = data.jumlah_pemakaian ?? '';
                satuan.value = data.satuan_pemakaian ?? '';

                daftarEdit.appendChild(fragmen);
                perbaruiNomorEdit();
            }

            function perbaruiNomorEdit() {
                if (!daftarEdit) return;

                daftarEdit.querySelectorAll('.edit-bahan-item').forEach((item, index) => {
                    const nomor = item.querySelector('.edit-nomor-bahan');

                    if (nomor) {
                        nomor.textContent = 'Bahan Baku ' + (index + 1);
                    }
                });
            }

            if (tombolTambahEdit) {
                tombolTambahEdit.addEventListener('click', function () {
                    tambahBarisEdit();
                });
            }

            if (daftarEdit) {
                daftarEdit.addEventListener('click', function (event) {
                    const tombol = event.target.closest('.edit-hapus-bahan');

                    if (!tombol) return;

                    const item = tombol.closest('.edit-bahan-item');
                    const semua = daftarEdit.querySelectorAll('.edit-bahan-item');

                    if (semua.length === 1) {
                        item.querySelectorAll('select, input').forEach(input => {
                            input.value = '';
                        });

                        return;
                    }

                    item.remove();
                    perbaruiNomorEdit();
                });

                // Isi satuan bahan berdasarkan pilihan bahan jika masih kosong.
                daftarEdit.addEventListener('change', function (event) {
                    if (!event.target.matches('.edit-pilih-bahan')) return;

                    const item = event.target.closest('.edit-bahan-item');
                    const opsi = event.target.selectedOptions[0];
                    const satuan = item.querySelector('[name="satuan_pemakaian[]"]');

                    if (opsi && opsi.dataset.satuan && satuan && !satuan.value) {
                        satuan.value = opsi.dataset.satuan;
                    }
                });
            }

            // Klik tombol Edit pada tabel.
            document.querySelectorAll('.btn-edit-produk').forEach(function (tombol) {
                tombol.addEventListener('click', function () {
                    if (!modalEdit || !formEdit || !daftarEdit) return;

                    formEdit.action = this.dataset.url;

                    document.getElementById('edit_produk_id').value = this.dataset.id;
                    document.getElementById('edit_nama_produk').value = this.dataset.nama || '';
                    document.getElementById('edit_satuan_produk').value = this.dataset.satuan || '';

                    // Isi nilai upah operator dari produk yang dipilih.
                    document.getElementById('edit_upah_operator').value =
                        this.dataset.upah || 0;

                    // Kosongkan komposisi sebelumnya.
                    daftarEdit.innerHTML = '';

                    // Ambil komposisi yang sudah tersimpan dari tombol Edit.
                    let komposisi = [];

                    try {
                        komposisi = JSON.parse(this.dataset.komposisi || '[]');
                    } catch (error) {
                        console.error('Data komposisi produk tidak valid:', error);
                    }

                    if (komposisi.length > 0) {
                        komposisi.forEach(function (bahan) {
                            tambahBarisEdit(bahan);
                        });
                    } else {
                        tambahBarisEdit();
                    }

                    if (window.jQuery && typeof jQuery.fn.modal === 'function') {
                        jQuery(modalEdit).modal('show');
                    }
                });
            });

            if (formEdit && tombolSimpanEdit) {
                formEdit.addEventListener('submit', function () {
                    tombolSimpanEdit.disabled = true;
                    tombolSimpanEdit.innerHTML =
                        '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                });
            }

            // Buka kembali modal yang sesuai jika validasi gagal.
            @if ($errors->any())
                @if (old('edit_produk_id'))
                    if (modalEdit && window.jQuery && typeof jQuery.fn.modal === 'function') {
                        jQuery(modalEdit).modal('show');
                    }
                @else
                    if (modalTambah && window.jQuery && typeof jQuery.fn.modal === 'function') {
                        jQuery(modalTambah).modal('show');
                    }
                @endif
            @endif

        });
    </script>

</x-admin-layout-stisla>