@props(['fullwidth' => false])

@php
    $user = auth()->user();

    // Avatar memakai URL yang tersedia pada model user (sama seperti sebelumnya).
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

    {{-- CSS Stisla (sama seperti admin-layout-stisla) --}}
    <link rel="stylesheet" href="{{ asset('stisla/assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('stisla/assets/modules/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('stisla/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('stisla/assets/css/components.css') }}">

    {{-- Tailwind tetap dimuat: dipakai navbar CS dan halaman Transaksi / Riwayat yang masih lama --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Font Awesome 6 (kelas fa-solid / fa-regular), dimuat setelah FA5 milik Stisla --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* =====================================================
           NAVBAR CS (tampilan sama persis dengan versi sebelumnya)
           Ditulis eksplisit agar tidak tertimpa CSS global Stisla/Bootstrap
           ===================================================== */
        #cs-navbar {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif,
                'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';
            font-size: 1rem;
            line-height: 1.5;
            font-weight: 400;
            position: sticky;
            top: 0;
            z-index: 1040;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgb(0 0 0 / .1), 0 1px 2px -1px rgb(0 0 0 / .1);
        }

        #cs-navbar .cs-nav-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: 76px;
            max-width: 1536px;
            margin: 0 auto;
            padding: 0 1.25rem;
        }

        @media (min-width: 768px) {
            #cs-navbar .cs-nav-inner { padding: 0 2rem; }
        }

        /* Logo */
        #cs-navbar .cs-brand {
            display: flex;
            flex-shrink: 0;
            align-items: center;
            gap: .75rem;
            text-decoration: none;
        }

        #cs-navbar .cs-brand-ring {
            display: flex;
            width: 2.75rem;
            height: 2.75rem;
            align-items: center;
            justify-content: center;
            border-radius: .75rem;
            background: linear-gradient(to bottom right, #6366f1, #3b82f6, #22d3ee);
            box-shadow: 0 4px 6px -1px rgb(59 130 246 / .2), 0 2px 4px -2px rgb(59 130 246 / .2);
        }

        #cs-navbar .cs-brand-ring > div {
            display: flex;
            width: 2.5rem;
            height: 2.5rem;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: #fff;
        }

        #cs-navbar .cs-brand-ring img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        #cs-navbar .cs-brand-title {
            margin: 0;
            font-size: 1.125rem;
            line-height: 1.25rem;
            font-weight: 800;
            letter-spacing: -.025em;
            color: #1d4ed8;
        }

        #cs-navbar .cs-brand-sub {
            display: flex;
            align-items: center;
            gap: .375rem;
            margin: .25rem 0 0;
            font-size: 10px;
            line-height: 1.5;
            font-weight: 500;
            color: #64748b;
        }

        #cs-navbar .cs-brand-sub span {
            width: .5rem;
            height: .5rem;
            border-radius: 9999px;
            background: #34d399;
        }

        /* Menu navigasi */
        #cs-navbar .cs-menu {
            display: flex;
            flex: 1 1 0%;
            align-items: center;
            justify-content: center;
            gap: .75rem;
            font-size: .875rem;
            line-height: 1.25rem;
            font-weight: 600;
        }

        @media (min-width: 768px) {
            #cs-navbar .cs-menu { gap: 1.5rem; }
        }

        #cs-navbar .cs-link {
            padding: .5rem .75rem;
            border-radius: .5rem;
            color: #475569;
            font-weight: 600;
            white-space: nowrap;
            text-decoration: none;
            transition: all .15s ease;
        }

        #cs-navbar .cs-link:hover {
            background: #f8fafc;
            color: #1d4ed8;
            text-decoration: none;
        }

        #cs-navbar .cs-link.is-active {
            background: #eff6ff;
            color: #1d4ed8;
        }

        #cs-navbar .cs-link i { margin-right: .25rem; }

        #cs-navbar .cs-only-sm  { display: none; }
        #cs-navbar .cs-only-xs  { display: inline; }
        @media (min-width: 640px) {
            #cs-navbar .cs-only-sm { display: inline; }
            #cs-navbar .cs-only-xs { display: none; }
        }

        /* Profil dan dropdown */
        #cs-navbar details.cs-profile {
            position: relative;
            flex-shrink: 0;
        }

        #cs-navbar details.cs-profile > summary {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .25rem;
            border-radius: 9999px;
            list-style: none;
            cursor: pointer;
            transition: background .15s ease;
        }

        #cs-navbar details.cs-profile > summary::-webkit-details-marker { display: none; }
        #cs-navbar details.cs-profile > summary:hover { background: #f8fafc; }

        #cs-navbar .cs-profile-text { display: none; text-align: right; }
        @media (min-width: 640px) {
            #cs-navbar .cs-profile-text { display: block; }
        }

        #cs-navbar .cs-profile-name {
            margin: 0;
            font-size: .75rem;
            line-height: 1rem;
            font-weight: 700;
            color: #1e293b;
        }

        #cs-navbar .cs-profile-role {
            margin: 0;
            font-size: 10px;
            line-height: 1.5;
            font-weight: 600;
            color: #2563eb;
        }

        #cs-navbar .cs-avatar {
            position: relative;
            width: 2.5rem;
            height: 2.5rem;
            flex-shrink: 0;
        }

        #cs-navbar .cs-avatar > img,
        #cs-navbar .cs-avatar > .cs-avatar-initial {
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 9999px;
            border: 2px solid #dbeafe;
            background: #eff6ff;
            object-fit: cover;
        }

        #cs-navbar .cs-avatar > .cs-avatar-initial {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #1d4ed8;
        }

        #cs-navbar .cs-avatar-dot {
            position: absolute;
            right: 0;
            bottom: 0;
            width: .625rem;
            height: .625rem;
            border-radius: 9999px;
            border: 2px solid #fff;
            background: #34d399;
        }

        #cs-navbar .cs-dropdown {
            position: absolute;
            top: 3rem;
            right: 0;
            z-index: 50;
            width: 12rem;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: .75rem;
            background: #fff;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / .1), 0 8px 10px -6px rgb(0 0 0 / .1);
        }

        #cs-navbar .cs-dropdown > * + * { border-top: 1px solid #f1f5f9; }

        #cs-navbar .cs-dropdown a,
        #cs-navbar .cs-dropdown button {
            display: flex;
            width: 100%;
            align-items: center;
            gap: .75rem;
            padding: .75rem 1rem;
            border: 0;
            background: transparent;
            font-size: .875rem;
            line-height: 1.25rem;
            font-weight: 400;
            text-align: left;
            text-decoration: none;
            cursor: pointer;
            transition: background .15s ease;
        }

        #cs-navbar .cs-dropdown a { color: #334155; }
        #cs-navbar .cs-dropdown a:hover { background: #eff6ff; color: #334155; }
        #cs-navbar .cs-dropdown a i { width: 1.25rem; color: #64748b; }

        #cs-navbar .cs-dropdown button { color: #dc2626; }
        #cs-navbar .cs-dropdown button:hover { background: #fef2f2; }
        #cs-navbar .cs-dropdown button i { width: 1.25rem; }

        /* =====================================================
           KONTEN & FOOTER (gaya Stisla, tanpa sidebar)
           ===================================================== */
        .cs-main-content {
            max-width: 1536px;
            margin: 0 auto;
            padding: 30px 1.25rem 10px;
        }

        @media (min-width: 768px) {
            .cs-main-content { padding: 30px 2rem 10px; }
        }

        .cs-footer {
            padding: 20px 0;
            margin-top: 30px;
            border-top: 1px solid #e3eaef;
            background: #fff;
            color: #98a6ad;
            font-size: 12px;
        }

        .cs-footer .cs-footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            max-width: 1536px;
            margin: 0 auto;
            padding: 0 1.25rem;
        }

        @media (min-width: 768px) {
            .cs-footer .cs-footer-inner { padding: 0 2rem; }
        }

        /* Mode fullwidth: konten memenuhi lebar layar (dipakai dashboard CS) */
        .cs-fullwidth { width: 100%; }
        .cs-fullwidth + .cs-footer { margin-top: 0; }
    </style>

    @stack('styles')
</head>

<body>
    <div id="app">

        {{-- ================= NAVBAR CS (tidak berubah) ================= --}}
        <header id="cs-navbar">
            <div class="cs-nav-inner">

                {{-- LOGO --}}
                <a href="{{ route('cs.dashboard') }}" class="cs-brand">
                    <div class="cs-brand-ring">
                        <div>
                            <img src="{{ asset('images/logo-cemerlang.png') }}" alt="Logo CV Cemerlang">
                        </div>
                    </div>

                    <div>
                        <h1 class="cs-brand-title">SISTA</h1>
                        <p class="cs-brand-sub">
                            <span></span>
                            CV Cemerlang
                        </p>
                    </div>
                </a>

                {{-- MENU NAVIGASI CS --}}
                <nav class="cs-menu">
                    <a href="{{ route('cs.dashboard') }}"
                       class="cs-link {{ request()->routeIs('cs.dashboard') ? 'is-active' : '' }}">
                        <i class="fa-solid fa-house"></i>
                        <span class="cs-only-sm">Dashboard</span>
                    </a>

                    <a href="{{ route('transaksi.create') }}"
                       class="cs-link {{ request()->routeIs('transaksi.create') ? 'is-active' : '' }}">
                        <i class="fa-solid fa-cart-shopping"></i>
                        Transaksi
                    </a>

                    <a href="{{ route('transaksi.index') }}"
                       class="cs-link {{ request()->routeIs('transaksi.index') ? 'is-active' : '' }}">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span class="cs-only-sm">Riwayat Transaksi</span>
                        <span class="cs-only-xs">Riwayat</span>
                    </a>
                </nav>

                {{-- PROFIL DAN DROPDOWN --}}
                <details class="cs-profile">
                    <summary>
                        <div class="cs-profile-text">
                            <p class="cs-profile-name">{{ $user->name }}</p>
                            <p class="cs-profile-role">{{ $user->role }}</p>
                        </div>

                        <div class="cs-avatar">
                            @if ($avatarUrl)
                                <img src="{{ $avatarUrl }}" alt="Avatar">
                            @else
                                <div class="cs-avatar-initial">
                                    {{ strtoupper(substr($user->name ?? 'C', 0, 1)) }}
                                </div>
                            @endif

                            <span class="cs-avatar-dot"></span>
                        </div>
                    </summary>

                    <div class="cs-dropdown">
                        <a href="{{ route('profile') }}">
                            <i class="fa-regular fa-user"></i>
                            Profil
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                Keluar
                            </button>
                        </form>
                    </div>
                </details>

            </div>
        </header>

        {{-- ================= KONTEN HALAMAN (gaya Stisla) ================= --}}
        @if ($fullwidth)
            <div class="cs-fullwidth">
                {{ $slot }}
            </div>
        @else
            <div class="main-content cs-main-content">
                <section class="section">
                    {{ $slot }}
                </section>
            </div>
        @endif

        {{-- FOOTER --}}
        <footer class="cs-footer">
            <div class="cs-footer-inner">
                <span>SISTA &copy; {{ date('Y') }} CV Cemerlang</span>
                <span>Customer Service</span>
            </div>
        </footer>
    </div>

    {{-- JavaScript Stisla (sama seperti admin-layout-stisla) --}}
    <script src="{{ asset('stisla/assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('stisla/assets/modules/popper.js') }}"></script>
    <script src="{{ asset('stisla/assets/modules/tooltip.js') }}"></script>
    <script src="{{ asset('stisla/assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('stisla/assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('stisla/assets/js/stisla.js') }}"></script>
    <script src="{{ asset('stisla/assets/js/scripts.js') }}"></script>
    <script src="{{ asset('stisla/assets/js/custom.js') }}"></script>

    @stack('scripts')
</body>
</html>