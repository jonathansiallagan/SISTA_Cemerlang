@props([
    'action' => '',
    'startDate' => now()->toDateString(),
    'endDate' => now()->toDateString(),
    'cabangs' => null,
    'showCabang' => false,
    'extraInputs' => null,
])

@php
    $adalahPusat = $showCabang && auth()->check() && auth()->user()->role === 'Admin Pusat';
    $hasExtra = isset($extraInputs) && !$extraInputs->isEmpty();

    $lgCols = 'lg:grid-cols-4';
    if ($adalahPusat && $hasExtra) {
        $lgCols = 'lg:grid-cols-5';
    } elseif (!$adalahPusat && !$hasExtra) {
        $lgCols = 'lg:grid-cols-3';
    }

    // Konfigurasi bentangan kolom (col-span) untuk pesan error agar selebar form
    $errorColSpan =
        $lgCols === 'lg:grid-cols-5'
            ? 'lg:col-span-5'
            : ($lgCols === 'lg:grid-cols-4'
                ? 'lg:col-span-4'
                : 'lg:col-span-3');
@endphp

<div class="mb-4 overflow-x-hidden rounded-[3px] bg-white p-4 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
    <form action="{{ $action }}" method="GET"
        class="filter-date-form grid grid-cols-1 items-end gap-x-2.5 gap-y-3 sm:grid-cols-2 {{ $lgCols }}">

        {{-- TANGGAL MULAI --}}
        <div class="w-full">
            <label for="start_date" class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                Tanggal Mulai / Dari Tanggal
            </label>
            <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" required
                class="filter-start-date h-[42px] w-full max-w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
        </div>

        {{-- TANGGAL AKHIR --}}
        <div class="w-full">
            <label for="end_date" class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                Tanggal Akhir / Sampai Tanggal
            </label>
            <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" required
                class="filter-end-date h-[42px] w-full max-w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
        </div>

        {{-- FILTER CABANG (KHUSUS ADMIN PUSAT) --}}
        @if ($adalahPusat)
            <div class="w-full">
                <label for="cabang_id" class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                    Cabang
                </label>
                <select id="cabang_id" name="cabang_id"
                    class="h-[42px] w-full max-w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                    <option value="" @selected(empty(request('cabang_id')))>Semua Cabang</option>
                    @if ($cabangs)
                        @foreach ($cabangs as $cabang)
                            <option value="{{ $cabang->id }}" @selected((string) request('cabang_id') === (string) $cabang->id)>
                                {{ $cabang->nama }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
        @endif

        @if ($hasExtra)
            {{ $extraInputs }}
        @endif

        {{-- TOMBOL AKSI --}}
        <div class="flex h-[42px] w-full items-center gap-2 sm:col-span-2 lg:col-span-1">
            <button type="submit"
                class="inline-flex h-[42px] w-full items-center justify-center rounded-[3px] bg-[#041C88] px-3 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
                <i class="fas fa-filter mr-1"></i> Filter
            </button>

            <a href="{{ $action }}"
                class="inline-flex h-[42px] w-full items-center justify-center rounded-[3px] bg-[#e3eaef] px-3 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                <i class="fas fa-redo mr-1"></i> Reset
            </a>
        </div>

        {{-- PESAN ERROR INLINE KUSTOM (DISembunyikan secara default) --}}
        <div
            class="date-error-message hidden w-full rounded-[3px] bg-[#fee2e2] px-4 py-3 text-sm text-[#991b1b] border border-[#f87171] sm:col-span-2 {{ $errorColSpan }}">
            <i class="fas fa-exclamation-circle mr-1"></i>
            <span class="error-text">Tanggal Mulai tidak boleh lebih besar dari Tanggal Akhir! Sistem telah
                menyesuaikannya ke awal dan akhir bulan. Silakan klik Filter kembali.</span>
        </div>
    </form>
</div>

@once
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.filter-date-form').forEach(function(form) {
                const startInput = form.querySelector('.filter-start-date');
                const endInput = form.querySelector('.filter-end-date');
                const errorMessage = form.querySelector('.date-error-message');

                // Sembunyikan error inline kembali jika pengguna mulai mengubah input tanggal secara manual
                if (startInput && endInput) {
                    startInput.addEventListener('change', () => errorMessage.classList.add('hidden'));
                    endInput.addEventListener('change', () => errorMessage.classList.add('hidden'));
                }

                form.addEventListener('submit', function(e) {
                    if (startInput && endInput && startInput.value && endInput.value) {
                        if (startInput.value > endInput.value) {
                            e.preventDefault(); // Hentikan pengiriman form

                            // Tampilkan pesan peringatan inline kustom
                            errorMessage.classList.remove('hidden');

                            // Eksekusi logika penyesuaian tanggal ke awal dan akhir bulan
                            const startDateObj = new Date(startInput.value);
                            const year = startDateObj.getFullYear();
                            const month = String(startDateObj.getMonth() + 1).padStart(2, '0');

                            // Mencari hari terakhir pada bulan yang dipilih
                            const lastDayObj = new Date(year, startDateObj.getMonth() + 1, 0);
                            const lastDay = String(lastDayObj.getDate()).padStart(2, '0');

                            // Update nilai di dalam input form
                            startInput.value = `${year}-${month}-01`;
                            endInput.value = `${year}-${month}-${lastDay}`;

                            // Menghilangkan pesan error inline secara otomatis setelah 7 detik
                            setTimeout(() => {
                                errorMessage.classList.add('hidden');
                            }, 7000);
                        }
                    }
                });
            });
        });
    </script>
@endonce
