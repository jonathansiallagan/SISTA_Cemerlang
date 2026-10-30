@props([
    'title', 
    'icon' => 'fa-table',
    'maxHeight' => 'max-h-[calc(100vh-270px)]'
])

<div class="relative overflow-hidden rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">

    {{-- Judul Tabel (Tidak Sticky, tetap di atas tabel) --}}
    <div class="flex min-h-[70px] items-center bg-[#6777ef] px-[25px] py-2">
        <h4 class="text-base font-semibold leading-7 text-white">
            <i class="fas {{ $icon }} mr-2"></i>
            {{ $title }}
        </h4>
    </div>

    {{-- KESALAHANMU DI SINI: Ubah overflow-x-auto menjadi overflow-auto dan tambahkan maxHeight --}}
    <div class="w-full overflow-auto {{ $maxHeight }}">
        <table class="w-full text-left text-[13px]">

            {{-- THEAD STICKY --}}
            <thead class="sticky top-0 z-10 shadow-sm">
                <tr class="border-b border-[#e4e6fc] text-[10px] font-bold uppercase tracking-[1.3px] text-[#666]">
                    {{ $head }}
                </tr>
            </thead>

            {{-- TBODY --}}
            <tbody>
                {{ $slot }}
            </tbody>

        </table>
    </div>
</div>