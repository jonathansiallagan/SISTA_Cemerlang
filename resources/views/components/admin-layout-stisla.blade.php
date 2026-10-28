@php
    $user = auth()->user();

    $avatarUrl = $user->avatar_url
        ?? $user->avatar
        ?? asset('stisla/assets/img/avatar/avatar-1.png');
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SISTA - {{ $user->role }}</title>

    {{-- CSS Stisla --}}
    <link rel="stylesheet" href="{{ asset('stisla/assets/modules/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('stisla/assets/modules/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('stisla/assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('stisla/assets/css/components.css') }}">

    {{-- Pertahankan sementara untuk halaman lama yang masih memakai Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">

            {{-- Navbar --}}
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
                <form class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li>
                            <a href="#" data-toggle="sidebar"
                               class="nav-link nav-link-lg">
                                <i class="fas fa-bars"></i>
                            </a>
                        </li>
                    </ul>
                </form>

                <ul class="navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown"
                           class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <img alt="Avatar"
                                 src="{{ $avatarUrl ?: asset('stisla/assets/img/avatar/avatar-1.png') }}"
                                 class="rounded-circle mr-1">
                            <div class="d-sm-none d-lg-inline-block">
                                {{ $user->name }}
                            </div>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-title">{{ $user->role }}</div>

                            <a href="{{ route('profile') }}" class="dropdown-item has-icon">
                                <i class="far fa-user"></i> Profil
                            </a>

                            <div class="dropdown-divider"></div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="dropdown-item has-icon text-danger">
                                    <i class="fas fa-sign-out-alt"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </nav>

            {{-- Sidebar --}}
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">
                    
                    {{-- Sidebar Brand --}}
                    <div class="sidebar-brand">
                        <a href="{{ url('/') }}"
                        style="display:flex; align-items:center; justify-content:flex-start;
                                gap:9px; padding:0 18px; height:70px; text-decoration:none;">

                            <img src="{{ asset('images/logo-cemerlang.png') }}"
                                alt="Logo CV Cemerlang"
                                style="width:38px; height:38px; object-fit:contain;
                                        flex-shrink:0; padding:2px; background:#fff;
                                        border:2px solid #6777ef; border-radius:11px;">

                            <div style="text-align:left; min-width:0; line-height:1.15;">
                                <div style="font-size:19px; font-weight:700;
                                            letter-spacing:-0.3px; color:#34395e;">
                                    SISTA
                                </div>

                                <div style="display:flex; align-items:center; gap:6px;
                                            margin-top:5px; font-size:11px;
                                            font-weight:400; color:#98a6ad;
                                            white-space:nowrap;">
                                    <span style="width:7px; height:7px; border-radius:50%;
                                                background:#47cfa0; flex-shrink:0;"></span>
                                    <span>CV Cemerlang</span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="sidebar-brand sidebar-brand-sm">
                        <a href="{{ url('/') }}">
                            <img src="{{ asset('images/logo-cemerlang.png') }}"
                                alt="Logo SISTA"
                                style="width:32px; height:32px; object-fit:contain;">
                        </a>
                    </div>

                    <ul class="sidebar-menu mt-2">
                        @foreach ($menuSections as $section)
                            @continue(($section['visible'] ?? true) === false)

                            <li class="menu-header">{{ $section['title'] }}</li>

                            @foreach ($section['items'] as $item)
                                @continue(($item['visible'] ?? true) === false)

                                @php
                                    $isActive = $item['active']
                                        ?? request()->routeIs(
                                            \Illuminate\Support\Str::beforeLast(
                                                $item['route'], '.'
                                            ) . '.*'
                                        );
                                @endphp

                                <li class="{{ $isActive ? 'active' : '' }}">
                                    <a class="nav-link"
                                       href="{{ route($item['route']) }}">
                                        <i class="fas {{ $item['icon'] }}"></i>
                                        <span>{{ $item['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        @endforeach
                    </ul>

                    <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
                        <div class="text-center text-muted small">
                            SISTA &copy; {{ date('Y') }} CV Cemerlang
                        </div>
                    </div>
                </aside>
            </div>

            {{-- Konten halaman --}}
            <div class="main-content">
                <section class="section">
                    {{ $slot }}
                </section>
            </div>

            <footer class="main-footer">
                <div class="footer-left">
                    SISTA &copy; {{ date('Y') }} CV Cemerlang
                </div>
            </footer>
        </div>
    </div>

    {{-- JavaScript Stisla --}}
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
