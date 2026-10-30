<x-admin-layout>

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Ubah Data Akun
        </h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">

        <form action="{{ route('akun.update', $akun->id) }}" method="POST">

            @csrf
            @method('PUT')

            <!-- Tipe Akun -->
            <div class="mb-4">

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tipe Akun
                </label>

                <select
                    name="tipe_akun"
                    id="tipe_akun"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    required
                >

                    <option value="">
                        -- Pilih Tipe Akun --
                    </option>

                    <option
                        value="Aset"
                        {{ old('tipe_akun', $akun->tipe_akun) == 'Aset' ? 'selected' : '' }}
                    >
                        Aset
                    </option>

                    <option
                        value="Kewajiban"
                        {{ old('tipe_akun', $akun->tipe_akun) == 'Kewajiban' ? 'selected' : '' }}
                    >
                        Kewajiban
                    </option>

                    <option
                        value="Ekuitas"
                        {{ old('tipe_akun', $akun->tipe_akun) == 'Ekuitas' ? 'selected' : '' }}
                    >
                        Ekuitas
                    </option>

                    <option
                        value="Pendapatan"
                        {{ old('tipe_akun', $akun->tipe_akun) == 'Pendapatan' ? 'selected' : '' }}
                    >
                        Pendapatan
                    </option>

                    <option
                        value="Beban/Biaya"
                        {{ old('tipe_akun', $akun->tipe_akun) == 'Beban/Biaya' ? 'selected' : '' }}
                    >
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
                    class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500"
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
                    value="{{ old('nama_akun', $akun->nama_akun) }}"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Contoh: Kas"
                    required
                >

                @error('nama_akun')
                    <p class="text-red-500 text-xs mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <!-- Kode Akun -->
            <div class="mb-4">

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Kode Akun
                </label>

                <input
                    type="text"
                    name="kode_akun"
                    id="kode_akun"
                    value="{{ old('kode_akun', $akun->kode_akun) }}"
                    class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-600"
                    readonly
                >

                <p class="text-xs text-gray-500 mt-1">
                    Kode akun dibuat otomatis berdasarkan tipe dan kelompok akun.
                </p>

            </div>

            <!-- Status -->
            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    required
                >

                    <option
                        value="aktif"
                        {{ old('status', $akun->status) == 'aktif' ? 'selected' : '' }}
                    >
                        Aktif
                    </option>

                    <option
                        value="nonaktif"
                        {{ old('status', $akun->status) == 'nonaktif' ? 'selected' : '' }}
                    >
                        Nonaktif
                    </option>

                </select>

                @error('status')
                    <p class="text-red-500 text-xs mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            <!-- Button -->
            <div class="flex gap-3">

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition"
                >
                    Perbarui Akun
                </button>

                <a
                    href="{{ route('akun.index') }}"
                    class="bg-gray-100 text-gray-600 px-6 py-2 rounded-lg font-medium hover:bg-gray-200 transition"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>

    <script>

        const tipeAkun =
            document.getElementById('tipe_akun');

        const kelompokAkun =
            document.getElementById('kelompok_akun');

        const kodeAkun =
            document.getElementById('kode_akun');

        const akunId =
            "{{ $akun->id }}";

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

        const kelompokLama =
            @json(old('kelompok_akun', $akun->kelompok_akun));

        function loadKelompok(tipe, selectedKelompok = '') {

            kelompokAkun.innerHTML =
                '<option value="">-- Pilih Kelompok Akun --</option>';


            if (!kelompokData[tipe]) {
                return;
            }

            Object.entries(
                kelompokData[tipe]
            ).forEach(([nama, kode]) => {

                const option =
                    document.createElement('option');

                option.value = nama;

                option.textContent = nama;

                if (nama === selectedKelompok) {
                    option.selected = true;
                }

                kelompokAkun.appendChild(option);

            });
        }

        loadKelompok(
            tipeAkun.value,
            kelompokLama
        );

        tipeAkun.addEventListener('change', function () {

            const tipe = this.value;

            loadKelompok(tipe);

            kodeAkun.value = '';

        });

        kelompokAkun.addEventListener('change', function () {

            const tipe =
                tipeAkun.value;

            const kelompok =
                this.value;


            if (!tipe || !kelompok) {

                kodeAkun.value = '';

                return;
            }

            const tipeLama =
                "{{ $akun->tipe_akun }}";

            const kelompokLamaDatabase =
                "{{ $akun->kelompok_akun }}";

            if (
                tipe === tipeLama &&
                kelompok === kelompokLamaDatabase
            ) {

                kodeAkun.value =
                    "{{ $akun->kode_akun }}";

                return;
            }

            kodeAkun.value = 'Menghitung...';

            fetch(
                `{{ route('akun.preview-kode') }}?id=${akunId}&tipe_akun=${encodeURIComponent(tipe)}&kelompok_akun=${encodeURIComponent(kelompok)}`
            )
            .then(response => response.json())
            .then(data => {

                if (data.kode) {
                    kodeAkun.value =
                        data.kode;
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