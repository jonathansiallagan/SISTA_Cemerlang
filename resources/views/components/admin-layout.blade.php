<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dasbor Admin - Sistem Percetakan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="h-screen grid grid-cols-[250px_1fr] grid-rows-[70px_1fr] bg-gray-50 text-gray-800 font-sans">
    <!-- KOTAK 1: NAMA SISTEM -->
    <div class="bg-white border-b border-r border-gray-200 flex items-center px-6 z-20">
        <span class="font-bold text-xl text-blue-700 tracking-wide">
            <i class="fa-solid fa-print mr-2"></i> SISTA
        </span>
    </div>
    <!-- KOTAK 2: NAVBAR -->
    <div class="bg-white border-b border-gray-200 flex items-center justify-between px-8 shadow-sm z-10">
        <div class="text-sm text-gray-500 font-medium">Selamat datang, Admin!</div>
        <!-- PROFILE DROPDOWN -->
        <details class="relative">
            <summary class="list-none cursor-pointer flex items-center hover:opacity-80 transition select-none">
                <i class="fa-regular fa-circle-user text-3xl text-gray-600"></i>
            </summary>
            <div class="absolute right-0 top-12 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-[9999] overflow-hidden">
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
                <a href="{{ route('admin') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('admin') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i> Dashboard
                </a>
                <a href="{{ route('transaksi.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('transaksi.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-cash-register w-5 text-center"></i> Transaksi
                </a>     
                <a href="{{ route('pengeluaran.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('pengeluaran.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-money-bill-transfer w-5 text-center"></i>Pengeluaran
                </a>
                <a href="{{ route('produk.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('produk.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-box w-5 text-center"></i> Produk
                </a>
                <a href="{{ route('jurnal.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('jurnal.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-book w-5 text-center"></i> Jurnal Umum
                </a>
                <a href="{{ route('buku_besar.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-medium transition {{ request()->routeIs('buku_besar.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    <i class="fa-solid fa-book-open w-5 text-center"></i> Buku Besar
                </a>
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
    <!-- KOTAK 4: MAIN CONTENT -->
    <div class="overflow-y-auto p-8 w-full">
        {{ $slot }}
    </div>

    <!-- PANGGIL KOMPONEN LOGOUT MODAL -->
    <x-logout-modal />
</body>
</html>