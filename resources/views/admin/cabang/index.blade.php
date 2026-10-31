<x-admin-layout-stisla>

    @php
        // Modal tambah dan modal edit memakai nama field yang sama (nama, no_hp, alamat, status).
        // Karena itu old input dan pesan error hanya ditampilkan pada modal yang barusan dikirim.
        $editGagal = $errors->any() && old('edit_cabang_id');
        $errTambah = $editGagal ? new \Illuminate\Support\ViewErrorBag() : $errors;
        $errEdit = $editGagal ? $errors : new \Illuminate\Support\ViewErrorBag();
        $oldTambah = fn(string $key, $default = null) => $editGagal ? $default : old($key, $default);
    @endphp

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
                <button type="button" data-alert-close aria-label="Tutup"
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
                <button type="button" data-alert-close aria-label="Tutup"
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">&times;</button>
            </div>
        @endif

        {{-- TABEL DATA CABANG --}}
        <x-card-table title="Daftar Cabang" icon="fa-store">

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
                @php($statusCabang = strtolower((string) $cabang->status))

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
                        @if ($statusCabang === 'aktif')
                            <span
                                class="rounded-full bg-[#54ca68] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">
                                {{ ucfirst($statusCabang) }}
                            </span>
                        @else
                            <span
                                class="rounded-full bg-[#cdd3d8] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">
                                {{ ucfirst($statusCabang) }}
                            </span>
                        @endif
                    </td>

                    <td class="whitespace-nowrap px-[15px] py-3 text-center align-middle">
                        {{-- Membuka modal edit; data diisi ke form oleh JavaScript di bawah. --}}
                        <button type="button" title="Edit Cabang" aria-label="Edit cabang {{ $cabang->nama }}"
                            data-id="{{ $cabang->id }}" data-nama="{{ $cabang->nama }}"
                            data-no-hp="{{ $cabang->no_hp }}" data-alamat="{{ $cabang->alamat }}"
                            data-status="{{ $statusCabang }}" data-url="{{ route('cabang.update', $cabang->id) }}"
                            class="btn-edit-cabang inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#394eea]">
                            <i class="fas fa-edit" aria-hidden="true"></i>
                        </button>
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

                    <input type="text" id="namaCabang" name="nama" value="{{ $oldTambah('nama') }}"
                        placeholder="Contoh: Cabang Arengka" maxlength="255" required autofocus
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none {{ $errTambah->has('nama') ? 'border-[#fc544b]' : 'border-[#e4e6fc] focus:border-[#95a0f4]' }}">

                    @if ($errTambah->has('nama'))
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $errTambah->first('nama') }}</div>
                    @endif
                </div>

                {{-- NOMOR HP --}}
                <div class="mb-[25px]">
                    <label for="noHpCabang" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Nomor HP/Telepon <span class="text-[#fc544b]">*</span>
                    </label>

                    <input type="text" id="noHpCabang" name="no_hp" value="{{ $oldTambah('no_hp') }}"
                        placeholder="Contoh: 081234567890" maxlength="20" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none {{ $errTambah->has('no_hp') ? 'border-[#fc544b]' : 'border-[#e4e6fc] focus:border-[#95a0f4]' }}">

                    @if ($errTambah->has('no_hp'))
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $errTambah->first('no_hp') }}</div>
                    @endif
                </div>

                {{-- ALAMAT --}}
                <div class="mb-[25px]">
                    <label for="alamatCabang" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Alamat Lengkap <span class="text-[#fc544b]">*</span>
                    </label>

                    <textarea id="alamatCabang" name="alamat" rows="3" maxlength="1000"
                        placeholder="Masukkan alamat lengkap cabang" required
                        class="w-full rounded-[3px] border bg-[#fdfdff] px-[15px] py-2.5 text-sm text-[#495057] focus:outline-none {{ $errTambah->has('alamat') ? 'border-[#fc544b]' : 'border-[#e4e6fc] focus:border-[#95a0f4]' }}">{{ $oldTambah('alamat') }}</textarea>

                    @if ($errTambah->has('alamat'))
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $errTambah->first('alamat') }}</div>
                    @endif
                </div>

                {{-- STATUS CABANG --}}
                <div>
                    <label for="statusCabang"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Status Cabang <span class="text-[#fc544b]">*</span>
                    </label>

                    <select id="statusCabang" name="status" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none {{ $errTambah->has('status') ? 'border-[#fc544b]' : 'border-[#e4e6fc] focus:border-[#95a0f4]' }}">
                        <option value="aktif" {{ $oldTambah('status', 'aktif') === 'aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>
                        <option value="nonaktif" {{ $oldTambah('status') === 'nonaktif' ? 'selected' : '' }}>
                            Nonaktif
                        </option>
                    </select>

                    @if ($errTambah->has('status'))
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $errTambah->first('status') }}</div>
                    @endif
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
                    class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-[.65]">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Cabang
                </button>
            </div>

        </form>
    </dialog>

    {{-- MODAL EDIT CABANG --}}
    <dialog id="modalEditCabang" aria-labelledby="judulModalEditCabang"
        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-[800px] overflow-y-auto rounded-[3px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_4px_25px_rgba(0,0,0,0.2)] backdrop:bg-black/40">

        {{-- HEADER MODAL --}}
        <div class="flex items-center justify-between bg-[#6777ef] px-[25px] py-4">
            <h5 id="judulModalEditCabang" class="text-lg font-semibold text-white">
                <i class="fas fa-edit mr-2"></i>
                Edit Data Cabang
            </h5>

            <button type="button" data-modal-close aria-label="Tutup"
                class="text-2xl leading-none text-white/80 hover:text-white">&times;</button>
        </div>

        {{-- FORM EDIT CABANG --}}
        <form id="formEditCabang" action="#" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="edit_cabang_id" id="edit_cabang_id" value="">

            <div class="p-[25px]">

                @if ($editGagal)
                    <div role="alert"
                        class="mb-6 rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-4 text-white">
                        <strong>Gagal memperbarui data cabang.</strong>
                        <ul class="mt-2 list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- NAMA CABANG --}}
                <div class="mb-[25px]">
                    <label for="edit_cabang_nama"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Nama Cabang <span class="text-[#fc544b]">*</span>
                    </label>

                    <input type="text" id="edit_cabang_nama" name="nama" maxlength="255" required autofocus
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none {{ $errEdit->has('nama') ? 'border-[#fc544b]' : 'border-[#e4e6fc] focus:border-[#95a0f4]' }}">

                    @if ($errEdit->has('nama'))
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $errEdit->first('nama') }}</div>
                    @endif
                </div>

                {{-- NOMOR HP --}}
                <div class="mb-[25px]">
                    <label for="edit_cabang_no_hp"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Nomor HP/Telepon <span class="text-[#fc544b]">*</span>
                    </label>

                    <input type="text" id="edit_cabang_no_hp" name="no_hp" maxlength="20" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none {{ $errEdit->has('no_hp') ? 'border-[#fc544b]' : 'border-[#e4e6fc] focus:border-[#95a0f4]' }}">

                    @if ($errEdit->has('no_hp'))
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $errEdit->first('no_hp') }}</div>
                    @endif
                </div>

                {{-- ALAMAT --}}
                <div class="mb-[25px]">
                    <label for="edit_cabang_alamat"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Alamat Lengkap <span class="text-[#fc544b]">*</span>
                    </label>

                    <textarea id="edit_cabang_alamat" name="alamat" rows="3" maxlength="1000" required
                        class="w-full rounded-[3px] border bg-[#fdfdff] px-[15px] py-2.5 text-sm text-[#495057] focus:outline-none {{ $errEdit->has('alamat') ? 'border-[#fc544b]' : 'border-[#e4e6fc] focus:border-[#95a0f4]' }}"></textarea>

                    @if ($errEdit->has('alamat'))
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $errEdit->first('alamat') }}</div>
                    @endif
                </div>

                {{-- STATUS CABANG --}}
                <div>
                    <label for="edit_cabang_status"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Status Cabang <span class="text-[#fc544b]">*</span>
                    </label>

                    <select id="edit_cabang_status" name="status" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none {{ $errEdit->has('status') ? 'border-[#fc544b]' : 'border-[#e4e6fc] focus:border-[#95a0f4]' }}">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>

                    @if ($errEdit->has('status'))
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $errEdit->first('status') }}</div>
                    @endif
                </div>

            </div>

            {{-- FOOTER MODAL --}}
            <div class="flex items-center justify-end gap-2 bg-[#f8f9fa] px-[25px] py-3">
                <button type="button" data-modal-close
                    class="inline-flex items-center rounded-[3px] bg-[#e3eaef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>

                <button type="submit" id="btnUpdateCabang"
                    class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-[.65]">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </dialog>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalTambah = document.getElementById('modalTambahCabang');
            const modalEdit = document.getElementById('modalEditCabang');
            const formTambah = document.getElementById('formTambahCabang');
            const formEdit = document.getElementById('formEditCabang');

            if (!modalTambah || !modalEdit || !formTambah || !formEdit) {
                console.error('Modal cabang tidak lengkap. Periksa id elemen pada halaman ini.');
                return;
            }

            const kolomEdit = {
                id: document.getElementById('edit_cabang_id'),
                nama: document.getElementById('edit_cabang_nama'),
                noHp: document.getElementById('edit_cabang_no_hp'),
                alamat: document.getElementById('edit_cabang_alamat'),
                status: document.getElementById('edit_cabang_status'),
            };

            // =========================================
            // FUNGSI BANTU
            // =========================================

            function bukaModal(dialog) {
                if (dialog && !dialog.open) {
                    dialog.showModal();
                }
            }

            // Isi modal edit dengan data cabang yang dipilih.
            function isiFormEdit(data) {
                kolomEdit.id.value = data.id || '';
                kolomEdit.nama.value = data.nama || '';
                kolomEdit.noHp.value = data.noHp || '';
                kolomEdit.alamat.value = data.alamat || '';
                kolomEdit.status.value = data.status || 'aktif';

                formEdit.setAttribute('action', data.url || '#');
            }

            // Cegah form terkirim berkali-kali, dan pulihkan tombol bila
            // halaman ditampilkan kembali dari cache (tombol Back browser).
            function kunciPengiriman(form, tombol, teksProses) {
                if (!tombol) return;

                const isiAwal = tombol.innerHTML;

                form.addEventListener('submit', function(event) {
                    if (event.defaultPrevented) return;

                    tombol.disabled = true;
                    tombol.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> ' + teksProses;
                });

                window.addEventListener('pageshow', function(event) {
                    if (event.persisted) {
                        tombol.disabled = false;
                        tombol.innerHTML = isiAwal;
                    }
                });
            }

            // =========================================
            // KLIK: BUKA/TUTUP MODAL, TOMBOL EDIT, TUTUP NOTIFIKASI
            // =========================================

            // Klik pada latar gelap hanya menutup modal jika tekan dan lepas mouse
            // sama-sama di latar. Tanpa ini, memilih teks di textarea lalu melepas
            // mouse di luar modal akan menutup modal dan membuang isian.
            let targetTekan = null;

            document.addEventListener('mousedown', function(event) {
                targetTekan = event.target;
            });

            document.addEventListener('click', function(event) {
                const buka = event.target.closest('[data-modal-open]');
                if (buka) {
                    bukaModal(document.getElementById(buka.dataset.modalOpen));
                    return;
                }

                const tutup = event.target.closest('[data-modal-close]');
                if (tutup) {
                    const dialog = tutup.closest('dialog');
                    if (dialog) dialog.close();
                    return;
                }

                const tutupNotifikasi = event.target.closest('[data-alert-close]');
                if (tutupNotifikasi) {
                    const notifikasi = tutupNotifikasi.closest('[role="alert"]');
                    if (notifikasi) notifikasi.remove();
                    return;
                }

                const tombolEdit = event.target.closest('.btn-edit-cabang');
                if (tombolEdit) {
                    isiFormEdit({
                        id: tombolEdit.dataset.id,
                        nama: tombolEdit.dataset.nama,
                        noHp: tombolEdit.dataset.noHp,
                        alamat: tombolEdit.dataset.alamat,
                        status: tombolEdit.dataset.status,
                        url: tombolEdit.dataset.url,
                    });
                    bukaModal(modalEdit);
                    return;
                }

                // Klik di latar gelap (target = elemen dialog itu sendiri).
                if (
                    event.target instanceof HTMLDialogElement &&
                    event.target.open &&
                    targetTekan === event.target
                ) {
                    event.target.close();
                }
            });

            // =========================================
            // PENGIRIMAN FORM
            // =========================================

            // Jangan kirim form edit jika URL update belum terisi.
            formEdit.addEventListener('submit', function(event) {
                const action = formEdit.getAttribute('action');

                if (!action || action === '#') {
                    event.preventDefault();
                    console.error('URL update cabang belum ditentukan.');
                }
            });

            kunciPengiriman(formTambah, document.getElementById('btnSimpanCabang'), 'Menyimpan...');
            kunciPengiriman(formEdit, document.getElementById('btnUpdateCabang'), 'Memperbarui...');

            // =========================================
            // BUKA KEMBALI MODAL JIKA VALIDASI GAGAL
            // =========================================

            @if ($editGagal)
                isiFormEdit({
                    id: @js(old('edit_cabang_id')),
                    nama: @js(old('nama', '')),
                    noHp: @js(old('no_hp', '')),
                    alamat: @js(old('alamat', '')),
                    status: @js(old('status', 'aktif')),
                    url: @js(route('cabang.update', old('edit_cabang_id'))),
                });
                bukaModal(modalEdit);
            @elseif ($errors->any())
                bukaModal(modalTambah);
            @endif
        });
    </script>

</x-admin-layout-stisla>
