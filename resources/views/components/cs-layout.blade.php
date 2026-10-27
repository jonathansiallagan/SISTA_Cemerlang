<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SISTA - Customer Service</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="min-h-screen bg-gray-50 font-sans text-slate-800">

    @php
        $user = auth()->user();

        // Avatar menggunakan URL yang tersedia pada model user.
        $avatarUrl = $user->avatar_url
            ?? $user->avatar
            ?? null;
    @endphp

    {{-- NAVBAR CS --}}
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white shadow-sm">
        <div class="mx-auto flex min-h-[76px] max-w-screen-2xl items-center justify-between gap-4 px-5 md:px-8">

            {{-- LOGO --}}
            <a href="{{ route('cs.dashboard') }}"
               class="flex shrink-0 items-center gap-3">

                <div class="flex h-11 w-11 items-center justify-center rounded-xl
                            bg-gradient-to-br from-indigo-500 via-blue-500 to-cyan-400
                            shadow-md shadow-blue-500/20">
                    <div class="flex h-10 w-10 items-center justify-center rounded-[9px] bg-white">
                        <img src="{{ asset('images/logo-cemerlang.png') }}"
                             alt="Logo CV Cemerlang"
                             class="h-full w-full object-contain">
                    </div>
                </div>

                <div>
                    <h1 class="text-lg font-extrabold leading-5 tracking-tight text-blue-700">
                        SISTA
                    </h1>

                    <p class="mt-1 flex items-center gap-1.5 text-[10px] font-medium text-slate-500">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        CV Cemerlang
                    </p>
                </div>
            </a>

            {{-- MENU NAVIGASI CS --}}
            <nav class="flex flex-1 items-center justify-center gap-3 text-sm font-semibold md:gap-6">

                <a href="{{ route('cs.dashboard') }}"
                   class="whitespace-nowrap rounded-lg px-3 py-2 transition
                   {{ request()->routeIs('cs.dashboard')
                        ? 'bg-blue-50 text-blue-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }}">
                    <i class="fa-solid fa-house mr-1"></i>
                    <span class="hidden sm:inline">Dashboard</span>
                </a>

                <a href="{{ route('transaksi.create') }}"
                   class="whitespace-nowrap rounded-lg px-3 py-2 transition
                   {{ request()->routeIs('transaksi.create')
                        ? 'bg-blue-50 text-blue-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }}">
                    <i class="fa-solid fa-cart-shopping mr-1"></i>
                    Transaksi
                </a>

                <a href="{{ route('transaksi.index') }}"
                   class="whitespace-nowrap rounded-lg px-3 py-2 transition
                   {{ request()->routeIs('transaksi.index')
                        ? 'bg-blue-50 text-blue-700'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }}">
                    <i class="fa-solid fa-clock-rotate-left mr-1"></i>
                    <span class="hidden sm:inline">Riwayat Transaksi</span>
                    <span class="sm:hidden">Riwayat</span>
                </a>

            </nav>

            {{-- PROFIL DAN DROPDOWN --}}
            <details class="relative shrink-0">
                <summary class="flex list-none cursor-pointer items-center gap-3 rounded-full p-1 transition hover:bg-slate-50">

                    <div class="hidden text-right sm:block">
                        <p class="text-xs font-bold text-slate-800">
                            {{ $user->name }}
                        </p>

                        <p class="text-[10px] font-semibold text-blue-600">
                            {{ $user->role }}
                        </p>
                    </div>

                    <div class="relative h-10 w-10 shrink-0">
                        @if ($avatarUrl)
                            <img src="{{ $avatarUrl }}"
                                 alt="Avatar"
                                 class="h-full w-full rounded-full border-2 border-blue-100 bg-blue-50 object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center rounded-full
                                        border-2 border-blue-100 bg-blue-50 font-bold text-blue-700">
                                {{ strtoupper(substr($user->name ?? 'C', 0, 1)) }}
                            </div>
                        @endif

                        <span class="absolute bottom-0 right-0 h-2.5 w-2.5
                                     rounded-full border-2 border-white bg-emerald-400">
                        </span>
                    </div>
                </summary>

                <div class="absolute right-0 top-12 z-50 w-48
                            divide-y divide-slate-100 overflow-hidden
                            rounded-xl border border-slate-200 bg-white shadow-xl">

                    <a href="{{ route('profile') }}"
                       class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50">
                        <i class="fa-regular fa-user w-5 text-slate-500"></i>
                        Profil
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button type="submit"
                                class="flex w-full items-center gap-3 px-4 py-3
                                       text-left text-sm text-red-600 transition hover:bg-red-50">
                            <i class="fa-solid fa-right-from-bracket w-5"></i>
                            Keluar
                        </button>
                    </form>
                </div>
            </details>

        </div>
    </header>

    {{-- KONTEN HALAMAN --}}
    <main class="mx-auto min-h-[calc(100vh-76px)] max-w-screen-2xl px-5 py-6 md:px-8 md:py-8">
        {{ $slot }}
    </main>

    {{-- FOOTER --}}
    <footer class="border-t border-slate-200 bg-white px-5 py-4 text-xs text-slate-400 md:px-8">
        <div class="mx-auto flex max-w-screen-2xl items-center justify-between gap-3">
            <span>SISTA © {{ date('Y') }} CV Cemerlang</span>
            <span>Customer Service</span>
        </div>
    </footer>

</body>
</html>