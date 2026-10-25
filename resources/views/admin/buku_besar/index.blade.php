<x-admin-layout>
    <!-- JUDUL + TOMBOL CETAK (sudut kanan) -->
    <div class="mb-6 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Buku Besar</h1>
            <p class="text-gray-500 mt-1">Laporan mutasi dan saldo per akun perkiraan.</p>
        </div>

        @if($akun_id)
        @php
            // Tombol cetak mengikuti filter yang sedang ditampilkan
            $paramCetak = ['akun_id' => $akun_id, 'start_date' => $start_date, 'end_date' => $end_date];
        @endphp
        <div class="flex gap-2">
            <a href="{{ route('buku_besar.pdf', $paramCetak) }}"
               class="bg-red-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-red-700 transition flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> Cetak PDF
            </a>
            <a href="{{ route('buku_besar.csv', $paramCetak) }}"
               class="bg-green-600 text-white px-5 py-2 rounded-lg font-medium hover:bg-green-700 transition flex items-center gap-2">
                <i class="fa-solid fa-file-csv"></i> Cetak CSV
            </a>
        </div>
        @endif
    </div>
        @if(session('error'))
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <!-- FORM FILTER -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('buku_besar.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Akun</label>
                <select name="akun_id" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500" required>
                    <option value="semua" {{ $akun_id === 'semua' ? 'selected' : '' }}>Semua Akun</option>
                    @foreach($akuns as $akun)
                        <option value="{{ $akun->id }}" {{ (string) $akun_id === (string) $akun->id ? 'selected' : '' }}>
                            {{ $akun->kode_akun }} - {{ $akun->nama_akun }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-1/4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $start_date }}" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="w-full md:w-1/4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $end_date }}" class="w-full px-4 py-2 border rounded-lg" required>
            </div>
            <div class="w-full md:w-auto">
                <button type="submit" class="w-full bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition flex items-center gap-2 justify-center">
                    <i class="fa-solid fa-filter"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- HASIL LAPORAN -->
    @if($laporan->isNotEmpty())

        {{-- Satu tabel untuk setiap akun --}}
        @foreach($laporan as $item)
            @include('admin.buku_besar._tabel', [
                'item' => $item,
                'start_date' => $start_date,
                'end_date' => $end_date,
            ])
        @endforeach

        {{-- Ringkasan total (hanya untuk mode Semua Akun) --}}
        @if($semuaAkun)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row gap-6 justify-between">
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">Ringkasan Semua Akun</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $laporan->count() }} akun memiliki saldo atau mutasi pada periode ini.</p>
                </div>
                <div class="flex gap-8 text-right">
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total</span>
                        <div class="font-bold text-gray-800">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total Debit</span>
                        <div class="font-bold text-gray-800">Rp {{ number_format($grandDebit, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total Kredit</span>
                        <div class="font-bold text-gray-800">Rp {{ number_format($grandKredit, 0, ',', '.') }}</div>
                    </div>
                    <div>
                        <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Status</span>
                        @if(round($grandDebit, 2) === round($grandKredit, 2))
                            <div class="font-bold text-green-600">Seimbang</div>
                        @else
                            <div class="font-bold text-red-600">Tidak Seimbang</div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

    @elseif($akun_id)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center text-gray-400">
            Tidak ada akun yang memiliki saldo atau mutasi pada periode ini.
        </div>
    @endif
</x-admin-layout>