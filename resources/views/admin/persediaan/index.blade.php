<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div
        class="mb-[30px] flex flex-wrap items-center gap-x-4 gap-y-2 rounded-[3px] bg-white px-[30px] py-5 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
        <h1 class="text-2xl font-bold text-[#34395e]">Persediaan Bahan Baku</h1>

        <ol class="ml-auto flex items-center text-[13px] text-[#6c757d]">
            <li>Persediaan</li>
            <li
                class="font-semibold text-[#6777ef] before:px-2 before:font-normal before:text-[#6c757d] before:content-['/']">
                Persediaan Bahan Baku
            </li>
        </ol>
    </div>

    <div>

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <p class="mb-2 text-[#6c757d]">
                Pantau stok dan nilai persediaan bahan baku CV Cemerlang.
            </p>

            <button type="button" data-modal-open="modalTambahPembelian"
                class="mb-2 inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
                <i class="fas fa-plus mr-1"></i>
                Tambah Pembelian
            </button>
        </div>

        {{-- NOTIFIKASI SUKSES --}}
        @if (session('success'))
            <div role="alert"
                class="mb-4 flex items-center rounded-[3px] border-l-[3px] border-black/20 bg-[#54ca68] px-5 py-2.5 text-white">
                <span class="min-w-0">{{ session('success') }}</span>
                <button type="button" aria-label="Tutup" onclick="this.closest('[role=alert]').remove()"
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">&times;</button>
            </div>
        @endif

        {{-- NOTIFIKASI ERROR --}}
        @if (session('error'))
            <div role="alert"
                class="mb-4 flex items-center rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2.5 text-white">
                <span class="min-w-0">{{ session('error') }}</span>
                <button type="button" aria-label="Tutup" onclick="this.closest('[role=alert]').remove()"
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">&times;</button>
            </div>
        @endif

        {{-- RINGKASAN PERSEDIAAN --}}
        <div class="mb-[30px] grid grid-cols-1 gap-[30px] md:grid-cols-2 lg:grid-cols-3">

            {{-- Jenis Bahan Baku --}}
            <div class="flex items-center rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
                <div
                    class="m-2.5 flex h-20 w-20 shrink-0 items-center justify-center rounded-[3px] bg-[#6777ef] text-[30px] text-white">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="min-w-0 flex-1 py-2.5 pr-5">
                    <h4 class="text-[13px] font-semibold uppercase tracking-[0.5px] text-[#98a6ad]">Jenis Bahan Baku
                    </h4>
                    <div class="text-xl font-bold text-[#34395e]">
                        {{ number_format($totalJenisBahan, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- Bahan Aktif --}}
            <div class="flex items-center rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
                <div
                    class="m-2.5 flex h-20 w-20 shrink-0 items-center justify-center rounded-[3px] bg-[#54ca68] text-[30px] text-white">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="min-w-0 flex-1 py-2.5 pr-5">
                    <h4 class="text-[13px] font-semibold uppercase tracking-[0.5px] text-[#98a6ad]">Bahan Aktif</h4>
                    <div class="text-xl font-bold text-[#34395e]">
                        {{ number_format($totalJenisAktif, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- Nilai Persediaan --}}
            <div class="flex items-center rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
                <div
                    class="m-2.5 flex h-20 w-20 shrink-0 items-center justify-center rounded-[3px] bg-[#ffa426] text-[30px] text-white">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="min-w-0 flex-1 py-2.5 pr-5">
                    <h4 class="text-[13px] font-semibold uppercase tracking-[0.5px] text-[#98a6ad]">Nilai Persediaan
                    </h4>
                    <div class="text-[19px] font-bold text-[#34395e]">
                        Rp {{ number_format($totalNilaiPersediaan, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL PERSEDIAAN --}}
        <x-card-table title="Data Riwayat Pengeluaran" icon="fa-receipt">

            {{-- HEADER TABEL --}}
            <x-slot:head>
                <th class="h-[45px] w-[5%] bg-[#fdfdff] px-[15px] text-center">#</th>
                <th class="h-[45px] w-[13%] bg-[#fdfdff] px-[15px] text-left">Kode Bahan</th>
                <th class="h-[45px] w-[22%] bg-[#fdfdff] px-[15px] text-left">Nama Bahan</th>
                <th class="h-[45px] w-[10%] bg-[#fdfdff] px-[15px] text-left">Satuan</th>
                <th class="h-[45px] w-[13%] bg-[#fdfdff] px-[15px] text-right">Stok</th>
                <th class="h-[45px] w-[17%] bg-[#fdfdff] px-[15px] text-right">Harga Rata-rata</th>
                <th class="h-[45px] w-[20%] bg-[#fdfdff] px-[15px] text-right">Nilai Persediaan</th>
            </x-slot:head>

            {{-- ISI TABEL --}}
            @forelse ($bahanBakus as $bahan)
                @php
                    $stok = (float) $bahan->stok;
                    $hargaRataRata = (float) $bahan->harga_rata_rata;
                    $nilaiPersediaan = $stok * $hargaRataRata;
                @endphp

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

                        @if (strtolower($bahan->status) !== 'aktif')
                            <span
                                class="ml-1 rounded-full bg-[#cdd3d8] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">
                                Nonaktif
                            </span>
                        @endif
                    </td>

                    <td class="px-[15px] py-3 align-middle">
                        {{ $bahan->satuan }}
                    </td>

                    <td class="px-[15px] py-3 text-right align-middle">
                        {{ number_format($stok, fmod($stok, 1) == 0 ? 0 : 3, ',', '.') }}
                    </td>

                    <td class="px-[15px] py-3 text-right align-middle">
                        Rp {{ number_format($hargaRataRata, 0, ',', '.') }}
                    </td>

                    <td class="px-[15px] py-3 text-right align-middle font-bold">
                        Rp {{ number_format($nilaiPersediaan, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-[15px] py-12 text-center text-[#6c757d]">
                        <i class="fas fa-box-open mb-2 text-[2em]"></i>
                        <p class="mb-1">Belum ada data persediaan.</p>
                        <small class="text-xs">
                            Daftarkan bahan baku terlebih dahulu melalui menu Bahan Baku.
                        </small>
                    </td>
                </tr>
            @endforelse
        </x-card-table>
    </div>

    <div class="border-t border-black/5 px-[25px] py-4 text-[#6c757d]">
        <small class="text-xs">
            Total jenis bahan:
            <strong class="font-bold">{{ number_format($totalJenisBahan, 0, ',', '.') }}</strong>

            <span class="mx-2">|</span>

            Total nilai persediaan:
            <strong class="font-bold">
                Rp {{ number_format($totalNilaiPersediaan, 2, ',', '.') }}
            </strong>
        </small>
    </div>

    {{-- ===================================================== --}}
    {{-- MODAL TAMBAH PEMBELIAN --}}
    {{-- ===================================================== --}}
    <dialog id="modalTambahPembelian" aria-labelledby="judulModalPembelian"
        class="m-auto max-h-[85vh] w-[92%] max-w-[1050px] overflow-hidden rounded-[10px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_4px_25px_rgba(0,0,0,0.2)] backdrop:bg-black/40">

        <div class="flex max-h-[85vh] flex-col">

            {{-- HEADER MODAL --}}
            <div class="flex shrink-0 items-center justify-between bg-[#6777ef] px-[25px] py-4">
                <h5 id="judulModalPembelian" class="text-lg font-semibold text-white">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Tambah Pembelian Bahan Baku
                </h5>

                <button type="button" data-modal-close aria-label="Tutup"
                    class="text-2xl leading-none text-white/80 hover:text-white">&times;</button>
            </div>

            {{-- FORM PEMBELIAN --}}
            <form action="{{ route('persediaan.store') }}" method="POST" id="formPembelian"
                class="flex min-h-0 flex-1 flex-col">
                @csrf

                <div class="min-h-0 flex-1 overflow-y-auto p-5">

                    {{-- ERROR VALIDASI --}}
                    @if ($errors->any())
                        <div
                            class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2.5 text-white">
                            <strong class="font-bold">Pembelian belum dapat disimpan.</strong>

                            <ul class="mb-0 mt-2 list-disc pl-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- PERINGATAN JIKA BAHAN KOSONG --}}
                    @if ($bahanBakus->where('status', 'aktif')->isEmpty())
                        <div
                            class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#ffa426] px-5 py-2.5 text-white">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Belum ada bahan baku aktif.

                            <a href="{{ route('bahan-baku.index') }}" class="font-bold underline">
                                Daftarkan bahan baku terlebih dahulu.
                            </a>
                        </div>
                    @endif

                    {{-- INFORMASI PEMBELIAN --}}
                    <div class="mb-[30px] rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
                        <div class="flex min-h-[70px] items-center border-b border-[#f9f9f9] px-[25px] py-1">
                            <h6 class="text-base font-semibold leading-7 text-[#6777ef]">
                                <i class="fas fa-info-circle mr-2"></i>
                                Informasi Pembelian
                            </h6>
                        </div>

                        <div class="p-[25px]">
                            <div class="grid grid-cols-1 gap-x-[30px] md:grid-cols-2">

                                {{-- TANGGAL --}}
                                <div class="mb-[25px]">
                                    <label for="tanggal_pembelian"
                                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                                        Tanggal Pembelian
                                        <span class="text-[#fc544b]">*</span>
                                    </label>

                                    <input type="date" name="tanggal_pembelian" id="tanggal_pembelian"
                                        value="{{ old('tanggal_pembelian', now()->toDateString()) }}" required
                                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('tanggal_pembelian') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">

                                    @error('tanggal_pembelian')
                                        <div class="mt-1 text-xs text-[#fc544b]">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                {{-- PEMASOK --}}
                                <div class="mb-[25px]">
                                    <label for="nama_pemasok"
                                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                                        Nama Pemasok
                                    </label>

                                    <input type="text" name="nama_pemasok" id="nama_pemasok"
                                        value="{{ old('nama_pemasok') }}" placeholder="Nama toko atau pemasok"
                                        maxlength="255"
                                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                                </div>

                                {{-- KETERANGAN --}}
                                <div class="md:col-span-2">
                                    <label for="keterangan"
                                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                                        Keterangan
                                    </label>

                                    <textarea name="keterangan" id="keterangan" rows="2" placeholder="Catatan tambahan (opsional)"
                                        class="w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] py-2.5 text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">{{ old('keterangan') }}</textarea>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- DETAIL BAHAN YANG DIBELI --}}
                    <div class="rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
                        <div
                            class="flex min-h-[70px] flex-wrap items-center justify-between border-b border-[#f9f9f9] px-[25px] py-1">
                            <h6 class="text-base font-semibold leading-7 text-[#6777ef]">
                                Detail Bahan yang Dibeli
                            </h6>

                            <button type="button" id="btnTambahBaris"
                                {{ $bahanBakus->where('status', 'aktif')->isEmpty() ? 'disabled' : '' }}
                                class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-65">
                                <i class="fas fa-plus mr-1"></i>
                                Tambah Baris
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-[13px]" id="tabelPembelian">

                                <thead>
                                    <tr class="text-[10px] font-bold uppercase tracking-[1.3px] text-[#666]">
                                        <th class="h-[45px] min-w-[220px] bg-[#fdfdff] px-[15px] text-left">
                                            Bahan Baku
                                        </th>

                                        <th class="h-[45px] min-w-[110px] bg-[#fdfdff] px-[15px] text-left">
                                            Jumlah
                                        </th>

                                        <th class="h-[45px] min-w-[160px] bg-[#fdfdff] px-[15px] text-left">
                                            Harga Satuan (Rp)
                                        </th>

                                        <th class="h-[45px] min-w-[150px] bg-[#fdfdff] px-[15px] text-right">
                                            Subtotal (Rp)
                                        </th>

                                        <th class="h-[45px] bg-[#fdfdff] px-[15px] text-center">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>

                                <tbody id="daftarItem">
                                    @php
                                        $oldItems = old('items', [
                                            [
                                                'bahan_baku_id' => '',
                                                'jumlah' => '',
                                                'harga_satuan' => '',
                                            ],
                                        ]);

                                        $bahanAktif = $bahanBakus->where('status', 'aktif')->values();
                                    @endphp

                                    @foreach ($oldItems as $i => $oldItem)
                                        <tr class="baris-item border-b border-[#f2f2f2]">
                                            {{-- PILIH BAHAN --}}
                                            <td class="px-[15px] py-3 align-top">
                                                <select name="items[{{ $i }}][bahan_baku_id]" required
                                                    {{ $bahanAktif->isEmpty() ? 'disabled' : '' }}
                                                    class="pilih-bahan h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">

                                                    <option value="">
                                                        -- Pilih Bahan --
                                                    </option>

                                                    @foreach ($bahanAktif as $bahan)
                                                        <option value="{{ $bahan->id }}"
                                                            data-satuan="{{ $bahan->satuan }}"
                                                            @selected((string) ($oldItem['bahan_baku_id'] ?? '') === (string) $bahan->id)>
                                                            {{ $bahan->kode_bahan }}
                                                            -
                                                            {{ $bahan->nama_bahan }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <small class="label-satuan mt-1 block text-xs text-[#6c757d]">
                                                    Satuan: -
                                                </small>
                                            </td>

                                            {{-- JUMLAH --}}
                                            <td class="px-[15px] py-3 align-top">
                                                <input type="number" name="items[{{ $i }}][jumlah]"
                                                    value="{{ $oldItem['jumlah'] ?? '' }}" min="0.001"
                                                    step="0.001" placeholder="0" required
                                                    class="input-jumlah h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                                            </td>

                                            {{-- HARGA SATUAN --}}
                                            <td class="px-[15px] py-3 align-top">
                                                <input type="number" name="items[{{ $i }}][harga_satuan]"
                                                    value="{{ $oldItem['harga_satuan'] ?? '' }}" min="0"
                                                    step="0.01" placeholder="0" required
                                                    class="input-harga h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                                            </td>

                                            {{-- SUBTOTAL --}}
                                            <td class="px-[15px] py-3 text-right align-middle">
                                                <span class="subtotal">
                                                    Rp 0,00
                                                </span>
                                            </td>

                                            {{-- HAPUS BARIS --}}
                                            <td class="px-[15px] py-3 text-center align-middle">
                                                <button type="button" title="Hapus baris"
                                                    class="btn-hapus inline-flex items-center rounded-[3px] bg-[#fc544b] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#fb160a]">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>

                        {{-- TOTAL --}}
                        <div class="border-t border-black/5 px-[25px] py-4">
                            <div class="flex justify-end">
                                <div class="w-full md:w-1/2 lg:w-5/12">
                                    <div class="flex items-center justify-between">
                                        <strong class="font-bold">Total Pembelian</strong>

                                        <h5 class="text-base font-semibold text-[#6777ef]" id="totalPembelian">
                                            Rp 0,00
                                        </h5>
                                    </div>

                                    <small class="text-xs text-[#6c757d]">
                                        Total dihitung dari jumlah dikali harga satuan setiap bahan.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- FOOTER MODAL --}}
                <div class="flex shrink-0 items-center justify-end gap-2 bg-[#f8f9fa] px-[25px] py-3">
                    <button type="button" data-modal-close
                        class="inline-flex items-center rounded-[3px] bg-[#cdd3d8] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#bcc4cb]">
                        <i class="fas fa-times mr-1"></i>
                        Batal
                    </button>

                    <button type="submit" id="btnSimpanPembelian"
                        {{ $bahanBakus->where('status', 'aktif')->isEmpty() ? 'disabled' : '' }}
                        class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-65">
                        <i class="fas fa-save mr-1"></i>
                        Simpan Pembelian
                    </button>
                </div>

            </form>

        </div>
    </dialog>

    {{-- JAVASCRIPT MODAL DAN PERHITUNGAN --}}
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
                const modal = document.getElementById('modalTambahPembelian');
                const form = document.getElementById('formPembelian');
                const daftarItem = document.getElementById('daftarItem');
                const btnTambah = document.getElementById('btnTambahBaris');
                const totalPembelian = document.getElementById('totalPembelian');
                const btnSimpan = document.getElementById('btnSimpanPembelian');

                if (!modal || !form || !daftarItem) {
                    return;
                }

                // Data bahan aktif dari Laravel.
                @php
                    $dataBahanOptions = $bahanBakus
                        ->where('status', 'aktif')
                        ->values()
                        ->map(function ($bahan) {
                            return [
                                'id' => $bahan->id,
                                'label' => $bahan->kode_bahan . ' - ' . $bahan->nama_bahan,
                                'satuan' => $bahan->satuan,
                            ];
                        })
                        ->all();
                @endphp

                // Pada file lama variabel ini dipakai buatBaris() tetapi tidak pernah dideklarasikan.
                const bahanOptions = @json($dataBahanOptions);

                // Class Tailwind untuk baris yang dibuat lewat JavaScript
                // (ditulis lengkap agar terbaca Tailwind).
                const kelasBaris = 'baris-item border-b border-[#f2f2f2]';
                const kelasSelDataTop = 'px-[15px] py-3 align-top';
                const kelasSelDataMid = 'px-[15px] py-3 text-right align-middle';
                const kelasSelAksi = 'px-[15px] py-3 text-center align-middle';
                const kelasSelect =
                    'pilih-bahan h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none';
                const kelasJumlah =
                    'input-jumlah h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none';
                const kelasHarga =
                    'input-harga h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none';
                const kelasSatuan = 'label-satuan mt-1 block text-xs text-[#6c757d]';
                const kelasHapus =
                    'btn-hapus inline-flex items-center rounded-[3px] bg-[#fc544b] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#fb160a]';

                // Format angka Rupiah.
                const rupiah = function(angka) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(angka);
                };

                // Atur nama input agar indeks selalu berurutan.
                function perbaruiNomorInput() {
                    daftarItem.querySelectorAll('.baris-item').forEach(function(baris, index) {
                        baris.querySelector('.pilih-bahan').name =
                            `items[${index}][bahan_baku_id]`;

                        baris.querySelector('.input-jumlah').name =
                            `items[${index}][jumlah]`;

                        baris.querySelector('.input-harga').name =
                            `items[${index}][harga_satuan]`;
                    });
                }

                // Hitung subtotal dan total pembelian.
                function hitungTotal() {
                    let total = 0;

                    daftarItem.querySelectorAll('.baris-item').forEach(function(baris) {
                        const jumlah = parseFloat(
                            baris.querySelector('.input-jumlah').value
                        ) || 0;

                        const harga = parseFloat(
                            baris.querySelector('.input-harga').value
                        ) || 0;

                        const subtotal = jumlah * harga;

                        baris.querySelector('.subtotal').textContent =
                            rupiah(subtotal);

                        total += subtotal;

                        const pilihan = baris.querySelector('.pilih-bahan');
                        const option = pilihan.options[pilihan.selectedIndex];

                        baris.querySelector('.label-satuan').textContent =
                            option && option.dataset.satuan ?
                            `Satuan: ${option.dataset.satuan}` :
                            'Satuan: -';
                    });

                    totalPembelian.textContent = rupiah(total);
                }

                // Buat baris pembelian baru.
                function buatBaris() {
                    const tr = document.createElement('tr');
                    tr.className = kelasBaris;

                    // Kolom bahan baku.
                    const tdBahan = document.createElement('td');
                    tdBahan.className = kelasSelDataTop;
                    const select = document.createElement('select');

                    select.className = kelasSelect;
                    select.required = true;

                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = '-- Pilih Bahan --';

                    select.appendChild(defaultOption);

                    bahanOptions.forEach(function(bahan) {
                        const option = document.createElement('option');

                        option.value = bahan.id;
                        option.textContent = bahan.label;
                        option.dataset.satuan = bahan.satuan;

                        select.appendChild(option);
                    });

                    const satuan = document.createElement('small');
                    satuan.className = kelasSatuan;
                    satuan.textContent = 'Satuan: -';

                    tdBahan.append(select, satuan);

                    // Kolom jumlah.
                    const tdJumlah = document.createElement('td');
                    tdJumlah.className = kelasSelDataTop;
                    const inputJumlah = document.createElement('input');

                    inputJumlah.type = 'number';
                    inputJumlah.className = kelasJumlah;
                    inputJumlah.min = '0.001';
                    inputJumlah.step = '0.001';
                    inputJumlah.placeholder = '0';
                    inputJumlah.required = true;

                    tdJumlah.appendChild(inputJumlah);

                    // Kolom harga satuan.
                    const tdHarga = document.createElement('td');
                    tdHarga.className = kelasSelDataTop;
                    const inputHarga = document.createElement('input');

                    inputHarga.type = 'number';
                    inputHarga.className = kelasHarga;
                    inputHarga.min = '0';
                    inputHarga.step = '0.01';
                    inputHarga.placeholder = '0';
                    inputHarga.required = true;

                    tdHarga.appendChild(inputHarga);

                    // Kolom subtotal.
                    const tdSubtotal = document.createElement('td');
                    tdSubtotal.className = kelasSelDataMid;

                    const subtotal = document.createElement('span');
                    subtotal.className = 'subtotal';
                    subtotal.textContent = rupiah(0);

                    tdSubtotal.appendChild(subtotal);

                    // Kolom aksi.
                    const tdAksi = document.createElement('td');
                    tdAksi.className = kelasSelAksi;

                    const hapus = document.createElement('button');
                    hapus.type = 'button';
                    hapus.className = kelasHapus;
                    hapus.title = 'Hapus baris';
                    hapus.innerHTML = '<i class="fas fa-trash"></i>';

                    tdAksi.appendChild(hapus);

                    // Gabungkan semua kolom.
                    tr.append(tdBahan, tdJumlah, tdHarga, tdSubtotal, tdAksi);
                    daftarItem.appendChild(tr);

                    perbaruiNomorInput();
                    hitungTotal();
                }

                // Tombol tambah baris.
                if (btnTambah) {
                    btnTambah.addEventListener('click', buatBaris);
                }

                // Hitung ulang saat jumlah atau harga berubah.
                daftarItem.addEventListener('input', function(event) {
                    if (
                        event.target.classList.contains('input-jumlah') ||
                        event.target.classList.contains('input-harga')
                    ) {
                        hitungTotal();
                    }
                });

                // Tampilkan satuan bahan yang dipilih.
                daftarItem.addEventListener('change', function(event) {
                    if (event.target.classList.contains('pilih-bahan')) {
                        hitungTotal();
                    }
                });

                // Hapus baris pembelian.
                daftarItem.addEventListener('click', function(event) {
                    const tombol = event.target.closest('.btn-hapus');

                    if (!tombol) {
                        return;
                    }

                    const baris = tombol.closest('.baris-item');
                    const semuaBaris = daftarItem.querySelectorAll('.baris-item');

                    if (semuaBaris.length > 1) {
                        baris.remove();
                    } else {
                        // Sisakan satu baris kosong.
                        baris.querySelector('.pilih-bahan').value = '';
                        baris.querySelector('.input-jumlah').value = '';
                        baris.querySelector('.input-harga').value = '';
                    }

                    perbaruiNomorInput();
                    hitungTotal();
                });

                // Cegah submit berulang.
                form.addEventListener('submit', function() {
                    if (!form.checkValidity()) {
                        return;
                    }

                    btnSimpan.disabled = true;
                    btnSimpan.innerHTML =
                        '<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...';
                });

                // Inisialisasi awal.
                perbaruiNomorInput();
                hitungTotal();

                // Buka kembali modal setelah validasi gagal.
                @if ($errors->any())
                    if (!modal.open) modal.showModal();
                @endif
            });
        </script>
    @endpush

</x-admin-layout-stisla>
