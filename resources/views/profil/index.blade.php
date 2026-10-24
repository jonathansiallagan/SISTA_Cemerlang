<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - SISTA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-50 font-sans text-gray-800">
    <div class="min-h-screen flex items-start justify-center">
        <div class="w-full max-w-md mt-14 px-4">
            
            <!-- TOMBOL KEMBALI -->
            <div class="mb-3">
                <a href="{{ route(Auth::user()->homeRoute())}}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-[11px] text-gray-600 hover:bg-gray-50 transition">
                    <i class="fa-solid fa-arrow-left text-[9px]"></i>
                    <span>Kembali</span>
                </a>
            </div>

            <!-- PROFILE CARD -->
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <!-- USER HEADER -->
                <div class="px-4 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg bg-blue-50 border border-blue-200 flex items-center justify-center flex-shrink-0">
                            <span class="text-lg font-bold text-blue-600">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <h1 class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</h1>
                                <span class="px-1.5 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[8px] font-medium">{{ Auth::user()->role }}</span>
                            </div>
                            <p class="text-[9px] text-gray-500 mt-0.5 truncate">{{ Auth::user()->email }}</p>
                            <div class="mt-1">
                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-green-50 text-green-600 text-[8px]">
                                    <span class="w-1 h-1 rounded-full bg-green-500"></span> Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PEMBATAS -->
                <div class="border-t border-gray-100"></div>

                <!-- DETAIL INFORMASI -->
                <div class="px-4 py-4">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-[9px] font-semibold text-gray-400 uppercase tracking-wide">Detail Informasi Pengguna</h2>
                    </div>

                    <div class="bg-gray-50 rounded-lg px-3 py-2.5 mb-2.5">
                        <p class="text-[8px] text-gray-400 mb-1">Nama Lengkap</p>
                        <p class="text-[10px] font-medium text-gray-800">{{ Auth::user()->name }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg px-3 py-2.5 mb-2.5">
                        <p class="text-[8px] text-gray-400 mb-1">Username Akun</p>
                        <p class="text-[10px] font-medium text-gray-800">{{ Auth::user()->username ?? '-' }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg px-3 py-2.5 mb-2.5">
                        <p class="text-[8px] text-gray-400 mb-1">Alamat Email</p>
                        <p class="text-[10px] font-medium text-gray-800 break-all">{{ Auth::user()->email }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-lg px-3 py-2.5">
                        <p class="text-[8px] text-gray-400 mb-1">Role / Hak Akses</p>
                        <p class="text-[10px] font-medium text-blue-600">{{ Auth::user()->role }}</p>
                    </div>
                </div>

                <!-- TOMBOL KELUAR -->
                <div class="px-4 pb-4">
                    <button type="button" onclick="openLogoutModal()" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-red-50 text-red-500 text-[10px] font-medium hover:bg-red-100 transition">
                        <i class="fa-solid fa-right-from-bracket text-[9px]"></i> Keluar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- PANGGIL KOMPONEN LOGOUT MODAL -->
    <x-logout-modal />
</body>
</html>