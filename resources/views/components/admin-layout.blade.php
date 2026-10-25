
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dasbor {{ Auth::user()->role }} - SISTA CV Cemerlang</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="min-h-screen bg-slate-100 font-sans text-slate-800">

    <div class="grid min-h-screen grid-cols-1 md:grid-cols-[250px_minmax(0,1fr)]">

        <!-- SIDEBAR -->
        <aside class="flex min-h-screen flex-col justify-between bg-[#19294b] text-white">

            <div>

                <!-- HEADER LOGO -->
                <div class="flex items-center gap-3 border-b border-slate-200 bg-white px-4 py-4">

                    <div class="shrink-0 rounded-xl bg-gradient-to-br from-indigo-500 via-blue-500 to-cyan-400 p-[3px] shadow-md shadow-blue-500/20">
                        <div class="flex h-10 w-10 items-center justify-center rounded-[9px] bg-white p-1">
                            <img
                                src="{{ asset('images/logo-cemerlang.png') }}"
                                alt="Logo CV Cemerlang"
                                class="h-full w-full object-contain"
                            >
                        </div>
                    </div>

                    <div class="min-w-0">
                        <h1 class="text-lg font-extrabold leading-5 tracking-tight text-blue-700">
                            SISTA
                        </h1>

                        <p class="mt-1 flex items-center gap-1.5 whitespace-nowrap text-[10px] font-medium text-slate-500">
                            <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                            CV Cemerlang
                        </p>
                    </div>

                </div>

                <!-- NAVIGASI SIDEBAR -->
                <div class="px-3 py-6">

                    <!-- MENU UTAMA -->
                    <div class="mb-3 flex items-center justify-between px-3">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
                            Menu Utama
                        </span>

                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                    </div>

                    <nav class="mb-8 flex flex-col gap-1.5">

                        <!-- DASHBOARD -->
                        <a href="{{ route(Auth::user()->homeRoute()) }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-semibold transition-all duration-200
                           {{ request()->is('admin') || request()->is('admin/*') || request()->is('cs') || request()->is('cs/*')
                               ? 'bg-gradient-to-r from-blue-600/30 to-indigo-600/30 text-white shadow-lg shadow-blue-500/10'
                               : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">

                            <i class="fa-solid fa-chart-pie w-5 text-center"></i>
                            <span>Dashboard</span>
                        </a>

                        <!-- TRANSAKSI -->
                        <a href="{{ route('transaksi.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition-all duration-200
                           {{ request()->routeIs('transaksi.*')
                               ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25'
                               : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">

                            <i class="fa-solid fa-cash-register w-5 text-center"></i>
                            <span>Transaksi</span>
                        </a>

                        <!-- PENGELUARAN -->
                        <a href="{{ route('pengeluaran.index') }}"
                           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition-all duration-200
                           {{ request()->routeIs('pengeluaran.*')
                               ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25'
                               : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">

                            <i class="fa-solid fa-money-bill-transfer w-5 text-center"></i>
                            <span>Pengeluaran</span>
                        </a>

                        @if (Auth::user()->isAdmin())

                            <!-- PRODUK -->
                            <a href="{{ route('produk.index') }}"
                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition-all duration-200
                               {{ request()->routeIs('produk.*')
                                   ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25'
                                   : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">

                                <i class="fa-solid fa-box w-5 text-center"></i>
                                <span>Produk</span>
                            </a>

                            <!-- JURNAL UMUM -->
                            <a href="{{ route('jurnal.index') }}"
                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition-all duration-200
                               {{ request()->routeIs('jurnal.*')
                                   ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25'
                                   : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">

                                <i class="fa-solid fa-book w-5 text-center"></i>
                                <span>Jurnal Umum</span>
                            </a>

                            <!-- BUKU BESAR -->
                            <a href="{{ route('buku_besar.index') }}"
                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition-all duration-200
                               {{ request()->routeIs('buku_besar.*')
                                   ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25'
                                   : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">

                                <i class="fa-solid fa-book-open w-5 text-center"></i>
                                <span>Buku Besar</span>
                            </a>

                        @endif

                    </nav>

                    <!-- MASTER DATA -->
                    @if (Auth::user()->role === 'Admin Pusat')

                        <div class="mb-3 flex items-center justify-between px-3">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                Master Data
                            </span>

                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        </div>

                        <nav class="flex flex-col gap-1.5">

                            <!-- CABANG -->
                            <a href="{{ route('cabang.index') }}"
                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition-all duration-200
                               {{ request()->routeIs('cabang.*')
                                   ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25'
                                   : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">

                                <i class="fa-solid fa-store w-5 text-center"></i>
                                <span>Cabang</span>
                            </a>

                            <!-- USER -->
                            <a href="{{ route('user.index') }}"
                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition-all duration-200
                               {{ request()->routeIs('user.*')
                                   ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25'
                                   : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">

                                <i class="fa-solid fa-users w-5 text-center"></i>
                                <span>User</span>
                            </a>

                            <!-- AKUN -->
                            <a href="{{ route('akun.index') }}"
                               class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium transition-all duration-200
                               {{ request()->routeIs('akun.*')
                                   ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-500/25'
                                   : 'text-slate-400 hover:bg-white/10 hover:text-white' }}">

                                <i class="fa-solid fa-list-ul w-5 text-center"></i>
                                <span>Akun</span>
                            </a>

                        </nav>

                    @endif

                </div>
            </div>

            <!-- FOOTER SIDEBAR -->
            <div class="border-t border-white/10 px-4 py-4">
                <p class="text-center text-[10px] text-slate-500">
                    SISTA © {{ date('Y') }} CV Cemerlang
                </p>
            </div>

        </aside>

        <!-- AREA KONTEN -->
        <div class="flex min-w-0 flex-col">

            <!-- NAVBAR -->
            <header class="sticky top-0 z-20 flex min-h-[76px] items-center justify-between gap-4 border-b border-slate-200 bg-white px-5 py-4 shadow-sm md:px-8">

                <div class="flex flex-wrap items-center gap-3">

                    <!-- STATUS SISTEM -->
                    <span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        Sistem Aktif
                    </span>

                    <!-- SAPAAN -->
                    <p class="text-sm text-slate-600">
                        Selamat datang,
                        <span class="font-bold text-blue-700">
                            {{ Auth::user()->name }}!
                        </span>
                    </p>

                </div>

                <!-- PROFIL DROPDOWN -->
                <details class="relative shrink-0">

                    <summary class="flex list-none cursor-pointer items-center gap-3">

                        <div class="hidden text-right sm:block">
                            <p class="text-xs font-bold text-slate-800">
                                {{ Auth::user()->name }}
                            </p>

                            <p class="text-[10px] font-semibold text-blue-600">
                                {{ Auth::user()->role }}
                            </p>
                        </div>

                        <div class="relative flex h-10 w-10 items-center justify-center rounded-full border-2 border-blue-100 bg-blue-50 text-blue-600 transition hover:bg-blue-100">
                            <i class="fa-regular fa-circle-user text-2xl"></i>

                            <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-white bg-emerald-400"></span>
                        </div>

                    </summary>

                    <div class="absolute right-0 top-12 z-50 w-48 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">

                        <a href="{{ route('profile') }}"
                           class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50">

                            <i class="fa-regular fa-user w-5 text-slate-500"></i>
                            Profil
                        </a>

                        <div class="border-t border-slate-100"></div>

                        <button
                            type="button"
                            onclick="openLogoutModal()"
                            class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-red-600 transition hover:bg-red-50">

                            <i class="fa-solid fa-right-from-bracket w-5"></i>
                            Keluar
                        </button>

                    </div>

                </details>

            </header>

            <!-- KONTEN HALAMAN -->
            <main class="min-w-0 flex-1 overflow-x-hidden bg-gradient-to-br from-slate-50 via-blue-50/40 to-indigo-50/40 p-4 md:p-8">

                <div class="mx-auto w-full max-w-[1600px]">
                    {{ $slot }}
                </div>

            </main>

<body class="h-screen grid grid-cols-[250px_1fr] grid-rows-[70px_1fr] bg-gray-50 text-gray-800 font-sans">
    <!-- KOTAK 1: NAMA SISTEM -->
    <div class="bg-white border-b border-r border-gray-200 flex items-center px-6 z-20">
        <span class="font-bold text-xl text-blue-700 tracking-wide">
            <i class="fa-solid fa-print mr-2"></i> SISTA
        </span>
    </div>
    <!-- KOTAK 2: NAVBAR -->
    <div class="bg-white border-b border-gray-200 flex items-center justify-between px-8 shadow-sm z-10">
        <div class="text-sm text-gray-500 font-medium">Selamat datang, {{ Auth::user()->name }}!</div>
        <!-- PROFILE DROPDOWN -->
        <details class="relative">
            <summary class="list-none cursor-pointer flex items-center gap-3 p-1.5 pr-3 hover:bg-gray-50 rounded-full border border-transparent hover:border-gray-200 transition select-none">

                <!-- Gambar Profil Kecil -->
                <div class="w-9 h-9 rounded-full border border-gray-200 shadow-sm overflow-hidden bg-gray-50 flex-shrink-0">
                    @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=E0F2FE&color=2563EB&bold=true" alt="Initials" class="w-full h-full object-cover">
                    @endif
                </div>

                <!-- Teks Nama (Tersembunyi di layar kecil) -->
                <div class="hidden sm:block text-left">
                    <p class="text-sm font-semibold text-gray-800 leading-tight">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-gray-500 font-medium">{{ Auth::user()->role }}</p>
                </div>

                <!-- Panah Bawah -->
                <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 ml-1"></i>
            </summary>

            <div class="absolute right-0 top-12 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-9999 overflow-hidden">
                <a href="{{ route('profile') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 transition">
                    <i class="fa-regular fa-user w-5 text-gray-500"></i>
                    <span>Profil</span>
                </a>
                <div class="border-t border-gray-100"></div>
                <button type="button" onclick="openLogoutModal()" class="w-full flex items-center gap-3 px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition text-left">
                    <i class="fa-solid fa-right-from-bracket w-5"></i>
                    <span>Keluar</span>
                </button>
            </div>
        </details>
    </div>
    <!-- KOTAK 3: SIDEBAR -->
    <div class="bg-white border-r border-gray-200 flex flex-col justify-between p-4 z-10 overflow-y-auto">
        <div>
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-3">Menu Utama</div>
            <nav class="flex flex-col gap-1 mb-8">
                <a href="{{ route(Auth::user()->homeRoute()) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('admin', 'cs') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard
                </a>
                <a href="{{ route('transaksi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('transaksi.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-cash-register w-5 text-center"></i> Transaksi
                </a>
                <a href="{{ route('pengeluaran.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('pengeluaran.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-money-bill-transfer w-5 text-center"></i>Pengeluaran
                </a>
                @if (Auth::user()->isAdmin())
                <a href="{{ route('produk.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('produk.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-box w-5 text-center"></i> Produk
                </a>
                <a href="{{ route('jurnal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('jurnal.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-book w-5 text-center"></i> Jurnal Umum
                </a>
                <a href="{{ route('buku_besar.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('buku_besar.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-book-open w-5 text-center"></i> Buku Besar
                </a>
                <a href="{{ route('laporan_laba_rugi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('laporan_laba_rugi.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        <i class="fa-solid fa-chart-line"></i>
                        Laporan Laba Rugi
                    </a>
                @endif
            </nav>
            <!-- MASTER DATA -->
            @if (Auth::user()->role === 'Admin Pusat')
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3 px-3">Master Data</div>
            <nav class="flex flex-col gap-1">
                <a href="{{ route('cabang.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('cabang.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-store w-5 text-center"></i> Cabang
                </a>
                <a href="{{ route('user.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('user.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-users w-5 text-center"></i> User
                </a>
                <a href="{{ route('akun.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('akun.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-list-ul w-5 text-center"></i> Akun
                </a>
            </nav>
            @endif
        </div>

    </div>

    <!-- MODAL LOGOUT -->
    <x-logout-modal />

</body>
</html>