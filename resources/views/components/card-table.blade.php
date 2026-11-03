@props(['title', 'icon' => 'fa-table', 'maxHeight' => 'max-h-[calc(100vh-270px)]'])

{{-- Wrapper Card: Latar gelap, tambah border tipis untuk depth di malam hari --}}
<div
    class="relative overflow-hidden rounded-[3px] bg-white dark:bg-[#1f2937] shadow-[0_4px_8px_rgba(0,0,0,0.03)] dark:shadow-none dark:border dark:border-[#374151]">

    {{-- Judul Tabel: Biru saat terang, gelap kehitaman saat malam --}}
    <div
        class="flex min-h-[70px] items-center bg-[#041C88] dark:bg-[#111827] dark:border-b dark:border-[#374151] px-[25px] py-2">
        <h4 class="text-base font-semibold leading-7 text-white">
            <i class="fas {{ $icon }} mr-2"></i>
            {{ $title }}
        </h4>
    </div>

    <div class="w-full overflow-auto {{ $maxHeight }}">
        {{-- 
            MAGIC CLASS: 
            Mendeteksi semua <th>, <td>, dan <tr> yang dikirim dari $slot dan $head, 
            lalu memaksanya menjadi gelap secara otomatis tanpa harus mengedit file view aslinya.
        --}}
        <table
            class="w-full text-left text-[13px] dark:text-[#a0aec0] [&_th]:dark:bg-[#1f2937] [&_th]:dark:border-b [&_th]:dark:border-[#374151] [&_th]:dark:text-[#98a6ad] [&_tr:nth-child(even)]:dark:bg-[#111827] [&_tr:hover]:dark:bg-[#374151]">

            {{-- THEAD STICKY --}}
            <thead class="sticky top-0 z-10 shadow-sm">
                <tr
                    class="border-b border-[#e4e6fc] dark:border-[#374151] text-[10px] font-bold uppercase tracking-[1.3px] text-[#666] dark:text-[#98a6ad]">
                    {{ $head }}
                </tr>
            </thead>

            {{-- TBODY --}}
            <tbody class="divide-y divide-transparent dark:divide-[#374151]/50">
                {{ $slot }}
            </tbody>

        </table>
    </div>
</div>
