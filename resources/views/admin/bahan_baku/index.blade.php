
<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div class="section-header">
        <h1>Data Bahan Baku</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Persediaan</div>
            <div class="breadcrumb-item">Bahan Baku</div>
        </div>
    </div>

    <div class="section-body">

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <p class="text-muted mb-2">
                Kelola daftar bahan baku yang digunakan dalam proses produksi percetakan CV Cemerlang.
            </p>

            <button type="button"
                    class="btn btn-primary btn-sm mb-2"
                    data-toggle="modal"
                    data-target="#modalTambahBahan">
                <i class="fas fa-plus mr-1"></i>
                Tambah Bahan Baku
            </button>
        </div>

        {{-- NOTIFIKASI SUKSES --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 mb-3"
                 role="alert">
                <i class="fas fa-check-circle mr-1"></i>
                {{ session('success') }}

                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- NOTIFIKASI ERROR --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show py-2 mb-3"
                 role="alert">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ session('error') }}

                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- TABEL BAHAN BAKU --}}
        <div class="card">
            <div class="card-header bg-primary py-2">
                <h4 class="text-white mb-0">
                    <i class="fas fa-boxes mr-2"></i>
                    Daftar Bahan Baku
                </h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sista">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th width="15%">Kode Bahan</th>
                                <th width="25%">Nama Bahan</th>
                                <th width="20%">Spesifikasi</th>
                                <th width="10%">Satuan</th>
                                <th width="12%" class="text-center">Status</th>
                                <th width="13%" class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($bahanBakus as $bahan)
                                <tr>
                                    <td class="text-center align-middle text-muted">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="align-middle">
                                        <strong class="text-primary">
                                            {{ $bahan->kode_bahan }}
                                        </strong>
                                    </td>

                                    <td class="align-middle font-weight-bold">
                                        {{ $bahan->nama_bahan }}
                                    </td>

                                    <td class="align-middle">
                                        {{ $bahan->spesifikasi ?: '-' }}
                                    </td>

                                    <td class="align-middle">
                                        {{ $bahan->satuan }}
                                    </td>

                                    <td class="text-center align-middle">
                                        @if (strtolower($bahan->status) === 'aktif')
                                            <span class="badge badge-success">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center align-middle text-nowrap">
                                        <a href="{{ route('bahan-baku.edit', $bahan->id) }}"
                                           class="btn btn-primary btn-sm"
                                           title="Edit Bahan Baku">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7"
                                        class="text-center text-muted py-4">
                                        <i class="fas fa-box-open fa-2x mb-2"></i>
                                        <p class="mb-0">
                                            Belum ada data bahan baku.
                                        </p>
                                        <small>
                                            Silakan tambahkan bahan baku terlebih dahulu.
                                        </small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer text-muted">
                <small>
                    Total bahan baku:
                    <strong>{{ $bahanBakus->count() }}</strong>
                </small>
            </div>
        </div>

    </div>

    {{-- MODAL TAMBAH BAHAN BAKU --}}
    <div class="modal fade"
         id="modalTambahBahan"
         tabindex="-1"
         role="dialog"
         aria-labelledby="judulModalTambahBahan"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered"
             role="document">

            <div class="modal-content">

                {{-- HEADER MODAL --}}
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"
                        id="judulModalTambahBahan">
                        <i class="fas fa-boxes mr-2"></i>
                        Tambah Bahan Baku
                    </h5>

                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal"
                            aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- FORM --}}
                <form id="formTambahBahan"
                      action="{{ route('bahan-baku.store') }}"
                      method="POST">

                    @csrf

                    <div class="modal-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Periksa kembali data berikut:
                                </strong>

                                <ul class="mb-0 mt-2 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-1"></i>
                            Daftarkan bahan baku terlebih dahulu sebelum mencatat pembelian.
                            Stok dan harga rata-rata akan diperbarui dari transaksi pembelian.
                        </div>

                        {{-- KODE BAHAN --}}
                        <div class="form-group">
                            <label for="kodeBahanBaru">Kode Bahan</label>

                            <input type="text"
                                   id="kodeBahanBaru"
                                   class="form-control bg-light"
                                   value="{{ $kodeBahan }}"
                                   readonly>

                            <small class="text-muted">
                                Kode bahan dibuat otomatis oleh sistem.
                            </small>
                        </div>

                        {{-- NAMA BAHAN --}}
                        <div class="form-group">
                            <label for="namaBahanBaru">
                                Nama Bahan <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   name="nama_bahan"
                                   id="namaBahanBaru"
                                   class="form-control @error('nama_bahan') is-invalid @enderror"
                                   value="{{ old('nama_bahan') }}"
                                   placeholder="Contoh: Kertas Art Paper"
                                   maxlength="255"
                                   required>

                            @error('nama_bahan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SPESIFIKASI --}}
                        <div class="form-group">
                            <label for="spesifikasiBahan">Spesifikasi</label>

                            <input type="text"
                                   name="spesifikasi"
                                   id="spesifikasiBahan"
                                   class="form-control @error('spesifikasi') is-invalid @enderror"
                                   value="{{ old('spesifikasi') }}"
                                   placeholder="Contoh: 260 gsm, ukuran A3"
                                   maxlength="255">

                            @error('spesifikasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- SATUAN --}}
                        <div class="form-group">
                            <label for="satuanBahan">
                                Satuan <span class="text-danger">*</span>
                            </label>

                            <select name="satuan"
                                    id="satuanBahan"
                                    class="form-control @error('satuan') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Satuan --</option>

                                @foreach ([
                                    'lembar',
                                    'rim',
                                    'pcs',
                                    'roll',
                                    'meter',
                                    'm2',
                                    'kg',
                                    'liter',
                                    'botol',
                                    'pak'
                                ] as $satuan)
                                    <option value="{{ $satuan }}"
                                        {{ old('satuan') === $satuan ? 'selected' : '' }}>
                                        {{ ucfirst($satuan) }}
                                    </option>
                                @endforeach
                            </select>

                            @error('satuan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- STATUS --}}
                        <div class="form-group mb-0">
                            <label for="statusBahan">
                                Status <span class="text-danger">*</span>
                            </label>

                            <select name="status"
                                    id="statusBahan"
                                    class="form-control @error('status') is-invalid @enderror"
                                    required>
                                <option value="aktif"
                                    {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>
                                    Aktif
                                </option>

                                <option value="nonaktif"
                                    {{ old('status') === 'nonaktif' ? 'selected' : '' }}>
                                    Nonaktif
                                </option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    {{-- FOOTER MODAL --}}
                    <div class="modal-footer bg-light">
                        <button type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>
                            Batal
                        </button>

                        <button type="submit"
                                id="btnSimpanBahan"
                                class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Simpan Bahan Baku
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- SCRIPT MODAL --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('modalTambahBahan');
                const form = document.getElementById('formTambahBahan');
                const tombolSimpan = document.getElementById('btnSimpanBahan');

                if (!modal || !form) {
                    return;
                }

                // Pindahkan modal ke body untuk menghindari masalah
                // stacking context pada layout Stisla.
                document.body.appendChild(modal);

                // Cegah pengiriman formulir berkali-kali.
                form.addEventListener('submit', function () {
                    if (tombolSimpan) {
                        tombolSimpan.disabled = true;
                        tombolSimpan.innerHTML =
                            '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                    }
                });

                // Tampilkan kembali modal jika validasi gagal.
                @if ($errors->any())
                    if (window.jQuery &&
                        typeof jQuery.fn.modal === 'function') {
                        jQuery(modal).modal('show');
                    }
                @endif
            });
        </script>
    @endpush

</x-admin-layout-stisla>
