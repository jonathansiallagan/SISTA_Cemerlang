<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div
        class="mb-[30px] flex flex-wrap items-center gap-x-4 gap-y-2 rounded-[3px] bg-white px-[30px] py-5 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
        <h1 class="text-2xl font-bold text-[#34395e]">Data Bahan Baku</h1>

        <ol class="ml-auto flex items-center text-[13px] text-[#6c757d]">
            <li class="font-semibold text-[#041C88]">Persediaan</li>
            <li class="before:px-2 before:content-['/']">Bahan Baku</li>
        </ol>
    </div>

    <div>

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <p class="mb-2 text-[#6c757d]">
                Kelola daftar bahan baku yang digunakan dalam proses produksi percetakan CV Cemerlang.
            </p>

            <button type="button" data-modal-open="modalTambahBahan"
                class="mb-2 inline-flex items-center rounded-[3px] bg-[#041C88] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
                <i class="fas fa-plus mr-1"></i>
                Tambah Bahan Baku
            </button>
        </div>

        {{-- NOTIFIKASI SUKSES --}}
        @if (session('success'))
            <div role="alert"
                class="mb-4 flex items-center rounded-[3px] border-l-[3px] border-black/20 bg-[#54ca68] px-5 py-2 text-white">
                <span class="min-w-0">
                    <i class="fas fa-check-circle mr-1"></i>
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
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    {{ session('error') }}
                </span>
                <button type="button" aria-label="Tutup" onclick="this.closest('[role=alert]').remove()"
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">&times;</button>
            </div>
        @endif

        {{-- TABEL BAHAN BAKU --}}
        <x-card-table title="Daftar Bahan Baku" icon="fa-receipt">

            {{-- HEADER TABEL --}}
            <x-slot:head>
                <th class="h-[45px] w-[5%] bg-[#fdfdff] px-[15px] text-center">#</th>
                <th class="h-[45px] w-[15%] bg-[#fdfdff] px-[15px] text-left">Kode Bahan</th>
                <th class="h-[45px] w-[25%] bg-[#fdfdff] px-[15px] text-left">Nama Bahan</th>
                <th class="h-[45px] w-[20%] bg-[#fdfdff] px-[15px] text-left">Spesifikasi</th>
                <th class="h-[45px] w-[10%] bg-[#fdfdff] px-[15px] text-left">Satuan</th>
                <th class="h-[45px] w-[13%] bg-[#fdfdff] px-[15px] text-center">Aksi</th>
            </x-slot:head>

            {{-- ISI TABEL --}}
            @forelse ($bahanBakus as $bahan)
                <tr class="odd:bg-black/[0.025]">
                    <td class="px-[15px] py-3 text-center align-middle text-[#6c757d]">
                        {{ $loop->iteration }}
                    </td>

                    <td class="px-[15px] py-3 align-middle">
                        <strong class="font-bold text-[#041C88]">
                            {{ $bahan->kode_bahan }}
                        </strong>
                    </td>

                    <td class="px-[15px] py-3 align-middle font-bold">
                        {{ $bahan->nama_bahan }}
                    </td>

                    <td class="px-[15px] py-3 align-middle">
                        {{ $bahan->spesifikasi ?: '-' }}
                    </td>

                    <td class="px-[15px] py-3 align-middle">
                        {{ $bahan->satuan }}
                    </td>

                    <td class="whitespace-nowrap px-[15px] py-3 text-center align-middle">
                        <button type="button" title="Edit Bahan Baku"
                            class="btn-edit-bahan inline-flex items-center rounded-[3px] bg-[#041C88] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#394eea]"
                            data-id="{{ $bahan->id }}" data-kode="{{ $bahan->kode_bahan }}"
                            data-nama="{{ $bahan->nama_bahan }}" data-spesifikasi="{{ $bahan->spesifikasi }}"
                            data-satuan="{{ $bahan->satuan }}"
                            data-url="{{ route('bahan-baku.update', $bahan->id) }}">
                            <i class="fas fa-edit"></i>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-[15px] py-6 text-center text-[#6c757d]">
                        <i class="fas fa-box-open mb-2 text-[2em]"></i>
                        <p>
                            Belum ada data bahan baku.
                        </p>
                        <small class="text-xs">
                            Silakan tambahkan bahan baku terlebih dahulu.
                        </small>
                    </td>
                </tr>
            @endforelse
        </x-card-table>
    </div>

    <div class="border-t border-black/5 px-[25px] py-4 text-[#6c757d]">
        <small class="text-xs">
            Total bahan baku:
            <strong class="font-bold">{{ $bahanBakus->count() }}</strong>
        </small>
    </div>

    {{-- MODAL TAMBAH BAHAN BAKU --}}
    <dialog id="modalTambahBahan" aria-labelledby="judulModalTambahBahan"
        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-[800px] overflow-y-auto rounded-[3px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_4px_25px_rgba(0,0,0,0.2)] backdrop:bg-black/40">

        {{-- HEADER MODAL --}}
        <div class="flex items-center justify-between bg-[#041C88] px-[25px] py-4">
            <h5 id="judulModalTambahBahan" class="text-lg font-semibold text-white">
                <i class="fas fa-boxes mr-2"></i>
                Tambah Bahan Baku
            </h5>

            <button type="button" data-modal-close aria-label="Tutup"
                class="text-2xl leading-none text-white/80 hover:text-white">&times;</button>
        </div>

        {{-- FORM --}}
        <form id="formTambahBahan" action="{{ route('bahan-baku.store') }}" method="POST">
            @csrf

            <div class="p-[25px]">

                @if ($errors->any())
                    <div class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2.5 text-white">
                        <strong class="font-bold">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Periksa kembali data berikut:
                        </strong>

                        <ul class="mb-0 mt-2 list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#3abaf4] px-5 py-2.5 text-white">
                    <i class="fas fa-info-circle mr-1"></i>
                    Daftarkan bahan baku terlebih dahulu sebelum mencatat pembelian.
                    Stok dan harga rata-rata akan diperbarui dari transaksi pembelian.
                </div>

                {{-- KODE BAHAN --}}
                <div class="mb-[25px]">
                    <label for="kodeBahanBaru" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Kode Bahan
                    </label>

                    <input type="text" id="kodeBahanBaru" value="{{ $kodeBahan }}" readonly
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#e9ecef] px-[15px] text-sm text-[#495057] focus:outline-none">

                    <small class="mt-1 block text-xs text-[#6c757d]">
                        Kode bahan dibuat otomatis oleh sistem.
                    </small>
                </div>

                {{-- NAMA BAHAN --}}
                <div class="mb-[25px]">
                    <label for="namaBahanBaru" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Nama Bahan <span class="text-[#fc544b]">*</span>
                    </label>

                    <input type="text" name="nama_bahan" id="namaBahanBaru" value="{{ old('nama_bahan') }}"
                        placeholder="Contoh: Kertas Art Paper" maxlength="255" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('nama_bahan') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">

                    @error('nama_bahan')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

                {{-- SPESIFIKASI --}}
                <div class="mb-[25px]">
                    <label for="spesifikasiBahan"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Spesifikasi
                    </label>

                    <input type="text" name="spesifikasi" id="spesifikasiBahan" value="{{ old('spesifikasi') }}"
                        placeholder="Contoh: 260 gsm, ukuran A3" maxlength="255"
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('spesifikasi') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">

                    @error('spesifikasi')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

                {{-- SATUAN --}}
                <div class="mb-[25px]">
                    <label for="satuanBahan" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Satuan <span class="text-[#fc544b]">*</span>
                    </label>

                    <select name="satuan" id="satuanBahan" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('satuan') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">
                        <option value="">-- Pilih Satuan --</option>

                        @foreach (['lembar', 'rim', 'pcs', 'roll', 'meter', 'm2', 'kg', 'liter', 'botol', 'pak'] as $satuan)
                            <option value="{{ $satuan }}" {{ old('satuan') === $satuan ? 'selected' : '' }}>
                                {{ ucfirst($satuan) }}
                            </option>
                        @endforeach
                    </select>

                    @error('satuan')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- FOOTER MODAL --}}
            <div class="flex items-center justify-end gap-2 bg-[#f8f9fa] px-[25px] py-3">
                <button type="button" data-modal-close
                    class="inline-flex items-center rounded-[3px] border border-red-600 bg-red-600 px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:border-red-700 hover:bg-red-700">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>

                <button type="button" id="btnSimpanBahan"
                    class="inline-flex items-center rounded-[3px] border border-[#47c363] bg-[#47c363] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:border-green-600 hover:bg-green-600"
                    data-confirm-form="formTambahBahan" data-confirm-theme="success"
                    data-confirm-title="Simpan Bahan Baku" data-confirm-message="Apakah data bahan baku sudah sesuai?"
                    data-confirm-action="Simpan Data">
                    <i class="fas fa-save mr-1"></i> Simpan Bahan Baku
                </button>
            </div>

        </form>
    </dialog>

    {{-- MODAL EDIT BAHAN BAKU REFACTOR (TAILWIND MURNI) --}}
    <dialog id="modalEditBahan" aria-labelledby="judulModalEditBahan"
        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-[800px] overflow-y-auto rounded-[3px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_4px_25px_rgba(0,0,0,0.2)] backdrop:bg-black/40">

        {{-- HEADER MODAL EDIT --}}
        <div class="flex items-center justify-between bg-[#041C88] px-[25px] py-4">
            <h5 id="judulModalEditBahan" class="text-lg font-semibold text-white">
                <i class="fas fa-edit mr-2"></i>
                Edit Bahan Baku
            </h5>

            <button type="button" data-modal-close aria-label="Tutup"
                class="text-2xl leading-none text-white/80 hover:text-white">&times;</button>
        </div>

        {{-- FORM EDIT --}}
        <form id="formEditBahan" method="POST" action="#">
            @csrf
            @method('PUT')

            <input type="hidden" name="edit_bahan_id" id="editBahanId">

            <div class="p-[25px]">

                {{-- KODE BAHAN --}}
                <div class="mb-[25px]">
                    <label for="editKodeBahan"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Kode Bahan
                    </label>

                    <input type="text" name="kode_bahan" id="editKodeBahan" readonly required
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#e9ecef] px-[15px] text-sm text-[#495057] focus:outline-none">

                    <small class="mt-1 block text-xs text-[#6c757d]">
                        Kode bahan dibuat otomatis oleh sistem.
                    </small>
                </div>

                {{-- NAMA BAHAN --}}
                <div class="mb-[25px]">
                    <label for="editNamaBahan"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Nama Bahan <span class="text-[#fc544b]">*</span>
                    </label>

                    <input type="text" name="nama_bahan" id="editNamaBahan" maxlength="255" required
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                </div>

                {{-- SPESIFIKASI --}}
                <div class="mb-[25px]">
                    <label for="editSpesifikasiBahan"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Spesifikasi
                    </label>

                    <input type="text" name="spesifikasi" id="editSpesifikasiBahan" maxlength="255"
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                </div>

                {{-- SATUAN --}}
                <div class="mb-[25px]">
                    <label for="editSatuanBahan"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Satuan <span class="text-[#fc544b]">*</span>
                    </label>

                    <select name="satuan" id="editSatuanBahan" required
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                        <option value="">-- Pilih Satuan --</option>
                        @foreach (['lembar', 'rim', 'pcs', 'roll', 'meter', 'm2', 'kg', 'liter', 'botol', 'pak'] as $satuan)
                            <option value="{{ $satuan }}">
                                {{ ucfirst($satuan) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- FOOTER MODAL EDIT --}}
            <div class="flex items-center justify-end gap-2 bg-[#f8f9fa] px-[25px] py-3">
                <button type="button" data-modal-close
                    class="inline-flex items-center rounded-[3px] border border-red-600 bg-red-600 px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:border-red-700 hover:bg-red-700">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>

                <button type="button" id="btnUpdateBahan"
                    class="inline-flex items-center rounded-[3px] border border-[#47c363] bg-[#47c363] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:border-green-600 hover:bg-green-600"
                    data-confirm-form="formEditBahan" data-confirm-theme="primary"
                    data-confirm-title="Update Bahan Baku"
                    data-confirm-message="Simpan perubahan untuk bahan baku ini?"
                    data-confirm-action="Simpan Perubahan">
                    <i class="fas fa-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>

    </dialog>

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

                    if (e.target instanceof HTMLDialogElement && e.target.open) {
                        e.target.close();
                    }
                });
            })();

            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('modalTambahBahan');
                const form = document.getElementById('formTambahBahan');
                const tombolSimpan = document.getElementById('btnSimpanBahan');

                if (!modal || !form) {
                    return;
                }

                // Tampilkan kembali modal jika validasi gagal.
                @if ($errors->any())
                    if (!modal.open) modal.showModal();
                @endif
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('modalEditBahan');
                const form = document.getElementById('formEditBahan');
                const tombolUpdate = document.getElementById('btnUpdateBahan');

                if (!modal || !form) {
                    return;
                }

                const idInput = document.getElementById('editBahanId');
                const kodeInput = document.getElementById('editKodeBahan');
                const namaInput = document.getElementById('editNamaBahan');
                const spesifikasiInput = document.getElementById('editSpesifikasiBahan');
                const satuanInput = document.getElementById('editSatuanBahan');

                document.querySelectorAll('.btn-edit-bahan').forEach(function(tombol) {
                    tombol.addEventListener('click', function() {
                        idInput.value = this.dataset.id || '';
                        kodeInput.value = this.dataset.kode || '';
                        namaInput.value = this.dataset.nama || '';
                        spesifikasiInput.value = this.dataset.spesifikasi || '';
                        satuanInput.value = this.dataset.satuan || '';

                        form.action = this.dataset.url || '';

                        tombolUpdate.disabled = false;
                        tombolUpdate.innerHTML =
                            '<i class="fas fa-save mr-1"></i> Simpan Perubahan';

                        // Membuka modal menggunakan native API dialog
                        modal.showModal();
                    });
                });

                // Buka kembali modal Edit jika validasi update gagal.
                const oldEditId = @json(old('edit_bahan_id'));

                if (oldEditId) {
                    const tombolLama = Array.from(
                        document.querySelectorAll('.btn-edit-bahan')
                    ).find(function(tombol) {
                        return String(tombol.dataset.id) === String(oldEditId);
                    });

                    if (tombolLama) {
                        idInput.value = oldEditId;
                        kodeInput.value = @json(old('kode_bahan', ''));
                        namaInput.value = @json(old('nama_bahan', ''));
                        spesifikasiInput.value = @json(old('spesifikasi', ''));
                        satuanInput.value = @json(old('satuan', ''));
                        form.action = tombolLama.dataset.url || '';

                        // Membuka modal menggunakan native API dialog
                        modal.showModal();
                    }
                }
            });
        </script>
    @endpush
</x-admin-layout-stisla>
