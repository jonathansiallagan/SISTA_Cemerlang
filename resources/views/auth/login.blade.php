
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
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
        }

        .login-background {
            background: #293f91;
            isolation: isolate;
        }

        .glow {
            position: absolute;
            pointer-events: none;
            border-radius: 50%;
            filter: blur(45px);
            z-index: -1;
        }

        .glow-red {
            width: 580px;
            height: 580px;
            background: #f20d18;
            opacity: .94;
            left: -235px;
            top: -20px;
        }

        .glow-red-top {
            width: 300px;
            height: 650px;
            background: #ed1728;
            opacity: .9;
            left: 30%;
            top: -390px;
            transform: rotate(-28deg);
        }

        .glow-yellow {
            width: 500px;
            height: 500px;
            background: #ffdf16;
            opacity: .98;
            right: -130px;
            bottom: -170px;
        }

        .glow-yellow-bottom {
            width: 250px;
            height: 260px;
            background: #ffdb16;
            opacity: .95;
            right: 26%;
            bottom: -180px;
        }

        .login-card {
            width: 440px;
            min-height: 611px;
            padding: 36px 32px;
            background: rgba(255,255,255,.16);
            border: 1.5px solid rgba(255,255,255,.55);
            border-radius: 36px;
            box-shadow: 0 25px 60px rgba(9,20,65,.25),
                        inset 0 1px 1px rgba(255,255,255,.3);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .login-input {
            display: block;
            width: 100%;
            height: 46px;
            padding: 0 14px;
            color: #172554;
            background: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            outline: none;
            transition: .2s;
        }

        .login-input:focus {
            border-color: #d60000;
            box-shadow: 0 0 0 3px rgba(255,255,255,.2);
        }

        .login-button {
            height: 48px;
            background: #d60000;
            box-shadow: 0 8px 18px rgba(150,0,0,.28);
            transition: .2s;
        }

        .login-button:hover {
            background: #bd0000;
            transform: translateY(-1px);
        }

        .color-line {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            height: 6px;
            overflow: hidden;
            border-radius: 99px;
        }

        @media (max-width: 520px) {
            .login-card {
                width: min(440px, calc(100vw - 32px));
                min-height: auto;
                padding: 32px 24px;
                border-radius: 28px;
            }

            .glow-red { left: -350px; }
            .glow-yellow { right: -300px; }
        }
    </style>
</head>

<body>
    <main class="login-background relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-8">

        <!-- Efek background merah, biru, dan kuning -->
        <div class="glow glow-red"></div>
        <div class="glow glow-red-top"></div>
        <div class="glow glow-yellow"></div>
        <div class="glow glow-yellow-bottom"></div>

        <!-- Kartu login transparan -->
        <section class="login-card relative z-10 text-white">

            <!-- Logo perusahaan -->
            <div class="mb-5 flex justify-center">
                <div class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-2xl bg-white p-2 shadow-lg">
                    <img
                        src="{{ asset('images/logo-cemerlang.png') }}"
                        alt="Logo CV Cemerlang"
                        class="h-full w-full object-contain"
                    >
                </div>
            </div>

            <!-- Identitas aplikasi -->
            <div class="mb-5 text-center">
                <h1 class="text-4xl font-extrabold tracking-wide">SISTA</h1>

                <p class="mt-1 text-sm font-medium text-white">
                    Sistem Informasi dan Transaksi Akuntansi
                </p>

                <p class="mt-1 text-4xl font-extrabold tracking-wide">
                    CV CEMERLANG
                </p>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <x-password-input id="password" name="password" autocomplete="current-password"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="••••••••" required />
            </div>

                @if ($errors->any())
                    <div class="rounded-lg border border-white/30 bg-red-900/30 p-3 text-sm text-white">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label for="username" class="mb-2 block pl-1 text-xs font-bold tracking-wide text-white">
                        USERNAME
                    </label>

                    <input
                        type="text"
                        name="username"
                        id="username"
                        value="{{ old('username') }}"
                        class="login-input"
                        autocomplete="username"
                        placeholder=""
                        required
                        autofocus
                    >
                </div>

                <div>
                    <label for="password" class="mb-2 block pl-1 text-xs font-bold tracking-wide text-white">
                        PASSWORD
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="login-input"
                        autocomplete="current-password"
                        placeholder=""
                        required
                    >
                </div>

                <button
                    type="submit"
                    class="login-button mt-1 w-full rounded-xl text-sm font-extrabold tracking-wide text-white"
                >
                     <i class="fa-solid fa-right-to-bracket mr-2"></i>
                    MASUK KE SISTA
                </button>
            </form>

            <!-- Aksen warna di bagian bawah -->
            <div class="color-line mt-7">
                <div style="background-color: #29387F;"></div>
                <div class="bg-red-600"></div>
                <div class="bg-yellow-400"></div>
            </div>

        </section>
    </main>
</body>
</html>