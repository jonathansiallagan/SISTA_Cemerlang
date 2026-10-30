<x-admin-layout-stisla>
    {{-- HEADER HALAMAN --}}
    <div
        class="mb-[30px] flex flex-wrap items-center gap-x-4 gap-y-2 rounded-[3px] bg-white px-[30px] py-5 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
        <h1 class="text-2xl font-bold text-[#34395e]">Buku Besar</h1>

        <ol class="ml-auto flex items-center text-[13px] text-[#6c757d]">
            <li class="font-semibold text-[#6777ef]">Buku Besar</li>
        </ol>
    </div>

    <div>

        {{-- HEADER DAN TOMBOL EXPORT --}}
        <div class="mb-4 flex flex-wrap items-center justify-between">
            <div class="mb-2">
                <p class="text-[#6c757d]">
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

            <div class="mb-2 flex flex-wrap">
                <a href="{{ route('buku_besar.pdf', $paramCetak) }}"
                    class="mb-1 mr-2 inline-flex items-center rounded-[3px] bg-[#fc544b] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#fb160a]">
                    <i class="fas fa-file-pdf mr-1"></i>
                    Export PDF
                </a>

                <a href="{{ route('buku_besar.excel', $paramCetak) }}"
                    class="mb-1 inline-flex items-center rounded-[3px] bg-[#54ca68] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#3fb556]">
                    <i class="fas fa-file-excel mr-1"></i>
                    Export Excel
                </a>
            </div>
        </div>

        {{-- FORM FILTER --}}
        <div class="mb-4 rounded-[3px] bg-white p-4 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
            <form action="{{ route('buku_besar.index') }}" method="GET"
                class="grid grid-cols-1 items-end gap-x-2.5 gap-y-2 md:grid-cols-2 lg:grid-cols-12">

                {{-- PILIH AKUN --}}
                <div class="lg:col-span-3">
                    <label for="akun_id" class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Pilih Akun
                    </label>
                    <select name="akun_id" id="akun_id"
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                        <option value="semua" {{ ($akun_id ?? 'semua') === 'semua' ? 'selected' : '' }}>
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
                <div class="lg:col-span-2">
                    <label for="start_date" class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Tanggal Mulai
                    </label>
                    <input type="date" id="start_date" name="start_date" value="{{ $start_date }}" required
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                </div>

                {{-- TANGGAL AKHIR --}}
                <div class="lg:col-span-2">
                    <label for="end_date" class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Tanggal Akhir
                    </label>
                    <input type="date" id="end_date" name="end_date" value="{{ $end_date }}"
                        min="{{ $start_date }}" required
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                </div>

                {{-- CABANG --}}
                @if (Auth::user()->role === 'Admin Pusat')
                    <div class="lg:col-span-2">
                        <label for="cabang_id"
                            class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Pilih Cabang
                        </label>
                        <select name="cabang_id" id="cabang_id"
                            class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
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
                <div class="md:col-span-2 lg:col-span-3">
                    <div class="flex flex-wrap items-center">
                        <button type="submit"
                            class="mb-1 mr-2 inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
                            <i class="fas fa-filter mr-1"></i>
                            Tampilkan
                        </button>

                        <a href="{{ route('buku_besar.index') }}"
                            class="mb-1 inline-flex items-center rounded-[3px] bg-[#e3eaef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                            <i class="fas fa-redo mr-1"></i>
                            Reset
                        </a>
                    </div>
                </div>

            </form>
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
            <div class="mt-4 overflow-hidden rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
                <div class="flex min-h-[70px] items-center bg-[#6777ef] px-[25px] py-2">
                    <h4 class="text-base font-semibold leading-7 text-white">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Ringkasan Buku Besar
                    </h4>
                </div>

                <div class="p-[25px]">
                    @php
                        // Saldo awal bersih: Debit positif, Kredit negatif.
                        $totalSaldoAwal = $laporan->sum(function ($item) {
                            return $item['sisiAwal'] === 'Debit' ? $item['saldoAwal'] : -$item['saldoAwal'];
                        });

                        // Saldo akhir bersih: Debit positif, Kredit negatif.
                        $totalSaldoAkhir = $laporan->sum(function ($item) {
                            return $item['sisiAkhir'] === 'Debit' ? $item['saldoAkhir'] : -$item['saldoAkhir'];
                        });

                        $seimbang = round($grandDebit, 2) === round($grandKredit, 2);
                    @endphp

                    <div class="mb-4 grid grid-cols-1 gap-x-[30px] gap-y-4 md:grid-cols-2 lg:grid-cols-4">

                        {{-- TOTAL DEBIT --}}
                        <div class="h-full rounded border border-[#dee2e6] p-4">
                            <div class="text-xs text-[#6c757d]">Total Debit</div>
                            <h6 class="mt-2 text-sm font-bold text-[#191d21]">
                                Rp {{ number_format($grandDebit, 2, ',', '.') }}
                            </h6>
                        </div>

                        {{-- SALDO AWAL --}}
                        <div class="h-full rounded border border-[#dee2e6] p-4">
                            <div class="text-xs text-[#6c757d]">Saldo Awal Bersih</div>
                            <h6 class="mt-2 text-sm font-bold text-[#191d21]">
                                Rp {{ number_format(abs($totalSaldoAwal), 2, ',', '.') }}
                            </h6>
                            <small class="text-[#6c757d]">
                                {{ $totalSaldoAwal < 0 ? 'Kredit' : 'Debit' }}
                            </small>
                        </div>

                        {{-- TOTAL KREDIT --}}
                        <div class="h-full rounded border border-[#dee2e6] p-4">
                            <div class="text-xs text-[#6c757d]">Total Kredit</div>
                            <h6 class="mt-2 text-sm font-bold text-[#191d21]">
                                Rp {{ number_format($grandKredit, 2, ',', '.') }}
                            </h6>
                        </div>

                        {{-- SALDO AKHIR --}}
                        <div class="h-full rounded border border-[#dee2e6] p-4">
                            <div class="text-xs text-[#6c757d]">Saldo Akhir Bersih</div>
                            <h6 class="mt-2 text-sm font-bold text-[#191d21]">
                                Rp {{ number_format(abs($totalSaldoAkhir), 2, ',', '.') }}
                            </h6>
                            <small class="text-[#6c757d]">
                                {{ $totalSaldoAkhir < 0 ? 'Kredit' : 'Debit' }}
                            </small>
                        </div>

                    </div>

                    {{-- STATUS KESEIMBANGAN JURNAL --}}
                    <div
                        class="flex flex-wrap items-center justify-between rounded border border-[#dee2e6] p-4">
                        <div class="mb-2 md:mb-0">
                            <div class="font-bold text-[#191d21]">
                                Status Keseimbangan Jurnal
                            </div>
                            <small class="text-[#6c757d]">
                                Perbandingan total debit dan total kredit
                            </small>
                        </div>

                        @if ($seimbang)
                            <span
                                class="inline-flex items-center rounded-full bg-[#54ca68] px-3 py-2 text-xs font-semibold tracking-[0.3px] text-white">
                                <i class="fas fa-check-circle mr-1"></i>
                                Seimbang
                            </span>
                        @else
                            <span
                                class="inline-flex items-center rounded-full bg-[#fc544b] px-3 py-2 text-xs font-semibold tracking-[0.3px] text-white">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                Tidak Seimbang
                            </span>
                        @endif
                    </div>

                </div>
            </div>
        @elseif($akun_id)
            <div class="rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
                <div class="px-[25px] py-6 text-center text-[#6c757d]">
                    <i class="fas fa-file-alt text-[2em]"></i>
                    <p>
                        Tidak ada akun yang memiliki saldo atau mutasi pada periode ini.
                    </p>
                </div>
            </div>
        @endif

    </div>
</x-admin-layout-stisla>