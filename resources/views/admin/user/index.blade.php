
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
                                            
                                            <button type="button"
                                                    class="btn btn-primary btn-sm mr-1 btn-edit-user"
                                                    title="Edit Pengguna"
                                                    data-id="{{ $user->id }}"
                                                    data-url="{{ route('user.update', $user->id) }}"
                                                    data-name="{{ $user->name }}"
                                                    data-email="{{ $user->email }}"
                                                    data-username="{{ $user->username }}"
                                                    data-role="{{ $user->role }}"
                                                    data-cabang="{{ $user->cabang_id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>


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

    
    {{-- SCRIPT MODAL TAMBAH DAN EDIT USER --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // ==========================================
        // MODAL TAMBAH USER
        // ==========================================
        const modalTambah = document.getElementById('modalTambahUser');
        const formTambah = document.getElementById('formTambahUser');
        const tombolSimpan = document.getElementById('btnSimpanUser');
        const roleTambah = document.getElementById('userRole');
        const cabangTambah = document.getElementById('userCabang');
        const labelCabangTambah = document.getElementById('labelWajibCabang');

        // ==========================================
        // MODAL EDIT USER
        // ==========================================
        const modalEdit = document.getElementById('modalEditUser');
        const formEdit = document.getElementById('formEditUser');
        const tombolUpdate = document.getElementById('btnUpdateUser');

        const editId = document.getElementById('edit_user_id');
        const editNama = document.getElementById('edit_user_name');
        const editEmail = document.getElementById('edit_user_email');
        const editUsername = document.getElementById('edit_user_username');
        const editPassword = document.getElementById('edit_user_password');
        const editRole = document.getElementById('edit_user_role');
        const editCabang = document.getElementById('edit_user_cabang');
        const labelCabangEdit = document.getElementById('editLabelWajibCabang');

        // ==========================================
        // PINDAHKAN MODAL KE BODY
        // ==========================================
        // Menghindari masalah stacking context pada layout Stisla.
        if (modalTambah) {
            document.body.appendChild(modalTambah);
        }

        if (modalEdit) {
            document.body.appendChild(modalEdit);
        }

        // ==========================================
        // ATUR PENEMPATAN CABANG
        // ==========================================
        function aturCabang(roleSelect, cabangSelect, labelWajib) {
            if (!roleSelect || !cabangSelect) {
                return;
            }

            const adminCabang = roleSelect.value === 'Admin Cabang';

            // Cabang wajib untuk Admin Cabang.
            cabangSelect.required = adminCabang;

            if (labelWajib) {
                labelWajib.classList.toggle('d-none', !adminCabang);
            }

            // Admin Pusat tidak ditempatkan pada cabang tertentu.
            if (roleSelect.value === 'Admin Pusat') {
                cabangSelect.value = '';
            }
        }

        // Event role untuk modal tambah.
        if (roleTambah) {
            roleTambah.addEventListener('change', function () {
                aturCabang(roleTambah, cabangTambah, labelCabangTambah);
            });

            aturCabang(roleTambah, cabangTambah, labelCabangTambah);
        }

        // Event role untuk modal edit.
        if (editRole) {
            editRole.addEventListener('change', function () {
                aturCabang(editRole, editCabang, labelCabangEdit);
            });
        }

        // ==========================================
        // ISI DATA MODAL EDIT
        // ==========================================
        
        document.querySelectorAll('.btn-edit-user').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                if (!modalEdit || !formEdit) {
                    console.error('Modal edit atau form edit tidak ditemukan.');
                    return;
                }

                editId.value = this.dataset.id || '';
                editNama.value = this.dataset.name || '';
                editEmail.value = this.dataset.email || '';
                editUsername.value = this.dataset.username || '';
                editPassword.value = '';
                editRole.value = this.dataset.role || 'Customer Service';
                editCabang.value = this.dataset.cabang || '';

                formEdit.action = this.dataset.url;

                aturCabang(editRole, editCabang, labelCabangEdit);

                if (tombolUpdate) {
                    tombolUpdate.disabled = false;
                    tombolUpdate.innerHTML =
                        '<i class="fas fa-save mr-1"></i> Simpan Perubahan';
                }

                if (window.jQuery && typeof jQuery.fn.modal === 'function') {
                    jQuery(modalEdit).modal('show');
                } else {
                    console.error(
                        'Bootstrap Modal tidak tersedia. Periksa JavaScript Bootstrap pada layout.'
                    );
                }
            });
        });


        // ==========================================
        // CEGAH PENGIRIMAN FORM BERULANG
        // ==========================================
        if (formTambah) {
            formTambah.addEventListener('submit', function () {
                if (tombolSimpan) {
                    tombolSimpan.disabled = true;
                    tombolSimpan.innerHTML =
                        '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                }
            });
        }

        if (formEdit) {
            formEdit.addEventListener('submit', function () {
                if (tombolUpdate) {
                    tombolUpdate.disabled = true;
                    tombolUpdate.innerHTML =
                        '<i class="fas fa-spinner fa-spin mr-1"></i> Memperbarui...';
                }
            });
        }

        // ==========================================
        // BUKA KEMBALI MODAL JIKA VALIDASI GAGAL
        // ==========================================
        const oldEditId = @json(old('edit_user_id'));

        const adaErrorTambah = @json(
            $errors->any() && !old('edit_user_id')
        );

        function bukaModal(modal) {
            if (!modal || !window.jQuery ||
                typeof jQuery.fn.modal !== 'function') {
                return;
            }

            jQuery(modal).modal('show');
        }

        // Pulihkan modal edit terlebih dahulu jika request berasal dari edit.
        if (oldEditId && formEdit) {
            const tombolLama = Array.from(
                document.querySelectorAll('.btn-edit-user')
            ).find(function (tombol) {
                return String(tombol.dataset.id) === String(oldEditId);
            });

            if (tombolLama) {
                // Isi data awal dari tombol.
                tombolLama.click();

                // Pulihkan nilai yang dikirim sebelum validasi gagal.
                if (editNama) {
                    editNama.value = @json(old('name', ''));
                }

                if (editEmail) {
                    editEmail.value = @json(old('email', ''));
                }

                if (editUsername) {
                    editUsername.value = @json(old('username', ''));
                }

                if (editRole) {
                    editRole.value = @json(old('role', 'Customer Service'));
                }

                if (editCabang) {
                    editCabang.value = @json(old('cabang_id', ''));
                }

                // Password sengaja tidak dipulihkan.
                if (editPassword) {
                    editPassword.value = '';
                }

                aturCabang(editRole, editCabang, labelCabangEdit);

                if (tombolUpdate) {
                    tombolUpdate.disabled = false;
                    tombolUpdate.innerHTML =
                        '<i class="fas fa-save mr-1"></i> Simpan Perubahan';
                }

                bukaModal(modalEdit);
            }
        } else if (adaErrorTambah) {
            // Pulihkan modal tambah jika validasi tambah gagal.
            if (tombolSimpan) {
                tombolSimpan.disabled = false;
                tombolSimpan.innerHTML =
                    '<i class="fas fa-save mr-1"></i> Simpan Pengguna';
            }

            aturCabang(roleTambah, cabangTambah, labelCabangTambah);
            bukaModal(modalTambah);
        }
    });
    </script>
    @endpush
    
    {{-- MODAL EDIT PENGGUNA --}}
    <div class="modal fade"
        id="modalEditUser"
        tabindex="-1"
        role="dialog"
        aria-labelledby="judulModalEditUser"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered"
            role="document">

            <div class="modal-content">

                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="judulModalEditUser">
                        <i class="fas fa-user-edit mr-2"></i>
                        Edit Pengguna
                    </h5>

                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formEditUser" method="POST" action="#">
                    @csrf
                    @method('PUT')

                    <input type="hidden"
                        name="edit_user_id"
                        id="edit_user_id">

                    <div class="modal-body">

                        @if ($errors->any() && old('edit_user_id'))
                            <div class="alert alert-danger">
                                <strong>Gagal memperbarui pengguna.</strong>
                                <ul class="mb-0 mt-2 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="edit_user_name">
                                    Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    name="name"
                                    id="edit_user_name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    maxlength="255"
                                    required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="edit_user_email">
                                    Email <span class="text-danger">*</span>
                                </label>
                                <input type="email"
                                    name="email"
                                    id="edit_user_email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    maxlength="255"
                                    required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="edit_user_username">
                                    Username <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                    name="username"
                                    id="edit_user_username"
                                    class="form-control @error('username') is-invalid @enderror"
                                    maxlength="255"
                                    required>
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="edit_user_password">Password Baru</label>
                                <input type="password"
                                    name="password"
                                    id="edit_user_password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    minlength="8"
                                    autocomplete="new-password"
                                    placeholder="Kosongkan jika tidak diubah">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Isi hanya jika ingin mengganti password.
                                </small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="edit_user_role">
                                    Role <span class="text-danger">*</span>
                                </label>
                                <select name="role"
                                        id="edit_user_role"
                                        class="form-control @error('role') is-invalid @enderror"
                                        required>
                                    <option value="Admin Pusat">Admin Pusat</option>
                                    <option value="Admin Cabang">Admin Cabang</option>
                                    <option value="Customer Service">Customer Service</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="edit_user_cabang">
                                    Penempatan Cabang
                                    <span id="editLabelWajibCabang"
                                        class="text-danger d-none">*</span>
                                </label>
                                <select name="cabang_id"
                                        id="edit_user_cabang"
                                        class="form-control @error('cabang_id') is-invalid @enderror">
                                    <option value="">-- Pusat / Tidak ada cabang spesifik --</option>
                                    @foreach ($cabangs as $cabang)
                                        <option value="{{ $cabang->id }}">
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

                    <div class="modal-footer bg-light">
                        <button type="button"
                                class="btn btn-light"
                                data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>
                            Batal
                        </button>

                        <button type="submit"
                                class="btn btn-primary"
                                id="btnUpdateUser">
                            <i class="fas fa-save mr-1"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>


</x-admin-layout-stisla>
