@php
    $user = auth()->user();

    $avatarUrl = $user->avatar_url ?? ($user->avatar ?? asset('stisla/assets/img/avatar/avatar-1.png'));
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
</head>

<body class="bg-[#f4f6f9] font-['Nunito',sans-serif] text-[13px] text-[#6c757d] antialiased">

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
            <div class="flex h-[70px] w-[var(--sidebar-w)] shrink-0 items-center bg-white">

                {{-- Logo lengkap (desktop, sidebar terbuka) --}}
                <a href="{{ url('/') }}"
                    class="hidden h-[70px] w-full items-center justify-start gap-[9px] px-[18px] no-underline lg:group-data-[mini=false]/layout:flex">

                    <img src="{{ asset('images/logo-cemerlang.png') }}" alt="Logo CV Cemerlang"
                        class="h-[38px] w-[38px] shrink-0 rounded-[11px] border-2 border-[#6777ef] bg-white object-contain p-[2px]">

                    <div class="min-w-0 text-left leading-[1.15]">
                        <div class="text-[19px] font-bold tracking-[-0.3px] text-[#34395e]">
                            SISTA
                        </div>

                        <div
                            class="mt-[5px] flex items-center gap-1.5 whitespace-nowrap text-[11px] font-normal text-[#98a6ad]">
                            <span class="h-[7px] w-[7px] shrink-0 rounded-full bg-[#47cfa0]"></span>
                            <span>CV Cemerlang</span>
                        </div>
                    </div>
                </a>

                {{-- Logo kecil (mobile / sidebar mini) --}}
                <a href="{{ url('/') }}"
                    class="flex h-[70px] w-full items-center justify-center lg:group-data-[mini=false]/layout:hidden">
                    <img src="{{ asset('images/logo-cemerlang.png') }}" alt="Logo SISTA" class="h-8 w-8 object-contain">
                </a>
            </div>

            {{-- 2. NAVBAR (kanan atas) --}}
            <nav class="flex min-w-0 flex-1 items-center justify-between bg-[#6777ef] px-4 lg:px-[30px]">

                {{-- Tombol buka/tutup sidebar --}}
                <button id="sidebar-toggle" type="button" aria-label="Buka atau tutup sidebar"
                    class="-ml-2.5 flex h-10 w-10 items-center justify-center text-lg text-white/80 transition-colors hover:text-white">
                    <i class="fas fa-bars"></i>
                </button>

                {{-- Menu pengguna --}}
                <div class="relative flex h-full items-center">
                    <button id="user-menu-button" type="button" aria-haspopup="true" aria-expanded="false"
                        class="flex items-center text-white/80 transition-colors hover:text-white">
                        <img alt="Avatar" src="{{ $avatarUrl ?: asset('stisla/assets/img/avatar/avatar-1.png') }}"
                            class="mr-1 h-[30px] w-[30px] rounded-full object-cover">
                        <div class="font-semibold sm:hidden lg:inline-block">
                            {{ $user->name }}
                        </div>
                        <i class="fas fa-caret-down ml-1.5 text-xs"></i>
                    </button>

                    <div id="user-menu" role="menu"
                        class="absolute right-0 top-full z-50 hidden min-w-[200px] rounded-[3px] bg-white py-2.5 shadow-[0_2px_6px_#e3e3e3]">

                        <div class="px-5 pb-2 pt-1 text-[10px] font-bold uppercase tracking-[1.5px] text-[#98a6ad]">
                            {{ $user->role }}
                        </div>

                        <a href="{{ route('profile') }}" role="menuitem"
                            class="flex items-center gap-2.5 px-5 py-2 text-[13px] text-[#6c757d] transition-colors hover:bg-[#f8f9fa] hover:text-[#6777ef]">
                            <i class="far fa-user w-4 text-center"></i> Profil
                        </a>

                        <div class="my-2 border-t border-[#e3eaef]"></div>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" role="menuitem"
                                class="flex w-full items-center gap-2.5 px-5 py-2 text-left text-[13px] text-[#fc544b] transition-colors hover:bg-[#f8f9fa]">
                                <i class="fas fa-sign-out-alt w-4 text-center"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
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
                class="fixed bottom-0 left-0 top-[70px] z-40 w-64 -translate-x-full overflow-y-auto overflow-x-hidden bg-white shadow-[0_4px_25px_0_rgba(0,0,0,0.1)] transition-transform duration-300 group-data-[drawer=true]/layout:translate-x-0 lg:relative lg:inset-auto lg:z-10 lg:w-[var(--sidebar-w)] lg:shrink-0 lg:translate-x-0 lg:transition-[width]">

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
                                        'bg-[#6777ef]/10 text-[#6777ef]' => $isActive,
                                        'text-[#78828a] hover:bg-[#6777ef]/5 hover:text-[#6777ef]' => !$isActive,
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
                    class="flex items-center justify-between border-t border-[#e3eaef] bg-[#f4f6f9] px-4 py-5 text-[#98a6ad] sm:px-[30px]">
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
</body>

</html>
