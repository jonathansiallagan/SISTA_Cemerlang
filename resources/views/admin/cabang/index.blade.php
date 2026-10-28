
<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div class="section-header">
        <h1>Data Cabang</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Data Cabang</div>
        </div>
    </div>

    <div class="section-body">

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <p class="text-muted mb-2">
                Kelola daftar cabang percetakan CV Cemerlang.
            </p>

            <button type="button"
                    class="btn btn-primary btn-sm mb-2"
                    data-toggle="modal"
                    data-target="#modalTambahCabang">
                <i class="fas fa-plus mr-1"></i>
                Tambah Cabang
            </button>
        </div>

        {{-- NOTIFIKASI SUKSES --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 mb-3"
                 role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}

                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- NOTIFIKASI ERROR --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show py-2 mb-3"
                 role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}

                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- TABEL DATA CABANG --}}
        <div class="card">
            <div class="card-header bg-primary py-2">
                <h4 class="text-white mb-0">
                    <i class="fas fa-store mr-2"></i>
                    Daftar Cabang
                </h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sista">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Nama Cabang</th>
                                <th>Alamat</th>
                                <th>No. HP</th>
                                <th class="text-center">Status Cabang</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($cabangs as $cabang)
                                <tr>
                                    <td class="text-center text-muted align-middle">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="font-weight-bold align-middle">
                                        {{ $cabang->nama }}
                                    </td>

                                    <td class="align-middle">
                                        {{ $cabang->alamat ?: '-' }}
                                    </td>

                                    <td class="align-middle text-nowrap">
                                        {{ $cabang->no_hp ?: '-' }}
                                    </td>

                                    <td class="text-center align-middle">
                                        @if (strtolower($cabang->status) === 'aktif')
                                            <span class="badge badge-success">
                                                {{ ucfirst($cabang->status) }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                {{ ucfirst($cabang->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="text-center align-middle text-nowrap">
                                        <a href="{{ route('cabang.edit', $cabang->id) }}"
                                           class="btn btn-primary btn-sm"
                                           title="Edit Cabang">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="text-center text-muted py-4">
                                        <i class="fas fa-store fa-2x mb-2"></i>
                                        <p class="mb-0">
                                            Belum ada data cabang.
                                            Silakan tambah cabang baru.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    {{-- MODAL TAMBAH CABANG --}}
    <div class="modal fade"
         id="modalTambahCabang"
         tabindex="-1"
         role="dialog"
         aria-labelledby="judulModalTambahCabang"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered"
             role="document">

            <div class="modal-content">

                {{-- HEADER MODAL --}}
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"
                        id="judulModalTambahCabang">
                        <i class="fas fa-store mr-2"></i>
                        Tambah Cabang Baru
                    </h5>

                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- FORM TAMBAH CABANG --}}
                <form id="formTambahCabang"
                      action="{{ route('cabang.store') }}"
                      method="POST">

                    @csrf

                    <div class="modal-body">

                        <p class="text-muted mb-4">
                            Masukkan informasi cabang percetakan dengan lengkap.
                        </p>

                        {{-- NAMA CABANG --}}
                        <div class="form-group">
                            <label for="namaCabang">
                                Nama Cabang <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   id="namaCabang"
                                   name="nama"
                                   value="{{ old('nama') }}"
                                   class="form-control @error('nama') is-invalid @enderror"
                                   placeholder="Contoh: Cabang Arengka"
                                   maxlength="255"
                                   required>

                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NOMOR HP --}}
                        <div class="form-group">
                            <label for="noHpCabang">
                                Nomor HP/Telepon <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   id="noHpCabang"
                                   name="no_hp"
                                   value="{{ old('no_hp') }}"
                                   class="form-control @error('no_hp') is-invalid @enderror"
                                   placeholder="Contoh: 081234567890"
                                   maxlength="20"
                                   required>

                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ALAMAT --}}
                        <div class="form-group">
                            <label for="alamatCabang">
                                Alamat Lengkap <span class="text-danger">*</span>
                            </label>

                            <textarea id="alamatCabang"
                                      name="alamat"
                                      rows="3"
                                      maxlength="1000"
                                      class="form-control @error('alamat') is-invalid @enderror"
                                      placeholder="Masukkan alamat lengkap cabang"
                                      required>{{ old('alamat') }}</textarea>

                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- STATUS CABANG --}}
                        <div class="form-group mb-0">
                            <label for="statusCabang">
                                Status Cabang <span class="text-danger">*</span>
                            </label>

                            <select id="statusCabang"
                                    name="status"
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
                                class="btn btn-light"
                                data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>
                            Batal
                        </button>

                        <button type="submit"
                                id="btnSimpanCabang"
                                class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Simpan Cabang
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
                const modal = document.getElementById('modalTambahCabang');
                const form = document.getElementById('formTambahCabang');
                const tombolSimpan = document.getElementById('btnSimpanCabang');

                if (!modal || !form) {
                    return;
                }

                /*
                 * Pindahkan modal ke body untuk menghindari masalah
                 * stacking context pada layout Stisla.
                 */
                document.body.appendChild(modal);

                // Cegah form terkirim berkali-kali.
                form.addEventListener('submit', function () {
                    if (tombolSimpan) {
                        tombolSimpan.disabled = true;
                        tombolSimpan.innerHTML =
                            '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                    }
                });

                // Jika validasi gagal, buka kembali modal dan tampilkan error.
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
