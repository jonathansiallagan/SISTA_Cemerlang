<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div
        class="mb-[30px] flex flex-wrap items-center gap-x-4 gap-y-2 rounded-[3px] bg-white px-[30px] py-5 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
        <h1 class="text-2xl font-bold text-[#34395e]">Data Bahan Baku</h1>

        <ol class="ml-auto flex items-center text-[13px] text-[#6c757d]">
            <li class="font-semibold text-[#6777ef]">Persediaan</li>
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
                class="mb-2 inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
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
        <div class="overflow-hidden rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
            <div class="flex min-h-[70px] items-center bg-[#6777ef] px-[25px] py-2">
                <h4 class="text-base font-semibold leading-7 text-white">
                    <i class="fas fa-boxes mr-2"></i>
                    Daftar Bahan Baku
                </h4>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead>
                        <tr class="text-[10px] font-bold uppercase tracking-[1.3px] text-[#666]">
                            <th class="h-[45px] w-[5%] bg-black/[0.04] px-[15px] text-center">#</th>
                            <th class="h-[45px] w-[15%] bg-black/[0.04] px-[15px] text-left">Kode Bahan</th>
                            <th class="h-[45px] w-[25%] bg-black/[0.04] px-[15px] text-left">Nama Bahan</th>
                            <th class="h-[45px] w-[20%] bg-black/[0.04] px-[15px] text-left">Spesifikasi</th>
                            <th class="h-[45px] w-[10%] bg-black/[0.04] px-[15px] text-left">Satuan</th>
                            <th class="h-[45px] w-[12%] bg-black/[0.04] px-[15px] text-center">Status</th>
                            <th class="h-[45px] w-[13%] bg-black/[0.04] px-[15px] text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($bahanBakus as $bahan)
                            <tr class="odd:bg-black/[0.025]">
                                <td class="px-[15px] py-3 text-center align-middle text-[#6c757d]">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-[15px] py-3 align-middle">
                                    <strong class="font-bold text-[#6777ef]">
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

                                <td class="px-[15px] py-3 text-center align-middle">
                                    @if (strtolower($bahan->status) === 'aktif')
                                        <span
                                            class="rounded-full bg-[#54ca68] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="rounded-full bg-[#cdd3d8] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-[15px] py-3 text-center align-middle">
                                    <a href="{{ route('bahan-baku.edit', $bahan->id) }}" title="Edit Bahan Baku"
                                        class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#394eea]">
                                        <i class="fas fa-edit"></i>
                                    </a>
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
                    </tbody>
                </table>
            </div>

            <div class="border-t border-black/5 px-[25px] py-4 text-[#6c757d]">
                <small class="text-xs">
                    Total bahan baku:
                    <strong class="font-bold">{{ $bahanBakus->count() }}</strong>
                </small>
            </div>
        </div>

    </div>

    {{-- MODAL TAMBAH BAHAN BAKU --}}
    <dialog id="modalTambahBahan" aria-labelledby="judulModalTambahBahan"
        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-[800px] overflow-y-auto rounded-[3px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_4px_25px_rgba(0,0,0,0.2)] backdrop:bg-black/40">

        {{-- HEADER MODAL --}}
        <div class="flex items-center justify-between bg-[#6777ef] px-[25px] py-4">
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
                    <div
                        class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2.5 text-white">
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
                    <label for="spesifikasiBahan" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
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

                {{-- STATUS --}}
                <div>
                    <label for="statusBahan" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Status <span class="text-[#fc544b]">*</span>
                    </label>

                    <select name="status" id="statusBahan" required
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
                    class="inline-flex items-center rounded-[3px] bg-[#cdd3d8] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#bcc4cb]">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>

                <button type="submit" id="btnSimpanBahan"
                    class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-65">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Bahan Baku
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

                // Cegah pengiriman formulir berkali-kali.
                form.addEventListener('submit', function() {
                    if (tombolSimpan) {
                        tombolSimpan.disabled = true;
                        tombolSimpan.innerHTML =
                            '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                    }
                });

                // Tampilkan kembali modal jika validasi gagal.
                @if ($errors->any())
                    if (!modal.open) modal.showModal();
                @endif
            });
        </script>
    @endpush

</x-admin-layout-stisla>