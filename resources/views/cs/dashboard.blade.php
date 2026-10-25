<x-cs-layout>
    <div class="cs-home">

        {{-- HEADER DASHBOARD --}}
        <header class="cs-home-header">

            <a href="{{ route('cs.dashboard') }}" class="cs-home-logo">
                <span class="cs-logo-icon">
                    <i class="fa-solid fa-print"></i>
                </span>
                <span>SISTA</span>
            </a>

            <div class="cs-navbar-title">
                WELCOME TO SISTA
            </div>

            <!-- PROFILE BUTTON -->
            <button type="button"
                class="cs-profile-button"
                id="csProfileButton"
                aria-label="Menu profil">
                <i class="fa-regular fa-circle-user"></i>
            </button>

            <div class="cs-profile-menu" id="csProfileMenu">
                <div class="cs-profile-details">
                    <div class="cs-profile-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'C', 0, 1)) }}
                    </div>
                    <div>
                        <strong>{{ auth()->user()->name ?? 'Customer Service' }}</strong>
                        <small>Customer Service</small>
                    </div>
                </div>

                <div class="cs-profile-divider"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="cs-profile-logout">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </header>

        {{-- TIGA MENU UTAMA --}}

        <main class="cs-menu-container">

            {{-- TRANSAKSI BARU --}}
            <a href="{{ route('transaksi.create') }}"
                class="cs-menu-card">
                <span class="cs-menu-icon">
                    <i class="fa-solid fa-cart-shopping"></i>
                </span>
                <span class="cs-menu-title">TRANSAKSI</span>
                <span class="cs-menu-description">
                    Buat transaksi baru
                </span>
                <span class="cs-menu-arrow">
                    <i class="fa-solid fa-arrow-right"></i>
                </span>
            </a>

            {{-- RIWAYAT TRANSAKSI --}}
            <a href="{{ route('transaksi.index') }}"
                class="cs-menu-card">
                <span class="cs-menu-icon">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </span>
                <span class="cs-menu-title">
                    RIWAYAT<br>TRANSAKSI
                </span>
                <span class="cs-menu-description">
                    Lihat transaksi sebelumnya
                </span>
                <span class="cs-menu-arrow">
                    <i class="fa-solid fa-arrow-right"></i>
                </span>
            </a>

            {{-- PENGATURAN --}}
            <a href="#" class="cs-menu-card">
                <span class="cs-menu-icon">
                    <i class="fa-solid fa-gear"></i>
                </span>

                <span class="cs-menu-title">PENGATURAN</span>

                <span class="cs-menu-description">
                    Kelola profil dan akun
                </span>

                <span class="cs-menu-arrow">
                    <i class="fa-solid fa-arrow-right"></i>
                </span>
            </a>

        </main>

        <footer class="cs-home-footer">
            <span>© {{ date('Y') }} Sistem Informasi Percetakan</span>
            <span>Customer Service</span>
        </footer>
    </div>

    <style>
        .cs-home {
            --home-blue: #1769D2;
            --home-blue-dark: #104DA5;
            --home-blue-light: #EAF3FF;
            --home-card: #CBD5E1;
            --home-text: #24344D;

            min-height: calc(100vh - 100px);
            margin: -28px -34px -40px;
            padding: 0 34px 20px;
            background: #fff;
            color: var(--home-text);
            position: relative;
            box-sizing: border-box;
        }

        /* NAVBAR */
        .cs-home-header {
            height: 85px;
            margin: 0;
            padding: 0 20px;
            border-bottom: 3px solid #0B3D91;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 10;
            background: var(--home-blue);
            box-sizing: border-box;
        }

        /* LOGO SISTA */
        .cs-home-logo {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            padding: 0;
            color: #fff;
            text-decoration: none;
            font-size: 25px;
            font-weight: 850;
            line-height: 1;
            flex-shrink: 0;
            transform: translateY(14px);
        }

        .cs-logo-icon {
            width: 40px;
            height: 40px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: #fff;
            color: var(--home-blue);
            font-size: 20px;
            flex-shrink: 0;
        }

        /* JUDUL TENGAH */
        .cs-navbar-title {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 25px;
            font-weight: 700;
            letter-spacing: 1px;
            white-space: nowrap;
            pointer-events: none;
            transform: translate(-50%, calc(-50% + 10px));

        }

        /* TOMBOL PROFIL */
        .cs-profile-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            margin: 0;
            padding: 0;
            border: none;
            border-radius: 50%;
            background: transparent;
            color: #fff;
            cursor: pointer;
            flex-shrink: 0;
            transform: translateY(14px);
        }

        .cs-profile-button i {
            display: block;
            font-size: 40px;
            line-height: 1;
            transform: none;
        }

        .cs-profile-button:hover {
            background: rgba(255, 255, 255, 0.12);
        }

        /* DROPDOWN PROFIL */
        .cs-profile-menu {
            display: none;
            position: absolute;
            top: calc(100% - 2px);
            right: 20px;
            width: 245px;
            padding: 15px;
            border: 1px solid #e4eaf3;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 12px 32px rgba(16, 77, 165, 0.15);
            box-sizing: border-box;
            z-index: 100;
        }

        .cs-profile-menu.show {
            display: block;
        }

        .cs-profile-details {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .cs-profile-avatar {
            width: 43px;
            height: 43px;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: var(--home-blue-light);
            color: var(--home-blue);
            font-size: 18px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .cs-profile-details strong {
            display: block;
            font-size: 13px;
        }

        .cs-profile-details small {
            color: #8592a5;
            font-size: 11px;
        }

        .cs-profile-divider {
            height: 1px;
            margin: 14px 0;
            background: #edf1f7;
        }

        .cs-profile-logout {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #d34848;
            text-align: left;
            font-size: 13px;
            cursor: pointer;
        }

        .cs-profile-logout:hover {
            background: #fff0f0;
        }

        /* MENU UTAMA */
        .cs-menu-container {
            min-height: 370px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 24px;
            padding: 25px 0 45px;
            box-sizing: border-box;
        }

        .cs-menu-card {
            width: 200px;
            height: 200px;
            padding: 20px 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            border: 1px solid #c0d0e3;
            border-radius: 24px;
            background: var(--home-card);
            color: #273a55;
            text-decoration: none;
            text-align: center;
            box-sizing: border-box;
            transition: transform 0.22s ease,
                box-shadow 0.22s ease,
                background 0.22s ease,
                color 0.22s ease;
        }

        .cs-menu-icon {
            display: grid;
            place-items: center;
            width: 48px;
            height: 48px;
            margin-bottom: 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.7);
            color: var(--home-blue-dark);
            font-size: 22px;
            flex-shrink: 0;
            transition: background 0.2s, color 0.2s;
        }

        .cs-menu-title {
            font-size: 17px;
            line-height: 1.5;
            font-weight: 750;
            letter-spacing: 0.1px;
        }

        .cs-menu-description {
            max-width: 170px;
            margin-top: 8px;
            color: #4e6077;
            font-size: 12px;
            line-height: 1.5;
        }

        .cs-menu-arrow {
            position: absolute;
            right: 13px;
            bottom: 12px;
            font-size: 11px;
            opacity: 0;
            transform: translateX(-4px);
            transition: 0.2s;
        }

        .cs-menu-card:hover {
            transform: translateY(-5px);
            background: var(--home-blue);
            color: #fff;
            border-color: var(--home-blue);
            box-shadow: 0 15px 28px rgba(23, 105, 210, 0.2);
        }

        .cs-menu-card:hover .cs-menu-icon {
            background: #fff;
            color: var(--home-blue);
        }

        .cs-menu-card:hover .cs-menu-description {
            color: #e3efff;
        }

        .cs-menu-card:hover .cs-menu-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        /* MENU YANG TIDAK BISA DIAKSES */
        .cs-menu-disabled {
            cursor: not-allowed;
            opacity: 0.75;
        }

        .cs-menu-disabled:hover {
            transform: none;
            background: var(--home-card);
            color: #273a55;
            border-color: #c0d0e3;
            box-shadow: none;
        }

        .cs-menu-disabled:hover .cs-menu-icon {
            background: rgba(255, 255, 255, 0.7);
            color: var(--home-blue-dark);
        }

        .cs-menu-disabled:hover .cs-menu-description {
            color: #4e6077;
        }

        .cs-menu-disabled:hover .cs-menu-arrow {
            opacity: 0;
            transform: none;
        }

        /* FOOTER */
        .cs-home-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 15px 0;
            border-top: 1px solid #e7edf5;
            color: #8b98aa;
            font-size: 12px;
        }

        /* RESPONSIF TABLET DAN HP */
        @media (max-width: 700px) {
            .cs-home-header {
                height: 76px;
                padding: 0 12px;
            }

            .cs-home-logo {
                gap: 6px;
                font-size: 20px;
            }

            .cs-logo-icon {
                width: 36px;
                height: 36px;
                font-size: 18px;
            }

            .cs-navbar-title {
                font-size: 11px;
                letter-spacing: 0;
            }

            .cs-profile-button {
                width: 42px;
                height: 42px;
            }

            .cs-profile-button i {
                font-size: 34px;
            }

            .cs-profile-menu {
                right: 12px;
                width: 235px;
            }

            .cs-menu-container {
                min-height: 0;
                gap: 16px;
                padding: 35px 0 45px;
            }

            .cs-menu-card {
                width: 155px;
                height: 155px;
                padding: 16px 10px;
                border-radius: 18px;
            }

            .cs-menu-icon {
                width: 40px;
                height: 40px;
                font-size: 19px;
                margin-bottom: 10px;
            }

            .cs-menu-title {
                font-size: 13px;
            }

            .cs-menu-description {
                font-size: 10px;
            }
        }

        /* HP KECIL */
        @media (max-width: 370px) {
            .cs-home-header {
                padding: 0 8px;
            }

            .cs-home-logo {
                gap: 5px;
                font-size: 17px;
            }

            .cs-logo-icon {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }

            .cs-navbar-title {
                font-size: 9px;
                letter-spacing: 0;
            }

            .cs-profile-button {
                width: 36px;
                height: 36px;
            }

            .cs-profile-button i {
                font-size: 30px;
            }

            .cs-menu-container {
                gap: 12px;
            }

            .cs-menu-card {
                width: 130px;
                height: 140px;
            }
        }
    </style>


    <script>
        const profileButton = document.getElementById('csProfileButton');
        const profileMenu = document.getElementById('csProfileMenu');

        profileButton?.addEventListener('click', function(event) {
            event.stopPropagation();
            profileMenu.classList.toggle('show');
            profileButton.classList.toggle('open');
        });

        document.addEventListener('click', function(event) {
            if (!profileMenu?.contains(event.target) &&
                !profileButton?.contains(event.target)) {
                profileMenu?.classList.remove('show');
                profileButton?.classList.remove('open');
            }
        });
    </script>
</x-cs-layout>