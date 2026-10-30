@php
    // Data satu akun dari controller
    $akun = $item['akun'];
    $sn = $akun->saldo_normal;

    // Format rupiah
    $rp = fn ($n) => 'Rp ' . number_format((float) $n, 0, ',', '.');
@endphp

<div class="mb-[30px]">
    {{-- MENGGUNAKAN KOMPONEN CARD-TABLE YANG SUDAH DIBUAT SEBELUMNYA --}}
    <x-card-table title="{{ $akun->kode_akun }} - {{ $akun->nama_akun }}" icon="fa-book">
        
        {{-- KUSTOMISASI HEADER: Menambahkan Periode dan Tipe Akun di bawah Judul Standar --}}
        <div class="bg-[#6777ef] px-[25px] pb-3 text-white border-t border-white/20">
            <div class="flex flex-wrap items-center justify-between text-[13px]">
                <div class="mb-2 md:mb-0">
                    <span class="opacity-80">Periode:</span> 
                    <span class="font-semibold">{{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</span>
                </div>
                
                <div class="flex flex-wrap gap-4">
                    <div>
                        <span class="opacity-80 block text-[11px]">Tipe Akun</span>
                        <span class="font-semibold">{{ $akun->tipe_akun }}</span>
                    </div>
                    <div>
                        <span class="opacity-80 block text-[11px]">Saldo Normal</span>
                        <span class="font-semibold">{{ $sn }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- SLOT HEADER KOLOM (Wajib bg-[#fdfdff] untuk mencegah tembus pandang) --}}
        <x-slot:head>
            <th class="h-[45px] bg-[#fdfdff] px-[15px] text-center">#</th>
            <th class="h-[45px] bg-[#fdfdff] px-[15px] text-left">Tanggal</th>
            <th class="h-[45px] bg-[#fdfdff] px-[15px] text-left">No Referensi</th>
            <th class="h-[45px] bg-[#fdfdff] px-[15px] text-left">Keterangan</th>
            <th class="h-[45px] bg-[#fdfdff] px-[15px] text-right">Debit</th>
            <th class="h-[45px] bg-[#fdfdff] px-[15px] text-right">Kredit</th>
            <th class="h-[45px] bg-[#fdfdff] px-[15px] text-right">Saldo</th>
        </x-slot:head>

        {{-- SALDO AWAL --}}
        <tr class="border-b border-[#e4e6fc] bg-[#f9fafb]">
            <td colspan="4" class="px-[15px] py-3 text-right font-bold align-middle text-[#34395e]">
                Saldo Awal:
            </td>
            <td></td>
            <td></td>
            <td class="px-[15px] py-3 text-right font-bold align-middle whitespace-nowrap text-[#34395e]">
                {{ $rp($item['saldoAwal']) }}
                <span class="text-[11px] font-normal text-[#6c757d]">({{ $item['sisiAwal'] }})</span>
            </td>
        </tr>

        {{-- MUTASI TRANSAKSI --}}
        @forelse ($item['baris'] as $detail)
            <tr class="border-b border-[#f2f2f2] hover:bg-[#f9fafb] transition-colors">
                <td class="px-[15px] py-3 text-center text-[#6c757d] align-middle">
                    {{ $loop->iteration }}
                </td>

                <td class="px-[15px] py-3 align-middle whitespace-nowrap">
                    {{ \Carbon\Carbon::parse($detail->jurnal->tanggal)->format('d M Y') }}
                </td>

                <td class="px-[15px] py-3 align-middle">
                    <span class="font-bold text-[#6777ef]">
                        {{ $detail->jurnal->no_referensi }}
                    </span>
                </td>

                <td class="px-[15px] py-3 align-middle">
                    {{ $detail->keterangan_buku_besar ?: '—' }}
                </td>

                <td class="px-[15px] py-3 text-right align-middle whitespace-nowrap">
                    {{ (float) $detail->debit > 0 ? $rp($detail->debit) : '—' }}
                </td>

                <td class="px-[15px] py-3 text-right align-middle whitespace-nowrap">
                    {{ (float) $detail->kredit > 0 ? $rp($detail->kredit) : '—' }}
                </td>

                <td class="px-[15px] py-3 text-right font-bold align-middle whitespace-nowrap text-[#34395e]">
                    {{ $rp($detail->saldo) }}
                    <span class="text-[11px] font-normal text-[#6c757d]">({{ $detail->sisi_saldo }})</span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-[15px] py-6 text-center text-[#6c757d]">
                    <i class="fas fa-file-alt text-[2em] mb-2"></i>
                    <p class="mb-0">
                        Tidak ada mutasi transaksi pada periode ini.
                    </p>
                </td>
            </tr>
        @endforelse

        {{-- TOTAL MUTASI DAN SALDO AKHIR --}}
        <tr class="border-t-[2px] border-[#e4e6fc] bg-[#f9fafb]">
            <td colspan="4" class="px-[15px] py-3 text-right font-bold align-middle text-[#34395e]">
                Total Mutasi / Saldo Akhir:
            </td>

            <td class="px-[15px] py-3 text-right font-bold align-middle whitespace-nowrap text-[#34395e]">
                {{ $rp($item['totalDebit']) }}
            </td>

            <td class="px-[15px] py-3 text-right font-bold align-middle whitespace-nowrap text-[#34395e]">
                {{ $rp($item['totalKredit']) }}
            </td>

            <td class="px-[15px] py-3 text-right font-bold align-middle whitespace-nowrap text-[#6777ef]">
                {{ $rp($item['saldoAkhir']) }}
                <span class="text-[11px] font-normal text-[#6c757d]">({{ $item['sisiAkhir'] }})</span>
            </td>
        </tr>
    </x-card-table>
</div>