<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Percetakan</title>

    <!-- INI ADALAH STANDAR LARAVEL (Menggunakan Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Login Sistem</h2>

        <!-- Menampilkan pesan error jika login gagal -->
        @error('email')
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm text-center">
                {{ $message }}
            </div>
        @enderror

        <form action="{{ route('login.authenticate') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Masukkan username" required autofocus>

                @error('username')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <x-password-input id="password" name="password" autocomplete="current-password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="••••••••" required />
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                Masuk
            </button>
        </form>
    </div>

</body>

</html>
