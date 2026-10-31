<x-admin-layout-stisla>

    @php
        // Daftar satuan produk (dipakai oleh modal Tambah dan modal Edit).
        $daftarSatuan = [
            'pcs' => 'Pcs',
            'lembar' => 'Lembar',
            'm2' => 'Meter Persegi (m²)',
            'buku' => 'Buku',
            'unit' => 'Unit',
            'set' => 'Set',
        ];
    @endphp

    {{-- HEADER HALAMAN --}}
    <div
        class="mb-[30px] flex flex-wrap items-center gap-x-4 gap-y-2 rounded-[3px] bg-white px-[30px] py-5 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
        <h1 class="text-2xl font-bold text-[#34395e]">Data Produk &amp; Layanan</h1>

        <ol class="ml-auto flex items-center text-[13px] text-[#6c757d]">
            <li class="font-semibold text-[#6777ef]">Produk &amp; Layanan</li>
        </ol>
    </div>

    <div>

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="mb-4 flex flex-wrap items-center justify-between">
            <p class="mb-2 text-[#6c757d]">
                Kelola daftar produk dan layanan percetakan CV Cemerlang.
            </p>

            <button type="button" data-modal-open="modalTambahProduk"
                class="mb-2 inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
                <i class="fas fa-plus mr-1"></i>
                Tambah Produk
            </button>
        </div>

        {{-- NOTIFIKASI --}}
        @if (session('success'))
            <div role="alert"
                class="mb-4 flex items-center rounded-[3px] border-l-[3px] border-black/20 bg-[#54ca68] px-5 py-2 text-white">
                <i class="fas fa-check-circle mr-1"></i>
                <span class="min-w-0">{{ session('success') }}</span>

                <button type="button" aria-label="Tutup" onclick="this.closest('[role=alert]').remove()"
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div role="alert"
                class="mb-4 flex items-center rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2 text-white">
                <i class="fas fa-exclamation-circle mr-1"></i>
                <span class="min-w-0">{{ session('error') }}</span>

                <button type="button" aria-label="Tutup" onclick="this.closest('[role=alert]').remove()"
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- TABEL PRODUK --}}
        <x-card-table title="Data Riwayat Pengeluaran" icon="fa-receipt">

            {{-- HEADER TABEL --}}
            <x-slot:head>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-center">#</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-left">Nama Produk/Layanan</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-left">Satuan</th>
                <th class="h-[45px] bg-[#fdfdff] px-[15px] text-center">Aksi</th>
            </x-slot:head>

            {{-- ISI TABEL --}}
            @forelse ($produks as $produk)
                <tr class="border-b border-[#f2f2f2]">
                    {{-- NOMOR URUT --}}
                    <td class="px-[15px] py-3 text-center align-middle text-[#6c757d]">
                        {{ $loop->iteration }}
                    </td>

                    {{-- NAMA PRODUK --}}
                    <td class="px-[15px] py-3 align-middle font-bold">
                        {{ $produk->nama_produk }}
                    </td>

                    {{-- SATUAN --}}
                    <td class="px-[15px] py-3 align-middle">
                        {{ $produk->satuan }}
                    </td>

                    {{-- AKSI --}}
                    <td class="whitespace-nowrap px-[15px] py-3 text-center align-middle">
                        <button type="button" title="Edit Produk"
                            class="btn-edit-produk inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#394eea]"
                            data-id="{{ $produk->id }}" data-url="{{ route('produk.update', $produk->id) }}"
                            data-nama="{{ $produk->nama_produk }}" data-satuan="{{ $produk->satuan }}"
                            data-upah="{{ $produk->upah_operator ?? 0 }}"
                            data-komposisi="{{ $produk->komposisi->map(
                                    fn($item) => [
                                        'bahan_baku_id' => $item->bahan_baku_id,
                                        'jumlah_pemakaian' => $item->jumlah_pemakaian,
                                        'satuan_pemakaian' => $item->satuan_pemakaian,
                                    ],
                                )->values()->toJson(JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}">
                            <i class="fas fa-edit"></i>
                        </button>

                        <form action="{{ route('produk.destroy', $produk->id) }}" method="POST" class="inline"
                            onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                            @csrf
                            @method('DELETE')

                            <button type="button" title="Hapus Produk"
                                class="btn-hapus-produk inline-flex items-center rounded-[3px] bg-[#fc544b] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#fb160a]"
                                data-url="{{ route('produk.destroy', $produk->id) }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-[15px] py-6 text-center text-[#6c757d]">
                        <i class="fas fa-box-open text-[2em]"></i>
                        <p>
                            Belum ada data produk.
                        </p>
                        <small>Silakan tambahkan produk atau layanan terlebih dahulu.</small>
                    </td>
                </tr>
            @endforelse
        </x-card-table>
    </div>

    <div class="border-t border-black/5 px-[25px] py-4 text-[#6c757d]">
        <small>
            Total produk dan layanan:
            <strong>{{ $produks->count() }}</strong>
        </small>
    </div>

    {{-- MODAL TAMBAH PRODUK --}}
    <dialog id="modalTambahProduk" aria-labelledby="judulModalTambahProduk"
        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-[800px] overflow-hidden rounded-[3px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_10px_40px_rgba(0,0,0,0.25)] backdrop:bg-black/50 open:flex open:flex-col">

        <div class="flex shrink-0 items-start justify-between bg-[#6777ef] px-6 py-4 text-white">
            <div>
                <h5 id="judulModalTambahProduk" class="text-lg font-semibold text-white">
                    <i class="fas fa-box mr-2"></i>
                    Tambah Produk &amp; Layanan
                </h5>
                <small>Isi informasi produk dan komposisi bahan baku.</small>
            </div>

            <button type="button" data-modal-close aria-label="Tutup"
                class="-mt-1 text-3xl leading-none text-white/80 hover:text-white">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <form action="{{ route('produk.store') }}" method="POST" id="formTambahProduk"
            class="flex min-h-0 flex-1 flex-col">
            @csrf

            <div class="min-h-0 flex-1 overflow-y-auto p-6">

                <h6 class="mb-4 text-sm font-bold text-[#6777ef]">
                    Informasi Produk
                </h6>

                <div class="mb-4">
                    <label for="modal_nama_produk"
                        class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Nama Produk / Layanan <span class="text-[#fc544b]">*</span>
                    </label>
                    <input type="text" id="modal_nama_produk" name="nama_produk" value="{{ old('nama_produk') }}"
                        maxlength="255" placeholder="Contoh: Spanduk Flexi" required autofocus
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none @error('nama_produk') border-[#fc544b] @else border-[#e4e6fc] @enderror">
                    @error('nama_produk')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="modal_satuan_produk"
                        class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Satuan Produk <span class="text-[#fc544b]">*</span>
                    </label>
                    <select id="modal_satuan_produk" name="satuan" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none @error('satuan') border-[#fc544b] @else border-[#e4e6fc] @enderror">
                        <option value="">-- Pilih Satuan --</option>
                        @foreach ($daftarSatuan as $value => $label)
                            <option value="{{ $value }}" @selected(old('satuan') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('satuan')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="modal_upah_operator"
                        class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Upah Operator (Rp) <span class="text-[#fc544b]">*</span>
                    </label>
                    <input type="number" id="modal_upah_operator" name="upah_operator" min="0"
                        step="any" value="{{ old('upah_operator', 0) }}" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none @error('upah_operator') border-[#fc544b] @else border-[#e4e6fc] @enderror">
                    <small class="mt-1 block text-[#6c757d]">
                        Upah operator untuk satu satuan produk.
                    </small>
                    @error('upah_operator')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

                <hr class="my-4 border-t border-black/10">

                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h6 class="mb-1 text-sm font-bold text-[#6777ef]">
                            Komposisi Bahan Baku
                        </h6>
                        <small class="text-[#6c757d]">
                            Tambahkan bahan yang diperlukan untuk satu satuan produk.
                        </small>
                    </div>

                    <button type="button" id="modalTambahBarisBahan"
                        class="inline-flex shrink-0 items-center rounded-[3px] bg-[#54ca68] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#3fb556]">
                        <i class="fas fa-plus mr-1"></i> Tambah Bahan
                    </button>
                </div>

                <div id="modalDaftarBahan">
                    @php
                        $oldBahanIds = old('bahan_baku_id', ['']);
                        $oldJumlah = old('jumlah_pemakaian', ['']);
                        $oldSatuan = old('satuan_pemakaian', ['']);
                    @endphp

                    {{-- Class modal-* di bawah dipakai oleh JavaScript, jangan dihapus. --}}
                    @foreach ($oldBahanIds as $i => $oldBahanId)
                        <div class="modal-bahan-item mb-4 rounded border border-[#dee2e6] p-4">
                            <div class="mb-2 flex items-center justify-between">
                                <strong class="modal-nomor-bahan font-bold text-[#34395e]">
                                    Bahan Baku {{ $i + 1 }}
                                </strong>
                                <button type="button"
                                    class="modal-hapus-bahan inline-flex items-center rounded-[3px] border border-[#fc544b] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-[#fc544b] transition-colors hover:bg-[#fc544b] hover:text-white">
                                    <i class="fas fa-trash mr-1"></i> Hapus
                                </button>
                            </div>

                            <div class="mb-4">
                                <label class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                                    Nama Bahan Baku
                                </label>
                                <select name="bahan_baku_id[]" required
                                    class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                                    <option value="">-- Pilih Bahan --</option>
                                    @foreach ($bahanBakus as $bahan)
                                        <option value="{{ $bahan->id }}" @selected((string) $oldBahanId === (string) $bahan->id)>
                                            {{ $bahan->nama_bahan }}
                                            (Stok: {{ $bahan->stok }} {{ $bahan->satuan }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 gap-x-2.5 gap-y-4 md:grid-cols-2">
                                <div>
                                    <label
                                        class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                                        Jumlah Pemakaian
                                    </label>
                                    <input type="number" name="jumlah_pemakaian[]" min="0.0001" step="0.0001"
                                        value="{{ $oldJumlah[$i] ?? '' }}" placeholder="Contoh: 1 atau 0.03" required
                                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                                </div>

                                <div>
                                    <label
                                        class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                                        Satuan Pemakaian
                                    </label>
                                    <input type="text" name="satuan_pemakaian[]" maxlength="30"
                                        value="{{ $oldSatuan[$i] ?? '' }}" placeholder="Contoh: m²" required
                                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($errors->has('bahan_baku_id') || $errors->has('jumlah_pemakaian') || $errors->has('satuan_pemakaian'))
                    <div class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-4 text-white">
                        Periksa kembali data komposisi bahan baku.
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <div class="rounded-[3px] border border-[#e3eaef] bg-[#fefefe] px-5 py-4 text-[#6c757d]">
                    <i class="fas fa-info-circle mr-1 text-[#6777ef]"></i>
                    Jumlah pemakaian adalah kebutuhan bahan untuk satu satuan produk.
                </div>
            </div>

            <div class="flex shrink-0 items-center justify-end gap-2 border-t border-[#e3eaef] bg-[#f6f8fb] px-6 py-4">
                <button type="button" data-modal-close
                    class="inline-flex items-center rounded-[3px] bg-[#e3eaef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                    Batal
                </button>
                <button type="submit" id="modalSimpanProduk"
                    class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-60">
                    <i class="fas fa-save mr-1"></i> Simpan Produk
                </button>
            </div>
        </form>
    </dialog>

    {{-- MODAL EDIT PRODUK --}}
    <dialog id="modalEditProduk" aria-labelledby="judulModalEditProduk"
        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-[800px] overflow-hidden rounded-[3px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_10px_40px_rgba(0,0,0,0.25)] backdrop:bg-black/50 open:flex open:flex-col">

        <div class="flex shrink-0 items-start justify-between bg-[#6777ef] px-6 py-4 text-white">
            <div>
                <h5 id="judulModalEditProduk" class="text-lg font-semibold text-white">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Produk &amp; Layanan
                </h5>
                <small>Perbarui informasi produk dan komposisi bahan baku.</small>
            </div>

            <button type="button" data-modal-close aria-label="Tutup"
                class="-mt-1 text-3xl leading-none text-white/80 hover:text-white">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <form action="#" method="POST" id="formEditProduk" class="flex min-h-0 flex-1 flex-col">
            @csrf
            @method('PUT')
            <input type="hidden" name="edit_produk_id" id="edit_produk_id" value="">

            <div class="min-h-0 flex-1 overflow-y-auto p-6">

                @if ($errors->any() && old('edit_produk_id'))
                    <div class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-4 text-white">
                        <strong>Periksa kembali data yang diisi.</strong>
                        <ul class="mt-2 list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h6 class="mb-4 text-sm font-bold text-[#6777ef]">
                    Informasi Produk
                </h6>

                <div class="mb-4">
                    <label for="edit_nama_produk"
                        class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Nama Produk / Layanan <span class="text-[#fc544b]">*</span>
                    </label>
                    <input type="text" id="edit_nama_produk" name="nama_produk" maxlength="255"
                        placeholder="Contoh: Spanduk Flexi" required
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                </div>

                <div class="mb-4">
                    <label for="edit_satuan_produk"
                        class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Satuan Produk <span class="text-[#fc544b]">*</span>
                    </label>
                    <select id="edit_satuan_produk" name="satuan" required
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                        <option value="">-- Pilih Satuan --</option>
                        @foreach ($daftarSatuan as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="edit_upah_operator"
                        class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Upah Operator (Rp)
                    </label>
                    <input type="number" id="edit_upah_operator" name="upah_operator" min="0"
                        step="any" value="0"
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                    <small class="mt-1 block text-[#6c757d]">
                        Upah operator untuk satu satuan produk.
                    </small>
                </div>

                <hr class="my-4 border-t border-black/10">

                {{-- KOMPOSISI BAHAN BAKU --}}
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h6 class="mb-1 text-sm font-bold text-[#6777ef]">
                            Komposisi Bahan Baku
                        </h6>
                        <small class="text-[#6c757d]">
                            Atur kebutuhan bahan untuk satu satuan produk.
                        </small>
                    </div>

                    <button type="button" id="editTambahBarisBahan"
                        class="inline-flex shrink-0 items-center rounded-[3px] bg-[#54ca68] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#3fb556]">
                        <i class="fas fa-plus mr-1"></i> Tambah Bahan
                    </button>
                </div>

                {{-- Baris bahan baku diisi melalui JavaScript --}}
                <div id="editDaftarBahan"></div>

                {{-- TEMPLATE BARIS BAHAN BAKU --}}
                {{-- Class edit-* di bawah dipakai oleh JavaScript, jangan dihapus. --}}
                <template id="templateEditBahan">
                    <div class="edit-bahan-item mb-4 rounded border border-[#dee2e6] p-4">
                        <div class="mb-2 flex items-center justify-between">
                            <strong class="edit-nomor-bahan font-bold text-[#34395e]">
                                Bahan Baku 1
                            </strong>
                            <button type="button"
                                class="edit-hapus-bahan inline-flex items-center rounded-[3px] border border-[#fc544b] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-[#fc544b] transition-colors hover:bg-[#fc544b] hover:text-white">
                                <i class="fas fa-trash mr-1"></i> Hapus
                            </button>
                        </div>

                        <div class="mb-4">
                            <label class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                                Nama Bahan Baku
                            </label>
                            <select name="bahan_baku_id[]" required
                                class="edit-pilih-bahan h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                                <option value="">-- Pilih Bahan --</option>
                                @foreach ($bahanBakus as $bahan)
                                    <option value="{{ $bahan->id }}" data-satuan="{{ $bahan->satuan }}">
                                        {{ $bahan->nama_bahan }}
                                        (Stok: {{ $bahan->stok }} {{ $bahan->satuan }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 gap-x-2.5 gap-y-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                                    Jumlah Pemakaian
                                </label>
                                <input type="number" name="jumlah_pemakaian[]" min="0.0001" step="0.0001"
                                    placeholder="Contoh: 1 atau 0.03" required
                                    class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                            </div>

                            <div>
                                <label class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                                    Satuan Pemakaian
                                </label>
                                <input type="text" name="satuan_pemakaian[]" maxlength="30"
                                    placeholder="Contoh: m²" required
                                    class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                            </div>
                        </div>
                    </div>
                </template>

                <div class="rounded-[3px] border border-[#e3eaef] bg-[#fefefe] px-5 py-4 text-[#6c757d]">
                    <i class="fas fa-info-circle mr-1 text-[#6777ef]"></i>
                    Jumlah pemakaian adalah kebutuhan bahan untuk satu satuan produk.
                </div>
            </div>

            <div class="flex shrink-0 items-center justify-end gap-2 border-t border-[#e3eaef] bg-[#f6f8fb] px-6 py-4">
                <button type="button" data-modal-close
                    class="inline-flex items-center rounded-[3px] bg-[#e3eaef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>
                <button type="submit" id="btnUpdateProduk"
                    class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-60">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </dialog>

    {{-- MODAL KONFIRMASI HAPUS --}}
    <dialog id="modalHapusProduk"
        class="m-auto max-w-[400px] rounded-[3px] bg-white p-0 shadow-[0_10px_40px_rgba(0,0,0,0.25)] backdrop:bg-black/50 open:flex open:flex-col">
        <div class="p-6 text-center">
            {{-- Ikon Peringatan --}}
            <div
                class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-[#fc544b]/10 text-[#fc544b]">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>

            <h5 class="mb-2 text-lg font-bold text-[#34395e]">Konfirmasi Hapus</h5>
            <p class="mb-6 text-sm text-[#6c757d]">
                Apakah Anda yakin ingin menghapus produk ini? <br>Data akan diarsipkan dan tidak muncul lagi di sistem.
            </p>

            <form id="formHapusProduk" method="POST" action="">
                @csrf
                @method('DELETE')

                <div class="flex justify-center gap-3">
                    <button type="button" data-modal-close
                        class="inline-flex items-center rounded-[3px] bg-[#e3eaef] px-4 py-2 text-sm font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                        Batal
                    </button>
                    <button type="submit" id="btnKonfirmasiHapus"
                        class="inline-flex items-center rounded-[3px] bg-[#fc544b] px-4 py-2 text-sm font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#fb160a] disabled:cursor-not-allowed disabled:opacity-60">
                        Ya, Hapus Produk
                    </button>
                </div>
            </form>
        </div>
    </dialog>

    @php
        // Dipakai untuk membuka kembali modal edit (lengkap dengan isinya) jika validasi gagal.
        $oldEditKomposisi = collect(old('bahan_baku_id', []))
            ->map(
                fn($bahanId, $i) => [
                    'bahan_baku_id' => $bahanId,
                    'jumlah_pemakaian' => old('jumlah_pemakaian.' . $i),
                    'satuan_pemakaian' => old('satuan_pemakaian.' . $i),
                ],
            )
            ->values();
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalTambah = document.getElementById('modalTambahProduk');
            const modalEdit = document.getElementById('modalEditProduk');

            if (!modalTambah || !modalEdit) return;

            // =========================================
            // FUNGSI BANTU
            // =========================================

            function kosongkanIsian(item) {
                item.querySelectorAll('select, input').forEach(function(el) {
                    el.value = '';
                });
            }

            function perbaruiNomor(daftar, selektorItem, selektorNomor) {
                daftar.querySelectorAll(selektorItem).forEach(function(item, index) {
                    const nomor = item.querySelector(selektorNomor);

                    if (nomor) {
                        nomor.textContent = 'Bahan Baku ' + (index + 1);
                    }
                });
            }

            function kunciTombolSimpan(form, tombol) {
                if (!form || !tombol) return;

                form.addEventListener('submit', function() {
                    tombol.disabled = true;
                    tombol.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                });
            }

            // Tombol tutup dan klik pada latar gelap menutup modal (elemen dialog bawaan browser).
            [modalTambah, modalEdit].forEach(function(modal) {
                modal.querySelectorAll('[data-modal-close]').forEach(function(tombol) {
                    tombol.addEventListener('click', function() {
                        modal.close();
                    });
                });

                modal.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        modal.close();
                    }
                });
            });

            // =========================================
            // MODAL TAMBAH PRODUK
            // =========================================

            const daftarTambah = document.getElementById('modalDaftarBahan');
            const tombolTambahBahan = document.getElementById('modalTambahBarisBahan');

            document.querySelectorAll('[data-modal-open="modalTambahProduk"]').forEach(function(tombol) {
                tombol.addEventListener('click', function() {
                    modalTambah.showModal();
                });
            });

            if (daftarTambah && tombolTambahBahan) {
                tombolTambahBahan.addEventListener('click', function() {
                    const pertama = daftarTambah.querySelector('.modal-bahan-item');

                    if (!pertama) return;

                    const baru = pertama.cloneNode(true);

                    kosongkanIsian(baru);
                    daftarTambah.appendChild(baru);
                    perbaruiNomor(daftarTambah, '.modal-bahan-item', '.modal-nomor-bahan');
                });

                daftarTambah.addEventListener('click', function(event) {
                    const tombol = event.target.closest('.modal-hapus-bahan');

                    if (!tombol) return;

                    const item = tombol.closest('.modal-bahan-item');

                    // Sisakan satu baris; jika tinggal satu, cukup dikosongkan.
                    if (daftarTambah.querySelectorAll('.modal-bahan-item').length === 1) {
                        kosongkanIsian(item);
                        return;
                    }

                    item.remove();
                    perbaruiNomor(daftarTambah, '.modal-bahan-item', '.modal-nomor-bahan');
                });
            }

            kunciTombolSimpan(
                document.getElementById('formTambahProduk'),
                document.getElementById('modalSimpanProduk')
            );

            // =========================================
            // MODAL EDIT PRODUK
            // =========================================

            const formEdit = document.getElementById('formEditProduk');
            const daftarEdit = document.getElementById('editDaftarBahan');
            const templateEdit = document.getElementById('templateEditBahan');
            const tombolTambahEdit = document.getElementById('editTambahBarisBahan');

            function tambahBarisEdit(data) {
                data = data || {};

                const fragmen = templateEdit.content.cloneNode(true);
                const item = fragmen.querySelector('.edit-bahan-item');

                item.querySelector('[name="bahan_baku_id[]"]').value = data.bahan_baku_id ?? '';
                item.querySelector('[name="jumlah_pemakaian[]"]').value = data.jumlah_pemakaian ?? '';
                item.querySelector('[name="satuan_pemakaian[]"]').value = data.satuan_pemakaian ?? '';

                daftarEdit.appendChild(fragmen);
                perbaruiNomor(daftarEdit, '.edit-bahan-item', '.edit-nomor-bahan');
            }

            function isiFormEdit(data) {
                formEdit.action = data.url;

                document.getElementById('edit_produk_id').value = data.id;
                document.getElementById('edit_nama_produk').value = data.nama || '';
                document.getElementById('edit_satuan_produk').value = data.satuan || '';
                document.getElementById('edit_upah_operator').value = data.upah || 0;

                // Kosongkan komposisi sebelumnya, lalu isi ulang.
                daftarEdit.innerHTML = '';

                if (data.komposisi && data.komposisi.length > 0) {
                    data.komposisi.forEach(function(bahan) {
                        tambahBarisEdit(bahan);
                    });
                } else {
                    tambahBarisEdit();
                }
            }

            tombolTambahEdit.addEventListener('click', function() {
                tambahBarisEdit();
            });

            daftarEdit.addEventListener('click', function(event) {
                const tombol = event.target.closest('.edit-hapus-bahan');

                if (!tombol) return;

                const item = tombol.closest('.edit-bahan-item');

                if (daftarEdit.querySelectorAll('.edit-bahan-item').length === 1) {
                    kosongkanIsian(item);
                    return;
                }

                item.remove();
                perbaruiNomor(daftarEdit, '.edit-bahan-item', '.edit-nomor-bahan');
            });

            // Isi satuan pemakaian otomatis dari satuan bahan jika masih kosong.
            daftarEdit.addEventListener('change', function(event) {
                if (!event.target.matches('.edit-pilih-bahan')) return;

                const item = event.target.closest('.edit-bahan-item');
                const opsi = event.target.selectedOptions[0];
                const satuan = item.querySelector('[name="satuan_pemakaian[]"]');

                if (opsi && opsi.dataset.satuan && satuan && !satuan.value) {
                    satuan.value = opsi.dataset.satuan;
                }
            });

            // Klik tombol Edit pada tabel.
            document.querySelectorAll('.btn-edit-produk').forEach(function(tombol) {
                tombol.addEventListener('click', function() {
                    let komposisi = [];

                    try {
                        komposisi = JSON.parse(this.dataset.komposisi || '[]');
                    } catch (error) {
                        console.error('Data komposisi produk tidak valid:', error);
                    }

                    isiFormEdit({
                        id: this.dataset.id,
                        url: this.dataset.url,
                        nama: this.dataset.nama,
                        satuan: this.dataset.satuan,
                        upah: this.dataset.upah,
                        komposisi: komposisi,
                    });

                    modalEdit.showModal();
                });
            });

            kunciTombolSimpan(formEdit, document.getElementById('btnUpdateProduk'));

            // =========================================
            // MODAL KONFIRMASI HAPUS
            // =========================================
            const modalHapus = document.getElementById('modalHapusProduk');
            const formHapus = document.getElementById('formHapusProduk');

            document.querySelectorAll('.btn-hapus-produk').forEach(function(tombol) {
                tombol.addEventListener('click', function() {
                    formHapus.action = this.dataset.url;

                    modalHapus.showModal();
                });
            });

            kunciTombolSimpan(formHapus, document.getElementById('btnKonfirmasiHapus'));

            modalHapus.querySelectorAll('[data-modal-close]').forEach(function(tombol) {
                tombol.addEventListener('click', function() {
                    modalHapus.close();
                });
            });
            modalHapus.addEventListener('click', function(event) {
                if (event.target === modalHapus) modalHapus.close();
            });

            // =========================================
            // BUKA KEMBALI MODAL JIKA VALIDASI GAGAL
            // =========================================

            @if ($errors->any())
                @if (old('edit_produk_id'))
                    isiFormEdit({
                        id: @js(old('edit_produk_id')),
                        url: @js(route('produk.update', old('edit_produk_id'))),
                        nama: @js(old('nama_produk')),
                        satuan: @js(old('satuan')),
                        upah: @js(old('upah_operator', 0)),
                        komposisi: @js($oldEditKomposisi),
                    });
                    modalEdit.showModal();
                @else
                    modalTambah.showModal();
                @endif
            @endif
        });
    </script>

</x-admin-layout-stisla>
