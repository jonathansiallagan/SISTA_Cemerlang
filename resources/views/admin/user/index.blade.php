
<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div class="section-header">
        <h1>Data Pengguna</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Manajemen Pengguna</div>
            <div class="breadcrumb-item">Data Pengguna</div>
        </div>
    </div>

    <div class="section-body">

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <p class="text-muted mb-2">
                Kelola data pengguna dan hak akses sistem informasi akuntansi CV Cemerlang.
            </p>

            <button type="button"
                    class="btn btn-primary btn-sm mb-2"
                    data-toggle="modal"
                    data-target="#modalTambahUser">
                <i class="fas fa-plus mr-1"></i>
                Tambah User
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
                Gagal menambahkan pengguna. Periksa kembali data pada formulir.

                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- TABEL DATA PENGGUNA --}}
        <div class="card">
            <div class="card-header bg-primary py-2">
                <h4 class="text-white mb-0">
                    <i class="fas fa-users mr-2"></i>
                    Daftar Pengguna
                </h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sista">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th width="22%">Nama</th>
                                <th width="18%">Username</th>
                                <th width="15%">Role</th>
                                <th width="25%">Cabang</th>
                                <th width="15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td class="text-center align-middle text-muted">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="align-middle font-weight-bold">
                                        {{ $user->name }}
                                    </td>

                                    <td class="align-middle">
                                        {{ $user->username ?? '-' }}
                                    </td>

                                    <td class="align-middle">
                                        @php
                                            $role = strtolower($user->role ?? '');
                                        @endphp

                                        @if ($role === 'admin pusat')
                                            <span class="badge badge-primary">
                                                {{ $user->role }}
                                            </span>
                                        @elseif ($role === 'admin cabang')
                                            <span class="badge badge-info">
                                                {{ $user->role }}
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                {{ $user->role ?? '-' }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="align-middle">
                                        {{ $user->cabang->nama ?? 'Pusat/Semua Cabang' }}
                                    </td>

                                    <td class="text-center align-middle text-nowrap">
                                        <div class="d-flex justify-content-center">
                                            <a href="{{ route('user.edit', $user->id) }}"
                                               class="btn btn-primary btn-sm mr-1"
                                               title="Edit Pengguna">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('user.destroy', $user->id) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-danger btn-sm"
                                                        title="Hapus Pengguna">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="text-center text-muted py-4">
                                        <i class="fas fa-users fa-2x mb-2"></i>
                                        <p class="mb-0">
                                            Belum ada data pengguna.
                                            Silakan tambah pengguna baru.
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
                    Total pengguna:
                    <strong>{{ $users->count() }}</strong>
                </small>
            </div>
        </div>

    </div>

    {{-- MODAL TAMBAH PENGGUNA --}}
    <div class="modal fade"
         id="modalTambahUser"
         tabindex="-1"
         role="dialog"
         aria-labelledby="judulModalTambahUser"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered"
             role="document">

            <div class="modal-content">

                {{-- HEADER MODAL --}}
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white"
                        id="judulModalTambahUser">
                        <i class="fas fa-user-plus mr-2"></i>
                        Tambah Pengguna Baru
                    </h5>

                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- FORM TAMBAH PENGGUNA --}}
                <form id="formTambahUser"
                      action="{{ route('user.store') }}"
                      method="POST">

                    @csrf

                    <div class="modal-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Gagal menyimpan pengguna
                                </strong>

                                <ul class="mb-0 mt-2 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- NAMA DAN EMAIL --}}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="userName">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       id="userName"
                                       name="name"
                                       value="{{ old('name') }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Masukkan nama lengkap"
                                       maxlength="255"
                                       autocomplete="name"
                                       required>

                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="userEmail">
                                    Email <span class="text-danger">*</span>
                                </label>

                                <input type="email"
                                       id="userEmail"
                                       name="email"
                                       value="{{ old('email') }}"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="nama@email.com"
                                       maxlength="255"
                                       autocomplete="email"
                                       required>

                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- USERNAME DAN PASSWORD --}}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="userUsername">
                                    Username <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       id="userUsername"
                                       name="username"
                                       value="{{ old('username') }}"
                                       class="form-control @error('username') is-invalid @enderror"
                                       placeholder="Masukkan username"
                                       maxlength="255"
                                       autocomplete="username"
                                       required>

                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="userPassword">
                                    Password <span class="text-danger">*</span>
                                </label>

                                <input type="password"
                                       id="userPassword"
                                       name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Minimal 8 karakter"
                                       minlength="8"
                                       autocomplete="new-password"
                                       required>

                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <small class="form-text text-muted">
                                    Password tidak akan ditampilkan kembali jika validasi gagal.
                                </small>
                            </div>
                        </div>

                        {{-- ROLE DAN CABANG --}}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="userRole">
                                    Role <span class="text-danger">*</span>
                                </label>

                                <select id="userRole"
                                        name="role"
                                        class="form-control @error('role') is-invalid @enderror"
                                        required>
                                    <option value="">-- Pilih Role --</option>

                                    <option value="Admin Pusat"
                                        {{ old('role') === 'Admin Pusat' ? 'selected' : '' }}>
                                        Admin Pusat
                                    </option>

                                    <option value="Admin Cabang"
                                        {{ old('role') === 'Admin Cabang' ? 'selected' : '' }}>
                                        Admin Cabang
                                    </option>

                                    <option value="Customer Service"
                                        {{ old('role') === 'Customer Service' ? 'selected' : '' }}>
                                        Customer Service
                                    </option>
                                </select>

                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="userCabang">
                                    Penempatan Cabang
                                    <span id="labelWajibCabang" class="text-danger d-none">*</span>
                                </label>

                                <select id="userCabang"
                                        name="cabang_id"
                                        class="form-control @error('cabang_id') is-invalid @enderror">
                                    <option value="">
                                        -- Pusat / Tidak ada cabang spesifik --
                                    </option>

                                    @foreach ($cabangs as $cabang)
                                        <option value="{{ $cabang->id }}"
                                            {{ (string) old('cabang_id') === (string) $cabang->id ? 'selected' : '' }}>
                                            {{ $cabang->nama }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('cabang_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <small class="form-text text-muted">
                                    Admin Cabang wajib memiliki penempatan cabang.
                                </small>
                            </div>
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
                                id="btnSimpanUser"
                                class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Simpan Pengguna
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
                const modal = document.getElementById('modalTambahUser');
                const form = document.getElementById('formTambahUser');
                const tombolSimpan = document.getElementById('btnSimpanUser');
                const roleSelect = document.getElementById('userRole');
                const cabangSelect = document.getElementById('userCabang');
                const labelWajibCabang = document.getElementById('labelWajibCabang');

                if (!modal || !form) {
                    return;
                }

                // Hindari masalah stacking context pada layout Stisla.
                document.body.appendChild(modal);

                // Cabang wajib dipilih jika role adalah Admin Cabang.
                function aturPenempatanCabang() {
                    if (!roleSelect || !cabangSelect) {
                        return;
                    }

                    const adminCabang = roleSelect.value === 'Admin Cabang';

                    cabangSelect.required = adminCabang;

                    if (labelWajibCabang) {
                        labelWajibCabang.classList.toggle('d-none', !adminCabang);
                    }

                    // Admin Pusat otomatis menggunakan penempatan pusat.
                    if (roleSelect.value === 'Admin Pusat') {
                        cabangSelect.value = '';
                    }
                }

                if (roleSelect) {
                    roleSelect.addEventListener('change', aturPenempatanCabang);
                    aturPenempatanCabang();
                }

                // Cegah form terkirim berkali-kali.
                form.addEventListener('submit', function () {
                    if (tombolSimpan) {
                        tombolSimpan.disabled = true;
                        tombolSimpan.innerHTML =
                            '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                    }
                });

                // Buka kembali modal jika validasi server gagal.
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
