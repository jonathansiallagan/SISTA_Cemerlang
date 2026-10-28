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
        <div class="row align-items-center mb-2">
            <div class="col-md-7">
                <p class="text-muted mb-0">
                    Riwayat dan rincian pengeluaran operasional perusahaan.
                </p>
            </div>

            <div class="col-md-5 text-md-right mt-1 mt-md-0">
                <a href="{{ route('pengeluaran.create') }}"
                   class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-1"></i>
                    Tambah Pengeluaran
                </a>
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
        <div class="card mb-2">
            <div class="card-body p-3">
                <form action="{{ route('pengeluaran.index') }}" method="GET">
                    <div class="form-row align-items-end">

                        {{-- TANGGAL MULAI --}}
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

                        {{-- TANGGAL AKHIR --}}
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

                        {{-- CABANG ADMIN PUSAT --}}
                        @if (Auth::user()->role === 'Admin Pusat')
                            <div class="form-group col-lg-3 col-md-6 mb-2">
                                <label for="cabang_id" class="d-block mb-1">
                                    Cabang
                                </label>

                                <select id="cabang_id"
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

                        {{-- TOMBOL FILTER DAN RESET --}}
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
                                    {{-- NOMOR URUT --}}
                                    <td class="text-center text-muted">
                                        {{ $loop->iteration }}
                                    </td>

                                    {{-- NO REFERENSI --}}
                                    <td class="font-weight-bold text-primary">
                                        {{ $pengeluaran->no_referensi }}
                                    </td>

                                    {{-- TANGGAL --}}
                                    <td class="text-nowrap">
                                        {{ $pengeluaran->tanggal->format('d M Y') }}
                                    </td>

                                    {{-- CABANG --}}
                                    <td>
                                        {{ $pengeluaran->cabang?->nama ?? '-' }}
                                    </td>

                                    {{-- KETERANGAN --}}
                                    <td>
                                        {{ $pengeluaran->keterangan ?: '-' }}
                                    </td>

                                    {{-- TOTAL PENGELUARAN --}}
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

        {{-- JUMLAH DATA --}}
        <p class="text-muted small mt-3">
            Menampilkan {{ $pengeluarans->count() }} data pengeluaran.
        </p>

    </div>
</x-admin-layout-stisla>
