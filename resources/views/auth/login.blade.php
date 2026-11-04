<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SISTA - CV Cemerlang</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body class="m-0 font-['Inter',sans-serif] antialiased">
    <main class="relative isolate flex min-h-screen items-center justify-center overflow-hidden bg-[#293f91] px-4 py-8">

        <!-- Efek background merah, biru, dan kuning -->
        <div aria-hidden="true"
            class="pointer-events-none absolute -left-[235px] -top-[20px] -z-10 h-[580px] w-[580px] rounded-full bg-[#f20d18] opacity-[.94] blur-[45px] max-[520px]:-left-[350px]">
        </div>
        <div aria-hidden="true"
            class="pointer-events-none absolute left-[30%] -top-[390px] -z-10 h-[650px] w-[300px] -rotate-[28deg] rounded-full bg-[#ed1728] opacity-90 blur-[45px]">
        </div>
        <div aria-hidden="true"
            class="pointer-events-none absolute -bottom-[170px] -right-[130px] -z-10 h-[500px] w-[500px] rounded-full bg-[#ffdf16] opacity-[.98] blur-[45px] max-[520px]:-right-[300px]">
        </div>
        <div aria-hidden="true"
            class="pointer-events-none absolute -bottom-[180px] right-[26%] -z-10 h-[260px] w-[250px] rounded-full bg-[#ffdb16] opacity-95 blur-[45px]">
        </div>

        <!-- Kartu login transparan -->
        <section
            class="relative z-10 min-h-[611px] w-[min(440px,calc(100vw_-_32px))] rounded-[36px] border-[1.5px] border-white/[.55] bg-white/[.16] px-8 py-9 text-white shadow-[0_25px_60px_rgba(9,20,65,0.25),inset_0_1px_1px_rgba(255,255,255,0.3)] backdrop-blur-[18px] max-[520px]:min-h-0 max-[520px]:rounded-[28px] max-[520px]:px-6 max-[520px]:py-8">

            <!-- Logo perusahaan -->
            <div class="mb-5 flex justify-center">
                <div
                    class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-2xl bg-white p-2 shadow-lg">
                    <img src="{{ asset('images/logo-cemerlang.png') }}" alt="Logo CV Cemerlang" width="96"
                        height="96" class="h-full w-full object-contain">
                </div>
            </div>

            <!-- Identitas aplikasi -->
            <div class="mb-5 text-center">
                <h1 class="text-4xl font-extrabold tracking-wide">SISTA</h1>

                <p class="mt-1 text-sm font-medium">
                    Sistem Informasi dan Transaksi Akuntansi
                </p>

                <p class="mt-1 text-4xl font-extrabold tracking-wide">
                    CV CEMERLANG
                </p>
            </div>

            <!-- FORM LOGIN -->
            <form action="{{ route('login') }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div role="alert"
                        class="mb-5 rounded-lg border border-white/30 bg-red-900/30 p-3 text-sm text-white">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="mb-4">
                    <label for="username" class="mb-2 block pl-1 text-xs font-bold tracking-wide text-white">
                        USERNAME
                    </label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}"
                        autocomplete="username" required autofocus
                        placeholder="admin"
                        class="block h-[46px] w-full rounded-xl border border-slate-300 bg-white px-[14px] text-[#172554] transition duration-200 focus:border-[#d60000] focus:outline-none focus:shadow-[0_0_0_3px_rgba(255,255,255,0.2)]">
                </div>

                <div class="mb-6">
                    <label for="password" class="mb-2 block pl-1 text-xs font-bold tracking-wide text-white">
                        PASSWORD
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password" autocomplete="current-password" required
                            placeholder="password"
                            class="block h-[46px] w-full rounded-xl border border-slate-300 bg-white pl-[14px] pr-11 text-[#172554] transition duration-200 focus:border-[#d60000] focus:outline-none focus:shadow-[0_0_0_3px_rgba(255,255,255,0.2)]">

                        <!-- Tombol toggle mata -->
                        <button type="button" id="togglePassword" aria-label="Tampilkan password" aria-pressed="false"
                            class="absolute inset-y-0 right-0 flex cursor-pointer items-center rounded-r-xl pr-4 text-[#172554] opacity-70 transition hover:opacity-100 focus:outline-none focus-visible:opacity-100 focus-visible:ring-2 focus-visible:ring-[#d60000]">
                            <i class="fa-solid fa-eye" id="eyeIcon" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="mt-1 h-12 w-full cursor-pointer rounded-xl bg-[#d60000] text-sm font-extrabold tracking-wide text-white shadow-[0_8px_18px_rgba(150,0,0,0.28)] transition duration-200 hover:-translate-y-px hover:bg-[#bd0000] focus:outline-none focus-visible:ring-4 focus-visible:ring-white/40 active:translate-y-0">
                    <i class="fa-solid fa-right-to-bracket mr-2" aria-hidden="true"></i>
                    MASUK KE SISTA
                </button>
            </form>

            <!-- Aksen warna di bagian bawah -->
            <div aria-hidden="true" class="mt-7 grid h-1.5 grid-cols-3 overflow-hidden rounded-full">
                <div class="bg-[#29387f]"></div>
                <div class="bg-red-600"></div>
                <div class="bg-yellow-400"></div>
            </div>

        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tombolToggle = document.getElementById('togglePassword');
            const inputPassword = document.getElementById('password');
            const ikonMata = document.getElementById('eyeIcon');

            tombolToggle.addEventListener('click', function () {
                // Jika sedang tersembunyi (type=password), tampilkan teksnya, dan sebaliknya.
                const tampilkan = inputPassword.type === 'password';

                inputPassword.type = tampilkan ? 'text' : 'password';

                // Mata terbuka saat password tersembunyi, mata tercoret saat terlihat.
                ikonMata.classList.toggle('fa-eye', !tampilkan);
                ikonMata.classList.toggle('fa-eye-slash', tampilkan);

                tombolToggle.setAttribute('aria-pressed', String(tampilkan));
                tombolToggle.setAttribute('aria-label', tampilkan ? 'Sembunyikan password' : 'Tampilkan password');
            });
        });
    </script>
</body>

</html>