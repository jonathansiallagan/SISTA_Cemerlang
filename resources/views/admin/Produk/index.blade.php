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

        {{-- TABEL PRODUK --}}
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
                                        <a href="{{ route('produk.edit', $produk->id) }}"
                                           class="btn btn-primary btn-sm"
                                           title="Edit Produk">
                                            <i class="fas fa-edit"></i>
                                        </a>

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

    
    {{-- MODAL TAMBAH PRODUK --}}
    <div class="modal fade"
        id="modalTambahProduk"
        tabindex="-1"
        role="dialog"
        aria-labelledby="judulModalTambahProduk"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered"
            role="document">

            <div class="modal-content">

                <div class="modal-header bg-primary text-white">
                    <div>
                        <h5 class="modal-title text-white"
                            id="judulModalTambahProduk">
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

                        <h6 class="font-weight-bold text-primary mb-3">
                            Informasi Produk
                        </h6>

                        <div class="form-group">
                            <label for="modal_nama_produk">
                                Nama Produk / Layanan <span class="text-danger">*</span>
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
                                Satuan Produk <span class="text-danger">*</span>
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

                        <hr>

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

    
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalTambahProduk');
    const daftar = document.getElementById('modalDaftarBahan');
    const tombolTambah = document.getElementById('modalTambahBarisBahan');
    const form = document.getElementById('formTambahProduk');
    const tombolSimpan = document.getElementById('modalSimpanProduk');

    if (!modal || !daftar || !tombolTambah || !form) return;

    // Hindari masalah lapisan layout Stisla.
    document.body.appendChild(modal);

    function perbaruiNomor() {
        daftar.querySelectorAll('.modal-bahan-item').forEach((item, index) => {
            item.querySelector('.modal-nomor-bahan').textContent =
                'Bahan Baku ' + (index + 1);
        });
    }

    tombolTambah.addEventListener('click', function () {
        const pertama = daftar.querySelector('.modal-bahan-item');
        if (!pertama) return;

        const baru = pertama.cloneNode(true);

        baru.querySelector('select').value = '';
        baru.querySelectorAll('input').forEach(input => {
            input.value = '';
        });

        daftar.appendChild(baru);
        perbaruiNomor();
    });

    daftar.addEventListener('click', function (event) {
        const tombolHapus = event.target.closest('.modal-hapus-bahan');
        if (!tombolHapus) return;

        const item = tombolHapus.closest('.modal-bahan-item');
        const semuaItem = daftar.querySelectorAll('.modal-bahan-item');

        if (semuaItem.length === 1) {
            item.querySelector('select').value = '';
            item.querySelectorAll('input').forEach(input => {
                input.value = '';
            });
            return;
        }

        item.remove();
        perbaruiNomor();
    });

    form.addEventListener('submit', function () {
        tombolSimpan.disabled = true;
        tombolSimpan.innerHTML =
            '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
    });

    @if ($errors->any())
        if (window.jQuery && typeof jQuery.fn.modal === 'function') {
            jQuery(modal).modal('show');
        }
    @endif
});
</script>


</x-admin-layout-stisla>
