<x-admin-layout-stisla>

    @php
        $editGagal = $errors->any() && filled(old('edit_user_id'));
        $tambahGagal = $errors->any() && !$editGagal;
        $errTambah = $editGagal ? new \Illuminate\Support\ViewErrorBag() : $errors;
        $errEdit = $editGagal ? $errors : new \Illuminate\Support\ViewErrorBag();
        $oldTambah = fn (string $key, $default = null) => $editGagal ? $default : old($key, $default);

        // Kelas Tailwind yang dipakai berulang.
        $kelasInput = 'h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none';
        $kelasInputNormal = 'border-[#e4e6fc] focus:border-[#95a0f4]';
        $kelasInputError = 'border-[#fc544b]';
        $kelasLabel = 'mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]';
        $kelasPesanError = 'mt-1 text-xs text-[#fc544b]';
        $kelasBantuan = 'mt-1 block text-xs text-[#6c757d]';
        $kelasBtnBatal = 'inline-flex items-center rounded-[3px] bg-[#e3eaef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]';
        $kelasBtnUtama = 'inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-65';
        $kelasDialog = 'm-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-[800px] overflow-y-auto rounded-[3px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_4px_25px_rgba(0,0,0,0.2)] backdrop:bg-black/40';

        $kelasField = fn($bag, string $field) => $kelasInput . ' ' . ($bag->has($field) ? $kelasInputError : $kelasInputNormal);
    @endphp

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
                <button type="button" aria-label="Tutup" data-alert-close
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">&times;</button>
            </div>
        @endif

        {{-- NOTIFIKASI ERROR (mis. gagal menghapus). Error validasi tampil di dalam modal masing-masing. --}}
        @if (session('error'))
            <div role="alert"
                class="mb-4 flex items-center rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2 text-white">
                <span class="min-w-0">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </span>
                <button type="button" aria-label="Tutup" data-alert-close
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">&times;</button>
            </div>
        @endif

        {{-- TABEL DATA PENGGUNA --}}
        <x-card-table title="Daftar Pengguna" icon="fa-users">

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
                @php
                    $role = strtolower($user->role ?? '');
                    $kelasBadge = match ($role) {
                        'admin pusat' => 'bg-[#6777ef]',
                        'admin cabang' => 'bg-[#3abaf4]',
                        default => 'bg-[#cdd3d8]',
                    };
                @endphp

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
                        <span
                            class="{{ $kelasBadge }} rounded-full px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">
                            {{ $user->role ?? '-' }}
                        </span>
                    </td>

                    <td class="px-[15px] py-3 align-middle">
                        {{ $user->cabang->nama ?? 'Pusat/Semua Cabang' }}
                    </td>

                    <td class="whitespace-nowrap px-[15px] py-3 text-center align-middle">
                        <div class="flex justify-center gap-1">

                            {{-- Tombol edit: membuka modal edit, data diisi oleh JavaScript --}}
                            <button type="button" title="Edit Pengguna" aria-label="Edit pengguna {{ $user->name }}"
                                class="btn-edit-user inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#394eea]"
                                data-id="{{ $user->id }}" data-url="{{ route('user.update', $user->id) }}"
                                data-name="{{ $user->name }}" data-email="{{ $user->email }}"
                                data-username="{{ $user->username }}" data-role="{{ $user->role }}"
                                data-cabang="{{ $user->cabang_id }}">
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- Tombol hapus: membuka modal konfirmasi (form hapus ada di modal) --}}
                            <button type="button" title="Hapus Pengguna" aria-label="Hapus pengguna {{ $user->name }}"
                                class="btn-hapus-user inline-flex items-center rounded-[3px] bg-[#fc544b] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#fb160a]"
                                data-url="{{ route('user.destroy', $user->id) }}">
                                <i class="fas fa-trash"></i>
                            </button>
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

        <p class="mt-3 text-xs text-[#6c757d]">
            Total pengguna:
            <strong class="font-bold">{{ $users->count() }}</strong>
        </p>
    </div>

    {{-- ====================================================================== --}}
    {{-- MODAL TAMBAH PENGGUNA                                                  --}}
    {{-- ====================================================================== --}}
    <dialog id="modalTambahUser" aria-labelledby="judulModalTambahUser" class="{{ $kelasDialog }}">

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

                <p class="mb-6 text-[#6c757d]">
                    Masukkan informasi pengguna dengan lengkap.
                </p>

                @if ($tambahGagal)
                    <div role="alert"
                        class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2.5 text-white">
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
                        <label for="userName" class="{{ $kelasLabel }}">
                            Nama Lengkap <span class="text-[#fc544b]">*</span>
                        </label>

                        <input type="text" id="userName" name="name" value="{{ $oldTambah('name') }}"
                            placeholder="Masukkan nama lengkap" maxlength="255" autocomplete="name" required autofocus
                            class="{{ $kelasField($errTambah, 'name') }}">

                        @if ($errTambah->has('name'))
                            <div class="{{ $kelasPesanError }}">{{ $errTambah->first('name') }}</div>
                        @endif
                    </div>

                    <div class="mb-[25px]">
                        <label for="userEmail" class="{{ $kelasLabel }}">
                            Email <span class="text-[#fc544b]">*</span>
                        </label>

                        <input type="email" id="userEmail" name="email" value="{{ $oldTambah('email') }}"
                            placeholder="nama@email.com" maxlength="255" autocomplete="email" required
                            class="{{ $kelasField($errTambah, 'email') }}">

                        @if ($errTambah->has('email'))
                            <div class="{{ $kelasPesanError }}">{{ $errTambah->first('email') }}</div>
                        @endif
                    </div>
                </div>

                {{-- USERNAME DAN PASSWORD --}}
                <div class="grid grid-cols-1 gap-x-2.5 md:grid-cols-2">
                    <div class="mb-[25px]">
                        <label for="userUsername" class="{{ $kelasLabel }}">
                            Username <span class="text-[#fc544b]">*</span>
                        </label>

                        <input type="text" id="userUsername" name="username"
                            value="{{ $oldTambah('username') }}" placeholder="Masukkan username" maxlength="255"
                            autocomplete="username" required class="{{ $kelasField($errTambah, 'username') }}">

                        @if ($errTambah->has('username'))
                            <div class="{{ $kelasPesanError }}">{{ $errTambah->first('username') }}</div>
                        @endif
                    </div>

                    <div class="mb-[25px]">
                        <label for="userPassword" class="{{ $kelasLabel }}">
                            Password <span class="text-[#fc544b]">*</span>
                        </label>

                        <input type="password" id="userPassword" name="password" placeholder="Minimal 8 karakter"
                            minlength="8" autocomplete="new-password" required
                            class="{{ $kelasField($errTambah, 'password') }}">

                        @if ($errTambah->has('password'))
                            <div class="{{ $kelasPesanError }}">{{ $errTambah->first('password') }}</div>
                        @endif

                        <small class="{{ $kelasBantuan }}">
                            Password tidak akan ditampilkan kembali jika validasi gagal.
                        </small>
                    </div>
                </div>

                {{-- ROLE DAN CABANG --}}
                <div class="grid grid-cols-1 gap-x-2.5 md:grid-cols-2">
                    <div>
                        <label for="userRole" class="{{ $kelasLabel }}">
                            Role <span class="text-[#fc544b]">*</span>
                        </label>

                        <select id="userRole" name="role" required class="{{ $kelasField($errTambah, 'role') }}">
                            <option value="">-- Pilih Role --</option>
                            @foreach (['Admin Pusat', 'Admin Cabang', 'Customer Service'] as $opsiRole)
                                <option value="{{ $opsiRole }}" @selected($oldTambah('role') === $opsiRole)>
                                    {{ $opsiRole }}
                                </option>
                            @endforeach
                        </select>

                        @if ($errTambah->has('role'))
                            <div class="{{ $kelasPesanError }}">{{ $errTambah->first('role') }}</div>
                        @endif
                    </div>

                    <div>
                        <label for="userCabang" class="{{ $kelasLabel }}">
                            Penempatan Cabang
                            <span id="labelWajibCabang" class="hidden text-[#fc544b]">*</span>
                        </label>

                        <select id="userCabang" name="cabang_id" class="{{ $kelasField($errTambah, 'cabang_id') }}">
                            <option value="">-- Pusat / Tidak ada cabang spesifik --</option>
                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->id }}" @selected((string) $oldTambah('cabang_id') === (string) $cabang->id)>
                                    {{ $cabang->nama }}
                                </option>
                            @endforeach
                        </select>

                        @if ($errTambah->has('cabang_id'))
                            <div class="{{ $kelasPesanError }}">{{ $errTambah->first('cabang_id') }}</div>
                        @endif

                        <small class="{{ $kelasBantuan }}">
                            Admin Cabang wajib memiliki penempatan cabang.
                        </small>
                    </div>
                </div>
            </div>

            {{-- FOOTER MODAL --}}
            <div class="flex items-center justify-end gap-2 bg-[#f8f9fa] px-[25px] py-3">
                <button type="button" data-modal-close class="{{ $kelasBtnBatal }}">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>

                <button type="submit" id="btnSimpanUser"
                    class="{{ $kelasBtnUtama }}">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Pengguna
                </button>
            </div>
        </form>
    </dialog>

    {{-- ====================================================================== --}}
    {{-- MODAL EDIT PENGGUNA                                                    --}}
    {{-- ====================================================================== --}}
    <dialog id="modalEditUser" aria-labelledby="judulModalEditUser" class="{{ $kelasDialog }}">

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

                @if ($editGagal)
                    <div role="alert"
                        class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2.5 text-white">
                        <strong class="font-bold">Gagal memperbarui pengguna.</strong>
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
                        <label for="edit_user_name" class="{{ $kelasLabel }}">
                            Nama Lengkap <span class="text-[#fc544b]">*</span>
                        </label>
                        <input type="text" name="name" id="edit_user_name" maxlength="255"
                            autocomplete="name" required autofocus class="{{ $kelasField($errEdit, 'name') }}">
                        @if ($errEdit->has('name'))
                            <div class="{{ $kelasPesanError }}">{{ $errEdit->first('name') }}</div>
                        @endif
                    </div>

                    <div class="mb-[25px]">
                        <label for="edit_user_email" class="{{ $kelasLabel }}">
                            Email <span class="text-[#fc544b]">*</span>
                        </label>
                        <input type="email" name="email" id="edit_user_email" maxlength="255"
                            autocomplete="email" required class="{{ $kelasField($errEdit, 'email') }}">
                        @if ($errEdit->has('email'))
                            <div class="{{ $kelasPesanError }}">{{ $errEdit->first('email') }}</div>
                        @endif
                    </div>
                </div>

                {{-- USERNAME DAN PASSWORD --}}
                <div class="grid grid-cols-1 gap-x-2.5 md:grid-cols-2">
                    <div class="mb-[25px]">
                        <label for="edit_user_username" class="{{ $kelasLabel }}">
                            Username <span class="text-[#fc544b]">*</span>
                        </label>
                        <input type="text" name="username" id="edit_user_username" maxlength="255"
                            autocomplete="username" required class="{{ $kelasField($errEdit, 'username') }}">
                        @if ($errEdit->has('username'))
                            <div class="{{ $kelasPesanError }}">{{ $errEdit->first('username') }}</div>
                        @endif
                    </div>

                    <div class="mb-[25px]">
                        <label for="edit_user_password" class="{{ $kelasLabel }}">
                            Password Baru
                        </label>
                        <input type="password" name="password" id="edit_user_password" minlength="8"
                            autocomplete="new-password" placeholder="Kosongkan jika tidak diubah"
                            class="{{ $kelasField($errEdit, 'password') }}">
                        @if ($errEdit->has('password'))
                            <div class="{{ $kelasPesanError }}">{{ $errEdit->first('password') }}</div>
                        @endif
                        <small class="{{ $kelasBantuan }}">
                            Isi hanya jika ingin mengganti password.
                        </small>
                    </div>
                </div>

                {{-- ROLE DAN CABANG --}}
                <div class="grid grid-cols-1 gap-x-2.5 md:grid-cols-2">
                    <div>
                        <label for="edit_user_role" class="{{ $kelasLabel }}">
                            Role <span class="text-[#fc544b]">*</span>
                        </label>
                        <select name="role" id="edit_user_role" required
                            class="{{ $kelasField($errEdit, 'role') }}">
                            <option value="Admin Pusat">Admin Pusat</option>
                            <option value="Admin Cabang">Admin Cabang</option>
                            <option value="Customer Service">Customer Service</option>
                        </select>
                        @if ($errEdit->has('role'))
                            <div class="{{ $kelasPesanError }}">{{ $errEdit->first('role') }}</div>
                        @endif
                    </div>

                    <div>
                        <label for="edit_user_cabang" class="{{ $kelasLabel }}">
                            Penempatan Cabang
                            <span id="editLabelWajibCabang" class="hidden text-[#fc544b]">*</span>
                        </label>
                        <select name="cabang_id" id="edit_user_cabang"
                            class="{{ $kelasField($errEdit, 'cabang_id') }}">
                            <option value="">-- Pusat / Tidak ada cabang spesifik --</option>
                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->id }}">{{ $cabang->nama }}</option>
                            @endforeach
                        </select>
                        @if ($errEdit->has('cabang_id'))
                            <div class="{{ $kelasPesanError }}">{{ $errEdit->first('cabang_id') }}</div>
                        @endif
                        <small class="{{ $kelasBantuan }}">
                            Admin Cabang wajib memiliki penempatan cabang.
                        </small>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 bg-[#f8f9fa] px-[25px] py-3">
                <button type="button" data-modal-close class="{{ $kelasBtnBatal }}">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>

                <button type="submit" id="btnUpdateUser"
                    class="{{ $kelasBtnUtama }}">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </dialog>

    {{-- ====================================================================== --}}
    {{-- MODAL KONFIRMASI HAPUS PENGGUNA                                        --}}
    {{-- ====================================================================== --}}
    <dialog id="modalHapusUser" aria-labelledby="judulModalHapusUser"
        class="m-auto w-[calc(100%-2rem)] max-w-[400px] rounded-[3px] bg-white p-0 shadow-[0_10px_40px_rgba(0,0,0,0.25)] backdrop:bg-black/50">
        <div class="p-6 text-center">
            <div
                class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[#fc544b]/10 text-[#fc544b]">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>

            <h5 id="judulModalHapusUser" class="mb-2 text-lg font-bold text-[#34395e]">Konfirmasi Hapus</h5>
            <p class="mb-6 text-sm text-[#6c757d]">
                Apakah Anda yakin ingin menghapus akun pengguna ini? <br>Aksi ini tidak dapat dibatalkan.
            </p>

            <form id="formHapusUser" method="POST" action="#">
                @csrf
                @method('DELETE')

                <div class="flex justify-center gap-3">
                    <button type="button" data-modal-close
                        class="inline-flex items-center rounded-[3px] bg-[#e3eaef] px-4 py-2 text-sm font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                        Batal
                    </button>
                    <button type="submit" id="btnKonfirmasiHapus"
                        class="inline-flex items-center rounded-[3px] bg-[#fc544b] px-4 py-2 text-sm font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#fb160a] disabled:cursor-not-allowed disabled:opacity-60">
                        Ya, Hapus Pengguna
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const html = document.documentElement;

            const modalTambah = document.getElementById('modalTambahUser');
            const modalEdit = document.getElementById('modalEditUser');
            const modalHapus = document.getElementById('modalHapusUser');
            const formTambah = document.getElementById('formTambahUser');
            const formEdit = document.getElementById('formEditUser');
            const formHapus = document.getElementById('formHapusUser');

            if (!modalTambah || !modalEdit || !modalHapus || !formTambah || !formEdit || !formHapus) {
                console.error('Modal pengguna tidak lengkap. Periksa id elemen pada halaman ini.');
                return;
            }

            const kolomTambah = {
                role: document.getElementById('userRole'),
                cabang: document.getElementById('userCabang'),
                label: document.getElementById('labelWajibCabang'),
            };

            const kolomEdit = {
                id: document.getElementById('edit_user_id'),
                nama: document.getElementById('edit_user_name'),
                email: document.getElementById('edit_user_email'),
                username: document.getElementById('edit_user_username'),
                password: document.getElementById('edit_user_password'),
                role: document.getElementById('edit_user_role'),
                cabang: document.getElementById('edit_user_cabang'),
                label: document.getElementById('editLabelWajibCabang'),
            };

            // =========================================
            // FUNGSI BANTU
            // =========================================

            function bukaModal(dialog) {
                if (dialog && !dialog.open) {
                    dialog.showModal();
                    html.classList.add('overflow-hidden'); // kunci scroll halaman di belakang modal
                }
            }

            // Event "close" tidak bubble, jadi didengarkan lewat capture.
            // Dipicu oleh tombol Batal, klik latar gelap, maupun tombol Esc.
            document.addEventListener('close', function (event) {
                if (event.target instanceof HTMLDialogElement && !document.querySelector('dialog[open]')) {
                    html.classList.remove('overflow-hidden');
                }
            }, true);

            // Cabang wajib untuk Admin Cabang; Admin Pusat tidak ditempatkan pada cabang tertentu.
            function aturCabang(roleSelect, cabangSelect, labelWajib) {
                if (!roleSelect || !cabangSelect) return;

                const adminCabang = roleSelect.value === 'Admin Cabang';

                cabangSelect.required = adminCabang;

                if (labelWajib) {
                    labelWajib.classList.toggle('hidden', !adminCabang);
                }

                if (roleSelect.value === 'Admin Pusat') {
                    cabangSelect.value = '';
                }
            }

            // Pilih <option> tanpa peduli huruf besar/kecil; pakai cadangan bila tidak ada yang cocok.
            function pilihOpsi(select, nilai, cadangan) {
                const cari = String(nilai === null || nilai === undefined ? '' : nilai).trim().toLowerCase();
                const opsi = Array.from(select.options).find(function (o) {
                    return o.value.toLowerCase() === cari;
                });
                select.value = opsi ? opsi.value : cadangan;
            }

            // Isi modal edit dengan data pengguna yang dipilih.
            function isiFormEdit(data) {
                kolomEdit.id.value = data.id || '';
                kolomEdit.nama.value = data.nama || '';
                kolomEdit.email.value = data.email || '';
                kolomEdit.username.value = data.username || '';
                kolomEdit.password.value = ''; // password tidak pernah diisi ulang

                pilihOpsi(kolomEdit.role, data.role, 'Customer Service');
                pilihOpsi(kolomEdit.cabang, data.cabang, '');

                formEdit.setAttribute('action', data.url || '#');

                aturCabang(kolomEdit.role, kolomEdit.cabang, kolomEdit.label);
            }

            // Cegah form terkirim berkali-kali, dan pulihkan tombol bila
            // halaman ditampilkan kembali dari cache (tombol Back browser).
            function kunciPengiriman(form, tombol, teksProses) {
                if (!tombol) return;

                const isiAwal = tombol.innerHTML;

                form.addEventListener('submit', function (event) {
                    if (event.defaultPrevented) return;

                    tombol.disabled = true;
                    tombol.innerHTML = '<i class="fas fa-spinner animate-spin mr-1"></i> ' + teksProses;
                });

                window.addEventListener('pageshow', function (event) {
                    if (event.persisted) {
                        tombol.disabled = false;
                        tombol.innerHTML = isiAwal;
                    }
                });
            }

            // =========================================
            // KLIK: BUKA/TUTUP MODAL, TOMBOL EDIT/HAPUS, TUTUP NOTIFIKASI
            // =========================================

            // Klik pada latar gelap hanya menutup modal jika tekan dan lepas mouse
            // sama-sama di latar. Tanpa ini, memilih teks di input lalu melepas
            // mouse di luar modal akan menutup modal dan membuang isian.
            let targetTekan = null;

            document.addEventListener('mousedown', function (event) {
                targetTekan = event.target;
            });

            document.addEventListener('click', function (event) {
                const target = event.target instanceof Element ? event.target : null;
                if (!target) return;

                const buka = target.closest('[data-modal-open]');
                if (buka) {
                    bukaModal(document.getElementById(buka.dataset.modalOpen));
                    return;
                }

                const tutup = target.closest('[data-modal-close]');
                if (tutup) {
                    const dialog = tutup.closest('dialog');
                    if (dialog) dialog.close();
                    return;
                }

                const tutupNotifikasi = target.closest('[data-alert-close]');
                if (tutupNotifikasi) {
                    const notifikasi = tutupNotifikasi.closest('[role="alert"]');
                    if (notifikasi) notifikasi.remove();
                    return;
                }

                const tombolEdit = target.closest('.btn-edit-user');
                if (tombolEdit) {
                    isiFormEdit({
                        id: tombolEdit.dataset.id,
                        nama: tombolEdit.dataset.name,
                        email: tombolEdit.dataset.email,
                        username: tombolEdit.dataset.username,
                        role: tombolEdit.dataset.role,
                        cabang: tombolEdit.dataset.cabang,
                        url: tombolEdit.dataset.url,
                    });
                    bukaModal(modalEdit);
                    return;
                }

                const tombolHapus = target.closest('.btn-hapus-user');
                if (tombolHapus) {
                    formHapus.setAttribute('action', tombolHapus.dataset.url || '#');
                    bukaModal(modalHapus);
                    return;
                }

                // Klik di latar gelap (target = elemen dialog itu sendiri).
                if (
                    target instanceof HTMLDialogElement &&
                    target.open &&
                    targetTekan === target
                ) {
                    target.close();
                }
            });

            // =========================================
            // PENGIRIMAN FORM
            // =========================================

            // Jangan kirim form edit/hapus jika URL tujuan belum terisi.
            [
                [formEdit, 'URL update pengguna belum ditentukan.'],
                [formHapus, 'URL hapus pengguna belum ditentukan.'],
            ].forEach(function (item) {
                item[0].addEventListener('submit', function (event) {
                    const action = item[0].getAttribute('action');

                    if (!action || action === '#') {
                        event.preventDefault();
                        console.error(item[1]);
                    }
                });
            });

            kunciPengiriman(formTambah, document.getElementById('btnSimpanUser'), 'Menyimpan...');
            kunciPengiriman(formEdit, document.getElementById('btnUpdateUser'), 'Memperbarui...');
            kunciPengiriman(formHapus, document.getElementById('btnKonfirmasiHapus'), 'Menghapus...');

            // =========================================
            // EVENT PERUBAHAN ROLE
            // =========================================

            if (kolomTambah.role) {
                kolomTambah.role.addEventListener('change', function () {
                    aturCabang(kolomTambah.role, kolomTambah.cabang, kolomTambah.label);
                });
            }

            if (kolomEdit.role) {
                kolomEdit.role.addEventListener('change', function () {
                    aturCabang(kolomEdit.role, kolomEdit.cabang, kolomEdit.label);
                });
            }

            aturCabang(kolomTambah.role, kolomTambah.cabang, kolomTambah.label);

            // =========================================
            // BUKA KEMBALI MODAL JIKA VALIDASI GAGAL
            // =========================================

            @if ($editGagal)
                isiFormEdit({
                    id: @js(old('edit_user_id')),
                    nama: @js(old('name', '')),
                    email: @js(old('email', '')),
                    username: @js(old('username', '')),
                    role: @js(old('role', 'Customer Service')),
                    cabang: @js(old('cabang_id', '')),
                    url: @js(route('user.update', old('edit_user_id'))),
                });
                bukaModal(modalEdit);
            @elseif ($tambahGagal)
                aturCabang(kolomTambah.role, kolomTambah.cabang, kolomTambah.label);
                bukaModal(modalTambah);
            @endif
        });
    </script>

</x-admin-layout-stisla>