<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div
        class="mb-[30px] flex flex-wrap items-center gap-x-4 gap-y-2 rounded-[3px] bg-white px-[30px] py-5 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
        <h1 class="text-2xl font-bold text-[#34395e]">Bagan Akun (Chart of Accounts)</h1>

        <ol class="ml-auto flex items-center text-[13px] text-[#6c757d]">
            <li class="font-semibold text-[#6777ef]">Akuntansi</li>
            <li class="before:px-2 before:content-['/']">Bagan Akun</li>
        </ol>
    </div>

    <div>

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <p class="mb-2 text-[#6c757d]">
                Kelola daftar akun perkiraan untuk pencatatan transaksi dan laporan keuangan CV Cemerlang.
            </p>

            <button type="button" data-modal-open="modalTambahAkun"
                class="mb-2 inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
                <i class="fas fa-plus mr-1"></i>
                Tambah Akun
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
                    Terdapat kesalahan pada formulir. Periksa kembali data akun.
                </span>
                <button type="button" aria-label="Tutup" onclick="this.closest('[role=alert]').remove()"
                    class="ml-auto pl-4 text-2xl leading-none text-white/80 hover:text-white">&times;</button>
            </div>
        @endif

        {{-- TABEL BAGAN AKUN --}}
        <div class="overflow-hidden rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
            <div class="flex min-h-[70px] items-center bg-[#6777ef] px-[25px] py-2">
                <h4 class="text-base font-semibold leading-7 text-white">
                    <i class="fas fa-book mr-2"></i>
                    Daftar Akun Perkiraan
                </h4>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead>
                        <tr class="text-[10px] font-bold uppercase tracking-[1.3px] text-[#666]">
                            <th class="h-[45px] w-[5%] bg-black/[0.04] px-[15px] text-center">#</th>
                            <th class="h-[45px] w-[17%] bg-black/[0.04] px-[15px] text-left">Kode Akun</th>
                            <th class="h-[45px] w-[30%] bg-black/[0.04] px-[15px] text-left">Nama Akun</th>
                            <th class="h-[45px] w-[20%] bg-black/[0.04] px-[15px] text-left">Tipe Akun</th>
                            <th class="h-[45px] w-[15%] bg-black/[0.04] px-[15px] text-center">Status</th>
                            <th class="h-[45px] w-[13%] bg-black/[0.04] px-[15px] text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($akuns as $akun)
                            <tr class="odd:bg-black/[0.025]">
                                <td class="px-[15px] py-3 text-center align-middle text-[#6c757d]">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-[15px] py-3 align-middle">
                                    <strong class="font-bold text-[#6777ef]">
                                        {{ $akun->kode_akun }}
                                    </strong>
                                </td>

                                <td class="px-[15px] py-3 align-middle font-bold">
                                    {{ $akun->nama_akun }}
                                </td>

                                <td class="px-[15px] py-3 align-middle">
                                    {{ $akun->tipe_akun }}
                                </td>

                                <td class="px-[15px] py-3 text-center align-middle">
                                    @if (strtolower($akun->status) === 'aktif')
                                        <span
                                            class="rounded-full bg-[#54ca68] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">Aktif</span>
                                    @else
                                        <span
                                            class="rounded-full bg-[#cdd3d8] px-2.5 py-[3px] text-xs font-semibold tracking-[0.3px] text-white">Nonaktif</span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-[15px] py-3 text-center align-middle">
                                    <button type="button" title="Edit Akun" data-modal-open="modalEditAkun"
                                        class="btn-edit-akun inline-flex items-center rounded-[3px] bg-[#6777ef] px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-[#394eea]"
                                        data-id="{{ $akun->id }}" data-url="{{ route('akun.update', $akun->id) }}"
                                        data-kode="{{ $akun->kode_akun }}" data-nama="{{ $akun->nama_akun }}"
                                        data-tipe="{{ $akun->tipe_akun }}"
                                        data-kelompok="{{ $akun->kelompok_akun }}"
                                        data-status="{{ $akun->status }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-[15px] py-6 text-center text-[#6c757d]">
                                    <i class="fas fa-book mb-2 text-[2em]"></i>
                                    <p>
                                        Belum ada data akun perkiraan.
                                        Silakan tambah akun baru.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-black/5 px-[25px] py-4 text-[#6c757d]">
                <small class="text-xs">
                    Total akun:
                    <strong class="font-bold">{{ $akuns->count() }}</strong>
                </small>
            </div>
        </div>

    </div>

    {{-- MODAL TAMBAH AKUN --}}
    <dialog id="modalTambahAkun" aria-labelledby="judulModalTambahAkun"
        class="m-auto max-h-[90vh] w-[calc(100%-2rem)] max-w-[800px] overflow-y-auto rounded-[3px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_4px_25px_rgba(0,0,0,0.2)] backdrop:bg-black/40">

        <div class="flex items-center justify-between bg-[#6777ef] px-[25px] py-4">
            <h5 id="judulModalTambahAkun" class="text-lg font-semibold text-white">
                <i class="fas fa-book mr-2"></i>
                Tambah Akun Baru
            </h5>

            <button type="button" data-modal-close aria-label="Tutup"
                class="text-2xl leading-none text-white/80 hover:text-white">&times;</button>
        </div>

        <form id="formTambahAkun" action="{{ route('akun.store') }}" method="POST">
            @csrf

            <div class="p-[25px]">

                @if ($errors->any())
                    <div
                        class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2.5 text-white">
                        <strong class="font-bold">Gagal menyimpan akun.</strong>
                        <ul class="mb-0 mt-2 list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- TIPE AKUN --}}
                <div class="mb-[25px]">
                    <label for="tipe_akun" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Tipe Akun <span class="text-[#fc544b]">*</span>
                    </label>

                    <select name="tipe_akun" id="tipe_akun" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('tipe_akun') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">
                        <option value="">-- Pilih Tipe Akun --</option>

                        <option value="Aset" {{ old('tipe_akun') === 'Aset' ? 'selected' : '' }}>
                            Aset
                        </option>

                        <option value="Kewajiban" {{ old('tipe_akun') === 'Kewajiban' ? 'selected' : '' }}>
                            Kewajiban
                        </option>

                        <option value="Ekuitas" {{ old('tipe_akun') === 'Ekuitas' ? 'selected' : '' }}>
                            Ekuitas
                        </option>

                        <option value="Pendapatan" {{ old('tipe_akun') === 'Pendapatan' ? 'selected' : '' }}>
                            Pendapatan
                        </option>

                        <option value="Beban/Biaya" {{ old('tipe_akun') === 'Beban/Biaya' ? 'selected' : '' }}>
                            Beban / Biaya
                        </option>
                    </select>

                    @error('tipe_akun')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

                {{-- KELOMPOK AKUN --}}
                <div class="mb-[25px]">
                    <label for="kelompok_akun"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Kelompok Akun <span class="text-[#fc544b]">*</span>
                    </label>

                    <select name="kelompok_akun" id="kelompok_akun" data-old-kelompok="{{ old('kelompok_akun') }}"
                        required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('kelompok_akun') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">
                        <option value="">-- Pilih Kelompok Akun --</option>
                    </select>

                    @error('kelompok_akun')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

                {{-- NAMA AKUN --}}
                <div class="mb-[25px]">
                    <label for="nama_akun" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Nama Akun <span class="text-[#fc544b]">*</span>
                    </label>

                    <input type="text" name="nama_akun" id="nama_akun" value="{{ old('nama_akun') }}"
                        placeholder="Contoh: Kas" maxlength="255" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('nama_akun') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">

                    @error('nama_akun')
                        <div class="mt-1 text-xs text-[#fc544b]">{{ $message }}</div>
                    @enderror
                </div>

                {{-- KODE AKUN --}}
                <div>
                    <label for="kode_akun" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Kode Akun
                    </label>

                    <input type="text" id="kode_akun" placeholder="Pilih tipe dan kelompok akun terlebih dahulu"
                        readonly
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#e9ecef] px-[15px] text-sm text-[#495057] focus:outline-none">

                    <small class="mt-1 block text-xs text-[#6c757d]">
                        Kode akun dihitung otomatis oleh sistem.
                    </small>
                </div>

            </div>

            <div class="flex items-center justify-end gap-2 bg-[#f8f9fa] px-[25px] py-3">
                <button type="button" data-modal-close
                    class="inline-flex items-center rounded-[3px] bg-[#e3eaef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>

                <button type="submit" id="btnSimpanAkun"
                    class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-65">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Akun
                </button>
            </div>

        </form>
    </dialog>

    {{-- SCRIPT MODAL DAN KODE AKUN --}}
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
                const modal = document.getElementById('modalTambahAkun');
                const form = document.getElementById('formTambahAkun');
                const tipeAkun = document.getElementById('tipe_akun');
                const kelompokAkun = document.getElementById('kelompok_akun');
                const kodeAkun = document.getElementById('kode_akun');
                const namaAkun = document.getElementById('nama_akun');
                const tombolSimpan = document.getElementById('btnSimpanAkun');

                if (!modal || !form || !tipeAkun || !kelompokAkun || !kodeAkun) {
                    return;
                }

                const kelompokData = {
                    'Aset': {
                        'Aset': 1100
                    },
                    'Kewajiban': {
                        'Kewajiban': 1100
                    },
                    'Ekuitas': {
                        'Ekuitas': 1100
                    },
                    'Pendapatan': {
                        'Pendapatan Usaha': 1100
                    },
                    'Beban/Biaya': {
                        'HPP': 1100,
                        'Beban Operasional': 2100,
                        'Biaya Lain-lain': 3100
                    }
                };

                const oldTipe = @json(old('tipe_akun', ''));
                const oldKelompok = kelompokAkun.dataset.oldKelompok || '';

                let permintaanKode = 0;

                function isiKelompok(tipe, pilihanLama = '') {
                    kelompokAkun.innerHTML =
                        '<option value="">-- Pilih Kelompok Akun --</option>';

                    const daftar = kelompokData[tipe];

                    if (!daftar) {
                        return;
                    }

                    Object.keys(daftar).forEach(function(nama) {
                        const option = document.createElement('option');
                        option.value = nama;
                        option.textContent = nama;
                        option.selected = nama === pilihanLama;

                        kelompokAkun.appendChild(option);
                    });
                }

                async function perbaruiKode() {
                    const tipe = tipeAkun.value;
                    const kelompok = kelompokAkun.value;
                    const nomorPermintaan = ++permintaanKode;

                    kodeAkun.value = '';

                    if (!tipe || !kelompok) {
                        return;
                    }

                    kodeAkun.value = 'Menghitung...';

                    try {
                        const url = new URL(
                            @json(route('akun.preview-kode')),
                            window.location.origin
                        );

                        url.searchParams.set('tipe_akun', tipe);
                        url.searchParams.set('kelompok_akun', kelompok);

                        const response = await fetch(url.toString(), {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Kode akun gagal dihitung.');
                        }

                        const data = await response.json();

                        // Abaikan hasil lama jika pengguna sudah mengubah pilihan.
                        if (nomorPermintaan !== permintaanKode) {
                            return;
                        }

                        kodeAkun.value = data.kode || '';
                    } catch (error) {
                        if (nomorPermintaan === permintaanKode) {
                            kodeAkun.value = '';
                            console.error(error);
                        }
                    }
                }

                tipeAkun.addEventListener('change', function() {
                    isiKelompok(this.value);
                    perbaruiKode();
                });

                kelompokAkun.addEventListener('change', perbaruiKode);

                // Pulihkan pilihan sebelumnya jika validasi gagal.
                if (oldTipe) {
                    tipeAkun.value = oldTipe;
                    isiKelompok(oldTipe, oldKelompok);
                    perbaruiKode();
                }

                // Cegah pengiriman formulir berkali-kali.
                form.addEventListener('submit', function() {
                    if (tombolSimpan) {
                        tombolSimpan.disabled = true;
                        tombolSimpan.innerHTML =
                            '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                    }
                });

                // Buka modal kembali setelah validasi server gagal.
                // Kondisi !old('akun_id') ditambahkan: kalau error berasal dari form edit,
                // modal tambah tidak ikut terbuka (dengan <dialog> keduanya akan bertumpuk).
                @if ($errors->any() && !old('akun_id'))
                    if (!modal.open) modal.showModal();
                @endif
            });
        </script>
    @endpush

    {{-- MODAL EDIT AKUN --}}
    <dialog id="modalEditAkun" aria-labelledby="judulModalEditAkun"
        class="m-auto max-h-[90vh] w-[calc(100%-30px)] max-w-[650px] overflow-y-auto rounded-[10px] bg-white p-0 text-[13px] text-[#6c757d] shadow-[0_4px_25px_rgba(0,0,0,0.2)] backdrop:bg-black/40">

        <div class="flex items-center justify-between bg-[#6777ef] px-[25px] py-4">
            <h5 id="judulModalEditAkun" class="text-lg font-semibold text-white">
                <i class="fas fa-edit mr-2"></i>
                Edit Akun
            </h5>

            <button type="button" data-modal-close aria-label="Tutup"
                class="text-2xl leading-none text-white/80 hover:text-white">&times;</button>
        </div>

        <form id="formEditAkun" method="POST" action="#">
            @csrf
            @method('PUT')

            <input type="hidden" name="akun_id" id="edit_akun_id">

            <div class="p-[25px]">

                @if ($errors->any() && old('akun_id'))
                    <div
                        class="mb-4 rounded-[3px] border-l-[3px] border-black/20 bg-[#fc544b] px-5 py-2.5 text-white">
                        <strong class="font-bold">Gagal memperbarui akun.</strong>
                        <ul class="mb-0 mt-2 list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- KODE AKUN --}}
                <div class="mb-[25px]">
                    <label for="edit_kode_akun"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">Kode Akun</label>
                    <input type="text" id="edit_kode_akun" readonly
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#e9ecef] px-[15px] text-sm text-[#495057] focus:outline-none">
                    <small class="mt-1 block text-xs text-[#6c757d]">
                        Kode akun tidak dapat diubah.
                    </small>
                </div>

                {{-- NAMA AKUN --}}
                <div class="mb-[25px]">
                    <label for="edit_nama_akun"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Nama Akun <span class="text-[#fc544b]">*</span>
                    </label>

                    <input type="text" name="nama_akun" id="edit_nama_akun" value="{{ old('nama_akun') }}"
                        maxlength="255" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('nama_akun') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">

                    @error('nama_akun')
                        <div class="mt-1 text-xs text-[#fc544b]">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- TIPE AKUN --}}
                <div class="mb-[25px]">
                    <label for="edit_tipe_akun"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">Tipe Akun</label>
                    <input type="text" id="edit_tipe_akun" readonly
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#e9ecef] px-[15px] text-sm text-[#495057] focus:outline-none">
                    <small class="mt-1 block text-xs text-[#6c757d]">
                        Tipe akun tidak dapat diubah.
                    </small>
                </div>

                {{-- KELOMPOK AKUN --}}
                <div class="mb-[25px]">
                    <label for="edit_kelompok_akun"
                        class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">Kelompok Akun</label>
                    <input type="text" id="edit_kelompok_akun" readonly
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#e9ecef] px-[15px] text-sm text-[#495057] focus:outline-none">
                    <small class="mt-1 block text-xs text-[#6c757d]">
                        Kelompok akun tidak dapat diubah.
                    </small>
                </div>

                {{-- STATUS --}}
                <div>
                    <label for="edit_status" class="mb-2 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Status <span class="text-[#fc544b]">*</span>
                    </label>

                    <select name="status" id="edit_status" required
                        class="h-[42px] w-full rounded-[3px] border bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:outline-none @error('status') border-[#fc544b] @else border-[#e4e6fc] focus:border-[#95a0f4] @enderror">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>

                    @error('status')
                        <div class="mt-1 text-xs text-[#fc544b]">
                            {{ $message }}
                        </div>
                    @enderror

                    <small class="mt-1 block text-xs text-[#6c757d]">
                        Pastikan akun yang dinonaktifkan tidak lagi
                        diperlukan untuk transaksi baru.
                    </small>
                </div>

            </div>

            <div class="flex items-center justify-end gap-2 bg-[#f8f9fa] px-[25px] py-3">
                <button type="button" data-modal-close
                    class="inline-flex items-center rounded-[3px] bg-[#e3eaef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                    <i class="fas fa-times mr-1"></i>
                    Batal
                </button>

                <button type="submit" id="btnUpdateAkun"
                    class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea] disabled:cursor-not-allowed disabled:opacity-65">
                    <i class="fas fa-save mr-1"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </dialog>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('modalEditAkun');
                const form = document.getElementById('formEditAkun');

                if (!modal || !form) return;

                const field = {
                    id: document.getElementById('edit_akun_id'),
                    kode: document.getElementById('edit_kode_akun'),
                    nama: document.getElementById('edit_nama_akun'),
                    tipe: document.getElementById('edit_tipe_akun'),
                    kelompok: document.getElementById('edit_kelompok_akun'),
                    status: document.getElementById('edit_status')
                };

                function isiModal(tombol) {
                    field.id.value = tombol.dataset.id;
                    field.kode.value = tombol.dataset.kode;
                    field.nama.value = tombol.dataset.nama;
                    field.tipe.value = tombol.dataset.tipe;
                    field.kelompok.value = tombol.dataset.kelompok;
                    field.status.value = tombol.dataset.status;

                    form.action = tombol.dataset.url;
                }

                // Modal dibuka oleh data-modal-open; di sini hanya mengisi datanya.
                document.querySelectorAll('.btn-edit-akun').forEach(function(tombol) {
                    tombol.addEventListener('click', function() {
                        isiModal(this);
                    });
                });

                // Cegah pengiriman formulir berkali-kali.
                form.addEventListener('submit', function() {
                    const tombol = document.getElementById('btnUpdateAkun');

                    tombol.disabled = true;
                    tombol.innerHTML =
                        '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
                });

                // Buka kembali modal jika validasi gagal.
                const oldId = @json(old('akun_id'));

                if (oldId) {
                    const tombolLama = Array.from(
                        document.querySelectorAll('.btn-edit-akun')
                    ).find(function(tombol) {
                        return tombol.dataset.id === String(oldId);
                    });

                    if (tombolLama) {
                        isiModal(tombolLama);

                        // Pulihkan input yang dikirim sebelum validasi gagal.
                        field.nama.value = @json(old('nama_akun', ''));
                        field.status.value = @json(old('status', 'aktif'));

                        if (!modal.open) modal.showModal();
                    }
                }
            });
        </script>
    @endpush

</x-admin-layout-stisla>