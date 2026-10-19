<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Buku Besar</h1>
        <p class="text-gray-500 mt-1">Laporan mutasi dan saldo per akun perkiraan.</p>
    </div>

    <!-- FORM FILTER -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('buku_besar.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Akun</label>
                <select name="akun_id" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500" required>
                    <option value="">-- Pilih Akun --</option>
                    @foreach($akuns as $akun)
                        <option value="{{ $akun->id }}" {{ $akun_id == $akun->id ? 'selected' : '' }}>
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

    <!-- TABEL LAPORAN -->
    @if($akunTerpilih)
    @php
        // Helper tampilan (hanya format, tanpa perhitungan)
        $rp  = fn($n) => 'Rp ' . number_format($n, 0, ',', '.');
        $qty = fn($n) => rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',');
        $sn  = $akunTerpilih->saldo_normal; // 'Debit' atau 'Kredit'
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Beban {{ $akunTerpilih->kode_akun }} - {{ $akunTerpilih->nama_akun }}</h3>
                <p class="text-sm text-gray-500 mt-1">Periode: {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</p>
            </div>
            <div class="flex gap-8 text-right">
                <div>
                    <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Tipe Akun</span>
                    <div class="font-bold text-blue-600">{{ $akunTerpilih->tipe_akun }}</div>
                </div>
                <div>
                    <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Saldo Normal</span>
                    <div class="font-bold text-blue-600">{{ $sn }}</div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-bold border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-4">Tanggal</th>
                        <th class="px-4 py-4">No Referensi</th>
                        <th class="px-4 py-4">Keterangan</th>
                        <th class="px-4 py-4 text-right">Qty</th>
                        <th class="px-4 py-4">Satuan</th>
                        <th class="px-4 py-4 text-right">Harga</th>
                        <th class="px-4 py-4 text-right">Total</th>
                        <th class="px-4 py-4 text-right bg-gray-100">Saldo Normal</th>
                        <th class="px-4 py-4 text-right">Debit</th>
                        <th class="px-4 py-4 text-right">Kredit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Baris Saldo Awal -->
                    <tr class="bg-orange-50/30">
                        <td colspan="7" class="px-4 py-4 font-bold text-gray-700 text-right">Saldo Awal :</td>
                        <td class="px-4 py-4 text-right font-bold text-gray-800 bg-orange-50 whitespace-nowrap">
                            {{ $rp($saldoAwal) }} <span class="text-xs text-gray-400">({{ $sn }})</span>
                        </td>
                        <td class="px-4 py-4"></td>
                        <td class="px-4 py-4"></td>
                    </tr>

                    <!-- Baris Mutasi -->
                    @forelse($baris as $detail)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($detail->jurnal->tanggal)->format('d M Y') }}</td>
                            <td class="px-4 py-4 text-blue-600 font-medium whitespace-nowrap">{{ $detail->jurnal->no_referensi }}</td>
                            <td class="px-4 py-4">{{ $detail->jurnal->keterangan }}</td>
                            <td class="px-4 py-4 text-right">{{ $detail->kuantitas !== null ? $qty($detail->kuantitas) : '-' }}</td>
                            <td class="px-4 py-4">{{ $detail->satuan ?: '-' }}</td>
                            <td class="px-4 py-4 text-right whitespace-nowrap">{{ $detail->harga !== null ? $rp($detail->harga) : '-' }}</td>
                            <td class="px-4 py-4 text-right whitespace-nowrap">{{ $rp($detail->total) }}</td>
                            <td class="px-4 py-4 text-right font-bold text-gray-800 bg-gray-50 whitespace-nowrap">{{ $rp($detail->saldo) }}</td>
                            <td class="px-4 py-4 text-right whitespace-nowrap">{{ $detail->debit > 0 ? $rp($detail->debit) : '-' }}</td>
                            <td class="px-4 py-4 text-right whitespace-nowrap">{{ $detail->kredit > 0 ? $rp($detail->kredit) : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-400">Tidak ada mutasi transaksi pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>

                <!-- Baris Total & Saldo Akhir -->
                <tfoot class="bg-gray-50 font-bold text-gray-800 border-t-2 border-gray-200">
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-right">Total Mutasi / Saldo Akhir :</td>
                        <td class="px-4 py-4 text-right bg-gray-100 whitespace-nowrap">
                            {{ $rp($saldoAkhir) }} <span class="text-xs text-gray-400">({{ $sn }})</span>
                        </td>
                        <td class="px-4 py-4 text-right whitespace-nowrap">{{ $rp($totalDebit) }}</td>
                        <td class="px-4 py-4 text-right whitespace-nowrap">{{ $rp($totalKredit) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
</x-admin-layout>