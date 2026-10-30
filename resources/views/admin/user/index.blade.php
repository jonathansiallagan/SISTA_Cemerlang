<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div
        class="mb-[30px] flex flex-wrap items-center gap-x-4 gap-y-2 rounded-[3px] bg-white px-[30px] py-5 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
        <h1 class="text-2xl font-bold text-[#34395e]">Data Pengguna</h1>

        <ol class="ml-auto flex items-center text-[13px] text-[#6c757d]">
            <li class="font-semibold text-[#6777ef]">Manajemen Pengguna</li>
            <li class="before:px-2 before:content-['/']">Data Pengguna</li>
        </ol>
    </div>

    <div>

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <p class="mb-2 text-[#6c757d]">
                Kelola data pengguna dan hak akses sistem informasi akuntansi CV Cemerlang.
            </p>

            <button type="button" data-modal-open="modalTambahUser"
                class="mb-2 inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
                <i class="fas fa-plus mr-1"></i>
                Tambah User
            </button>
        </div>

        {{-- NOTIFIKASI SUKSES --}}
        @if (session('success'))
            <div role="alert"
                class="mb-4 flex items-center rounded-[3px] border-l-[3px] border-black/20 bg-[#54ca68] px-5 py-2 text-white">
                <span class="min-w-0">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </span>
                <button type="button" aria-label="Tutup" onclick="this.closest('[role=alert]').remove()"
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">&times;</button>
            </div>
        @endif

        {{-- NOTIFIKASI ERROR --}}
        @if ($errors->any())
            <div role="alert"
                class="mb-4 flex items-center rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2 text-white">
                <span class="min-w-0">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    Gagal menambahkan pengguna. Periksa kembali data pada formulir.
                </span>
                <button type="button" aria-label="Tutup" onclick="this.closest('[role=alert]').remove()"
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">&times;</button>
            </div>
        @endif

        {{-- TABEL DATA PENGGUNA --}}
        <x-card-table title="Data Riwayat Pengeluaran" icon="fa-receipt">

            {{-- HEADER TABEL --}}
            <x-slot:head>
                <th class="h-[45px] w-[5%] bg-[#fdfdff] px-[15px] text-center">#</th>
                <th class="h-[45px] w-[22%] bg-[#fdfdff] px-[15px] text-left">Nama</th>
                <th class="h-[45px] w-[18%] bg-[#fdfdff] px-[15px] text-left">Username</th>
                <th class="h-[45px] w-[15%] bg-[#fdfdff] px-[15px] text-left">Role</th>
                <th class="h-[45px] w-[25%] bg-[#fdfdff] px-[15px] text-left">Cabang</th>
                <th class="h-[45px] w-[15%] bg-[#fdfdff] px-[15px] text-center">Aksi</th>
            </x-slot:head>

            {{-- ISI TABEL --}}
            @forelse ($users as $user)
                <tr class="odd:bg-black/[0.025]">
                    <td class="px-[15px] py-3 text-center align-middle text-[#6c757d]">
                        {{ $loop->iteration }}
                    </td>

                    <td class="px-[15px] py-3 align-middle font-bold">
                        {{ $user->name }}
                    </td>

                    <td class="px-[15px] py-3 align-middle">
                        {{ $user->username ?? '-' }}
                    </td>

                    <td class="px-[15px] py-3 align-middle">
                        @php
                            $role = strtolower($user->role ?? '');
                        @endphp

                        @if ($role === 'admin pusat')
                            <span
                                class="rounded-full bg-[#6777ef] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">
                                {{ $user->role }}
                            </span>
                        @elseif ($role === 'admin cabang')
                            <span
                                class="rounded-full bg-[#3abaf4] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">
                                {{ $user->role }}
                            </span>
                        @else
                            <span
                                class="rounded-full bg-[#cdd3d8] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">
                                {{ $user->role ?? '-' }}
                            </span>
                        @endif
                    </td>

                    <td class="px-[15px] py-3 align-middle">
                        {{ $user->cabang->nama ?? 'Pusat/Semua Cabang' }}
                    </td>

                    <td class="whitespace-nowrap px-[15px] py-3 text-center align-middle">
                        <div class="flex justify-center">

                            <button type="button" title="Edit Pengguna"
                                class="btn-edit-user mr-1 inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#394eea]"
                                data-id="{{ $user->id }}" data-url="{{ route('user.update', $user->id) }}"
                                data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                data-username="{{ $user->username }}" data-role="{{ $user->role }}"
                                data-cabang="{{ $user->cabang_id }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            <form action="{{ route('user.destroy', $user->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus pengguna ini?');">
                                @csrf
                                @method('DELETE')

                                <button type="submit" title="Hapus Pengguna"
                                    class="inline-flex items-center rounded-[3px] bg-[#fc544b] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#fb160a]">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-[15px] py-6 text-center text-[#6c757d]">
                        <i class="fas fa-users mb-2 text-[2em]"></i>
                        <p>
                            Belum ada data pengguna.
                            Silakan tambah pengguna baru.
                        </p>
                    </td>
                </tr>
            @endforelse
        </x-card-table>
    </div>

    <div class="border-t border-black/5 px-[25px] py-4 text-[#6c757d]">
        <small class="text-xs">
            Total pengguna:
            <strong class="font-bold">{{ $users->count() }}</strong>
        </small>
    </div>

    {{-- MODAL TAMBAH PENGGUNA --}}
    <dialog id="modalTambahUser" aria-labelledby="judulModalTambahUser"
        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-[800px] overflow-y-auto rounded-[3px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_4px_25px_rgba(0,0,0,0.2)] backdrop:bg-black/40">

        {{-- HEADER MODAL --}}
        <div class="flex items-center justify-between bg-[#6777ef] px-[25px] py-4">
            <h5 id="judulModalTambahUser" class="text-lg font-semibold text-white">
                <i class="fas fa-user-plus mr-2"></i>
                Tambah Pengguna Baru
            </h5>

            <button type="button" data-modal-close aria-label="Tutup"
                class="text-2xl leading-none text-white/80 hover:text-white">&times;</button>
        </div>

        {{-- FORM TAMBAH PENGGUNA --}}
        <form id="formTambahUser" action="{{ route('user.store') }}" method="POST">
            @csrf

            <div class="p-[25px]">

                @if ($errors->any())
                    <div class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2.5 text-white">
                        <strong class="font-bold">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Gagal menyimpan pengguna
                        </strong>

                        <ul class="mb-0 mt-2 list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- NAMA DAN EMAIL --}}
                <div class="grid grid-cols-1 gap-x-2.5 md:grid-cols-2">
                    <div class="mb-[25px]">
                        <label for="userName" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Nama Lengkap <span class="text-[#fc544b]">*</span>
                        </label>

                        <input type="text" id="userName" name="name" value="{{ old('name') }}"
                            placeholder="Masukkan nama lengkap" maxlength="255" autocomplete="name" required
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('name') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">

                        @error('name')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-[25px]">
                        <label for="userEmail"
                            class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Email <span class="text-[#fc544b]">*</span>
                        </label>

                        <input type="email" id="userEmail" name="email" value="{{ old('email') }}"
                            placeholder="nama@email.com" maxlength="255" autocomplete="email" required
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('email') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">

                        @error('email')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- USERNAME DAN PASSWORD --}}
                <div class="grid grid-cols-1 gap-x-2.5 md:grid-cols-2">
                    <div class="mb-[25px]">
                        <label for="userUsername"
                            class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Username <span class="text-[#fc544b]">*</span>
                        </label>

                        <input type="text" id="userUsername" name="username" value="{{ old('username') }}"
                            placeholder="Masukkan username" maxlength="255" autocomplete="username" required
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('username') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">

                        @error('username')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-[25px]">
                        <label for="userPassword"
                            class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Password <span class="text-[#fc544b]">*</span>
                        </label>

                        <input type="password" id="userPassword" name="password" placeholder="Minimal 8 karakter"
                            minlength="8" autocomplete="new-password" required
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('password') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">

                        @error('password')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror

                        <small class="mt-1 block text-xs text-[#6c757d]">
                            Password tidak akan ditampilkan kembali jika validasi gagal.
                        </small>
                    </div>
                </div>

                {{-- ROLE DAN CABANG --}}
                <div class="grid grid-cols-1 gap-x-2.5 md:grid-cols-2">
                    <div>
                        <label for="userRole"
                            class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Role <span class="text-[#fc544b]">*</span>
                        </label>

                        <select id="userRole" name="role" required
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('role') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">
                            <option value="">-- Pilih Role --</option>

                            <option value="Admin Pusat" {{ old('role') === 'Admin Pusat' ? 'selected' : '' }}>
                                Admin Pusat
                            </option>

                            <option value="Admin Cabang" {{ old('role') === 'Admin Cabang' ? 'selected' : '' }}>
                                Admin Cabang
                            </option>

                            <option value="Customer Service"
                                {{ old('role') === 'Customer Service' ? 'selected' : '' }}>
                                Customer Service
                            </option>
                        </select>

                        @error('role')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="userCabang"
                            class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Penempatan Cabang
                            <span id="labelWajibCabang" class="hidden text-[#fc544b]">*</span>
                        </label>

                        <select id="userCabang" name="cabang_id"
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('cabang_id') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">
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
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror

                        <small class="mt-1 block text-xs text-[#6c757d]">
                            Admin Cabang wajib memiliki penempatan cabang.
                        </small>
                    </div>
                </div>

            </div>

            {{-- FOOTER MODAL --}}
            <div class="flex items-center justify-end gap-2 bg-[#f8f9fa] px-[25px] py-3">
                <button type="button" data-modal-close
                    class="inline-flex items-center rounded-[3px] bg-[#e3eaef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>

                <button type="submit" id="btnSimpanUser"
                    class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-65">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Pengguna
                </button>
            </div>

        </form>
    </dialog>

    {{-- SCRIPT MODAL TAMBAH DAN EDIT USER --}}
    @push('scripts')
        <script>
            // Pembuka/penutup <dialog> (data-modal-open, data-modal-close, klik latar gelap).
            // Cukup dimuat sekali per halaman; Esc sudah ditangani browser.
            (function() {
                if (window.__sistaModalReady) return;
                window.__sistaModalReady = true;

                document.addEventListener('click', function(e) {
                    const buka = e.target.closest('[data-modal-open]');
                    if (buka) {
                        const dialog = document.getElementById(buka.dataset.modalOpen);
                        if (dialog && !dialog.open) dialog.showModal();
                        return;
                    }

                    const tutup = e.target.closest('[data-modal-close]');
                    if (tutup) {
                        const dialog = tutup.closest('dialog');
                        if (dialog) dialog.close();
                        return;
                    }

                    if (e.target instanceof HTMLDialogElement && e.target.open) {
                        e.target.close();
                    }
                });
            })();

            document.addEventListener('DOMContentLoaded', function() {
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
                        labelWajib.classList.toggle('hidden', !adminCabang);
                    }

                    // Admin Pusat tidak ditempatkan pada cabang tertentu.
                    if (roleSelect.value === 'Admin Pusat') {
                        cabangSelect.value = '';
                    }
                }

                // Event role untuk modal tambah.
                if (roleTambah) {
                    roleTambah.addEventListener('change', function() {
                        aturCabang(roleTambah, cabangTambah, labelCabangTambah);
                    });

                    aturCabang(roleTambah, cabangTambah, labelCabangTambah);
                }

                // Event role untuk modal edit.
                if (editRole) {
                    editRole.addEventListener('change', function() {
                        aturCabang(editRole, editCabang, labelCabangEdit);
                    });
                }

                // ==========================================
                // ISI DATA MODAL EDIT
                // ==========================================
                document.querySelectorAll('.btn-edit-user').forEach(function(tombol) {
                    tombol.addEventListener('click', function() {
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

                        if (!modalEdit.open) modalEdit.showModal();
                    });
                });

                // ==========================================
                // CEGAH PENGIRIMAN FORM BERULANG
                // ==========================================
                if (formTambah) {
                    formTambah.addEventListener('submit', function() {
                        if (tombolSimpan) {
                            tombolSimpan.disabled = true;
                            tombolSimpan.innerHTML =
                                '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                        }
                    });
                }

                if (formEdit) {
                    formEdit.addEventListener('submit', function() {
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

                const adaErrorTambah = @json($errors->any() && !old('edit_user_id'));

                function bukaModal(modal) {
                    if (modal && !modal.open) {
                        modal.showModal();
                    }
                }

                // Pulihkan modal edit terlebih dahulu jika request berasal dari edit.
                if (oldEditId && formEdit) {
                    const tombolLama = Array.from(
                        document.querySelectorAll('.btn-edit-user')
                    ).find(function(tombol) {
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
    <dialog id="modalEditUser" aria-labelledby="judulModalEditUser"
        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-[800px] overflow-y-auto rounded-[3px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_4px_25px_rgba(0,0,0,0.2)] backdrop:bg-black/40">

        <div class="flex items-center justify-between bg-[#6777ef] px-[25px] py-4">
            <h5 id="judulModalEditUser" class="text-lg font-semibold text-white">
                <i class="fas fa-user-edit mr-2"></i>
                Edit Pengguna
            </h5>

            <button type="button" data-modal-close aria-label="Tutup"
                class="text-2xl leading-none text-white/80 hover:text-white">&times;</button>
        </div>

        <form id="formEditUser" method="POST" action="#">
            @csrf
            @method('PUT')

            <input type="hidden" name="edit_user_id" id="edit_user_id">

            <div class="p-[25px]">

                @if ($errors->any() && old('edit_user_id'))
                    <div class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2.5 text-white">
                        <strong class="font-bold">Gagal memperbarui pengguna.</strong>
                        <ul class="mb-0 mt-2 list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-x-2.5 md:grid-cols-2">
                    <div class="mb-[25px]">
                        <label for="edit_user_name"
                            class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Nama Lengkap <span class="text-[#fc544b]">*</span>
                        </label>
                        <input type="text" name="name" id="edit_user_name" maxlength="255" required
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('name') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">
                        @error('name')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-[25px]">
                        <label for="edit_user_email"
                            class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Email <span class="text-[#fc544b]">*</span>
                        </label>
                        <input type="email" name="email" id="edit_user_email" maxlength="255" required
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('email') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">
                        @error('email')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-x-2.5 md:grid-cols-2">
                    <div class="mb-[25px]">
                        <label for="edit_user_username"
                            class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Username <span class="text-[#fc544b]">*</span>
                        </label>
                        <input type="text" name="username" id="edit_user_username" maxlength="255" required
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('username') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">
                        @error('username')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-[25px]">
                        <label for="edit_user_password"
                            class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Password Baru
                        </label>
                        <input type="password" name="password" id="edit_user_password" minlength="8"
                            autocomplete="new-password" placeholder="Kosongkan jika tidak diubah"
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('password') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">
                        @error('password')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                        <small class="mt-1 block text-xs text-[#6c757d]">
                            Isi hanya jika ingin mengganti password.
                        </small>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-x-2.5 md:grid-cols-2">
                    <div>
                        <label for="edit_user_role"
                            class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Role <span class="text-[#fc544b]">*</span>
                        </label>
                        <select name="role" id="edit_user_role" required
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('role') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">
                            <option value="Admin Pusat">Admin Pusat</option>
                            <option value="Admin Cabang">Admin Cabang</option>
                            <option value="Customer Service">Customer Service</option>
                        </select>
                        @error('role')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="edit_user_cabang"
                            class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Penempatan Cabang
                            <span id="editLabelWajibCabang" class="hidden text-[#fc544b]">*</span>
                        </label>
                        <select name="cabang_id" id="edit_user_cabang"
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('cabang_id') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">
                            <option value="">-- Pusat / Tidak ada cabang spesifik --</option>
                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->id }}">
                                    {{ $cabang->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('cabang_id')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                        <small class="mt-1 block text-xs text-[#6c757d]">
                            Admin Cabang wajib memiliki penempatan cabang.
                        </small>
                    </div>
                </div>

            </div>

            <div class="flex items-center justify-end gap-2 bg-[#f8f9fa] px-[25px] py-3">
                <button type="button" data-modal-close
                    class="inline-flex items-center rounded-[3px] bg-[#e3eaef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>

                <button type="submit" id="btnUpdateUser"
                    class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-65">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </dialog>

</x-admin-layout-stisla>
