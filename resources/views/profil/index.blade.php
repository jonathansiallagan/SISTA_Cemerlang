<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - SISTA</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Inline JS sederhana untuk mengatur pergantian Tab --}}
    <script>
        function switchTab(tabId) {
            // Sembunyikan semua konten tab
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Hapus status aktif dari semua tombol tab
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('border-blue-600', 'text-blue-600');
                el.classList.add('border-transparent', 'text-gray-500');
            });

            // Tampilkan konten tab yang dipilih
            document.getElementById(tabId).classList.remove('hidden');
            // Tandai tombol tab yang dipilih sebagai aktif
            const activeBtn = document.querySelector(`[data-tab="${tabId}"]`);
            activeBtn.classList.remove('border-transparent', 'text-gray-500');
            activeBtn.classList.add('border-blue-600', 'text-blue-600');
        }
    </script>
</head>

<body class="min-h-screen bg-gray-50 font-sans text-gray-800 pb-10">
    <div class="max-w-3xl mx-auto mt-10 px-4">

        <!-- HEADER & TOMBOL KEMBALI -->
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route(Auth::user()->homeRoute()) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Dashboard</span>
            </a>
            <h1 class="text-xl font-bold text-gray-800">Pengaturan Akun</h1>
        </div>

        <!-- NOTIFIKASI SUKSES -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
            <i class="fa-solid fa-circle-check"></i>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        @endif

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">

            <!-- HEADER PROFIL (FOTO & INFO SINGKAT) -->
            <div class="px-6 py-6 border-b border-gray-100 bg-gray-50">
                <div class="flex items-center gap-4">
                    <div class="relative w-20 h-20 rounded-full border-4 border-white shadow-sm overflow-hidden bg-white">
                        @if(Auth::user()->avatar)
                        {{-- Jika punya avatar, panggil dari storage --}}
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                        {{-- Jika tidak punya, buat inisial otomatis pakai API ui-avatars --}}
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=E0F2FE&color=2563EB&bold=true&size=128" alt="Initials" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ Auth::user()->name }}</h2>
                        <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                        <span class="inline-block mt-2 px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold">
                            {{ Auth::user()->role }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- MENU NAVIGASI TAB -->
            <div class="border-b border-gray-200 flex overflow-x-auto">
                <button onclick="switchTab('tab-profil')" data-tab="tab-profil" class="tab-btn flex items-center gap-2 px-6 py-4 border-b-2 font-medium text-sm transition-colors border-blue-600 text-blue-600 whitespace-nowrap">
                    <i class="fa-regular fa-id-badge"></i> Profil Dasar
                </button>
                <button onclick="switchTab('tab-keamanan')" data-tab="tab-keamanan" class="tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm transition-colors whitespace-nowrap">
                    <i class="fa-solid fa-shield-halved"></i> Keamanan
                </button>
                <button onclick="switchTab('tab-preferensi')" data-tab="tab-preferensi" class="tab-btn flex items-center gap-2 px-6 py-4 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm transition-colors whitespace-nowrap">
                    <i class="fa-solid fa-palette"></i> Preferensi Tampilan
                </button>
            </div>

            <div class="p-6">
                <!-- ============================== -->
                <!-- 1. KONTEN TAB PROFIL DASAR -->
                <!-- ============================== -->
                <div id="tab-profil" class="tab-content">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5 max-w-xl">
                            <!-- Input Foto Profil -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ganti Foto Profil</label>
                                <input type="file" name="avatar" accept="image/jpeg,image/png,image/jpg"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="text-xs text-gray-400 mt-1">Format: JPG, JPEG, PNG. Maksimal 2MB.</p>
                                @error('avatar') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Input Nama Lengkap -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Input Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
                                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Input Username (Readonly/Disabled karena biasanya username tidak boleh diganti) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Username (Tidak dapat diubah)</label>
                                <input type="text" value="{{ Auth::user()->username }}" disabled
                                    class="w-full px-4 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 text-sm">
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                    Simpan Perubahan Profil
                                </button>
                            </div>
                        </div>
                    </form>
                </div>


                <!-- ============================== -->
                <!-- 2. KONTEN TAB KEAMANAN -->
                <!-- ============================== -->
                <div id="tab-keamanan" class="tab-content hidden">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5 max-w-xl">
                            <div class="bg-orange-50 border border-orange-200 text-orange-800 p-4 rounded-lg mb-4 text-sm">
                                <i class="fa-solid fa-triangle-exclamation mr-2"></i> Pastikan Anda menggunakan password yang kuat, terdiri dari minimal 8 karakter dengan kombinasi huruf dan angka.
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini</label>
                                <input type="password" name="current_password" required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                                <input type="password" name="new_password" required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                @error('new_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru</label>
                                <input type="password" name="new_password_confirmation" required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="px-6 py-2 bg-gray-800 text-white text-sm font-medium rounded-lg hover:bg-gray-900 transition">
                                    Update Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>


                <!-- ============================== -->
                <!-- 3. KONTEN TAB PREFERENSI -->
                <!-- ============================== -->
                <div id="tab-preferensi" class="tab-content hidden">
                    <form action="{{ route('profile.preferences') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="space-y-5 max-w-xl">

                            @php
                            // Ambil preferensi tema saat ini dari JSON, default: 'light'
                            $currentTheme = Auth::user()->preferences['theme'] ?? 'light';
                            @endphp

                            <label class="block text-sm font-medium text-gray-700 mb-3">Tema Aplikasi</label>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <!-- Light Mode -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="theme" value="light" class="peer sr-only" {{ $currentTheme == 'light' ? 'checked' : '' }}>
                                    <div class="border border-gray-200 rounded-lg p-4 text-center hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:ring-1 peer-checked:ring-blue-500 transition">
                                        <i class="fa-regular fa-sun text-2xl text-yellow-500 mb-2 block"></i>
                                        <span class="text-sm font-medium text-gray-700">Terang (Light)</span>
                                    </div>
                                </label>

                                <!-- Dark Mode -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="theme" value="dark" class="peer sr-only" {{ $currentTheme == 'dark' ? 'checked' : '' }}>
                                    <div class="border border-gray-200 rounded-lg p-4 text-center bg-gray-800 hover:bg-gray-700 peer-checked:border-blue-500 peer-checked:ring-1 peer-checked:ring-blue-500 transition">
                                        <i class="fa-solid fa-moon text-2xl text-blue-300 mb-2 block"></i>
                                        <span class="text-sm font-medium text-gray-200">Gelap (Dark)</span>
                                    </div>
                                </label>

                                <!-- System Mode -->
                                <label class="cursor-pointer">
                                    <input type="radio" name="theme" value="system" class="peer sr-only" {{ $currentTheme == 'system' ? 'checked' : '' }}>
                                    <div class="border border-gray-200 rounded-lg p-4 text-center hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:ring-1 peer-checked:ring-blue-500 transition">
                                        <i class="fa-solid fa-desktop text-2xl text-gray-500 mb-2 block"></i>
                                        <span class="text-sm font-medium text-gray-700">Ikuti Sistem</span>
                                    </div>
                                </label>
                            </div>
                            @error('theme') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                            <div class="pt-6 border-t border-gray-100 mt-8">
                                <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                                    Terapkan Preferensi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <!-- TOMBOL KELUAR DI BAWAH -->
        <div class="mt-6 flex justify-end">
            <button type="button" onclick="openLogoutModal()" class="flex items-center gap-2 px-5 py-2.5 rounded-lg bg-white border border-red-200 text-red-600 text-sm font-medium hover:bg-red-50 transition shadow-sm">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar dari Sistem
            </button>
        </div>
    </div>

    <!-- PANGGIL KOMPONEN LOGOUT MODAL -->
    <x-logout-modal />
</body>

</html>