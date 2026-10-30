<x-admin-layout>

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Tambah Akun Baru
        </h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">

        <form action="{{ route('akun.store') }}" method="POST">

            @csrf

            <!-- Tipe Akun -->
            <div class="mb-4">

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tipe Akun
                </label>

                <select
                    name="tipe_akun"
                    id="tipe_akun"
                    class="w-full px-4 py-2 border rounded-lg"
                    required
                >

                    <option value="">
                        -- Pilih Tipe Akun --
                    </option>

                    <option value="Aset">
                        Aset
                    </option>

                    <option value="Kewajiban">
                        Kewajiban
                    </option>

                    <option value="Ekuitas">
                        Ekuitas
                    </option>

                    <option value="Pendapatan">
                        Pendapatan
                    </option>

                    <option value="Beban/Biaya">
                        Beban / Biaya
                    </option>

                </select>

                @error('tipe_akun')
                    <p class="text-red-500 text-xs mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <!-- Kelompok Akun -->
            <div class="mb-4">

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kelompok Akun
                </label>

                <select
                    name="kelompok_akun"
                    id="kelompok_akun"
                    class="w-full px-4 py-2 border rounded-lg"
                    required
                >
                    <option value="">
                        -- Pilih Kelompok Akun --
                    </option>

                </select>

                @error('kelompok_akun')
                    <p class="text-red-500 text-xs mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <!-- Nama Akun -->
            <div class="mb-4">

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Nama Akun
                </label>

                <input
                    type="text"
                    name="nama_akun"
                    value="{{ old('nama_akun') }}"
                    class="w-full px-4 py-2 border rounded-lg"
                    placeholder="Contoh: Kas"
                    required
                >

            </div>

            <!-- Kode Akun -->
            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kode Akun
                </label>

                <input
                    type="text"
                    name="kode_akun"
                    id="kode_akun"
                    class="w-full px-4 py-2 border rounded-lg bg-gray-100"
                    placeholder="Otomatis"
                    readonly
                >

            </div>

            <div class="flex gap-3">

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg"
                >
                    Simpan Akun
                </button>

                <a
                    href="{{ route('akun.index') }}"
                    class="bg-gray-100 text-gray-600 px-6 py-2 rounded-lg"
                >
                    Batal
                </a>

            </div>

        </form>

    </div>

    <script>
        const tipeAkun = document.getElementById('tipe_akun');
        const kelompokAkun = document.getElementById('kelompok_akun');
        const kodeAkun = document.getElementById('kode_akun');  

        const kelompokData = {

            Aset: {
                "Aset": 1100
            },

            Kewajiban: {
                "Kewajiban": 1100
            },

            Ekuitas: {
                "Ekuitas": 1100
            },

            Pendapatan: {
                "Pendapatan Usaha": 1100
            },

            "Beban/Biaya": {
                "HPP": 1100,
                "Beban Operasional": 2100,
                "Biaya Lain-lain": 3100
            }

        };

        tipeAkun.addEventListener('change', function () {

            const tipe = this.value;

            kelompokAkun.innerHTML =
                '<option value="">-- Pilih Kelompok Akun --</option>';

            kodeAkun.value = '';


            if (kelompokData[tipe]) {

                Object.entries(kelompokData[tipe]).forEach(
                    ([nama, kode]) => {

                        const option =
                            document.createElement('option');

                        option.value = nama;

                        option.textContent = nama;

                        kelompokAkun.appendChild(option);

                    }
                );

            }

        });

        kelompokAkun.addEventListener('change', function () {

            const tipe = tipeAkun.value;
            const kelompok = this.value;


            if (!tipe || !kelompok) {

                kodeAkun.value = '';

                return;
            }

            kodeAkun.value = 'Menghitung...';

            fetch(
                `{{ route('akun.preview-kode') }}?tipe_akun=${encodeURIComponent(tipe)}&kelompok_akun=${encodeURIComponent(kelompok)}`
            )
            .then(response => response.json())
            .then(data => {

                if (data.kode) {

                    kodeAkun.value = data.kode;

                } else {

                    kodeAkun.value = '';

                }

            })
            .catch(error => {

                console.error(error);

                kodeAkun.value =
                    'Gagal menghitung kode';

            });

        });

    </script>

</x-admin-layout>