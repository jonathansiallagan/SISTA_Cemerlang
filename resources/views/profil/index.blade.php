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

    <style>
        .cropper-container {
            max-height: 400px;
        }

        .avatar-overlay {
            opacity: 0;
            transition: opacity 0.2s;
        }

        .group:hover .avatar-overlay {
            opacity: 1;
        }
    </style>

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
            <div class="px-6 py-8 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex flex-col items-center sm:flex-row sm:items-start gap-6">

                <!-- AVATAR DENGAN OVERLAY KLIK -->
                <div class="relative w-28 h-28 rounded-full shadow-md group cursor-pointer border-4 border-white bg-white overflow-hidden" onclick="document.getElementById('avatar-input').click()">

                    <img id="current-avatar"
                        src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=E0F2FE&color=2563EB&bold=true&size=128' }}"
                        alt="Avatar" class="w-full h-full object-cover">

                    <!-- Overlay Hitam saat di-hover -->
                    <div class="avatar-overlay absolute inset-0 bg-black bg-opacity-50 flex flex-col items-center justify-center text-white">
                        <i class="fa-solid fa-camera text-xl mb-1"></i>
                        <span class="text-[10px] font-medium uppercase tracking-wider">Ubah Foto</span>
                    </div>
                </div>

                <!-- Input File Tersembunyi -->
                <input type="file" id="avatar-input" class="hidden" accept="image/jpeg,image/png,image/jpg">

                <div class="text-center sm:text-left mt-2 sm:mt-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ Auth::user()->name }}</h2>
                    <p class="text-sm text-gray-500 mb-2">{{ Auth::user()->email }}</p>
                    <span class="inline-block px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold border border-blue-100">
                        {{ Auth::user()->role }}
                    </span>
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

        <!-- MODAL CROPPER.JS -->
        <div id="cropper-modal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-75 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden">
                <div class="p-4 border-b flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Sesuaikan Gambar Profil</h3>
                    <button onclick="closeCropper()" class="text-gray-400 hover:text-red-500"><i class="fa-solid fa-xmark text-xl"></i></button>
                </div>
                <div class="p-4 bg-gray-100 flex justify-center">
                    <div class="max-w-full">
                        <img id="cropper-image" class="max-h-[60vh] max-w-full">
                    </div>
                </div>
                <div class="p-4 border-t flex justify-end gap-3">
                    <button onclick="closeCropper()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium">Batal</button>
                    <button id="save-crop-btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Foto
                    </button>
                </div>
            </div>
        </div>

        <!-- SCRIPT CROPPER DAN AJAX -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
        <script>
            function switchTab(tabId) {
                document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('.tab-btn').forEach(el => {
                    el.classList.remove('border-blue-600', 'text-blue-600');
                    el.classList.add('border-transparent', 'text-gray-500');
                });
                document.getElementById(tabId).classList.remove('hidden');
                const active = document.querySelector(`[data-tab="${tabId}"]`);
                active.classList.remove('border-transparent', 'text-gray-500');
                active.classList.add('border-blue-600', 'text-blue-600');
            }

            // LOGIKA CROPPER
            let cropper = null;
            const avatarInput = document.getElementById('avatar-input');
            const cropperModal = document.getElementById('cropper-modal');
            const cropperImage = document.getElementById('cropper-image');
            const saveCropBtn = document.getElementById('save-crop-btn');
            const currentAvatar = document.getElementById('current-avatar');

            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        cropperImage.src = event.target.result;
                        cropperModal.classList.remove('hidden');

                        if (cropper) {
                            cropper.destroy();
                        }

                        cropper = new Cropper(cropperImage, {
                            aspectRatio: 1, // Kunci rasio 1:1 (Persegi/Lingkaran)
                            viewMode: 1,
                            autoCropArea: 1,
                        });
                    };
                    reader.readAsDataURL(file);
                }
            });

            function closeCropper() {
                cropperModal.classList.add('hidden');
                avatarInput.value = ''; // Reset input file
                if (cropper) cropper.destroy();
            }

            // KETIKA TOMBOL "SIMPAN FOTO" DITEKAN (UPLOAD VIA AJAX)
            saveCropBtn.addEventListener('click', function() {
                if (!cropper) return;

                saveCropBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Menyimpan...';
                saveCropBtn.disabled = true;

                // Dapatkan gambar hasil crop sebagai File/Blob (dikompres otomatis)
                cropper.getCroppedCanvas({
                    width: 500,
                    height: 500
                }).toBlob(function(blob) {

                    const formData = new FormData();
                    formData.append('avatar', blob, 'avatar.jpg');

                    // Kirim ke server secara diam-diam (AJAX)
                    fetch("{{ route('profile.avatar') }}", {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                currentAvatar.src = data.avatar_url; // Ganti gambar di layar
                                closeCropper();
                            }
                        })
                        .catch(error => alert('Terjadi kesalahan saat mengunggah gambar.'))
                        .finally(() => {
                            saveCropBtn.innerHTML = '<i class="fa-solid fa-save"></i> Simpan Foto';
                            saveCropBtn.disabled = false;
                        });
                }, 'image/jpeg', 0.9);
            });
        </script>

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