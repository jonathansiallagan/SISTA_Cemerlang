<x-admin-layout-stisla>
    {{-- HEADER HALAMAN --}}
    <div class="section-header">
        <h1>Riwayat Transaksi</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item">
                <a href="{{ route('transaksi.index') }}">Transaksi</a>
            </div>
            <div class="breadcrumb-item active">Riwayat Transaksi</div>
        </div>
    </div>

    <div class="section-body">

        {{-- DESKRIPSI DAN EXPORT --}}
        <div class="row align-items-center mb-2">
            <div class="col-md-6">
                <p class="text-muted mb-0">
                    Riwayat transaksi dan rincian produk.
                </p>
            </div>

            <div class="col-md-6 text-md-right mt-1 mt-md-0">
                <a href="{{ route('transaksi.export.pdf', request()->query()) }}"
                   class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf mr-1"></i> Export PDF
                </a>

                <a href="{{ route('transaksi.export.excel', request()->query()) }}"
                   class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 mb-3">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show py-2 mb-3">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- FILTER TRANSAKSI: RINGKAS --}}
        <div class="card mb-2">
            <div class="card-body py-3 px-4">
                <form action="{{ route('transaksi.index') }}" method="GET">
                    <div class="form-row align-items-end">

                        {{-- TANGGAL MULAI --}}
                        <div class="form-group col-lg-3 col-md-6 mb-2">
                            <label for="tanggal_mulai" class="mb-1">
                                Tanggal Mulai
                            </label>
                            <input type="date"
                                   id="tanggal_mulai"
                                   name="tanggal_mulai"
                                   value="{{ request('tanggal_mulai') }}"
                                   class="form-control form-control-sm">
                        </div>

                        {{-- TANGGAL AKHIR --}}
                        <div class="form-group col-lg-3 col-md-6 mb-2">
                            <label for="tanggal_akhir" class="mb-1">
                                Tanggal Akhir
                            </label>
                            <input type="date"
                                   id="tanggal_akhir"
                                   name="tanggal_akhir"
                                   value="{{ request('tanggal_akhir') }}"
                                   class="form-control form-control-sm">
                        </div>

                        {{-- CABANG --}}
                        @if (auth()->user()->role === 'Admin Pusat')
                            <div class="form-group col-lg-3 col-md-6 mb-2">
                                <label for="cabang_id" class="mb-1">
                                    Cabang
                                </label>
                                <select id="cabang_id"
                                        name="cabang_id"
                                        class="form-control form-control-sm">
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

                        {{-- TOMBOL --}}
                        <div class="form-group col-lg-3 col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <button type="submit"
                                        class="btn btn-primary btn-sm mr-2">
                                    <i class="fas fa-filter mr-1"></i> Filter
                                </button>

                                <a href="{{ route('transaksi.index') }}"
                                   class="btn btn-light btn-sm">
                                    <i class="fas fa-redo mr-1"></i> Reset
                                </a>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        {{-- TABEL RIWAYAT TRANSAKSI --}}
        <div class="card">
            <div class="card-header bg-primary py-2">
                <h4 class="text-white mb-0">
                    <i class="fas fa-table mr-2"></i>
                    Data Riwayat Transaksi
                </h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sista">
                        <thead>
                            <tr>
                                <th class="text-center text-nowrap">#</th>
                                <th class="text-nowrap">No Referensi</th>
                                <th class="text-nowrap">Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Cabang</th>
                                <th>Nama Produk</th>
                                <th class="text-right">Qty</th>
                                <th>Satuan</th>
                                <th class="text-right text-nowrap">Total Transaksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($transaksis as $trx)
                                @php
                                    $details = $trx->detailTransaksis;
                                    $jumlahBaris = $details->count();
                                @endphp

                                @forelse ($details as $detail)
                                    <tr>
                                        @if ($loop->first)
                                            {{-- NOMOR URUT --}}
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="text-center align-middle text-muted">
                                                {{ $loop->parent->iteration }}
                                            </td>

                                            {{-- NO REFERENSI --}}
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="align-middle font-weight-bold text-primary text-nowrap">
                                                {{ $trx->no_referensi }}
                                            </td>

                                            {{-- TANGGAL --}}
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="align-middle text-nowrap">
                                                {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}
                                            </td>

                                            {{-- PELANGGAN --}}
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="align-middle">
                                                {{ $trx->nama_pelanggan }}
                                            </td>

                                            {{-- CABANG --}}
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="align-middle">
                                                {{ $trx->cabang->nama ?? '-' }}
                                            </td>
                                        @endif

                                        {{-- NAMA PRODUK --}}
                                        <td class="align-middle">
                                            {{ $detail->produk->nama_produk ?? 'Produk tidak ditemukan' }}
                                        </td>

                                        {{-- QUANTITY --}}
                                        <td class="text-right align-middle text-nowrap">
                                            @php
                                                $satuanProduk = strtolower(
                                                    trim($detail->produk->satuan ?? '')
                                                );

                                                $meteran = $satuanProduk === 'm2'
                                                    || $satuanProduk === 'meter persegi'
                                                    || str_contains($satuanProduk, 'm²');

                                                $qty = $meteran && (float) $detail->luas > 0
                                                    ? (float) $detail->luas
                                                    : (float) $detail->jumlah;
                                            @endphp

                                            {{ rtrim(rtrim(number_format($qty, 2, ',', '.'), '0'), ',') }}
                                        </td>

                                        {{-- SATUAN --}}
                                        <td class="align-middle text-nowrap">
                                            {{ $detail->produk->satuan ?? '-' }}
                                        </td>

                                        {{-- TOTAL TRANSAKSI --}}
                                        @if ($loop->first)
                                            <td rowspan="{{ $jumlahBaris }}"
                                                class="text-right align-middle font-weight-bold text-nowrap">
                                                Rp {{ number_format((float) $trx->total_transaksi, 0, ',', '.') }}
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-center align-middle text-muted">
                                            {{ $loop->parent->iteration }}
                                        </td>

                                        <td class="align-middle font-weight-bold text-primary text-nowrap">
                                            {{ $trx->no_referensi }}
                                        </td>

                                        <td class="align-middle text-nowrap">
                                            {{ \Carbon\Carbon::parse($trx->tanggal)->format('d M Y') }}
                                        </td>

                                        <td class="align-middle">
                                            {{ $trx->nama_pelanggan }}
                                        </td>

                                        <td class="align-middle">
                                            {{ $trx->cabang->nama ?? '-' }}
                                        </td>

                                        <td colspan="3" class="align-middle text-muted">
                                            Belum ada detail produk.
                                        </td>

                                        <td class="text-right align-middle font-weight-bold text-nowrap">
                                            Rp {{ number_format((float) $trx->total_transaksi, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        <i class="fas fa-receipt fa-2x mb-2"></i>
                                        <p class="mb-0">
                                            Belum ada transaksi untuk filter yang dipilih.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</x-admin-layout-stisla>
