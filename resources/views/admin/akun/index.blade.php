
<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div class="section-header">
        <h1>Bagan Akun (Chart of Accounts)</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Akuntansi</div>
            <div class="breadcrumb-item">Bagan Akun</div>
        </div>
    </div>

    <div class="section-body">

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <p class="text-muted mb-2">
                Kelola daftar akun perkiraan untuk pencatatan transaksi dan laporan keuangan CV Cemerlang.
            </p>

            <button type="button"
                    class="btn btn-primary btn-sm mb-2"
                    data-toggle="modal"
                    data-target="#modalTambahAkun">
                <i class="fas fa-plus mr-1"></i>
                Tambah Akun
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
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show py-2 mb-3"
                 role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i>
                Terdapat kesalahan pada formulir. Periksa kembali data akun.

                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- TABEL BAGAN AKUN --}}
        <div class="card">
            <div class="card-header bg-primary py-2">
                <h4 class="text-white mb-0">
                    <i class="fas fa-book mr-2"></i>
                    Daftar Akun Perkiraan
                </h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sista">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th width="17%">Kode Akun</th>
                                <th width="30%">Nama Akun</th>
                                <th width="20%">Tipe Akun</th>
                                <th width="15%" class="text-center">Status</th>
                                <th width="13%" class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($akuns as $akun)
                                <tr>
                                    <td class="text-center align-middle text-muted">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="align-middle">
                                        <strong class="text-primary">
                                            {{ $akun->kode_akun }}
                                        </strong>
                                    </td>

                                    <td class="align-middle font-weight-bold">
                                        {{ $akun->nama_akun }}
                                    </td>

                                    <td class="align-middle">
                                        {{ $akun->tipe_akun }}
                                    </td>

                                    <td class="text-center align-middle">
                                        @if (strtolower($akun->status) === 'aktif')
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        @endif
                                    </td>

                                    <td class="text-center align-middle text-nowrap">
                                        <button type="button"
                                            class="btn btn-primary btn-sm btn-edit-akun"
                                            title="Edit Akun"
                                            data-toggle="modal"
                                            data-target="#modalEditAkun"
                                            data-id="{{ $akun->id }}"
                                            data-url="{{ route('akun.update', $akun->id) }}"
                                            data-kode="{{ $akun->kode_akun }}"
                                            data-nama="{{ $akun->nama_akun }}"
                                            data-tipe="{{ $akun->tipe_akun }}"
                                            data-kelompok="{{ $akun->kelompok_akun }}"
                                            data-status="{{ $akun->status }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="text-center text-muted py-4">
                                        <i class="fas fa-book fa-2x mb-2"></i>
                                        <p class="mb-0">
                                            Belum ada data akun perkiraan.
                                            Silakan tambah akun baru.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer text-muted">
                <small>
                    Total akun:
                    <strong>{{ $akuns->count() }}</strong>
                </small>
            </div>
        </div>

    </div>

    {{-- MODAL TAMBAH AKUN --}}
    <div class="modal fade"
         id="modalTambahAkun"
         tabindex="-1"
         role="dialog"
         aria-labelledby="judulModalTambahAkun"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered"
             role="document">

            <div class="modal-content">

                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"
                        id="judulModalTambahAkun">
                        <i class="fas fa-book mr-2"></i>
                        Tambah Akun Baru
                    </h5>

                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formTambahAkun"
                      action="{{ route('akun.store') }}"
                      method="POST">

                    @csrf

                    <div class="modal-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Gagal menyimpan akun.</strong>
                                <ul class="mb-0 mt-2 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- TIPE AKUN --}}
                        <div class="form-group">
                            <label for="tipe_akun">
                                Tipe Akun <span class="text-danger">*</span>
                            </label>

                            <select name="tipe_akun"
                                    id="tipe_akun"
                                    class="form-control @error('tipe_akun') is-invalid @enderror"
                                    required>
                                <option value="">-- Pilih Tipe Akun --</option>

                                <option value="Aset"
                                    {{ old('tipe_akun') === 'Aset' ? 'selected' : '' }}>
                                    Aset
                                </option>

                                <option value="Kewajiban"
                                    {{ old('tipe_akun') === 'Kewajiban' ? 'selected' : '' }}>
                                    Kewajiban
                                </option>

                                <option value="Ekuitas"
                                    {{ old('tipe_akun') === 'Ekuitas' ? 'selected' : '' }}>
                                    Ekuitas
                                </option>

                                <option value="Pendapatan"
                                    {{ old('tipe_akun') === 'Pendapatan' ? 'selected' : '' }}>
                                    Pendapatan
                                </option>

                                <option value="Beban/Biaya"
                                    {{ old('tipe_akun') === 'Beban/Biaya' ? 'selected' : '' }}>
                                    Beban / Biaya
                                </option>
                            </select>

                            @error('tipe_akun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- KELOMPOK AKUN --}}
                        <div class="form-group">
                            <label for="kelompok_akun">
                                Kelompok Akun <span class="text-danger">*</span>
                            </label>

                            <select name="kelompok_akun"
                                    id="kelompok_akun"
                                    class="form-control @error('kelompok_akun') is-invalid @enderror"
                                    data-old-kelompok="{{ old('kelompok_akun') }}"
                                    required>
                                <option value="">-- Pilih Kelompok Akun --</option>
                            </select>

                            @error('kelompok_akun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- NAMA AKUN --}}
                        <div class="form-group">
                            <label for="nama_akun">
                                Nama Akun <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   name="nama_akun"
                                   id="nama_akun"
                                   value="{{ old('nama_akun') }}"
                                   class="form-control @error('nama_akun') is-invalid @enderror"
                                   placeholder="Contoh: Kas"
                                   maxlength="255"
                                   required>

                            @error('nama_akun')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- KODE AKUN --}}
                        <div class="form-group mb-0">
                            <label for="kode_akun">
                                Kode Akun
                            </label>

                            <input type="text"
                                   id="kode_akun"
                                   class="form-control bg-light"
                                   placeholder="Pilih tipe dan kelompok akun terlebih dahulu"
                                   readonly>

                            <small class="form-text text-muted">
                                Kode akun dihitung otomatis oleh sistem.
                            </small>
                        </div>

                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button"
                                class="btn btn-light"
                                data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>
                            Batal
                        </button>

                        <button type="submit"
                                id="btnSimpanAkun"
                                class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Simpan Akun
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- SCRIPT MODAL DAN KODE AKUN --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('modalTambahAkun');
                const form = document.getElementById('formTambahAkun');
                const tipeAkun = document.getElementById('tipe_akun');
                const kelompokAkun = document.getElementById('kelompok_akun');
                const kodeAkun = document.getElementById('kode_akun');
                const namaAkun = document.getElementById('nama_akun');
                const tombolSimpan = document.getElementById('btnSimpanAkun');

                if (!modal || !form || !tipeAkun || !kelompokAkun || !kodeAkun) {
                    return;
                }

                // Hindari masalah stacking context pada layout Stisla.
                document.body.appendChild(modal);

                const kelompokData = {
                    'Aset': {
                        'Aset': 1100
                    },
                    'Kewajiban': {
                        'Kewajiban': 1100
                    },
                    'Ekuitas': {
                        'Ekuitas': 1100
                    },
                    'Pendapatan': {
                        'Pendapatan Usaha': 1100
                    },
                    'Beban/Biaya': {
                        'HPP': 1100,
                        'Beban Operasional': 2100,
                        'Biaya Lain-lain': 3100
                    }
                };

                const oldTipe = @json(old('tipe_akun', ''));
                const oldKelompok = kelompokAkun.dataset.oldKelompok || '';

                let permintaanKode = 0;

                function isiKelompok(tipe, pilihanLama = '') {
                    kelompokAkun.innerHTML =
                        '<option value="">-- Pilih Kelompok Akun --</option>';

                    const daftar = kelompokData[tipe];

                    if (!daftar) {
                        return;
                    }

                    Object.keys(daftar).forEach(function (nama) {
                        const option = document.createElement('option');
                        option.value = nama;
                        option.textContent = nama;
                        option.selected = nama === pilihanLama;

                        kelompokAkun.appendChild(option);
                    });
                }

                async function perbaruiKode() {
                    const tipe = tipeAkun.value;
                    const kelompok = kelompokAkun.value;
                    const nomorPermintaan = ++permintaanKode;

                    kodeAkun.value = '';

                    if (!tipe || !kelompok) {
                        return;
                    }

                    kodeAkun.value = 'Menghitung...';

                    try {
                        const url = new URL(
                            @json(route('akun.preview-kode')),
                            window.location.origin
                        );

                        url.searchParams.set('tipe_akun', tipe);
                        url.searchParams.set('kelompok_akun', kelompok);

                        const response = await fetch(url.toString(), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Kode akun gagal dihitung.');
                        }

                        const data = await response.json();

                        // Abaikan hasil lama jika pengguna sudah mengubah pilihan.
                        if (nomorPermintaan !== permintaanKode) {
                            return;
                        }

                        kodeAkun.value = data.kode || '';
                    } catch (error) {
                        if (nomorPermintaan === permintaanKode) {
                            kodeAkun.value = '';
                            console.error(error);
                        }
                    }
                }

                tipeAkun.addEventListener('change', function () {
                    isiKelompok(this.value);
                    perbaruiKode();
                });

                kelompokAkun.addEventListener('change', perbaruiKode);

                // Pulihkan pilihan sebelumnya jika validasi gagal.
                if (oldTipe) {
                    tipeAkun.value = oldTipe;
                    isiKelompok(oldTipe, oldKelompok);
                    perbaruiKode();
                }

                // Cegah pengiriman formulir berkali-kali.
                form.addEventListener('submit', function () {
                    if (tombolSimpan) {
                        tombolSimpan.disabled = true;
                        tombolSimpan.innerHTML =
                            '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                    }
                });

                // Buka modal kembali setelah validasi server gagal.
                @if ($errors->any())
                    if (window.jQuery &&
                        typeof jQuery.fn.modal === 'function') {
                        jQuery(modal).modal('show');
                    }
                @endif
            });
        </script>
    @endpush

    
{{-- MODAL EDIT AKUN --}}
<div class="modal fade"
     id="modalEditAkun"
     tabindex="-1"
     role="dialog"
     aria-labelledby="judulModalEditAkun"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered"
         role="document"
         style="max-width: 650px; width: calc(100% - 30px);">

        <div class="modal-content"
             style="border-radius: 10px; overflow: hidden;">

            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white"
                    id="judulModalEditAkun">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Akun
                </h5>

                <button type="button"
                        class="close text-white"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="formEditAkun" method="POST" action="#">
                @csrf
                @method('PUT')

                <input type="hidden"
                       name="akun_id"
                       id="edit_akun_id">

                <div class="modal-body">

                    @if ($errors->any() && old('akun_id'))
                        <div class="alert alert-danger">
                            <strong>Gagal memperbarui akun.</strong>
                            <ul class="mb-0 mt-2 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- KODE AKUN --}}
                    <div class="form-group">
                        <label for="edit_kode_akun">Kode Akun</label>
                        <input type="text"
                               id="edit_kode_akun"
                               class="form-control bg-light"
                               readonly>
                        <small class="form-text text-muted">
                            Kode akun tidak dapat diubah.
                        </small>
                    </div>

                    {{-- NAMA AKUN --}}
                    <div class="form-group">
                        <label for="edit_nama_akun">
                            Nama Akun <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="nama_akun"
                               id="edit_nama_akun"
                               class="form-control @error('nama_akun') is-invalid @enderror"
                               value="{{ old('nama_akun') }}"
                               maxlength="255"
                               required>

                        @error('nama_akun')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- TIPE AKUN --}}
                    <div class="form-group">
                        <label for="edit_tipe_akun">Tipe Akun</label>
                        <input type="text"
                               id="edit_tipe_akun"
                               class="form-control bg-light"
                               readonly>
                        <small class="form-text text-muted">
                            Tipe akun tidak dapat diubah.
                        </small>
                    </div>

                    {{-- KELOMPOK AKUN --}}
                    <div class="form-group">
                        <label for="edit_kelompok_akun">Kelompok Akun</label>
                        <input type="text"
                               id="edit_kelompok_akun"
                               class="form-control bg-light"
                               readonly>
                        <small class="form-text text-muted">
                            Kelompok akun tidak dapat diubah.
                        </small>
                    </div>

                    {{-- STATUS --}}
                    <div class="form-group mb-0">
                        <label for="edit_status">
                            Status <span class="text-danger">*</span>
                        </label>

                        <select name="status"
                                id="edit_status"
                                class="form-control @error('status') is-invalid @enderror"
                                required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="form-text text-muted">
                            Pastikan akun yang dinonaktifkan tidak lagi
                            diperlukan untuk transaksi baru.
                        </small>
                    </div>

                </div>

                <div class="modal-footer bg-light">
                    <button type="button"
                            class="btn btn-light"
                            data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i>
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary"
                            id="btnUpdateAkun">
                        <i class="fas fa-save mr-1"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalEditAkun');
    const form = document.getElementById('formEditAkun');

    if (!modal || !form) return;

    // Hindari masalah posisi modal pada layout Stisla.
    document.body.appendChild(modal);

    const field = {
        id: document.getElementById('edit_akun_id'),
        kode: document.getElementById('edit_kode_akun'),
        nama: document.getElementById('edit_nama_akun'),
        tipe: document.getElementById('edit_tipe_akun'),
        kelompok: document.getElementById('edit_kelompok_akun'),
        status: document.getElementById('edit_status')
    };

    function isiModal(tombol) {
        field.id.value = tombol.dataset.id;
        field.kode.value = tombol.dataset.kode;
        field.nama.value = tombol.dataset.nama;
        field.tipe.value = tombol.dataset.tipe;
        field.kelompok.value = tombol.dataset.kelompok;
        field.status.value = tombol.dataset.status;

        form.action = tombol.dataset.url;
    }

    document.querySelectorAll('.btn-edit-akun').forEach(function (tombol) {
        tombol.addEventListener('click', function () {
            isiModal(this);
        });
    });

    // Cegah pengiriman formulir berkali-kali.
    form.addEventListener('submit', function () {
        const tombol = document.getElementById('btnUpdateAkun');

        tombol.disabled = true;
        tombol.innerHTML =
            '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
    });

    // Buka kembali modal jika validasi gagal.
    const oldId = @json(old('akun_id'));

    if (oldId) {
        const tombolLama = Array.from(
            document.querySelectorAll('.btn-edit-akun')
        ).find(function (tombol) {
            return tombol.dataset.id === String(oldId);
        });

        if (tombolLama) {
            isiModal(tombolLama);

            // Pulihkan input yang dikirim sebelum validasi gagal.
            field.nama.value = @json(old('nama_akun', ''));
            field.status.value = @json(old('status', 'aktif'));

            if (window.jQuery && typeof jQuery.fn.modal === 'function') {
                jQuery(modal).modal('show');
            }
        }
    }
});
</script>
@endpush


</x-admin-layout-stisla>
