@php
    $user = auth()->user();

    $avatarUrl =
        $user->avatar_url ??
        ($user->avatar ? asset('storage/' . $user->avatar) : asset('stisla/assets/img/avatar/avatar-1.png'));

    $themePref = $user->preferences['theme'] ?? 'light';
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SISTA - {{ $user->role }}</title>

    {{-- Font (sama seperti tema Stisla) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">

    {{-- Ikon Font Awesome (dipakai menu sidebar & navbar) --}}
    <link rel="stylesheet" href="{{ asset('stisla/assets/modules/fontawesome/css/all.min.css') }}">

    {{-- Tailwind CSS --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
    class="bg-[#f4f6f9] dark:bg-[#0b1120] font-['Nunito',sans-serif] text-[13px] text-[#6c757d] dark:text-[#a0aec0] antialiased">

    {{--
        Kerangka layout (grid 2x2 dengan flex):

        ┌──────────────┬───────────────────────────┐
        │ 1. Logo      │ 2. Navbar                 │
        ├──────────────┼───────────────────────────┤
        │ 3. Sidebar   │ 4. Konten utama           │
        └──────────────┴───────────────────────────┘

        Lebar kolom kiri (logo + sidebar) diatur satu tempat lewat --sidebar-w
        supaya keduanya selalu sejajar.

        data-mini   : sidebar dipersempit (hanya ikon) di layar >= lg
        data-drawer : sidebar terbuka sebagai drawer di layar < lg
    --}}
    <div id="app" data-mini="false" data-drawer="false"
        class="group/layout flex h-screen flex-col overflow-hidden [--sidebar-w:65px] lg:[--sidebar-w:16rem] lg:data-[mini=true]:[--sidebar-w:65px]">

        {{-- ============ BARIS ATAS: LOGO + NAVBAR ============ --}}
        <div class="relative z-50 flex h-[70px] shrink-0">

            {{-- 1. LOGO (kiri atas) --}}
            <div class="flex h-[70px] w-[var(--sidebar-w)] shrink-0 items-center bg-[#041C88]">

                {{-- Logo lengkap (desktop, sidebar terbuka) --}}
                <a href="{{ url('/') }}"
                    class="hidden h-[70px] w-full items-center justify-start gap-[9px] px-[18px] no-underline lg:group-data-[mini=false]/layout:flex">

                    <img src="{{ asset('images/logo-cemerlang.png') }}" alt="Logo CV Cemerlang"
                        class="h-[38px] w-[38px] shrink-0 rounded-[11px] border-2 border-white bg-white object-contain p-[2px]">

                    <div class="min-w-0 text-left leading-[1.15]">
                        {{-- Ubah teks SISTA menjadi putih --}}
                        <div class="text-[19px] font-bold tracking-[-0.3px] text-white">
                            SISTA
                        </div>

                        {{-- Ubah teks CV Cemerlang menjadi putih sedikit transparan --}}
                        <div
                            class="mt-[5px] flex items-center gap-1.5 whitespace-nowrap text-[11px] font-normal text-white/80">
                            <span class="h-[7px] w-[7px] shrink-0 rounded-full bg-[#47cfa0]"></span>
                            <span>CV Cemerlang</span>
                        </div>
                    </div>
                </a>

                {{-- Logo kecil (mobile / sidebar mini) --}}
                <a href="{{ url('/') }}"
                    class="flex h-[70px] w-full items-center justify-center lg:group-data-[mini=false]/layout:hidden">
                    <img src="{{ asset('images/logo-cemerlang.png') }}" alt="Logo SISTA"
                        class="h-8 w-8 rounded-lg bg-white p-0.5 object-contain">
                </a>
            </div>

            {{-- 2. NAVBAR (kanan atas) --}}
            <nav class="flex min-w-0 flex-1 items-center justify-between bg-[#041C88] px-4 lg:px-[30px]">

                {{-- Tombol buka/tutup sidebar --}}
                <button id="sidebar-toggle" type="button" aria-label="Buka atau tutup sidebar"
                    class="-ml-2.5 flex h-10 w-10 items-center justify-center text-lg text-white/80 transition-colors hover:text-white">
                    <i class="fas fa-bars"></i>
                </button>

                {{-- Menu pengguna --}}
                <details id="user-dropdown" class="group relative flex h-full shrink-0 items-center">
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

                        {{-- AVATAR DENGAN INDIKATOR STATUS --}}
                        <div class="relative h-[34px] w-[34px] shrink-0">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="Avatar"
                                    class="h-full w-full rounded-full border-2 border-white/20 bg-white/10 object-cover">
                            @else
                                <div
                                    class="flex h-full w-full items-center justify-center rounded-full border-2 border-white/20 bg-white/10 text-xs font-bold text-white">
                                    {{ strtoupper(substr($user->name ?? 'A', 0, 1)) }}
                                </div>
                            @endif
                            {{-- Titik hijau online --}}
                            <span
                                class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-[#041C88] bg-emerald-400"></span>
                        </div>

                        <i
                            class="fas fa-chevron-down hidden text-xs text-white/80 transition group-open:rotate-180 sm:block"></i>
                    </summary>

                    {{-- DROPDOWN MENU --}}
                    <div
                        class="absolute right-0 top-[calc(100%-10px)] z-50 w-48 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">

                        <a href="{{ route('profile') }}"
                            class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">
                            <i class="far fa-user w-5 text-slate-400"></i>
                            Profil
                        </a>

                        <div class="border-t border-slate-100"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="button" role="menuitem"
                                class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-medium text-[#fc544b] transition hover:bg-red-50"
                                data-confirm-url="{{ route('logout') }}" data-confirm-method="POST"
                                data-confirm-theme="logout" data-confirm-title="Konfirmasi Keluar"
                                data-confirm-message="Sesi akun Anda akan diakhiri. Anda harus memasukkan username dan kata sandi kembali untuk mengakses sistem."
                                data-confirm-action="Ya, Keluar">
                                <i class="fas fa-sign-out-alt w-5 text-red-400"></i>
                                Keluar
                            </button>
                        </form>
                    </div>
                </details>
            </nav>
        </div>

        {{-- ============ BARIS BAWAH: SIDEBAR + KONTEN ============ --}}
        <div class="relative flex min-h-0 flex-1">

            {{-- Latar gelap saat drawer sidebar terbuka (mobile) --}}
            <div id="sidebar-overlay"
                class="fixed inset-x-0 bottom-0 top-[70px] z-30 hidden bg-black/40 group-data-[drawer=true]/layout:max-lg:block">
            </div>

            {{-- 3. SIDEBAR (kiri bawah) --}}
            <aside id="sidebar"
                class="fixed bottom-0 left-0 top-[70px] z-40 w-64 -translate-x-full overflow-y-auto overflow-x-hidden bg-white dark:bg-[#111827] dark:border-r dark:border-[#1f2937] shadow-[0_4px_25px_0_rgba(0,0,0,0.1)] transition-transform duration-300 group-data-[drawer=true]/layout:translate-x-0 lg:relative lg:inset-auto lg:z-10 lg:w-[var(--sidebar-w)] lg:shrink-0 lg:translate-x-0 lg:transition-[width]">

                <ul class="mt-2">
                    @foreach ($menuSections as $section)
                        @continue(($section['visible'] ?? true) === false)

                        <li
                            class="px-6 pb-2 pt-5 text-xs font-semibold uppercase tracking-[1.3px] text-[#98a6ad] lg:group-data-[mini=true]/layout:hidden">
                            {{ $section['title'] }}
                        </li>

                        @foreach ($section['items'] as $item)
                            @continue(($item['visible'] ?? true) === false)

                            @php
                                $isActive =
                                    $item['active'] ??
                                    request()->routeIs(\Illuminate\Support\Str::beforeLast($item['route'], '.') . '.*');
                            @endphp

                            <li>
                                <a href="{{ route($item['route']) }}" title="{{ $item['label'] }}"
                                    @if ($isActive) aria-current="page" @endif
                                    @class([
                                        'flex h-[46px] items-center gap-3 px-6 text-[13px] font-semibold transition-colors',
                                        'lg:group-data-[mini=true]/layout:justify-center lg:group-data-[mini=true]/layout:px-0',
                                        'bg-[#041C88]/10 text-[#041C88]' => $isActive,
                                        'text-[#78828a] hover:bg-[#041C88]/5 hover:text-[#041C88]' => !$isActive,
                                    ])>
                                    <i class="fas {{ $item['icon'] }} w-5 shrink-0 text-center text-sm"></i>
                                    <span
                                        class="truncate lg:group-data-[mini=true]/layout:hidden">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    @endforeach
                </ul>

                <div class="mb-4 mt-4 p-3 lg:group-data-[mini=true]/layout:hidden">
                    <div class="text-center text-xs text-[#6c757d]">
                        SISTA &copy; {{ date('Y') }} CV Cemerlang
                    </div>
                </div>
            </aside>

            {{-- 4. KONTEN UTAMA (kanan bawah) --}}
            <main class="flex min-w-0 flex-1 flex-col overflow-y-auto">
                <div class="flex-1 px-4 py-[30px] sm:px-[30px]">
                    <section class="relative z-[1]">
                        {{ $slot }}
                    </section>
                </div>

                <footer
                    class="flex items-center justify-between border-t border-[#e3eaef] dark:border-[#1f2937] bg-[#f4f6f9] dark:bg-[#0b1120] px-4 py-5 text-[#98a6ad] sm:px-[30px]">
                    <div>
                        SISTA &copy; {{ date('Y') }} CV Cemerlang
                    </div>
                </footer>
            </main>
        </div>
    </div>

    {{-- Interaksi sidebar & dropdown (tanpa library tambahan) --}}
    <script>
        (function() {
            const app = document.getElementById('app');
            const toggle = document.getElementById('sidebar-toggle');
            const overlay = document.getElementById('sidebar-overlay');
            const userButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');
            const isDesktop = () => window.matchMedia('(min-width: 1024px)').matches;
            const flip = (key) => app.dataset[key] = app.dataset[key] === 'true' ? 'false' : 'true';

            // Sidebar: mini di desktop, drawer di mobile
            toggle.addEventListener('click', () => flip(isDesktop() ? 'mini' : 'drawer'));
            overlay.addEventListener('click', () => app.dataset.drawer = 'false');

            // Dropdown pengguna
            const setUserMenu = (open) => {
                userMenu.classList.toggle('hidden', !open);
                userButton.setAttribute('aria-expanded', open ? 'true' : 'false');
            };

            userButton.addEventListener('click', (e) => {
                e.stopPropagation();
                setUserMenu(userMenu.classList.contains('hidden'));
            });

            document.addEventListener('click', (e) => {
                if (!userMenu.contains(e.target)) setUserMenu(false);
            });

            document.addEventListener('keydown', (e) => {
                if (e.key !== 'Escape') return;
                setUserMenu(false);
                app.dataset.drawer = 'false';
            });
        })();
    </script>

    @stack('scripts')

    <x-modal-konfirmasi />

</body>

</html>
