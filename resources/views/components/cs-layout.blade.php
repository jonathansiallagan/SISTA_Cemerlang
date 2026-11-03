@props(['fullwidth' => false])

@php
    $user = auth()->user();
    $avatarUrl = $user->avatar_url ?? ($user->avatar ? asset('storage/' . $user->avatar) : null);
    $themePref = $user->preferences['theme'] ?? 'light';
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTA - Customer Service</title>

    {{-- Font Nunito (Identik dengan Admin Stisla) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS dan JavaScript --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @stack('styles')

    {{-- SKRIP INJEKSI DARK MODE --}}
    <script>
        (function() {
            const theme = "{{ $themePref }}";
            if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>

<body
    class="min-h-screen bg-[#f4f6f9] dark:bg-[#0b1120] font-['Nunito',sans-serif] text-[13px] text-[#6c757d] dark:text-[#a0aec0] antialiased transition-colors duration-300">

    <div id="app" class="flex min-h-screen flex-col">

        {{-- ================= NAVBAR CS (WARNA NAVY ADMIN #041C88) ================= --}}
        <header id="cs-navbar" class="sticky top-0 z-50 bg-[#041C88] shadow-md border-b border-blue-900/40">

            <div
                class="mx-auto flex min-h-[70px] max-w-screen-2xl flex-wrap items-center justify-between gap-4 px-5 py-2.5 md:px-8">

                {{-- LOGO --}}
                <a href="{{ route('cs.dashboard') }}" class="flex shrink-0 items-center gap-[9px] no-underline">
                    <img src="{{ asset('images/logo-cemerlang.png') }}" alt="Logo CV Cemerlang"
                        class="h-[38px] w-[38px] shrink-0 rounded-[11px] border-2 border-white bg-white object-contain p-[2px]">

                    <div class="min-w-0 text-left leading-[1.15]">
                        <div class="text-[19px] font-bold tracking-[-0.3px] text-white">
                            SISTA
                        </div>
                        <div
                            class="mt-[5px] flex items-center gap-1.5 whitespace-nowrap text-[11px] font-normal text-white/80">
                            <span class="h-[7px] w-[7px] shrink-0 rounded-full bg-[#47cfa0]"></span>
                            <span>CV Cemerlang</span>
                        </div>
                    </div>
                </a>

                {{-- MENU NAVIGASI --}}
                <nav class="flex flex-1 items-center justify-center gap-1 text-sm font-semibold sm:gap-2 md:gap-3">

                    {{-- DASHBOARD --}}
                    <a href="{{ route('cs.dashboard') }}"
                        class="rounded-xl px-3 py-2 transition-all
                        {{ request()->routeIs('cs.dashboard')
                            ? 'bg-white/15 text-white shadow-sm font-bold'
                            : 'text-white/80 hover:bg-white/10 hover:text-white' }}">

                        <i class="fa-solid fa-house mr-1.5 text-xs"></i>
                        <span class="hidden sm:inline">Dashboard</span>
                    </a>

                    {{-- TRANSAKSI --}}
                    <a href="{{ route('transaksi.create') }}"
                        class="rounded-xl px-3 py-2 transition-all
                        {{ request()->routeIs('transaksi.create')
                            ? 'bg-white/15 text-white shadow-sm font-bold'
                            : 'text-white/80 hover:bg-white/10 hover:text-white' }}">

                        <i class="fa-solid fa-cart-shopping mr-1.5 text-xs"></i>
                        Transaksi
                    </a>

                    {{-- RIWAYAT --}}
                    <a href="{{ route('transaksi.index') }}"
                        class="rounded-xl px-3 py-2 transition-all
                        {{ request()->routeIs('transaksi.index')
                            ? 'bg-white/15 text-white shadow-sm font-bold'
                            : 'text-white/80 hover:bg-white/10 hover:text-white' }}">

                        <i class="fa-solid fa-clock-rotate-left mr-1.5 text-xs"></i>
                        <span class="hidden sm:inline">Riwayat Transaksi</span>
                        <span class="sm:hidden">Riwayat</span>
                    </a>
                </nav>

                {{-- PROFIL DAN DROPDOWN --}}
                <details class="group relative shrink-0">

                    <summary
                        class="flex cursor-pointer list-none items-center gap-3 rounded-full p-1.5 transition hover:bg-white/10">

                        {{-- INFORMASI PROFIL --}}
                        <div class="hidden text-right sm:block">
                            <p class="text-xs font-bold leading-4 text-white">
                                {{ $user->name }}
                            </p>
                            <p class="text-[10px] font-semibold text-white/70">
                                {{ $user->role }}
                            </p>
                        </div>

                        {{-- AVATAR --}}
                        <div class="relative h-[34px] w-[34px] shrink-0">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="Avatar"
                                    class="h-full w-full rounded-full border-2 border-white/20 bg-white/10 object-cover">
                            @else
                                <div
                                    class="flex h-full w-full items-center justify-center rounded-full border-2 border-white/20 bg-white/10 text-xs font-bold text-white">
                                    {{ strtoupper(substr($user->name ?? 'C', 0, 1)) }}
                                </div>
                            @endif

                            <span
                                class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-[#041C88] bg-emerald-400"></span>
                        </div>

                        <i
                            class="fa-solid fa-chevron-down hidden text-xs text-white/80 transition group-open:rotate-180 sm:block"></i>
                    </summary>

                    {{-- DROPDOWN --}}
                    <div
                        class="absolute right-0 top-full z-50 mt-2 w-48 overflow-hidden rounded-xl border border-slate-200 dark:border-gray-800 bg-white dark:bg-[#111827] shadow-xl">

                        {{-- PROFIL --}}
                        <a href="{{ route('profile') }}"
                            class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 dark:text-gray-200 transition hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-[#041C88] dark:hover:text-blue-400">

                            <i class="fa-regular fa-user w-4 text-slate-400"></i>
                            Profil
                        </a>

                        <div class="border-t border-slate-100 dark:border-gray-800"></div>

                        {{-- LOGOUT --}}
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="button" role="menuitem"
                                class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-medium text-[#fc544b] dark:text-red-400 transition hover:bg-red-50 dark:hover:bg-red-950/30"
                                data-confirm-url="{{ route('logout') }}" data-confirm-method="POST"
                                data-confirm-theme="logout" data-confirm-title="Konfirmasi Keluar"
                                data-confirm-message="Sesi akun Anda akan diakhiri. Anda harus memasukkan username dan kata sandi kembali untuk mengakses sistem."
                                data-confirm-action="Ya, Keluar">
                                <i class="fas fa-sign-out-alt w-4 text-red-400"></i> Keluar
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
            <main class="mx-auto w-full max-w-screen-2xl flex-1 px-4 py-6 md:px-8">
                <section>
                    {{ $slot }}
                </section>
            </main>
        @endif

        {{-- ================= FOOTER ================= --}}
        <footer class="mt-auto border-t border-[#e3eaef] dark:border-[#1f2937] bg-[#f4f6f9] dark:bg-[#0b1120] py-5">
            <div
                class="mx-auto flex max-w-screen-2xl flex-col items-center justify-between gap-2 px-5 text-xs text-[#98a6ad] sm:flex-row md:px-8">
                <span>
                    SISTA &copy; {{ date('Y') }} CV Cemerlang
                </span>
                <span>
                    Customer Service Panel
                </span>
            </div>
        </footer>
    </div>

    @stack('scripts')
    <x-modal-konfirmasi />
</body>

</html>
