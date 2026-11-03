@php
    $currentTheme = Auth::user()->preferences['theme'] ?? 'light';
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengaturan Akun - SISTA</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" rel="stylesheet">

    {{-- SKRIP INJEKSI DARK MODE INSTAN --}}
    <script>
        (function() {
            const theme = "{{ $currentTheme }}";
            if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <style>
        .cropper-container {
            max-height: 50vh;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease-in-out;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body
    class="min-h-screen bg-[#f4f6f9] dark:bg-[#0b1120] text-[#6c757d] dark:text-[#a0aec0] font-sans antialiased transition-colors duration-300">

    {{-- Decorative Background Top --}}
    <div
        class="absolute top-0 left-0 right-0 h-[280px] bg-gradient-to-b from-[#041C88] to-[#122bad] dark:from-[#0f172a] dark:to-[#1e293b] z-0 transition-colors duration-300">
    </div>

    <div class="relative z-10 max-w-5xl mx-auto mt-10 px-4 pb-16">

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <h1 class="text-3xl font-bold text-white tracking-tight">Pengaturan Akun</h1>

            <a href="{{ route(Auth::user()->homeRoute()) }}"
                class="inline-flex items-center gap-2 rounded-full bg-white/10 dark:bg-gray-800/40 px-5 py-2.5 text-sm font-semibold text-white backdrop-blur-sm transition-all hover:bg-white hover:text-[#041C88] dark:hover:bg-gray-700 dark:hover:text-white shadow-sm border border-white/10">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>

        <!-- FLASH MESSAGE GLOBAL (Hilang Otomatis 3 Detik) -->
        @if (session('success'))
            <div id="flash-message"
                class="mb-6 rounded-xl border-l-4 border-emerald-500 bg-white dark:bg-[#111827] dark:border-emerald-400 p-4 shadow-sm flex items-center gap-4 transition-all duration-500">
                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                    <i class="fa-solid fa-check text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800 dark:text-gray-100">Berhasil Disimpan</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- ============================== -->
            <!-- KOLOM KIRI: INFO & NAVIGASI -->
            <!-- ============================== -->
            <div class="lg:col-span-4 space-y-6">

                <!-- CARD PROFIL SINGKAT -->
                <div
                    class="rounded-2xl bg-white dark:bg-[#111827] border border-transparent dark:border-gray-800 shadow-[0_4px_25px_0_rgba(0,0,0,0.05)] overflow-hidden transition-colors duration-300">
                    <div class="p-8 flex flex-col items-center text-center">

                        <!-- AVATAR UPLOAD -->
                        <div class="relative w-32 h-32 mb-5 group cursor-pointer"
                            onclick="document.getElementById('avatar-input').click()">
                            <img id="current-avatar"
                                src="{{ Auth::user()->avatar_url ?? (Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=eff6ff&color=2563eb&bold=true&size=128') }}"
                                alt="Avatar"
                                class="w-full h-full rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-lg transition-transform duration-300 group-hover:scale-105">

                            <!-- Overlay Hover -->
                            <div
                                class="absolute inset-0 rounded-full bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center text-white backdrop-blur-[2px]">
                                <i class="fa-solid fa-camera mb-1 text-xl"></i>
                                <span class="text-[10px] font-bold uppercase tracking-widest">Ubah Foto</span>
                            </div>

                            <!-- Badge Role -->
                            <div class="absolute bottom-1 right-1 h-8 w-8 rounded-full bg-[#041C88] dark:bg-blue-600 border-2 border-white dark:border-gray-800 flex items-center justify-center text-white shadow-sm"
                                title="{{ Auth::user()->role }}">
                                <i class="fa-solid fa-user-shield text-[10px]"></i>
                            </div>
                        </div>

                        <input type="file" id="avatar-input" class="hidden" accept="image/jpeg,image/png,image/jpg">

                        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white leading-tight">
                            {{ Auth::user()->name }}</h2>
                        <p class="text-sm font-medium text-[#041C88] dark:text-blue-400 mt-1">{{ Auth::user()->role }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 truncate max-w-full">
                            {{ Auth::user()->email }}</p>
                    </div>
                </div>

                <!-- MENU NAVIGASI VERTIKAL -->
                <div
                    class="rounded-2xl bg-white dark:bg-[#111827] border border-transparent dark:border-gray-800 shadow-[0_4px_25px_0_rgba(0,0,0,0.05)] p-2 transition-colors duration-300">
                    <nav class="flex flex-col space-y-1">
                        <button onclick="switchTab('tab-profil', this)"
                            class="nav-tab flex items-center gap-3 w-full text-left px-5 py-3.5 rounded-xl font-semibold text-sm transition-all bg-blue-50 dark:bg-blue-900/30 text-[#041C88] dark:text-blue-400">
                            <i class="fa-regular fa-id-badge w-5 text-center text-lg"></i>
                            Informasi Dasar
                        </button>

                        <button onclick="switchTab('tab-keamanan', this)"
                            class="nav-tab flex items-center gap-3 w-full text-left px-5 py-3.5 rounded-xl font-semibold text-sm text-gray-600 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white">
                            <i class="fa-solid fa-shield-halved w-5 text-center text-lg"></i>
                            Keamanan Akun
                        </button>

                        <button onclick="switchTab('tab-preferensi', this)"
                            class="nav-tab flex items-center gap-3 w-full text-left px-5 py-3.5 rounded-xl font-semibold text-sm text-gray-600 dark:text-gray-400 transition-all hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white">
                            <i class="fa-solid fa-palette w-5 text-center text-lg"></i>
                            Preferensi Tampilan
                        </button>
                    </nav>
                </div>

                <!-- TOMBOL KELUAR -->
                <form action="{{ route('logout') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="button"
                        class="flex w-full items-center justify-center gap-2 rounded-xl bg-white dark:bg-[#111827] border border-transparent dark:border-gray-800 px-5 py-3.5 text-sm font-bold text-red-500 dark:text-red-400 shadow-sm transition hover:bg-red-50 dark:hover:bg-red-950/30 hover:border-red-100"
                        data-confirm-url="{{ route('logout') }}" data-confirm-method="POST" data-confirm-theme="logout"
                        data-confirm-title="Konfirmasi Keluar"
                        data-confirm-message="Sesi akun Anda akan diakhiri. Lanjutkan keluar?"
                        data-confirm-action="Ya, Keluar">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        Keluar dari Sistem
                    </button>
                </form>

            </div>

            <!-- ============================== -->
            <!-- KOLOM KANAN: AREA FORM -->
            <!-- ============================== -->
            <div class="lg:col-span-8 relative">

                <!-- KARD KONTEN UTAMA -->
                <div
                    class="rounded-2xl bg-white dark:bg-[#111827] border border-transparent dark:border-gray-800 shadow-[0_4px_25px_0_rgba(0,0,0,0.05)] overflow-hidden min-h-[400px] transition-colors duration-300">

                    <!-- TAB 1: PROFIL DASAR -->
                    <div id="tab-profil" class="tab-content active p-8">
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Dasar</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Perbarui identitas pribadi dan kontak
                                Anda.</p>
                        </div>

                        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="col-span-1 md:col-span-2">
                                    <label
                                        class="block text-[13px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">Nama
                                        Lengkap</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fa-regular fa-user text-gray-400"></i>
                                        </div>
                                        <input type="text" name="name"
                                            value="{{ old('name', Auth::user()->name) }}" required
                                            class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:bg-white dark:focus:bg-gray-900 focus:border-[#041C88] dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all text-sm font-medium">
                                    </div>
                                    @error('name')
                                        <p class="text-xs font-semibold text-red-500 mt-1.5">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-[13px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">Alamat
                                        Email</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fa-regular fa-envelope text-gray-400"></i>
                                        </div>
                                        <input type="email" name="email"
                                            value="{{ old('email', Auth::user()->email) }}" required
                                            class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:bg-white dark:focus:bg-gray-900 focus:border-[#041C88] dark:focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all text-sm font-medium">
                                    </div>
                                    @error('email')
                                        <p class="text-xs font-semibold text-red-500 mt-1.5">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label
                                        class="block text-[13px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">Username</label>
                                    <div class="relative opacity-60 cursor-not-allowed">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-at text-gray-400"></i>
                                        </div>
                                        <input type="text" value="{{ Auth::user()->username }}" disabled
                                            class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-sm font-medium cursor-not-allowed"
                                            title="Username tidak dapat diubah">
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 mt-6 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#041C88] dark:bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/30 transition-all hover:bg-[#122bad] dark:hover:bg-blue-700 hover:-translate-y-0.5">
                                    <i class="fa-solid fa-check"></i>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 2: KEAMANAN -->
                    <div id="tab-keamanan" class="tab-content p-8">
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Keamanan Akun</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pastikan akun Anda menggunakan kata
                                sandi yang kuat.</p>
                        </div>

                        <div
                            class="mb-8 rounded-xl border border-amber-200 dark:border-amber-900/50 bg-amber-50 dark:bg-amber-950/20 p-4 flex gap-4">
                            <div class="shrink-0 pt-0.5">
                                <i class="fa-solid fa-shield-cat text-amber-500 text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-amber-800 dark:text-amber-300">Tips Kata Sandi</h4>
                                <p class="text-xs text-amber-700 dark:text-amber-400 mt-1 leading-relaxed">Gunakan
                                    minimal 8 karakter dengan perpaduan huruf besar, huruf kecil, dan angka. Jangan
                                    gunakan kata sandi yang sama dengan akun lain.</p>
                            </div>
                        </div>

                        <form action="{{ route('profile.password') }}" method="POST" class="space-y-6 max-w-xl">
                            @csrf
                            @method('PUT')

                            <div>
                                <label
                                    class="block text-[13px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">Password
                                    Saat Ini</label>
                                <x-password-input name="current_password" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:bg-white dark:focus:bg-gray-900 focus:border-amber-500 outline-none transition-all text-sm" />
                            </div>

                            <div>
                                <label
                                    class="block text-[13px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">Password
                                    Baru</label>
                                <x-password-input name="new_password" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:bg-white dark:focus:bg-gray-900 focus:border-amber-500 outline-none transition-all text-sm" />
                            </div>

                            <div>
                                <label
                                    class="block text-[13px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">Konfirmasi
                                    Password Baru</label>
                                <x-password-input name="new_password_confirmation" required
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:bg-white dark:focus:bg-gray-900 focus:border-amber-500 outline-none transition-all text-sm" />
                            </div>

                            <div class="pt-6 mt-6 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-800 dark:bg-gray-700 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-gray-500/20 transition-all hover:bg-gray-900 dark:hover:bg-gray-600 hover:-translate-y-0.5">
                                    <i class="fa-solid fa-lock"></i>
                                    Perbarui Password
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB 3: PREFERENSI (OTOMATIS TANPA TOMBOL) -->
                    <div id="tab-preferensi" class="tab-content p-8">
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Preferensi Tampilan</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pilih tema antarmuka. Perubahan akan
                                langsung diterapkan dan disimpan.</p>
                        </div>

                        <form id="form-theme-preference" action="{{ route('profile.preferences') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <label
                                class="block text-[13px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-4">Mode
                                Warna Sistem</label>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                                <!-- Light Mode -->
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="theme" value="light" class="peer sr-only"
                                        {{ $currentTheme == 'light' ? 'checked' : '' }}>
                                    <div
                                        class="h-full border-2 border-transparent bg-gray-50 dark:bg-gray-800/60 rounded-2xl p-5 text-center transition-all peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/40 peer-checked:border-blue-500 peer-checked:shadow-md group-hover:bg-gray-100 dark:group-hover:bg-gray-800">
                                        <div
                                            class="mx-auto w-12 h-12 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center shadow-sm mb-3 text-yellow-500 text-xl">
                                            <i class="fa-solid fa-sun"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Terang</h4>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 leading-tight">
                                            Cocok untuk ruang dengan cahaya cukup.</p>
                                    </div>
                                    <div
                                        class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 text-blue-600 dark:text-blue-400 transition-opacity">
                                        <i class="fa-solid fa-circle-check text-lg"></i>
                                    </div>
                                </label>

                                <!-- Dark Mode -->
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="theme" value="dark" class="peer sr-only"
                                        {{ $currentTheme == 'dark' ? 'checked' : '' }}>
                                    <div
                                        class="h-full border-2 border-transparent bg-gray-50 dark:bg-gray-800/60 rounded-2xl p-5 text-center transition-all peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/40 peer-checked:border-blue-500 peer-checked:shadow-md group-hover:bg-gray-100 dark:group-hover:bg-gray-800">
                                        <div
                                            class="mx-auto w-12 h-12 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center shadow-sm mb-3 text-blue-400 text-xl">
                                            <i class="fa-solid fa-moon"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Gelap</h4>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 leading-tight">
                                            Mengurangi kelelahan mata malam hari.</p>
                                    </div>
                                    <div
                                        class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 text-blue-600 dark:text-blue-400 transition-opacity">
                                        <i class="fa-solid fa-circle-check text-lg"></i>
                                    </div>
                                </label>

                                <!-- System Mode -->
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="theme" value="system" class="peer sr-only"
                                        {{ $currentTheme == 'system' ? 'checked' : '' }}>
                                    <div
                                        class="h-full border-2 border-transparent bg-gray-50 dark:bg-gray-800/60 rounded-2xl p-5 text-center transition-all peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/40 peer-checked:border-blue-500 peer-checked:shadow-md group-hover:bg-gray-100 dark:group-hover:bg-gray-800">
                                        <div
                                            class="mx-auto w-12 h-12 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center shadow-sm mb-3 text-gray-600 dark:text-gray-300 text-xl">
                                            <i class="fa-solid fa-laptop"></i>
                                        </div>
                                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">Otomatis</h4>
                                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 leading-tight">
                                            Menyesuaikan pengaturan OS perangkat.</p>
                                    </div>
                                    <div
                                        class="absolute top-3 right-3 opacity-0 peer-checked:opacity-100 text-blue-600 dark:text-blue-400 transition-opacity">
                                        <i class="fa-solid fa-circle-check text-lg"></i>
                                    </div>
                                </label>

                            </div>

                            @error('theme')
                                <p class="text-xs font-semibold text-red-500 mt-3">{{ $message }}</p>
                            @enderror
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MODAL CROPPER FOTO PROFIL -->
    <div id="cropper-modal"
        class="hidden fixed inset-0 z-50 bg-gray-900/80 backdrop-blur-sm flex items-center justify-center p-4 transition-opacity">
        <div
            class="bg-white dark:bg-[#111827] rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-transparent dark:border-gray-800">

            <div
                class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50">
                <h3 class="font-bold text-gray-900 dark:text-white text-lg">Posisikan Foto Anda</h3>
                <button onclick="closeCropper()"
                    class="h-8 w-8 rounded-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-400 hover:text-red-500 hover:border-red-200 transition flex items-center justify-center">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="p-6 bg-white dark:bg-[#111827] flex justify-center">
                <div
                    class="w-full max-w-[400px] overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                    <img id="cropper-image" class="block max-w-full">
                </div>
            </div>

            <div
                class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50 flex justify-end gap-3">
                <button onclick="closeCropper()"
                    class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 font-bold text-sm transition">Batal</button>
                <button id="save-crop-btn"
                    class="px-5 py-2.5 bg-[#041C88] dark:bg-blue-600 text-white rounded-xl hover:bg-[#122bad] dark:hover:bg-blue-700 font-bold text-sm transition flex items-center gap-2 shadow-md">
                    <i class="fa-solid fa-crop-simple"></i> Potong & Simpan
                </button>
            </div>
        </div>
    </div>

    <!-- SCRIPT UTAMA -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    <script>
        // --- 1. Auto Hide Flash Message Setelah 3 Detik ---
        const flashMsg = document.getElementById('flash-message');
        if (flashMsg) {
            setTimeout(() => {
                flashMsg.classList.add('opacity-0', '-translate-y-2');
                setTimeout(() => flashMsg.remove(), 500);
            }, 3000);
        }

        // --- 2. Logika Switch Theme Instan & Auto Save AJAX ---
        function applyThemeLocally(theme) {
            if (theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        document.querySelectorAll('input[name="theme"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const selectedTheme = this.value;

                // Ganti tampilan visual instan di browser
                applyThemeLocally(selectedTheme);

                // Kirim preferensi ke server via AJAX di latar belakang
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute(
                    'content'));
                formData.append('_method', 'PUT');
                formData.append('theme', selectedTheme);

                fetch("{{ route('profile.preferences') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                }).catch(err => console.error('Gagal menyimpan preferensi:', err));
            });
        });

        // --- 3. Logika Navigasi Tab ---
        function switchTab(tabId, btnElement) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');

            document.querySelectorAll('.nav-tab').forEach(el => {
                el.classList.remove('bg-blue-50', 'dark:bg-blue-900/30', 'text-[#041C88]', 'dark:text-blue-400');
                el.classList.add('text-gray-600', 'dark:text-gray-400', 'hover:bg-gray-50',
                    'dark:hover:bg-gray-800');
            });

            btnElement.classList.remove('text-gray-600', 'dark:text-gray-400', 'hover:bg-gray-50',
            'dark:hover:bg-gray-800');
            btnElement.classList.add('bg-blue-50', 'dark:bg-blue-900/30', 'text-[#041C88]', 'dark:text-blue-400');
        }

        // --- 4. Logika Upload Foto Cropper.js ---
        let cropper = null;
        const avatarInput = document.getElementById('avatar-input');
        const cropperModal = document.getElementById('cropper-modal');
        const cropperImage = document.getElementById('cropper-image');
        const saveCropBtn = document.getElementById('save-crop-btn');
        const currentAvatar = document.getElementById('current-avatar');

        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert("Ukuran gambar terlalu besar. Maksimal 5MB.");
                    avatarInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(event) {
                    cropperImage.src = event.target.result;
                    cropperModal.classList.remove('hidden');

                    if (cropper) cropper.destroy();

                    cropper = new Cropper(cropperImage, {
                        aspectRatio: 1,
                        viewMode: 1,
                        dragMode: 'move',
                        autoCropArea: 0.9,
                        guides: false,
                        center: false,
                        highlight: false,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        toggleDragModeOnDblclick: false,
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        function closeCropper() {
            cropperModal.classList.add('hidden');
            avatarInput.value = '';
            if (cropper) cropper.destroy();
        }

        saveCropBtn.addEventListener('click', function() {
            if (!cropper) return;

            const originalBtnContent = saveCropBtn.innerHTML;
            saveCropBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Mengunggah...';
            saveCropBtn.disabled = true;
            saveCropBtn.classList.add('opacity-70', 'cursor-not-allowed');

            cropper.getCroppedCanvas({
                width: 400,
                height: 400
            }).toBlob(function(blob) {
                const formData = new FormData();
                formData.append('avatar', blob, 'avatar.jpg');

                fetch("{{ route('profile.avatar') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            currentAvatar.src = data.avatar_url;
                            closeCropper();
                        } else {
                            throw new Error("Gagal menyimpan.");
                        }
                    })
                    .catch(error => alert('Terjadi kesalahan saat mengunggah foto.'))
                    .finally(() => {
                        saveCropBtn.innerHTML = originalBtnContent;
                        saveCropBtn.disabled = false;
                        saveCropBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                    });
            }, 'image/jpeg', 0.85);
        });
    </script>

    <x-modal-konfirmasi />
</body>

</html>
