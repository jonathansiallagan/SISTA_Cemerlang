<x-admin-layout-stisla>
    {{-- HEADER HALAMAN --}}
    <div
        class="mb-[30px] flex flex-wrap items-center gap-x-4 gap-y-2 rounded-[3px] bg-white px-[30px] py-5 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
        <h1 class="text-2xl font-bold text-[#34395e]">Riwayat Pengeluaran</h1>

        <ol class="ml-auto flex items-center text-[13px] text-[#6c757d]">
            <li>
                <a href="{{ route('pengeluaran.index') }}" class="hover:text-[#6777ef]">Pengeluaran</a>
            </li>
            <li
                class="font-semibold text-[#6777ef] before:px-2 before:font-normal before:text-[#6c757d] before:content-['/']">
                Riwayat Pengeluaran
            </li>
        </ol>
    </div>

    <div>

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <p class="text-[#6c757d]">
                Riwayat dan rincian pengeluaran operasional perusahaan.
            </p>

            <button type="button" data-modal-open="modalTambahPengeluaran"
                class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
                <i class="fas fa-plus mr-1"></i>
                Tambah Pengeluaran
            </button>
        </div>

        {{-- NOTIFIKASI SUKSES --}}
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

        {{-- NOTIFIKASI ERROR --}}
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

        {{-- FILTER PENGELUARAN --}}
        <div class="mb-4 rounded-[3px] bg-white p-4 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
            <form action="{{ route('pengeluaran.index') }}" method="GET"
                class="grid grid-cols-1 items-end gap-x-2.5 gap-y-2 md:grid-cols-2 lg:grid-cols-4">

                <div>
                    <label for="start_date" class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Dari Tanggal
                    </label>

                    <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" required
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                </div>

                <div>
                    <label for="end_date" class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Sampai Tanggal
                    </label>

                    <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                        min="{{ $startDate }}" required
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                </div>

                @if (Auth::user()->role === 'Admin Pusat')
                    <div>
                        <label for="filter_cabang_id"
                            class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Cabang
                        </label>

                        <select id="filter_cabang_id" name="cabang_id"
                            class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                            <option value="">Semua Cabang</option>

                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->id }}" @selected((string) request('cabang_id') === (string) $cabang->id)>
                                    {{ $cabang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div>
                    <div class="flex flex-wrap items-center">
                        <button type="submit"
                            class="mb-1 mr-2 inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
                            <i class="fas fa-filter mr-1"></i>
                            Filter
                        </button>

                        <a href="{{ route('pengeluaran.index') }}"
                            class="mb-1 inline-flex items-center rounded-[3px] bg-[#e3eaef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                            <i class="fas fa-redo mr-1"></i>
                            Reset
                        </a>
                    </div>
                </div>

            </form>
        </div>

        {{-- TABEL RIWAYAT PENGELUARAN --}}
        <div class="overflow-hidden rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
            <div class="flex min-h-[70px] items-center bg-[#6777ef] px-[25px] py-2">
                <h4 class="text-base font-semibold leading-7 text-white">
                    <i class="fas fa-receipt mr-2"></i>
                    Data Riwayat Pengeluaran
                </h4>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead>
                        <tr class="text-[10px] font-bold uppercase tracking-[1.3px] text-[#666]">
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-center">#</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">No Referensi</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Tanggal</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Cabang</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Keterangan</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-right">Total Pengeluaran</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($pengeluarans as $pengeluaran)
                            <tr class="border-b border-[#f2f2f2]">
                                <td class="px-[15px] py-3 text-center text-[#6c757d]">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-[15px] py-3 font-bold text-[#6777ef]">
                                    {{ $pengeluaran->no_referensi }}
                                </td>

                                <td class="whitespace-nowrap px-[15px] py-3">
                                    {{ $pengeluaran->tanggal->format('d M Y') }}
                                </td>

                                <td class="px-[15px] py-3">
                                    {{ $pengeluaran->cabang?->nama ?? '-' }}
                                </td>

                                <td class="px-[15px] py-3">
                                    {{ $pengeluaran->keterangan ?: '-' }}
                                </td>

                                <td class="whitespace-nowrap px-[15px] py-3 text-right font-bold">
                                    Rp {{ number_format((float) $pengeluaran->total_pengeluaran, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-[15px] py-6 text-center text-[#6c757d]">
                                    <i class="fas fa-receipt text-[2em]"></i>

                                    <p class="mb-1 font-bold">
                                        Belum ada data pengeluaran
                                    </p>

                                    <p>
                                        Klik tombol Tambah Pengeluaran
                                        untuk mencatat pengeluaran baru.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <p class="mt-4 text-xs text-[#6c757d]">
            Menampilkan {{ $pengeluarans->count() }} data pengeluaran.
        </p>

    </div>

    {{-- =====================================================
         MODAL TAMBAH PENGELUARAN
         Memakai elemen <dialog> bawaan browser (tanpa Bootstrap/jQuery).
         Dibuka dengan tombol [data-modal-open], ditutup dengan
         [data-modal-close], klik latar gelap, atau tombol Esc.
    ====================================================== --}}
    <dialog id="modalTambahPengeluaran" aria-labelledby="judulModalPengeluaran"
        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-[800px] overflow-hidden rounded-[3px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_10px_40px_rgba(0,0,0,0.25)] backdrop:bg-black/50 open:flex open:flex-col">

        {{-- HEADER MODAL --}}
        <div class="flex shrink-0 items-start justify-between bg-[#6777ef] px-6 py-4 text-white">
            <div>
                <h5 id="judulModalPengeluaran" class="text-lg font-semibold text-white">
                    <i class="fas fa-receipt mr-2"></i>
                    Tambah Pengeluaran
                </h5>

                <small class="text-white">
                    Catat pengeluaran operasional perusahaan.
                </small>
            </div>

            <button type="button" data-modal-close aria-label="Tutup"
                class="-mt-1 text-3xl leading-none text-white/80 hover:text-white">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        {{-- FORM --}}
        <form action="{{ route('pengeluaran.store') }}" method="POST" id="formTambahPengeluaran"
            class="flex min-h-0 flex-1 flex-col">

            @csrf

            <div class="min-h-0 flex-1 overflow-y-auto p-6">

                {{-- INFORMASI DASAR --}}
                <div class="mb-6 border-b border-[#e3eaef] pb-2">
                    <h6 class="mb-1 text-sm font-bold text-[#6777ef]">
                        <i class="fas fa-info-circle mr-1"></i>
                        Informasi Pengeluaran
                    </h6>

                    <small class="text-[#6c757d]">
                        Isi tanggal, referensi, cabang, dan jenis beban.
                    </small>
                </div>

                <div class="mb-4 grid grid-cols-1 gap-x-2.5 gap-y-4 md:grid-cols-2">

                    {{-- TANGGAL --}}
                    <div>
                        <label for="modal_tanggal"
                            class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Tanggal Pengeluaran
                            <span class="text-[#fc544b]">*</span>
                        </label>

                        <input type="date" id="modal_tanggal" name="tanggal"
                            value="{{ old('tanggal', date('Y-m-d')) }}" required autofocus
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none @error('tanggal') border-[#fc544b] @else border-[#e4e6fc] @enderror">

                        @error('tanggal')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- NOMOR REFERENSI PREVIEW --}}
                    <div>
                        <label for="modal_no_referensi_preview"
                            class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Nomor Referensi
                        </label>

                        <input type="text" id="modal_no_referensi_preview" value=""
                            placeholder="Dibuat otomatis saat disimpan" readonly
                            class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#f6f8fb] px-[15px] text-sm text-[#495057] focus:outline-none">

                        <small class="mt-1 block text-[#6c757d]">
                            Preview format nomor referensi.
                        </small>
                    </div>

                    {{-- CABANG --}}
                    <div>
                        <label for="modal_cabang_nama"
                            class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Cabang
                            <span class="text-[#fc544b]">*</span>
                        </label>

                        <input type="hidden" id="modal_cabang_id" name="cabang_id"
                            value="{{ old('cabang_id', Auth::user()->cabang_id) }}">

                        <input type="text" id="modal_cabang_nama"
                            value="{{ Auth::user()->cabang?->nama ?? 'Cabang belum diatur' }}" readonly
                            class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#f6f8fb] px-[15px] text-sm text-[#495057] focus:outline-none">

                        @error('cabang_id')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- JENIS BEBAN --}}
                    <div>
                        <label for="modal_akun_id"
                            class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Jenis Beban
                            <span class="text-[#fc544b]">*</span>
                        </label>

                        <select id="modal_akun_id" name="akun_id" required
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none @error('akun_id') border-[#fc544b] @else border-[#e4e6fc] @enderror">

                            <option value="">Pilih Jenis Beban</option>

                            @foreach ($akuns as $akun)
                                <option value="{{ $akun->id }}" data-kode="{{ $akun->kode_akun }}"
                                    @selected((string) old('akun_id') === (string) $akun->id)>
                                    {{ $akun->kode_akun }} — {{ $akun->nama_akun }}
                                </option>
                            @endforeach
                        </select>

                        @error('akun_id')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- DETAIL PENGELUARAN --}}
                <div class="mb-6 mt-2 border-b border-[#e3eaef] pb-2">
                    <h6 class="mb-1 text-sm font-bold text-[#6777ef]">
                        <i class="fas fa-list-alt mr-1"></i>
                        Detail Pengeluaran
                    </h6>

                    <small class="text-[#6c757d]">
                        Masukkan keterangan, jumlah, satuan, dan harga.
                    </small>
                </div>

                {{-- KETERANGAN --}}
                <div class="mb-4">
                    <label for="modal_keterangan"
                        class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Keterangan
                        <span class="text-[#fc544b]">*</span>
                    </label>

                    <textarea id="modal_keterangan" name="keterangan" rows="3" maxlength="1000" required
                        placeholder="Contoh: Pembelian kertas untuk kebutuhan percetakan"
                        class="w-full rounded-[3px] border bg-[#fdfdff] px-[15px] py-2.5 text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none @error('keterangan') border-[#fc544b] @else border-[#e4e6fc] @enderror">{{ old('keterangan') }}</textarea>

                    @error('keterangan')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

                {{-- QTY DAN SATUAN --}}
                <div class="mb-4 grid grid-cols-1 gap-x-2.5 gap-y-4 md:grid-cols-2">

                    <div>
                        <label for="modal_qty"
                            class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Qty
                            <span class="text-[#fc544b]">*</span>
                        </label>

                        <input type="number" id="modal_qty" name="qty" value="{{ old('qty', '1') }}"
                            min="1" max="9999999999" step="1" required placeholder="Masukkan jumlah"
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none @error('qty') border-[#fc544b] @else border-[#e4e6fc] @enderror">

                        @error('qty')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="modal_satuan"
                            class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Satuan
                            <span class="text-[#fc544b]">*</span>
                        </label>

                        <input type="text" id="modal_satuan" name="satuan" value="{{ old('satuan') }}"
                            maxlength="30" required placeholder="Contoh: Unit, Pcs, Orang"
                            class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none @error('satuan') border-[#fc544b] @else border-[#e4e6fc] @enderror">

                        @error('satuan')
                            <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                {{-- HARGA --}}
                <div class="mb-4">
                    <label for="modal_harga"
                        class="mb-2 inline-block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Harga per Satuan (Rp)
                        <span class="text-[#fc544b]">*</span>
                    </label>

                    <div class="flex">
                        <span
                            class="flex items-center rounded-l-[3px] border border-r-0 border-[#e4e6fc] bg-[#f6f8fb] px-[15px] text-sm text-[#6c757d]">
                            Rp
                        </span>

                        <input type="number" id="modal_harga" name="harga" value="{{ old('harga') }}"
                            min="1" max="9999999999999" step="1" required
                            placeholder="Masukkan harga per satuan"
                            class="h-[42px] min-w-0 flex-1 rounded-r-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none @error('harga') border-[#fc544b] @else border-[#e4e6fc] @enderror">
                    </div>

                    @error('harga')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

                {{-- TOTAL OTOMATIS --}}
                <div class="rounded-[3px] border border-[#6777ef] bg-[#f6f8fb] p-[25px]">
                    <label for="modal_total_pengeluaran" class="mb-2 inline-block font-bold text-[#191d21]">
                        <i class="fas fa-calculator mr-1 text-[#6777ef]"></i>
                        Total Pengeluaran
                    </label>

                    <div class="flex">
                        <span
                            class="flex items-center rounded-l-[3px] border border-r-0 border-[#e4e6fc] bg-[#f6f8fb] px-[15px] text-sm font-bold text-[#6c757d]">
                            Rp
                        </span>

                        <input type="text" id="modal_total_pengeluaran" value="0" readonly
                            class="h-[52px] min-w-0 flex-1 rounded-r-[3px] border border-[#e4e6fc] bg-white px-[15px] text-[22px] font-bold text-[#6777ef] focus:outline-none">
                    </div>

                    <small class="mt-1 block text-[#6c757d]">
                        Total dihitung otomatis dari Qty × Harga per Satuan.
                    </small>
                </div>

            </div>

            {{-- FOOTER MODAL --}}
            <div class="flex shrink-0 items-center justify-end gap-2 border-t border-[#e3eaef] bg-[#f6f8fb] px-6 py-4">
                <button type="button" data-modal-close
                    class="inline-flex items-center rounded-[3px] bg-[#e3eaef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>

                <button type="submit" id="btnSimpanPengeluaran"
                    class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-60">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Pengeluaran
                </button>
            </div>

        </form>
    </dialog>

    {{-- SCRIPT MODAL --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modalTambahPengeluaran');
            const qtyInput = document.getElementById('modal_qty');
            const hargaInput = document.getElementById('modal_harga');
            const totalInput = document.getElementById('modal_total_pengeluaran');
            const akunSelect = document.getElementById('modal_akun_id');
            const tanggalInput = document.getElementById('modal_tanggal');
            const referensiInput = document.getElementById('modal_no_referensi_preview');
            const form = document.getElementById('formTambahPengeluaran');
            const btnSimpan = document.getElementById('btnSimpanPengeluaran');

            // Buka dan tutup modal (elemen <dialog> bawaan browser).
            document.querySelectorAll('[data-modal-open="modalTambahPengeluaran"]').forEach(function(tombol) {
                tombol.addEventListener('click', function() {
                    modal.showModal();
                });
            });

            modal.querySelectorAll('[data-modal-close]').forEach(function(tombol) {
                tombol.addEventListener('click', function() {
                    modal.close();
                });
            });

            // Klik pada latar gelap menutup modal.
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.close();
                }
            });

            const prefixMap = {
                '5-2100': 'PAY',
                '5-2200': 'UTIL',
                '5-2300': 'TRN',
                '5-2400': 'PRO',
                '5-2500': 'ADM',
                '5-2600': 'MNT'
            };

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 0
                }).format(angka);
            }

            function updateTotal() {
                const qty = Number(qtyInput.value) || 0;
                const harga = Number(hargaInput.value) || 0;

                totalInput.value = formatRupiah(qty * harga);
            }

            function updateReferensiPreview() {
                const option = akunSelect.options[akunSelect.selectedIndex];
                const kode = option?.dataset?.kode || '';
                const prefix = prefixMap[kode];
                const tanggal = tanggalInput.value;

                if (!prefix || !tanggal) {
                    referensiInput.value = '';
                    referensiInput.placeholder =
                        'Pilih jenis beban dan tanggal';
                    return;
                }

                const tanggalFormat = tanggal.replaceAll('-', '');
                referensiInput.value = `${prefix}-${tanggalFormat}-AUTO`;
            }

            qtyInput.addEventListener('input', updateTotal);
            hargaInput.addEventListener('input', updateTotal);
            akunSelect.addEventListener('change', updateReferensiPreview);
            tanggalInput.addEventListener('change', updateReferensiPreview);

            // Format huruf awal satuan menjadi kapital.
            document.getElementById('modal_satuan').addEventListener('input', function() {
                if (this.value.length > 0) {
                    this.value = this.value.charAt(0).toUpperCase() +
                        this.value.slice(1);
                }
            });

            // Cegah klik simpan berulang kali.
            form.addEventListener('submit', function() {
                btnSimpan.disabled = true;
                btnSimpan.innerHTML =
                    '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
            });

            updateTotal();
            updateReferensiPreview();

            // Jika validasi Laravel gagal, buka kembali modal.
            @if ($errors->any())
                modal.showModal();
            @endif
        });
    </script>

</x-admin-layout-stisla>