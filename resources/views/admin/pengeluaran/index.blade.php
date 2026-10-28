
<x-admin-layout-stisla>
    {{-- HEADER HALAMAN --}}
    <div class="section-header">
        <h1>Riwayat Pengeluaran</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item">
                <a href="{{ route('pengeluaran.index') }}">Pengeluaran</a>
            </div>
            <div class="breadcrumb-item active">Riwayat Pengeluaran</div>
        </div>
    </div>

    <div class="section-body">

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="row align-items-center mb-3">
            <div class="col-md-7">
                <p class="text-muted mb-0">
                    Riwayat dan rincian pengeluaran operasional perusahaan.
                </p>
            </div>

            <div class="col-md-5 text-md-right mt-2 mt-md-0">
                <button type="button"
                        class="btn btn-primary"
                        data-toggle="modal"
                        data-target="#modalTambahPengeluaran">
                    <i class="fas fa-plus mr-1"></i>
                    Tambah Pengeluaran
                </button>
            </div>
        </div>

        {{-- NOTIFIKASI SUKSES --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 mb-3"
                 role="alert">
                <i class="fas fa-check-circle mr-1"></i>
                {{ session('success') }}

                <button type="button" class="close" data-dismiss="alert"
                        aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- NOTIFIKASI ERROR --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show py-2 mb-3"
                 role="alert">
                <i class="fas fa-exclamation-circle mr-1"></i>
                {{ session('error') }}

                <button type="button" class="close" data-dismiss="alert"
                        aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- FILTER PENGELUARAN --}}
        <div class="card mb-3">
            <div class="card-body p-3">
                <form action="{{ route('pengeluaran.index') }}" method="GET">
                    <div class="form-row align-items-end">

                        <div class="form-group col-lg-3 col-md-6 mb-2">
                            <label for="start_date" class="d-block mb-1">
                                Dari Tanggal
                            </label>

                            <input type="date"
                                   id="start_date"
                                   name="start_date"
                                   value="{{ $startDate }}"
                                   required
                                   class="form-control">
                        </div>

                        <div class="form-group col-lg-3 col-md-6 mb-2">
                            <label for="end_date" class="d-block mb-1">
                                Sampai Tanggal
                            </label>

                            <input type="date"
                                   id="end_date"
                                   name="end_date"
                                   value="{{ $endDate }}"
                                   min="{{ $startDate }}"
                                   required
                                   class="form-control">
                        </div>

                        @if (Auth::user()->role === 'Admin Pusat')
                            <div class="form-group col-lg-3 col-md-6 mb-2">
                                <label for="filter_cabang_id" class="d-block mb-1">
                                    Cabang
                                </label>

                                <select id="filter_cabang_id"
                                        name="cabang_id"
                                        class="form-control">
                                    <option value="">Semua Cabang</option>

                                    @foreach ($cabangs as $cabang)
                                        <option value="{{ $cabang->id }}"
                                            @selected((string) request('cabang_id') === (string) $cabang->id)>
                                            {{ $cabang->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="form-group col-lg-3 col-md-6 mb-2">
                            <div class="d-flex align-items-center flex-wrap">
                                <button type="submit"
                                        class="btn btn-primary mr-2 mb-1">
                                    <i class="fas fa-filter mr-1"></i>
                                    Filter
                                </button>

                                <a href="{{ route('pengeluaran.index') }}"
                                   class="btn btn-light mb-1">
                                    <i class="fas fa-redo mr-1"></i>
                                    Reset
                                </a>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- TABEL RIWAYAT PENGELUARAN --}}
        <div class="card">
            <div class="card-header bg-primary py-2">
                <h4 class="text-white mb-0">
                    <i class="fas fa-receipt mr-2"></i>
                    Data Riwayat Pengeluaran
                </h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sista">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>No Referensi</th>
                                <th>Tanggal</th>
                                <th>Cabang</th>
                                <th>Keterangan</th>
                                <th class="text-right">Total Pengeluaran</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($pengeluarans as $pengeluaran)
                                <tr>
                                    <td class="text-center text-muted">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="font-weight-bold text-primary">
                                        {{ $pengeluaran->no_referensi }}
                                    </td>

                                    <td class="text-nowrap">
                                        {{ $pengeluaran->tanggal->format('d M Y') }}
                                    </td>

                                    <td>
                                        {{ $pengeluaran->cabang?->nama ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $pengeluaran->keterangan ?: '-' }}
                                    </td>

                                    <td class="text-right font-weight-bold text-nowrap">
                                        Rp {{ number_format(
                                            (float) $pengeluaran->total_pengeluaran,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-receipt fa-2x mb-2"></i>

                                        <p class="font-weight-bold mb-1">
                                            Belum ada data pengeluaran
                                        </p>

                                        <p class="mb-0">
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
        </div>

        <p class="text-muted small mt-3">
            Menampilkan {{ $pengeluarans->count() }} data pengeluaran.
        </p>

    </div>

    {{-- =====================================================
         MODAL TAMBAH PENGELUARAN
    ====================================================== --}}
    <div class="modal fade"
         id="modalTambahPengeluaran"
         tabindex="-1"
         role="dialog"
         aria-labelledby="judulModalPengeluaran"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"
             role="document">

            <div class="modal-content">

                {{-- HEADER MODAL --}}
                <div class="modal-header bg-primary text-white">
                    <div>
                        <h5 class="modal-title text-white"
                            id="judulModalPengeluaran">
                            <i class="fas fa-receipt mr-2"></i>
                            Tambah Pengeluaran
                        </h5>

                        <small class="text-white">
                            Catat pengeluaran operasional perusahaan.
                        </small>
                    </div>

                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal"
                            aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- FORM --}}
                <form action="{{ route('pengeluaran.store') }}"
                      method="POST"
                      id="formTambahPengeluaran">

                    @csrf

                    <div class="modal-body">

                        {{-- INFORMASI DASAR --}}
                        <div class="border-bottom pb-2 mb-4">
                            <h6 class="font-weight-bold text-primary mb-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Informasi Pengeluaran
                            </h6>

                            <small class="text-muted">
                                Isi tanggal, referensi, cabang, dan jenis beban.
                            </small>
                        </div>

                        <div class="form-row">

                            {{-- TANGGAL --}}
                            <div class="form-group col-md-6">
                                <label for="modal_tanggal">
                                    Tanggal Pengeluaran
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="date"
                                       id="modal_tanggal"
                                       name="tanggal"
                                       value="{{ old('tanggal', date('Y-m-d')) }}"
                                       required
                                       class="form-control @error('tanggal') is-invalid @enderror">

                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- NOMOR REFERENSI PREVIEW --}}
                            <div class="form-group col-md-6">
                                <label for="modal_no_referensi_preview">
                                    Nomor Referensi
                                </label>

                                <input type="text"
                                       id="modal_no_referensi_preview"
                                       value=""
                                       placeholder="Dibuat otomatis saat disimpan"
                                       readonly
                                       class="form-control bg-light">

                                <small class="form-text text-muted">
                                    Preview format nomor referensi.
                                </small>
                            </div>

                            {{-- CABANG --}}
                            <div class="form-group col-md-6">
                                <label for="modal_cabang_nama">
                                    Cabang
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="hidden"
                                       id="modal_cabang_id"
                                       name="cabang_id"
                                       value="{{ old('cabang_id', Auth::user()->cabang_id) }}">

                                <input type="text"
                                       id="modal_cabang_nama"
                                       value="{{ Auth::user()->cabang?->nama ?? 'Cabang belum diatur' }}"
                                       readonly
                                       class="form-control bg-light">

                                @error('cabang_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- JENIS BEBAN --}}
                            <div class="form-group col-md-6">
                                <label for="modal_akun_id">
                                    Jenis Beban
                                    <span class="text-danger">*</span>
                                </label>

                                <select id="modal_akun_id"
                                        name="akun_id"
                                        required
                                        class="form-control @error('akun_id') is-invalid @enderror">

                                    <option value="">Pilih Jenis Beban</option>

                                    @foreach ($akuns as $akun)
                                        <option value="{{ $akun->id }}"
                                                data-kode="{{ $akun->kode_akun }}"
                                            @selected((string) old('akun_id') === (string) $akun->id)>
                                            {{ $akun->kode_akun }} — {{ $akun->nama_akun }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('akun_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        {{-- DETAIL PENGELUARAN --}}
                        <div class="border-bottom pb-2 mb-4 mt-2">
                            <h6 class="font-weight-bold text-primary mb-1">
                                <i class="fas fa-list-alt mr-1"></i>
                                Detail Pengeluaran
                            </h6>

                            <small class="text-muted">
                                Masukkan keterangan, jumlah, satuan, dan harga.
                            </small>
                        </div>

                        {{-- KETERANGAN --}}
                        <div class="form-group">
                            <label for="modal_keterangan">
                                Keterangan
                                <span class="text-danger">*</span>
                            </label>

                            <textarea id="modal_keterangan"
                                      name="keterangan"
                                      rows="3"
                                      maxlength="1000"
                                      required
                                      placeholder="Contoh: Pembelian kertas untuk kebutuhan percetakan"
                                      class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan') }}</textarea>

                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- QTY DAN SATUAN --}}
                        <div class="form-row">

                            <div class="form-group col-md-6">
                                <label for="modal_qty">
                                    Qty
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="number"
                                       id="modal_qty"
                                       name="qty"
                                       value="{{ old('qty', '1') }}"
                                       min="1"
                                       max="9999999999"
                                       step="1"
                                       required
                                       placeholder="Masukkan jumlah"
                                       class="form-control @error('qty') is-invalid @enderror">

                                @error('qty')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="modal_satuan">
                                    Satuan
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       id="modal_satuan"
                                       name="satuan"
                                       value="{{ old('satuan') }}"
                                       maxlength="30"
                                       required
                                       placeholder="Contoh: Unit, Pcs, Orang"
                                       class="form-control @error('satuan') is-invalid @enderror">

                                @error('satuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        {{-- HARGA --}}
                        <div class="form-group">
                            <label for="modal_harga">
                                Harga per Satuan (Rp)
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">Rp</span>
                                </div>

                                <input type="number"
                                       id="modal_harga"
                                       name="harga"
                                       value="{{ old('harga') }}"
                                       min="1"
                                       max="9999999999999"
                                       step="1"
                                       required
                                       placeholder="Masukkan harga per satuan"
                                       class="form-control @error('harga') is-invalid @enderror">
                            </div>

                            @error('harga')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- TOTAL OTOMATIS --}}
                        <div class="card border border-primary mb-0">
                            <div class="card-body bg-light">
                                <label for="modal_total_pengeluaran"
                                       class="font-weight-bold text-dark">
                                    <i class="fas fa-calculator text-primary mr-1"></i>
                                    Total Pengeluaran
                                </label>

                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text font-weight-bold">Rp</span>
                                    </div>

                                    <input type="text"
                                           id="modal_total_pengeluaran"
                                           value="0"
                                           readonly
                                           class="form-control bg-white font-weight-bold text-primary"
                                           style="font-size: 22px; height: 52px;">
                                </div>

                                <small class="form-text text-muted">
                                    Total dihitung otomatis dari Qty × Harga per Satuan.
                                </small>
                            </div>
                        </div>

                    </div>

                    {{-- FOOTER MODAL --}}
                    <div class="modal-footer bg-light">
                        <button type="button"
                                class="btn btn-light"
                                data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>
                            Batal
                        </button>

                        <button type="submit"
                                id="btnSimpanPengeluaran"
                                class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>
                            Simpan Pengeluaran
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT MODAL --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const qtyInput = document.getElementById('modal_qty');
            const hargaInput = document.getElementById('modal_harga');
            const totalInput = document.getElementById('modal_total_pengeluaran');
            const akunSelect = document.getElementById('modal_akun_id');
            const tanggalInput = document.getElementById('modal_tanggal');
            const referensiInput = document.getElementById('modal_no_referensi_preview');
            const form = document.getElementById('formTambahPengeluaran');
            const btnSimpan = document.getElementById('btnSimpanPengeluaran');

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
            document.getElementById('modal_satuan').addEventListener('input', function () {
                if (this.value.length > 0) {
                    this.value = this.value.charAt(0).toUpperCase()
                        + this.value.slice(1);
                }
            });

            // Cegah klik simpan berulang kali.
            form.addEventListener('submit', function () {
                btnSimpan.disabled = true;
                btnSimpan.innerHTML =
                    '<i class="fas fa-spinner fa-spin mr-1"></i> Menyimpan...';
            });

            updateTotal();
            updateReferensiPreview();

            // Jika validasi Laravel gagal, buka kembali modal.
            @if ($errors->any())
                if (window.jQuery && typeof jQuery.fn.modal === 'function') {
                    jQuery('#modalTambahPengeluaran').modal('show');
                }
            @endif
        });
    </script>
     {{-- PERBAIKAN POSISI MODAL --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('modalTambahPengeluaran');

            if (modal) {
                document.body.appendChild(modal);
            }
        });
    </script>

</x-admin-layout-stisla>
    