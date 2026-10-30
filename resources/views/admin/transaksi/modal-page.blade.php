
<x-cs-layout :fullwidth="true">

    {{-- MODAL TRANSAKSI TAILWIND --}}
    <div id="halaman-modal-transaksi"
         class="fixed inset-0 z-[1000] flex items-center justify-center bg-gray-900/60 p-3 sm:p-5">

        <div class="flex w-full max-w-[1500px] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
             style="height: calc(100vh - 40px);">

            {{-- HEADER MODAL --}}
            <div class="flex shrink-0 items-center justify-between border-b border-gray-200 bg-white px-5 py-4 sm:px-7">

                <div>
                    <h2 class="text-xl font-bold text-gray-800"
                        id="judul-modal-transaksi">
                        Transaksi Baru
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Input pesanan pelanggan
                    </p>
                </div>

                <button type="button"
                        id="modal-tutup"
                        aria-label="Tutup"
                        class="flex h-9 w-9 items-center justify-center rounded-full text-2xl text-gray-500 transition hover:bg-gray-100 hover:text-gray-800">
                    &times;
                </button>

            </div>

            {{-- PESAN ERROR --}}
            @if (session('error'))
                <div class="mx-5 mt-4 shrink-0 rounded-lg border border-red-300 bg-red-50 p-4 text-red-700 sm:mx-7">
                    <strong>Transaksi gagal disimpan!</strong>
                    <p class="mt-1 text-sm">{{ session('error') }}</p>
                </div>
            @endif

            {{-- BODY MODAL --}}
            <div class="min-h-0 flex-1 overflow-y-auto overflow-x-hidden p-4 sm:p-6">

                @include('admin.transaksi.form')

            </div>

        </div>
    </div>

    <script>
        (function () {
            function inisialisasiModalTransaksi() {

                const modalElement = document.getElementById(
                    'halaman-modal-transaksi'
                );

                if (!modalElement) {
                    console.error('Modal transaksi tidak ditemukan.');
                    return;
                }

                /*
                 * Pindahkan modal langsung ke body.
                 * Menghindari masalah stacking context dari layout.
                 */
                if (modalElement.parentElement !== document.body) {
                    document.body.appendChild(modalElement);
                }

                const form = document.getElementById(
                    'form-transaksi-modal'
                );

                const daftar = document.getElementById(
                    'modal-daftar-produk'
                );

                const template = document.getElementById(
                    'modal-template-produk'
                );

                const tombolTambah = document.getElementById(
                    'modal-tambah-produk'
                );

                const tombolTutup = document.getElementById(
                    'modal-tutup'
                );

                const tombolBatal = document.getElementById(
                    'modal-tombol-batal'
                );

                if (!form || !daftar || !template || !tombolTambah) {
                    console.error(
                        'Elemen form tidak lengkap. Periksa form.blade.php.'
                    );
                    return;
                }

                if (form.dataset.initialized === 'true') {
                    return;
                }

                form.dataset.initialized = 'true';

                let modalDitutup = false;
                let sedangKonfirmasi = false;

                function tutupModal() {
                    if (modalDitutup) return;

                    modalDitutup = true;
                    modalElement.classList.add('hidden');

                    window.location.href = "{{ route('transaksi.index') }}";
                }

                if (tombolTutup) {
                    tombolTutup.addEventListener('click', tutupModal);
                }

                if (tombolBatal) {
                    tombolBatal.addEventListener('click', tutupModal);
                }

                // Klik area luar modal untuk menutup.
                modalElement.addEventListener('click', function (event) {
                    if (event.target === modalElement) {
                        tutupModal();
                    }
                });

                // Tombol Escape untuk menutup modal.
                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && !modalDitutup) {
                        tutupModal();
                    }
                });

                const angka = input =>
                    Number.parseFloat(input?.value) || 0;

                const rupiah = nilai =>
                    'Rp' + Number(nilai || 0).toLocaleString('id-ID', {
                        maximumFractionDigits: 0
                    });

                function isM2(satuan) {
                    return ['m2', 'm²', 'meter persegi'].includes(
                        (satuan || '').toLowerCase().trim()
                    );
                }

                // HITUNG TOTAL
                function hitungTotal() {
                    let totalBarang = 0;
                    let totalEdit = 0;

                    daftar.querySelectorAll('.modal-produk-row')
                        .forEach(row => {

                            totalBarang += angka(
                                row.querySelector('.modal-harga')
                            );

                            totalEdit += angka(
                                row.querySelector('.modal-edit')
                            );

                        });

                    const totalBarangEl = document.getElementById(
                        'modal-total-barang'
                    );

                    const totalEditEl = document.getElementById(
                        'modal-total-edit'
                    );

                    const totalPendapatanEl = document.getElementById(
                        'modal-total-pendapatan'
                    );

                    if (totalBarangEl) {
                        totalBarangEl.textContent = rupiah(totalBarang);
                    }

                    if (totalEditEl) {
                        totalEditEl.textContent = rupiah(totalEdit);
                    }

                    if (totalPendapatanEl) {
                        totalPendapatanEl.textContent =
                            rupiah(totalBarang + totalEdit);
                    }
                }

                // HITUNG PER BARIS PRODUK
                function hitungBaris(row) {
                    if (!row) return;

                    const select = row.querySelector(
                        '.modal-produk-select'
                    );

                    const option = select.options[select.selectedIndex];

                    const berbasisLuas = Boolean(select.value) &&
                        isM2(option?.dataset.satuan);

                    const ukuran = row.querySelector('.modal-ukuran');

                    const tanpaUkuran = row.querySelector(
                        '.modal-tanpa-ukuran'
                    );

                    const tinggi = row.querySelector('.modal-tinggi');
                    const lebar = row.querySelector('.modal-lebar');

                    ukuran.classList.toggle('hidden', !berbasisLuas);

                    tanpaUkuran.classList.toggle('hidden', berbasisLuas);

                    tinggi.required = berbasisLuas;
                    lebar.required = berbasisLuas;

                    if (!berbasisLuas) {
                        tinggi.value = '';
                        lebar.value = '';
                    }

                    const jumlah = Math.max(
                        1,
                        angka(row.querySelector('.modal-jumlah'))
                    );

                    const luas = berbasisLuas
                        ? angka(tinggi) * angka(lebar) * jumlah
                        : 0;

                    const status = row.querySelector('.modal-status');
                    const infoLuas = row.querySelector('.modal-luas');

                    if (status) {
                        status.textContent = !select.value
                            ? 'Pilih produk.'
                            : berbasisLuas
                                ? 'Perhitungan berdasarkan luas.'
                                : 'Perhitungan berdasarkan jumlah barang.';
                    }

                    if (infoLuas) {
                        infoLuas.textContent = berbasisLuas
                            ? 'Luas total: ' +
                              luas.toLocaleString('id-ID', {
                                  maximumFractionDigits: 2
                              }) + ' m²'
                            : '';
                    }

                    const harga = angka(
                        row.querySelector('.modal-harga')
                    );

                    const edit = angka(
                        row.querySelector('.modal-edit')
                    );

                    const subtotal = row.querySelector('.modal-subtotal');

                    if (subtotal) {
                        subtotal.textContent = rupiah(harga + edit);
                    }

                    hitungTotal();
                }

                // PERBARUI NOMOR BARIS
                function perbaruiNomor() {
                    const rows = daftar.querySelectorAll(
                        '.modal-produk-row'
                    );

                    rows.forEach((row, index) => {
                        const nomor = row.querySelector('.modal-nomor');

                        const tombolHapus = row.querySelector(
                            '.modal-hapus-produk'
                        );

                        if (nomor) {
                            nomor.textContent = index + 1;
                        }

                        if (tombolHapus) {
                            tombolHapus.disabled = rows.length === 1;

                            tombolHapus.classList.toggle(
                                'opacity-40',
                                tombolHapus.disabled
                            );

                            tombolHapus.classList.toggle(
                                'cursor-not-allowed',
                                tombolHapus.disabled
                            );
                        }
                    });
                }

                // TAMBAH PRODUK
                function tambahProduk() {
                    const fragment = template.content.cloneNode(true);

                    const row = fragment.querySelector(
                        '.modal-produk-row'
                    );

                    daftar.appendChild(fragment);

                    perbaruiNomor();
                    hitungBaris(row);
                }

                tombolTambah.addEventListener('click', tambahProduk);

                // EVENT PERUBAHAN SELECT
                daftar.addEventListener('change', function (event) {
                    const row = event.target.closest('.modal-produk-row');

                    if (row) {
                        hitungBaris(row);
                    }
                });

                // EVENT INPUT
                daftar.addEventListener('input', function (event) {
                    if (event.target.matches(
                        '.modal-jumlah, .modal-tinggi, .modal-lebar, .modal-harga, .modal-edit'
                    )) {
                        hitungBaris(
                            event.target.closest('.modal-produk-row')
                        );
                    }
                });

                // HAPUS PRODUK
                daftar.addEventListener('click', function (event) {
                    const tombol = event.target.closest(
                        '.modal-hapus-produk'
                    );

                    if (!tombol || tombol.disabled) return;

                    tombol.closest('.modal-produk-row').remove();

                    perbaruiNomor();
                    hitungTotal();
                });

                // KONFIRMASI SIMPAN
                form.addEventListener('submit', function (event) {

                    if (sedangKonfirmasi) {
                        return;
                    }

                    event.preventDefault();

                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    if (typeof Swal === 'undefined') {
                        if (window.confirm(
                            'Yakin ingin menyimpan transaksi ini?'
                        )) {
                            sedangKonfirmasi = true;
                            HTMLFormElement.prototype.submit.call(form);
                        }

                        return;
                    }

                    sedangKonfirmasi = true;

                    Swal.fire({
                        title: 'Simpan transaksi?',
                        text: 'Pastikan data pelanggan, produk, jumlah, dan harga sudah benar.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Periksa Kembali',
                        reverseButtons: true,
                        confirmButtonColor: '#2563eb',
                        cancelButtonColor: '#6b7280'
                    }).then(function (result) {

                        if (result.isConfirmed) {

                            const tombolSimpan = document.getElementById(
                                'modal-tombol-proses'
                            );

                            if (tombolSimpan) {
                                tombolSimpan.disabled = true;
                                tombolSimpan.textContent =
                                    'Menyimpan transaksi...';
                            }

                            HTMLFormElement.prototype.submit.call(form);

                        } else {
                            sedangKonfirmasi = false;
                        }

                    }).catch(function (error) {
                        sedangKonfirmasi = false;

                        console.error(
                            'Konfirmasi transaksi gagal:',
                            error
                        );
                    });
                });

                // BARIS PERTAMA
                if (!daftar.querySelector('.modal-produk-row')) {
                    tambahProduk();
                }

            }

            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    inisialisasiModalTransaksi,
                    { once: true }
                );
            } else {
                inisialisasiModalTransaksi();
            }

        })();
    </script>

</x-cs-layout>
