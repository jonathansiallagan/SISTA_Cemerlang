<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div
        class="mb-[30px] flex flex-wrap items-center gap-x-4 gap-y-2 rounded-[3px] bg-white px-[30px] py-5 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
        <h1 class="text-2xl font-bold text-[#34395e]">Data Cabang</h1>

        <ol class="ml-auto flex items-center text-[13px] text-[#6c757d]">
            <li class="font-semibold text-[#6777ef]">Data Cabang</li>
        </ol>
    </div>

    <div>

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <p class="mb-2 text-[#6c757d]">
                Kelola daftar cabang percetakan CV Cemerlang.
            </p>

            <button type="button" data-modal-open="modalTambahCabang"
                class="mb-2 inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
                <i class="fas fa-plus mr-1"></i>
                Tambah Cabang
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
        @if (session('error'))
            <div role="alert"
                class="mb-4 flex items-center rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2 text-white">
                <span class="min-w-0">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </span>
                <button type="button" aria-label="Tutup" onclick="this.closest('[role=alert]').remove()"
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">&times;</button>
            </div>
        @endif

        {{-- TABEL DATA CABANG --}}
        <x-card-table title="Data Riwayat Pengeluaran" icon="fa-receipt">

            {{-- HEADER TABEL --}}
            <x-slot:head>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-center">#</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-left">Nama Cabang</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-left">Alamat</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-left">No. HP</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-center">Status Cabang</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-center">Aksi</th>
            </x-slot:head>

            {{-- ISI TABEL --}}
            @forelse ($cabangs as $cabang)
                <tr class="odd:bg-black/[0.025]">
                    <td class="px-[15px] py-3 text-center align-middle text-[#6c757d]">
                        {{ $loop->iteration }}
                    </td>

                    <td class="px-[15px] py-3 align-middle font-bold">
                        {{ $cabang->nama }}
                    </td>

                    <td class="px-[15px] py-3 align-middle">
                        {{ $cabang->alamat ?: '-' }}
                    </td>

                    <td class="whitespace-nowrap px-[15px] py-3 align-middle">
                        {{ $cabang->no_hp ?: '-' }}
                    </td>

                    <td class="px-[15px] py-3 text-center align-middle">
                        @if (strtolower($cabang->status) === 'aktif')
                            <span
                                class="rounded-full bg-[#54ca68] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">
                                {{ ucfirst($cabang->status) }}
                            </span>
                        @else
                            <span
                                class="rounded-full bg-[#cdd3d8] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">
                                {{ ucfirst($cabang->status) }}
                            </span>
                        @endif
                    </td>

                    <td class="whitespace-nowrap px-[15px] py-3 text-center align-middle">
                        <a href="{{ route('cabang.edit', $cabang->id) }}" title="Edit Cabang"
                            class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#394eea]">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-[15px] py-6 text-center text-[#6c757d]">
                        <i class="fas fa-store mb-2 text-[2em]"></i>
                        <p>
                            Belum ada data cabang.
                            Silakan tambah cabang baru.
                        </p>
                    </td>
                </tr>
            @endforelse
        </x-card-table>
    </div>

    {{-- MODAL TAMBAH CABANG --}}
    <dialog id="modalTambahCabang" aria-labelledby="judulModalTambahCabang"
        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-[800px] overflow-y-auto rounded-[3px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_4px_25px_rgba(0,0,0,0.2)] backdrop:bg-black/40">

        {{-- HEADER MODAL --}}
        <div class="flex items-center justify-between bg-[#6777ef] px-[25px] py-4">
            <h5 id="judulModalTambahCabang" class="text-lg font-semibold text-white">
                <i class="fas fa-store mr-2"></i>
                Tambah Cabang Baru
            </h5>

            <button type="button" data-modal-close aria-label="Tutup"
                class="text-2xl leading-none text-white/80 hover:text-white">&times;</button>
        </div>

        {{-- FORM TAMBAH CABANG --}}
        <form id="formTambahCabang" action="{{ route('cabang.store') }}" method="POST">
            @csrf

            <div class="p-[25px]">

                <p class="mb-6 text-[#6c757d]">
                    Masukkan informasi cabang percetakan dengan lengkap.
                </p>

                {{-- NAMA CABANG --}}
                <div class="mb-[25px]">
                    <label for="namaCabang" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Nama Cabang <span class="text-[#fc544b]">*</span>
                    </label>

                    <input type="text" id="namaCabang" name="nama" value="{{ old('nama') }}"
                        placeholder="Contoh: Cabang Arengka" maxlength="255" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('nama') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">

                    @error('nama')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

                {{-- NOMOR HP --}}
                <div class="mb-[25px]">
                    <label for="noHpCabang" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Nomor HP/Telepon <span class="text-[#fc544b]">*</span>
                    </label>

                    <input type="text" id="noHpCabang" name="no_hp" value="{{ old('no_hp') }}"
                        placeholder="Contoh: 081234567890" maxlength="20" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('no_hp') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">

                    @error('no_hp')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ALAMAT --}}
                <div class="mb-[25px]">
                    <label for="alamatCabang" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Alamat Lengkap <span class="text-[#fc544b]">*</span>
                    </label>

                    <textarea id="alamatCabang" name="alamat" rows="3" maxlength="1000" placeholder="Masukkan alamat lengkap cabang"
                        required
                        class="w-full rounded-[3px] border bg-[#fdfdff] px-[15px] py-2.5 text-sm text-[#495057] focus:outline-none @error('alamat') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">{{ old('alamat') }}</textarea>

                    @error('alamat')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

                {{-- STATUS CABANG --}}
                <div>
                    <label for="statusCabang"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Status Cabang <span class="text-[#fc544b]">*</span>
                    </label>

                    <select id="statusCabang" name="status" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('status') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">
                        <option value="aktif" {{ old('status', 'aktif') === 'aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>
                        <option value="nonaktif" {{ old('status') === 'nonaktif' ? 'selected' : '' }}>
                            Nonaktif
                        </option>
                    </select>

                    @error('status')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- FOOTER MODAL --}}
            <div class="flex items-center justify-end gap-2 bg-[#f8f9fa] px-[25px] py-3">
                <button type="button" data-modal-close
                    class="inline-flex items-center rounded-[3px] bg-[#e3eaef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>

                <button type="submit" id="btnSimpanCabang"
                    class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-65">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Cabang
                </button>
            </div>

        </form>
    </dialog>

    
    {{-- MODAL EDIT CABANG --}}
    <div class="modal fade"
        id="modalEditCabang"
        tabindex="-1"
        role="dialog"
        aria-labelledby="judulModalEditCabang"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered"
            role="document">

            <div class="modal-content">

                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="judulModalEditCabang">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Data Cabang
                    </h5>

                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form id="formEditCabang" method="POST" action="#">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="edit_cabang_id" id="edit_cabang_id">

                    <div class="modal-body">

                        @if ($errors->any() && old('edit_cabang_id'))
                            <div class="alert alert-danger">
                                <strong>Gagal memperbarui data cabang.</strong>
                                <ul class="mb-0 mt-2 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="edit_cabang_nama">
                                Nama Cabang <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                name="nama"
                                id="edit_cabang_nama"
                                class="form-control @error('nama') is-invalid @enderror"
                                maxlength="255"
                                required>
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="edit_cabang_no_hp">
                                Nomor HP/Telepon <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                name="no_hp"
                                id="edit_cabang_no_hp"
                                class="form-control @error('no_hp') is-invalid @enderror"
                                maxlength="20"
                                required>
                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="edit_cabang_alamat">
                                Alamat Lengkap <span class="text-danger">*</span>
                            </label>
                            <textarea name="alamat"
                                    id="edit_cabang_alamat"
                                    rows="3"
                                    maxlength="1000"
                                    class="form-control @error('alamat') is-invalid @enderror"
                                    required></textarea>
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label for="edit_cabang_status">
                                Status Cabang <span class="text-danger">*</span>
                            </label>
                            <select name="status"
                                    id="edit_cabang_status"
                                    class="form-control @error('status') is-invalid @enderror"
                                    required>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                id="btnUpdateCabang">
                            <i class="fas fa-save mr-1"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    {{-- SCRIPT MODAL --}}
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

                    // Klik di latar gelap (target = elemen dialog itu sendiri)
                    if (e.target instanceof HTMLDialogElement && e.target.open) {
                        e.target.close();
                    }
                });
            })();

            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('modalTambahCabang');
                const form = document.getElementById('formTambahCabang');
                const tombolSimpan = document.getElementById('btnSimpanCabang');

                if (!modal || !form) {
                    return;
                }

                // Cegah form terkirim berkali-kali.
                form.addEventListener('submit', function() {
                    if (tombolSimpan) {
                        tombolSimpan.disabled = true;
                        tombolSimpan.innerHTML =
                            '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                    }
                });

                // Jika validasi gagal, buka kembali modal dan tampilkan error.
                @if ($errors->any())
                    if (!modal.open) modal.showModal();
                @endif
            });
        </script>
        
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEdit = document.getElementById('modalEditCabang');
            const formEdit = document.getElementById('formEditCabang');
            const tombolUpdate = document.getElementById('btnUpdateCabang');

            if (!modalEdit || !formEdit) {
                console.error('Modal Edit Cabang atau form tidak ditemukan.');
                return;
            }

            // Pindahkan modal ke body untuk menghindari masalah layout Stisla.
            document.body.appendChild(modalEdit);

            const editId = document.getElementById('edit_cabang_id');
            const editNama = document.getElementById('edit_cabang_nama');
            const editNoHp = document.getElementById('edit_cabang_no_hp');
            const editAlamat = document.getElementById('edit_cabang_alamat');
            const editStatus = document.getElementById('edit_cabang_status');

            // Isi form berdasarkan tombol Edit yang diklik.
            document.querySelectorAll('.btn-edit-cabang').forEach(function (tombol) {
                tombol.addEventListener('click', function () {
                    editId.value = this.dataset.id || '';
                    editNama.value = this.dataset.nama || '';
                    editNoHp.value = this.dataset.noHp || '';
                    editAlamat.value = this.dataset.alamat || '';
                    editStatus.value = this.dataset.status || 'aktif';

                    formEdit.action = this.dataset.url || '';

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

            // Cegah pengiriman form berkali-kali.
            formEdit.addEventListener('submit', function (event) {
                if (!formEdit.action || formEdit.action.endsWith('#')) {
                    event.preventDefault();
                    console.error('URL update cabang belum ditentukan.');
                    return;
                }

                if (tombolUpdate) {
                    tombolUpdate.disabled = true;
                    tombolUpdate.innerHTML =
                        '<i class="fas fa-spinner fa-spin mr-1"></i> Memperbarui...';
                }
            });

            // Buka kembali modal edit apabila validasi server gagal.
            const oldEditId = @json(old('edit_cabang_id'));

            if (oldEditId) {
                const tombolLama = Array.from(
                    document.querySelectorAll('.btn-edit-cabang')
                ).find(function (tombol) {
                    return String(tombol.dataset.id) === String(oldEditId);
                });

                if (tombolLama) {
                    editId.value = oldEditId;
                    editNama.value = @json(old('nama', ''));
                    editNoHp.value = @json(old('no_hp', ''));
                    editAlamat.value = @json(old('alamat', ''));
                    editStatus.value = @json(old('status', 'aktif'));

                    formEdit.action = tombolLama.dataset.url || '';

                    if (window.jQuery && typeof jQuery.fn.modal === 'function') {
                        jQuery(modalEdit).modal('show');
                    }
                }
            }
        });
        </script>

    @endpush

</x-admin-layout-stisla>
