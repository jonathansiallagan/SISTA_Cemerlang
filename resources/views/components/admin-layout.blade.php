<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor {{ $user->role }} - SISTA CV Cemerlang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="grid h-screen w-full grid-cols-1 md:grid-cols-[250px_1fr] grid-rows-[76px_1fr] overflow-hidden bg-slate-100 font-sans text-slate-800">

    {{-- ==================== 1. LOGO & NAMA SISTEM ==================== --}}
    <div class="hidden md:flex col-start-1 row-start-1 items-center gap-3 border-b border-r border-slate-200 bg-white px-4">
        <div class="shrink-0 rounded-xl bg-gradient-to-br from-indigo-500 via-blue-500 to-cyan-400 p-[3px] shadow-md shadow-blue-500/20">
            <div class="flex h-10 w-10 items-center justify-center rounded-[9px] bg-white p-1">
                <img src="{{ asset('images/logo-cemerlang.png') }}" alt="Logo CV Cemerlang" class="h-full w-full object-contain">
            </div>
        </div>
        <div class="min-w-0">
            <h1 class="text-lg font-extrabold leading-5 tracking-tight text-blue-700">SISTA</h1>
            <p class="mt-1 flex items-center gap-1.5 whitespace-nowrap text-[10px] font-medium text-slate-500">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span> CV Cemerlang
            </p>
        </div>
    </div>

    {{-- ==================== 2. NAVBAR ==================== --}}
    <header class="col-start-1 md:col-start-2 row-start-1 flex items-center justify-between gap-4 border-b border-slate-200 bg-white px-5 shadow-sm md:px-8">
        <div class="flex flex-wrap items-center gap-3">
            <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Sistem Aktif
            </span>
            <p class="hidden sm:block text-sm text-slate-600">
                Selamat datang, <span class="font-bold text-blue-700">{{ $user->name }}!</span>
            </p>
        </div>

        <details class="relative shrink-0">
            <summary class="flex list-none cursor-pointer items-center gap-3 rounded-full p-1 transition hover:bg-slate-50">
                <div class="hidden text-right sm:block">
                    <p class="text-xs font-bold text-slate-800">{{ $user->name }}</p>
                    <p class="text-[10px] font-semibold text-blue-600">{{ $user->role }}</p>
                </div>
                <div class="relative h-10 w-10 shrink-0">
                    <img src="{{ $avatarUrl }}" alt="Avatar" class="h-full w-full rounded-full border-2 border-blue-100 bg-blue-50 object-cover transition hover:opacity-90">
                    <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-white bg-emerald-400"></span>
                </div>
            </summary>
            
            <div class="absolute right-0 top-12 z-50 w-48 divide-y divide-slate-100 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50">
                    <i class="fa-regular fa-user w-5 text-slate-500"></i> Profil
                </a>
                <button type="button" onclick="openLogoutModal()" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-red-600 transition hover:bg-red-50">
                    <i class="fa-solid fa-right-from-bracket w-5"></i> Keluar
                </button>
            </div>
        </details>
    </header>

    {{-- ==================== 3. SIDEBAR ==================== --}}
    <aside class="hidden md:flex col-start-1 row-start-2 flex-col justify-between overflow-y-auto bg-[#19294b] text-white scrollbar-thin">
        <div class="flex-1 space-y-8 px-3 py-6">
            @foreach ($menuSections as $section)
                @continue(($section['visible'] ?? true) === false)

                <section>
                    <div class="mb-3 flex items-center justify-between px-3">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $section['title'] }}</span>
                        <span class="h-1.5 w-1.5 rounded-full {{ $section['dot'] }}"></span>
                    </div>

                    <nav class="flex flex-col gap-1.5">
                        @foreach ($section['items'] as $item)
                            @continue(($item['visible'] ?? true) === false)

                            {{-- Pengecekan status rute aktif --}}
                            @php
                                $isActive = $item['active'] ?? request()->routeIs(Str::beforeLast($item['route'], '.') . '.*');
                            @endphp

                            <a href="{{ route($item['route']) }}" @class([$theme['linkBase'], $theme['linkActive'] => $isActive, $theme['linkIdle'] => ! $isActive])>
                                <i class="fa-solid {{ $item['icon'] }} w-5 text-center"></i>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        @endforeach
                    </nav>
                </section>
            @endforeach
        </div>

        <div class="border-t border-white/10 px-4 py-4 shrink-0">
            <p class="text-center text-[10px] text-slate-500">SISTA © {{ date('Y') }} CV Cemerlang</p>
        </div>
    </aside>

    {{-- ==================== 4. MAIN CONTENT ==================== --}}
    <main class="col-start-1 md:col-start-2 row-start-2 overflow-y-auto bg-gradient-to-br from-slate-50 via-blue-50/40 to-indigo-50/40 p-4 md:p-8 relative">
        <div class="mx-auto w-full max-w-[1600px]">
            {{ $slot }}
        </div>
    </main>

    {{-- Modal logout global --}}
    <x-logout-modal />

</body>
</html>