
<x-admin-layout-stisla>
    <div class="section-header">
        <h1>Persediaan Bahan Baku</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item">Persediaan</div>
            <div class="breadcrumb-item active">Persediaan Bahan Baku</div>
        </div>
    </div>

    <div class="section-body">

        {{-- DESKRIPSI DAN TOMBOL TAMBAH --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <p class="text-muted mb-2">
                Pantau stok dan nilai persediaan bahan baku CV Cemerlang.
            </p>

            <button type="button"
                    class="btn btn-primary mb-2"
                    data-toggle="modal"
                    data-target="#modalTambahPembelian">
                <i class="fas fa-plus mr-1"></i>
                Tambah Pembelian
            </button>
        </div>

        {{-- NOTIFIKASI SUKSES --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <div class="alert-body">
                    {{ session('success') }}

                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            </div>
        @endif

        {{-- NOTIFIKASI ERROR --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <div class="alert-body">
                    {{ session('error') }}

                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            </div>
        @endif

        {{-- RINGKASAN PERSEDIAAN --}}
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Jenis Bahan Baku</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($totalJenisBahan, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Bahan Aktif</h4>
                        </div>
                        <div class="card-body">
                            {{ number_format($totalJenisAktif, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Nilai Persediaan</h4>
                        </div>
                        <div class="card-body" style="font-size: 19px;">
                            Rp {{ number_format($totalNilaiPersediaan, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL PERSEDIAAN --}}
        <div class="card">
            <div class="card-header bg-primary py-2">
                <h4 class="text-white mb-0">
                    <i class="fas fa-warehouse mr-2"></i>
                    Daftar Stok Bahan Baku
                </h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sista">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">#</th>
                                <th width="13%">Kode Bahan</th>
                                <th width="22%">Nama Bahan</th>
                                <th width="10%">Satuan</th>
                                <th width="13%" class="text-right">Stok</th>
                                <th width="17%" class="text-right">Harga Rata-rata</th>
                                <th width="20%" class="text-right">Nilai Persediaan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($bahanBakus as $bahan)
                                @php
                                    $stok = (float) $bahan->stok;
                                    $hargaRataRata = (float) $bahan->harga_rata_rata;
                                    $nilaiPersediaan = $stok * $hargaRataRata;
                                @endphp

                                <tr>
                                    <td class="text-center align-middle text-muted">
                                        {{ $loop->iteration }}
                                    </td>

                                    <td class="align-middle">
                                        <strong class="text-primary">
                                            {{ $bahan->kode_bahan }}
                                        </strong>
                                    </td>

                                    <td class="align-middle font-weight-bold">
                                        {{ $bahan->nama_bahan }}

                                        @if (strtolower($bahan->status) !== 'aktif')
                                            <span class="badge badge-secondary ml-1">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <td class="align-middle">
                                        {{ $bahan->satuan }}
                                    </td>

                                    <td class="text-right align-middle">
                                        {{ number_format($stok, fmod($stok, 1) == 0 ? 0 : 3, ',', '.') }}
                                    </td>

                                    <td class="text-right align-middle">
                                        Rp {{ number_format($hargaRataRata, 0, ',', '.') }}
                                    </td>

                                    <td class="text-right align-middle font-weight-bold">
                                        Rp {{ number_format($nilaiPersediaan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="fas fa-box-open fa-2x mb-2"></i>
                                        <p class="mb-1">Belum ada data persediaan.</p>
                                        <small>
                                            Daftarkan bahan baku terlebih dahulu melalui menu Bahan Baku.
                                        </small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer text-muted">
                <small>
                    Total jenis bahan:
                    <strong>{{ number_format($totalJenisBahan, 0, ',', '.') }}</strong>

                    <span class="mx-2">|</span>

                    Total nilai persediaan:
                    <strong>
                        Rp {{ number_format($totalNilaiPersediaan, 2, ',', '.') }}
                    </strong>
                </small>
            </div>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- MODAL TAMBAH PEMBELIAN --}}
    {{-- ===================================================== --}}
    <div class="modal fade"
         id="modalTambahPembelian"
         tabindex="-1"
         role="dialog"
         aria-labelledby="judulModalPembelian"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"
             role="document" style="width: 92%; max-width: 1050px; margin: 20px auto;">

            <div class="modal-content" style="max-height: 85vh; border-radius: 10px; overflow: hidden;">

                {{-- HEADER MODAL --}}
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="judulModalPembelian">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Tambah Pembelian Bahan Baku
                    </h5>

                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal"
                            aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                {{-- FORM PEMBELIAN --}}
                <form action="{{ route('persediaan.store') }}"
                      method="POST"
                      id="formPembelian">

                    @csrf

                    <div class="modal-body" style="overflow-y: auto; max-height: calc(85vh - 130px); padding: 20px;">

                        {{-- ERROR VALIDASI --}}
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <strong>Pembelian belum dapat disimpan.</strong>

                                <ul class="mb-0 mt-2 pl-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- PERINGATAN JIKA BAHAN KOSONG --}}
                        @if ($bahanBakus->where('status', 'aktif')->isEmpty())
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Belum ada bahan baku aktif.

                                <a href="{{ route('bahan-baku.index') }}" class="alert-link">
                                    Daftarkan bahan baku terlebih dahulu.
                                </a>
                            </div>
                        @endif

                        {{-- INFORMASI PEMBELIAN --}}
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Informasi Pembelian
                                </h6>
                            </div>

                            <div class="card-body">
                                <div class="row">

                                    {{-- TANGGAL --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tanggal_pembelian">
                                                Tanggal Pembelian
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="date"
                                                   name="tanggal_pembelian"
                                                   id="tanggal_pembelian"
                                                   class="form-control @error('tanggal_pembelian') is-invalid @enderror"
                                                   value="{{ old('tanggal_pembelian', now()->toDateString()) }}"
                                                   required>

                                            @error('tanggal_pembelian')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- PEMASOK --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama_pemasok">
                                                Nama Pemasok
                                            </label>

                                            <input type="text"
                                                   name="nama_pemasok"
                                                   id="nama_pemasok"
                                                   class="form-control"
                                                   value="{{ old('nama_pemasok') }}"
                                                   placeholder="Nama toko atau pemasok"
                                                   maxlength="255">
                                        </div>
                                    </div>

                                    {{-- KETERANGAN --}}
                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label for="keterangan">
                                                Keterangan
                                            </label>

                                            <textarea name="keterangan"
                                                      id="keterangan"
                                                      class="form-control"
                                                      rows="2"
                                                      placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- DETAIL BAHAN YANG DIBELI --}}
                        <div class="card mb-0">
                            <div class="card-header d-flex flex-wrap align-items-center justify-content-between">
                                <h6 class="mb-0">
                                    Detail Bahan yang Dibeli
                                </h6>

                                <button type="button"
                                        class="btn btn-primary btn-sm"
                                        id="btnTambahBaris"
                                        {{ $bahanBakus->where('status', 'aktif')->isEmpty() ? 'disabled' : '' }}>
                                    <i class="fas fa-plus mr-1"></i>
                                    Tambah Baris
                                </button>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sista mb-0"
                                           id="tabelPembelian">

                                        <thead>
                                            <tr>
                                                <th style="min-width: 220px;">
                                                    Bahan Baku
                                                </th>

                                                <th style="min-width: 110px;">
                                                    Jumlah
                                                </th>

                                                <th style="min-width: 160px;">
                                                    Harga Satuan (Rp)
                                                </th>

                                                <th style="min-width: 150px;"
                                                    class="text-right">
                                                    Subtotal (Rp)
                                                </th>

                                                <th class="text-center">
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

                                                $bahanAktif = $bahanBakus
                                                    ->where('status', 'aktif')
                                                    ->values();
                                            @endphp

                                            @foreach ($oldItems as $i => $oldItem)
                                                <tr class="baris-item">
                                                    {{-- PILIH BAHAN --}}
                                                    <td>
                                                        <select name="items[{{ $i }}][bahan_baku_id]"
                                                                class="form-control pilih-bahan"
                                                                required
                                                                {{ $bahanAktif->isEmpty() ? 'disabled' : '' }}>

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

                                                        <small class="text-muted label-satuan">
                                                            Satuan: -
                                                        </small>
                                                    </td>

                                                    {{-- JUMLAH --}}
                                                    <td>
                                                        <input type="number"
                                                               name="items[{{ $i }}][jumlah]"
                                                               class="form-control input-jumlah"
                                                               value="{{ $oldItem['jumlah'] ?? '' }}"
                                                               min="0.001"
                                                               step="0.001"
                                                               placeholder="0"
                                                               required>
                                                    </td>

                                                    {{-- HARGA SATUAN --}}
                                                    <td>
                                                        <input type="number"
                                                               name="items[{{ $i }}][harga_satuan]"
                                                               class="form-control input-harga"
                                                               value="{{ $oldItem['harga_satuan'] ?? '' }}"
                                                               min="0"
                                                               step="0.01"
                                                               placeholder="0"
                                                               required>
                                                    </td>

                                                    {{-- SUBTOTAL --}}
                                                    <td class="text-right align-middle">
                                                        <span class="subtotal">
                                                            Rp 0,00
                                                        </span>
                                                    </td>

                                                    {{-- HAPUS BARIS --}}
                                                    <td class="text-center align-middle">
                                                        <button type="button"
                                                                class="btn btn-danger btn-sm btn-hapus"
                                                                title="Hapus baris">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>

                                    </table>
                                </div>
                            </div>

                            {{-- TOTAL --}}
                            <div class="card-footer">
                                <div class="row justify-content-end">
                                    <div class="col-md-6 col-lg-5">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <strong>Total Pembelian</strong>

                                            <h5 class="text-primary mb-0"
                                                id="totalPembelian">
                                                Rp 0,00
                                            </h5>
                                        </div>

                                        <small class="text-muted">
                                            Total dihitung dari jumlah dikali harga satuan setiap bahan.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- FOOTER MODAL --}}
                    <div class="modal-footer bg-light">
                        <button type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i>
                            Batal
                        </button>

                        <button type="submit"
                                id="btnSimpanPembelian"
                                class="btn btn-primary"
                                {{ $bahanBakus->where('status', 'aktif')->isEmpty() ? 'disabled' : '' }}>
                            <i class="fas fa-save mr-1"></i>
                            Simpan Pembelian
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    {{-- JAVASCRIPT MODAL DAN PERHITUNGAN --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.getElementById('modalTambahPembelian');
                const form = document.getElementById('formPembelian');
                const daftarItem = document.getElementById('daftarItem');
                const btnTambah = document.getElementById('btnTambahBaris');
                const totalPembelian = document.getElementById('totalPembelian');
                const btnSimpan = document.getElementById('btnSimpanPembelian');

                if (!modal || !form || !daftarItem) {
                    return;
                }

                // Pindahkan modal ke body untuk menghindari masalah stacking context.
                document.body.appendChild(modal);

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

                // Format angka Rupiah.
                const rupiah = function (angka) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(angka);
                };

                // Atur nama input agar indeks selalu berurutan.
                function perbaruiNomorInput() {
                    daftarItem.querySelectorAll('.baris-item').forEach(function (baris, index) {
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

                    daftarItem.querySelectorAll('.baris-item').forEach(function (baris) {
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
                            option && option.dataset.satuan
                                ? `Satuan: ${option.dataset.satuan}`
                                : 'Satuan: -';
                    });

                    totalPembelian.textContent = rupiah(total);
                }

                // Buat baris pembelian baru.
                function buatBaris() {
                    const tr = document.createElement('tr');
                    tr.className = 'baris-item';

                    // Kolom bahan baku.
                    const tdBahan = document.createElement('td');
                    const select = document.createElement('select');

                    select.className = 'form-control pilih-bahan';
                    select.required = true;

                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = '-- Pilih Bahan --';

                    select.appendChild(defaultOption);

                    bahanOptions.forEach(function (bahan) {
                        const option = document.createElement('option');

                        option.value = bahan.id;
                        option.textContent = bahan.label;
                        option.dataset.satuan = bahan.satuan;

                        select.appendChild(option);
                    });

                    const satuan = document.createElement('small');
                    satuan.className = 'text-muted label-satuan';
                    satuan.textContent = 'Satuan: -';

                    tdBahan.append(select, satuan);

                    // Kolom jumlah.
                    const tdJumlah = document.createElement('td');
                    const inputJumlah = document.createElement('input');

                    inputJumlah.type = 'number';
                    inputJumlah.className = 'form-control input-jumlah';
                    inputJumlah.min = '0.001';
                    inputJumlah.step = '0.001';
                    inputJumlah.placeholder = '0';
                    inputJumlah.required = true;

                    tdJumlah.appendChild(inputJumlah);

                    // Kolom harga satuan.
                    const tdHarga = document.createElement('td');
                    const inputHarga = document.createElement('input');

                    inputHarga.type = 'number';
                    inputHarga.className = 'form-control input-harga';
                    inputHarga.min = '0';
                    inputHarga.step = '0.01';
                    inputHarga.placeholder = '0';
                    inputHarga.required = true;

                    tdHarga.appendChild(inputHarga);

                    // Kolom subtotal.
                    const tdSubtotal = document.createElement('td');
                    tdSubtotal.className = 'text-right align-middle';

                    const subtotal = document.createElement('span');
                    subtotal.className = 'subtotal';
                    subtotal.textContent = rupiah(0);

                    tdSubtotal.appendChild(subtotal);

                    // Kolom aksi.
                    const tdAksi = document.createElement('td');
                    tdAksi.className = 'text-center align-middle';

                    const hapus = document.createElement('button');
                    hapus.type = 'button';
                    hapus.className = 'btn btn-danger btn-sm btn-hapus';
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
                daftarItem.addEventListener('input', function (event) {
                    if (
                        event.target.classList.contains('input-jumlah') ||
                        event.target.classList.contains('input-harga')
                    ) {
                        hitungTotal();
                    }
                });

                // Tampilkan satuan bahan yang dipilih.
                daftarItem.addEventListener('change', function (event) {
                    if (event.target.classList.contains('pilih-bahan')) {
                        hitungTotal();
                    }
                });

                // Hapus baris pembelian.
                daftarItem.addEventListener('click', function (event) {
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
                form.addEventListener('submit', function () {
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
                    if (window.jQuery) {
                        $('#modalTambahPembelian').modal('show');
                    }
                @endif
            });
        </script>
    @endpush

</x-admin-layout-stisla>
