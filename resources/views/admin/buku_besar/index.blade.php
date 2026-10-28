<x-admin-layout-stisla>
    {{-- HEADER HALAMAN --}}
    <div class="section-header">
        <h1>Buku Besar</h1>

        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active">Buku Besar</div>
        </div>
    </div>

    <div class="section-body">

        {{-- HEADER DAN TOMBOL EXPORT --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
            <div class="mb-2">
                <p class="text-muted mb-0">
                    Laporan mutasi dan saldo per akun perkiraan.
                </p>
            </div>

            @php
                $paramCetak = [
                    'akun_id' => $akun_id ?: 'semua',
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                ];
            @endphp

            <div class="d-flex flex-wrap mb-2">
                <a href="{{ route('buku_besar.pdf', $paramCetak) }}"
                   class="btn btn-danger btn-sm mr-2 mb-1">
                    <i class="fas fa-file-pdf mr-1"></i>
                    Export PDF
                </a>

                <a href="{{ route('buku_besar.excel', $paramCetak) }}"
                   class="btn btn-success btn-sm mb-1">
                    <i class="fas fa-file-excel mr-1"></i>
                    Export Excel
                </a>
            </div>
        </div>

        {{-- FORM FILTER --}}
        <div class="card mb-3">
            <div class="card-body p-3">
                <form action="{{ route('buku_besar.index') }}" method="GET">
                    <div class="form-row align-items-end">

                        {{-- PILIH AKUN --}}
                        <div class="form-group col-lg-3 col-md-6 mb-2">
                            <label for="akun_id" class="d-block mb-1">Pilih Akun</label>
                            <select name="akun_id"
                                    id="akun_id"
                                    class="form-control">
                                <option value="semua"
                                    {{ ($akun_id ?? 'semua') === 'semua' ? 'selected' : '' }}>
                                    Semua Akun
                                </option>

                                @foreach ($akuns as $akun)
                                    <option value="{{ $akun->id }}"
                                        {{ (string) ($akun_id ?? 'semua') === (string) $akun->id ? 'selected' : '' }}>
                                        {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- TANGGAL MULAI --}}
                        <div class="form-group col-lg-2 col-md-6 mb-2">
                            <label for="start_date" class="d-block mb-1">Tanggal Mulai</label>
                            <input type="date"
                                id="start_date"
                                name="start_date"
                                value="{{ $start_date }}"
                                class="form-control"
                                required>
                        </div>

                        {{-- TANGGAL AKHIR --}}
                        <div class="form-group col-lg-2 col-md-6 mb-2">
                            <label for="end_date" class="d-block mb-1">Tanggal Akhir</label>
                            <input type="date"
                                id="end_date"
                                name="end_date"
                                value="{{ $end_date }}"
                                min="{{ $start_date }}"
                                class="form-control"
                                required>
                        </div>

                        {{-- CABANG --}}
                        @if (Auth::user()->role === 'Admin Pusat')
                            <div class="form-group col-lg-2 col-md-6 mb-2">
                                <label for="cabang_id" class="d-block mb-1">Pilih Cabang</label>
                                <select name="cabang_id"
                                        id="cabang_id"
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

                        {{-- TOMBOL --}}
                        <div class="form-group col-lg-3 col-md-12 mb-2">
                            <div class="d-flex align-items-center flex-wrap">
                                <button type="submit" class="btn btn-primary mr-2 mb-1">
                                    <i class="fas fa-filter mr-1"></i>
                                    Tampilkan
                                </button>

                                <a href="{{ route('buku_besar.index') }}"
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

        {{-- HASIL LAPORAN --}}
        @if ($laporan->isNotEmpty())

            {{-- SATU TABEL UNTUK SETIAP AKUN --}}
            @foreach ($laporan as $item)
                @include('admin.buku_besar.tabel', [
                    'item' => $item,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                ])
            @endforeach

            {{-- RINGKASAN BUKU BESAR --}}
            <div class="card mt-3">
                <div class="card-header bg-primary py-2">
                    <h4 class="text-white mb-0">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Ringkasan Buku Besar
                    </h4>
                </div>

                <div class="card-body">
                    @php
                        // Saldo awal bersih: Debit positif, Kredit negatif.
                        $totalSaldoAwal = $laporan->sum(function ($item) {
                            return $item['sisiAwal'] === 'Debit'
                                ? $item['saldoAwal']
                                : -$item['saldoAwal'];
                        });

                        // Saldo akhir bersih: Debit positif, Kredit negatif.
                        $totalSaldoAkhir = $laporan->sum(function ($item) {
                            return $item['sisiAkhir'] === 'Debit'
                                ? $item['saldoAkhir']
                                : -$item['saldoAkhir'];
                        });

                        $seimbang = round($grandDebit, 2) === round($grandKredit, 2);
                    @endphp

                    <div class="row">

                        {{-- TOTAL DEBIT --}}
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Total Debit</div>
                                <h6 class="font-weight-bold text-dark mt-2 mb-0">
                                    Rp {{ number_format($grandDebit, 2, ',', '.') }}
                                </h6>
                            </div>
                        </div>

                        {{-- SALDO AWAL --}}
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Saldo Awal Bersih</div>
                                <h6 class="font-weight-bold text-dark mt-2 mb-0">
                                    Rp {{ number_format(abs($totalSaldoAwal), 2, ',', '.') }}
                                </h6>
                                <small class="text-muted">
                                    {{ $totalSaldoAwal < 0 ? 'Kredit' : 'Debit' }}
                                </small>
                            </div>
                        </div>

                        {{-- TOTAL KREDIT --}}
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Total Kredit</div>
                                <h6 class="font-weight-bold text-dark mt-2 mb-0">
                                    Rp {{ number_format($grandKredit, 2, ',', '.') }}
                                </h6>
                            </div>
                        </div>

                        {{-- SALDO AKHIR --}}
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted small">Saldo Akhir Bersih</div>
                                <h6 class="font-weight-bold text-dark mt-2 mb-0">
                                    Rp {{ number_format(abs($totalSaldoAkhir), 2, ',', '.') }}
                                </h6>
                                <small class="text-muted">
                                    {{ $totalSaldoAkhir < 0 ? 'Kredit' : 'Debit' }}
                                </small>
                            </div>
                        </div>

                    </div>

                    {{-- STATUS KESEIMBANGAN JURNAL --}}
                    <div class="border rounded p-3 d-flex flex-wrap align-items-center justify-content-between">
                        <div class="mb-2 mb-md-0">
                            <div class="font-weight-bold text-dark">
                                Status Keseimbangan Jurnal
                            </div>
                            <small class="text-muted">
                                Perbandingan total debit dan total kredit
                            </small>
                        </div>

                        @if ($seimbang)
                            <span class="badge badge-success p-2">
                                <i class="fas fa-check-circle mr-1"></i>
                                Seimbang
                            </span>
                        @else
                            <span class="badge badge-danger p-2">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Tidak Seimbang
                            </span>
                        @endif
                    </div>

                </div>
            </div>

        @elseif($akun_id)
            <div class="card">
                <div class="card-body text-center text-muted py-4">
                    <i class="fas fa-file-alt fa-2x mb-2"></i>
                    <p class="mb-0">
                        Tidak ada akun yang memiliki saldo atau mutasi pada periode ini.
                    </p>
                </div>
            </div>
        @endif

    </div>
</x-admin-layout-stisla>