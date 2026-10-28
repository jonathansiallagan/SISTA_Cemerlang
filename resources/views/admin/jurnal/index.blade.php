<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div class="section-header">
        <h1>Jurnal Umum</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Jurnal Umum</div>
        </div>
    </div>

    <div class="section-body">

        {{-- FILTER PERIODE DAN CABANG --}}
        <div class="card mb-2">
            <div class="card-body p-3">
                <form action="{{ route('jurnal.index') }}" method="GET">
                    <div class="form-row align-items-end">

                        {{-- TANGGAL MULAI --}}
                        <div class="form-group col-lg-{{ Auth::user()->role === 'Admin Pusat' ? '3' : '4' }} col-md-6 mb-2">
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
                        <div class="form-group col-lg-{{ Auth::user()->role === 'Admin Pusat' ? '3' : '4' }} col-md-6 mb-2">
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
                                            {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                            {{ $cabang->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        {{-- TOMBOL FILTER DAN RESET --}}
                        <div class="form-group col-lg-{{ Auth::user()->role === 'Admin Pusat' ? '3' : '4' }} col-md-6 mb-2">
                            <div class="d-flex align-items-center flex-wrap">
                                <button type="submit"
                                        class="btn btn-primary mr-2 mb-1">
                                    <i class="fas fa-filter mr-1"></i>
                                    Filter
                                </button>

                                <a href="{{ route('jurnal.index') }}"
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

        {{-- NOTIFIKASI SUKSES --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show py-2 mb-3"
                 role="alert">
                <i class="fas fa-check-circle mr-1"></i>
                {{ session('success') }}

                <button type="button"
                        class="close"
                        data-dismiss="alert"
                        aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- TABEL JURNAL UMUM --}}
        <div class="card">
            <div class="card-header bg-primary py-2">
                <h4 class="text-white mb-0">
                    <i class="fas fa-book-open mr-2"></i>
                    Daftar Jurnal
                </h4>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sista">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Tanggal</th>
                                <th>No Referensi</th>
                                <th>Keterangan</th>
                                <th>Nama Akun</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Kredit</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($jurnals as $jurnal)
                                @php
                                    $nomorJurnal = $loop->iteration;
                                    $details = $jurnal->detailJurnals;

                                    /*
                                     * Kelompokkan detail berdasarkan keterangan detail.
                                     * Jika keterangan detail kosong, gunakan keterangan
                                     * header jurnal sebagai cadangan.
                                     */
                                    $groups = $details->groupBy(function ($detail) use ($jurnal) {
                                        return $detail->keterangan
                                            ?: ($jurnal->keterangan ?: 'Tanpa Keterangan');
                                    });

                                    /*
                                     * Urutkan kelompok agar keterangan HPP muncul
                                     * setelah kelompok pendapatan/penjualan.
                                     */
                                    $groups = $groups->sortBy(function ($group, $keterangan) {
                                        return str_starts_with(
                                            mb_strtolower(trim((string) $keterangan)),
                                            'hpp '
                                        ) || str_contains(
                                            mb_strtolower((string) $keterangan),
                                            'beban pokok penjualan'
                                        ) ? 1 : 0;
                                    });

                                    $jumlahBaris = $details->count();
                                    $barisGlobal = 0;
                                @endphp

                                @foreach ($groups as $keterangan => $group)
                                    @foreach ($group as $detail)
                                        <tr>
                                            
                                            {{-- TANGGAL DAN REFERENSI SEKALI PER JURNAL --}}
                                            @if ($barisGlobal === 0)
                                                <td rowspan="{{ $jumlahBaris }}"
                                                    class="text-center text-muted align-middle">
                                                    {{ $nomorJurnal }}
                                                </td>
                                                <td rowspan="{{ $jumlahBaris }}"
                                                    class="align-middle text-nowrap">
                                                    {{ \Carbon\Carbon::parse($jurnal->tanggal)->format('d M Y') }}
                                                </td>

                                                <td rowspan="{{ $jumlahBaris }}"
                                                    class="font-weight-bold text-primary align-middle">
                                                    {{ $jurnal->no_referensi }}
                                                </td>
                                            @endif

                                            {{-- KETERANGAN SEKALI PER KELOMPOK --}}
                                            @if ($loop->first)
                                                <td rowspan="{{ $group->count() }}"
                                                    class="font-weight-bold align-middle">
                                                    {{ $keterangan }}
                                                </td>
                                            @endif

                                            {{-- NAMA AKUN --}}
                                            <td class="align-middle">
                                                {{ $detail->akun->nama_akun ?? '-' }}
                                            </td>

                                            {{-- DEBIT --}}
                                            <td class="text-right align-middle text-nowrap">
                                                {{ (float) $detail->debit > 0
                                                    ? 'Rp ' . number_format((float) $detail->debit, 0, ',', '.')
                                                    : '-' }}
                                            </td>

                                            {{-- KREDIT --}}
                                            <td class="text-right align-middle text-nowrap">
                                                {{ (float) $detail->kredit > 0
                                                    ? 'Rp ' . number_format((float) $detail->kredit, 0, ',', '.')
                                                    : '-' }}
                                            </td>
                                        </tr>

                                        @php
                                            $barisGlobal++;
                                        @endphp
                                    @endforeach
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="6"
                                        class="text-center text-muted py-4">
                                        <i class="fas fa-book-open fa-2x mb-2"></i>

                                        <p class="mb-0">
                                            Belum ada pencatatan jurnal.
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
