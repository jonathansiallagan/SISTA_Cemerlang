
@props(['fullwidth' => false])

@php
    $user = auth()->user();

    $avatarUrl = $user->avatar_url
        ?? $user->avatar
        ?? null;
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SISTA - Customer Service</title>

    {{-- Tailwind CSS dan JavaScript --}}
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')
</head>

<body class="min-h-screen bg-gray-50 font-sans text-gray-800">

    <div id="app" class="flex min-h-screen flex-col">

        {{-- ================= NAVBAR CS ================= --}}
        <header id="cs-navbar"
                class="sticky top-0 z-50 border-b border-gray-200 bg-white shadow-sm">

            <div class="mx-auto flex min-h-[76px] max-w-screen-2xl flex-wrap items-center justify-between gap-4 px-5 py-3 md:px-8">

                {{-- LOGO --}}
                <a href="{{ route('cs.dashboard') }}"
                   class="flex shrink-0 items-center gap-3 no-underline">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 via-blue-500 to-cyan-400 p-1 shadow-md shadow-blue-200">
                        <div class="flex h-full w-full items-center justify-center rounded-lg bg-white">
                            <img src="{{ asset('images/logo-cemerlang.png') }}"
                                 alt="Logo CV Cemerlang"
                                 class="h-full w-full object-contain">
                        </div>
                    </div>

                    <div>
                        <h1 class="m-0 text-lg font-extrabold leading-5 tracking-tight text-blue-700">
                            SISTA
                        </h1>

                        <p class="mt-1 flex items-center gap-1.5 text-[10px] font-medium leading-4 text-slate-500">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            CV Cemerlang
                        </p>
                    </div>
                </a>

                {{-- MENU NAVIGASI --}}
                <nav class="flex flex-1 items-center justify-center gap-1 text-sm font-semibold sm:gap-3 md:gap-5">

                    {{-- DASHBOARD --}}
                    <a href="{{ route('cs.dashboard') }}"
                       class="rounded-lg px-2 py-2 transition sm:px-3
                       {{ request()->routeIs('cs.dashboard')
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }}">

                        <i class="fa-solid fa-house mr-1"></i>
                        <span class="hidden sm:inline">Dashboard</span>
                    </a>

                    {{-- TRANSAKSI --}}
                    <a href="{{ route('transaksi.create') }}"
                       class="rounded-lg px-2 py-2 transition sm:px-3
                       {{ request()->routeIs('transaksi.create')
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }}">

                        <i class="fa-solid fa-cart-shopping mr-1"></i>
                        Transaksi
                    </a>

                    {{-- RIWAYAT --}}
                    <a href="{{ route('transaksi.index') }}"
                       class="rounded-lg px-2 py-2 transition sm:px-3
                       {{ request()->routeIs('transaksi.index')
                            ? 'bg-blue-50 text-blue-700'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-blue-700' }}">

                        <i class="fa-solid fa-clock-rotate-left mr-1"></i>
                        <span class="hidden sm:inline">Riwayat Transaksi</span>
                        <span class="sm:hidden">Riwayat</span>
                    </a>

                </nav>

                {{-- PROFIL DAN DROPDOWN --}}
                <details class="group relative shrink-0">

                    <summary class="flex cursor-pointer list-none items-center gap-3 rounded-full p-1 transition hover:bg-slate-50">

                        {{-- INFORMASI PROFIL --}}
                        <div class="hidden text-right sm:block">
                            <p class="text-xs font-bold leading-4 text-slate-800">
                                {{ $user->name }}
                            </p>

                            <p class="text-[10px] font-semibold text-blue-600">
                                {{ $user->role }}
                            </p>
                        </div>

                        {{-- AVATAR --}}
                        <div class="relative h-10 w-10 shrink-0">

                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}"
                                     alt="Avatar"
                                     class="h-full w-full rounded-full border-2 border-blue-100 bg-blue-50 object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center rounded-full border-2 border-blue-100 bg-blue-50 font-bold text-blue-700">
                                    {{ strtoupper(substr($user->name ?? 'C', 0, 1)) }}
                                </div>
                            @endif

                            <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-white bg-emerald-400"></span>
                        </div>

                        <i class="fa-solid fa-chevron-down hidden text-xs text-gray-400 transition group-open:rotate-180 sm:block"></i>
                    </summary>

                    {{-- DROPDOWN --}}
                    <div class="absolute right-0 top-full z-50 mt-3 w-48 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">

                        {{-- PROFIL --}}
                        <a href="{{ route('profile') }}"
                           class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">

                            <i class="fa-regular fa-user w-5 text-slate-400"></i>
                            Profil
                        </a>

                        <div class="border-t border-slate-100"></div>

                        {{-- LOGOUT --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button type="submit"
                                    class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-red-600 transition hover:bg-red-50">

                                <i class="fa-solid fa-right-from-bracket w-5"></i>
                                Keluar
                            </button>
                        </form>

                    </div>

                </details>

            </div>
        </header>

        {{-- ================= KONTEN HALAMAN ================= --}}
        @if ($fullwidth)

            <main class="w-full flex-1">
                {{ $slot }}
            </main>

        @else

            <main class="mx-auto w-full max-w-screen-2xl flex-1 px-5 py-8 md:px-8">

                <section>
                    {{ $slot }}
                </section>

            </main>

        @endif

        {{-- ================= FOOTER ================= --}}
        <footer class="mt-auto border-t border-gray-200 bg-white py-5">

            <div class="mx-auto flex max-w-screen-2xl flex-col items-center justify-between gap-2 px-5 text-xs text-gray-400 sm:flex-row md:px-8">

                <span>
                    SISTA &copy; {{ date('Y') }} CV Cemerlang
                </span>

                <span>
                    Customer Service
                </span>

            </div>

        </footer>

    </div>

    {{-- SCRIPT TAMBAHAN HALAMAN --}}
    @stack('scripts')

</body>
</html>
