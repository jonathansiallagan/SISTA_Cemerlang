<x-admin-layout-stisla>

    {{-- HEADER HALAMAN --}}
    <div
        class="mb-[30px] flex flex-wrap items-center gap-x-4 gap-y-2 rounded-[3px] bg-white px-[30px] py-5 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
        <h1 class="text-2xl font-bold text-[#34395e]">Laporan Laba Rugi</h1>

        <ol class="ml-auto flex items-center text-[13px] text-[#6c757d]">
            <li class="font-semibold text-[#6777ef]">Laporan Laba Rugi</li>
        </ol>
    </div>

    <div>

        {{-- HEADER DAN TOMBOL EKSPOR --}}
        <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
            <p class="mb-2 text-[#6c757d]">
                Laporan pendapatan, HPP, beban, dan laba bersih perusahaan.
            </p>

            <div class="mb-2 flex flex-wrap items-center">
                <button type="button" onclick="exportLaporanPDF()"
                    class="mb-1 mr-2 inline-flex items-center rounded-[3px] bg-[#fc544b] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#fb160a] disabled:cursor-not-allowed disabled:opacity-65">
                    <i class="fas fa-file-pdf mr-1"></i>
                    Ekspor PDF
                </button>

                <button type="button" onclick="exportLaporanExcel()"
                    class="mb-1 inline-flex items-center rounded-[3px] bg-[#54ca68] px-2.5 py-1.5 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#3fb556] disabled:cursor-not-allowed disabled:opacity-65">
                    <i class="fas fa-file-excel mr-1"></i>
                    Ekspor Excel
                </button>
            </div>
        </div>

        {{-- FILTER TANGGAL DAN CABANG --}}
        <div class="mb-4 rounded-[3px] bg-white p-4 shadow-[0_4px_8px_rgba(0,0,0,0.03)]">
            <form action="{{ route('laporan_laba_rugi.index') }}" method="GET"
                class="grid grid-cols-1 items-end gap-x-2.5 gap-y-2 md:grid-cols-2 {{ Auth::user()->role === 'Admin Pusat' ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }}">

                {{-- CABANG ADMIN PUSAT --}}
                @if (Auth::user()->role === 'Admin Pusat')
                    <div>
                        <label for="cabang_id" class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                            Cabang
                        </label>

                        <select name="cabang_id" id="cabang_id"
                            class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                            <option value="">Semua Cabang</option>

                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->id }}"
                                    {{ (string) request('cabang_id') === (string) $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- TANGGAL MULAI --}}
                <div>
                    <label for="start_date" class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Tanggal Mulai
                    </label>

                    <input type="date" id="start_date" name="start_date" value="{{ $start_date }}" required
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                </div>

                {{-- TANGGAL AKHIR --}}
                <div>
                    <label for="end_date" class="mb-1 block text-xs font-semibold tracking-[0.5px] text-[#34395e]">
                        Tanggal Akhir
                    </label>

                    <input type="date" id="end_date" name="end_date" value="{{ $end_date }}"
                        min="{{ $start_date }}" required
                        class="h-[42px] w-full rounded-[3px] border border-[#e4e6fc] bg-[#fdfdff] px-[15px] text-sm text-[#495057] focus:border-[#95a0f4] focus:outline-none">
                </div>

                {{-- TOMBOL FILTER DAN RESET --}}
                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit"
                        class="inline-flex items-center rounded-[3px] bg-[#6777ef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-white transition-colors hover:bg-[#394eea]">
                        <i class="fas fa-filter mr-1"></i>
                        Tampilkan
                    </button>

                    <a href="{{ route('laporan_laba_rugi.index') }}" title="Reset filter"
                        class="inline-flex items-center rounded-[3px] bg-[#e3eaef] px-3 py-2 text-xs font-semibold tracking-[0.5px] text-[#191d21] transition-colors hover:bg-[#d3dde4]">
                        <i class="fas fa-redo mr-1"></i>
                        Reset
                    </a>
                </div>

            </form>
        </div>

        {{-- TABEL LAPORAN --}}
        <div id="laporan-laba-rugi"
            class="mb-4 overflow-hidden rounded-[3px] bg-white shadow-[0_4px_8px_rgba(0,0,0,0.03)]">

            {{-- JUDUL TABEL --}}
            <div class="flex min-h-[70px] items-center justify-center bg-[#6777ef] px-[25px] py-2">
                <h6 class="text-base font-bold leading-7 text-white">
                    LAPORAN LABA RUGI
                </h6>
            </div>

            {{-- PERIODE --}}
            {{-- Class "card-body" dipertahankan: dipakai JavaScript ekspor (querySelector('.card-body p')). --}}
            <div class="card-body border-b border-[#e3eaef] px-[25px] py-2 text-center">
                <p class="text-[#6c757d]">
                    Periode:
                    <span class="font-bold text-[#191d21]">
                        {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }}
                        s/d
                        {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
                    </span>
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-[13px]">
                    <thead>
                        <tr class="text-[10px] font-bold uppercase tracking-[1.3px] text-[#666]">
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-center">#</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Kode Akun</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-left">Nama Akun</th>
                            <th class="h-[45px] bg-black/[0.04] px-[15px] text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>

                    <tbody>

                        {{-- A. PENDAPATAN --}}
                        <tr class="bg-[#54ca68]/15">
                            <td colspan="4" class="px-[15px] py-3 font-bold">
                                A. PENDAPATAN
                            </td>
                        </tr>

                        @forelse ($pendapatan as $item)
                            <tr class="border-b border-[#f2f2f2]">
                                <td class="px-[15px] py-3 text-center text-[#6c757d]">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-[15px] py-3 text-[#6c757d]">
                                    {{ $item['akun']->kode_akun }}
                                </td>
                                <td class="px-[15px] py-3">
                                    {{ $item['akun']->nama_akun }}
                                </td>
                                <td class="whitespace-nowrap px-[15px] py-3 text-right">
                                    {{ number_format($item['saldo'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-[15px] py-4 text-center text-[#6c757d]">
                                    Belum ada pendapatan.
                                </td>
                            </tr>
                        @endforelse

                        <tr class="bg-[#f8f9fa]">
                            <td colspan="3" class="px-[15px] py-3 text-right font-bold">
                                Total Pendapatan
                            </td>
                            <td class="whitespace-nowrap px-[15px] py-3 text-right font-bold">
                                {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- B. HARGA POKOK PENJUALAN --}}
                        <tr class="bg-[#ffa426]/15">
                            <td colspan="4" class="px-[15px] py-3 font-bold">
                                B. HARGA POKOK PENJUALAN (HPP)
                            </td>
                        </tr>

                        @forelse ($hpp as $item)
                            <tr class="border-b border-[#f2f2f2]">
                                <td class="px-[15px] py-3 text-center text-[#6c757d]">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-[15px] py-3 text-[#6c757d]">
                                    {{ $item['akun']->kode_akun }}
                                </td>
                                <td class="px-[15px] py-3">
                                    {{ $item['akun']->nama_akun }}
                                </td>
                                <td class="whitespace-nowrap px-[15px] py-3 text-right">
                                    {{ number_format($item['saldo'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-[15px] py-4 text-center text-[#6c757d]">
                                    Belum ada HPP.
                                </td>
                            </tr>
                        @endforelse

                        <tr class="bg-[#f8f9fa]">
                            <td colspan="3" class="px-[15px] py-3 text-right font-bold">
                                Total HPP
                            </td>
                            <td class="whitespace-nowrap px-[15px] py-3 text-right font-bold">
                                {{ number_format($totalHpp, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- LABA KOTOR --}}
                        <tr class="bg-[#3abaf4]/15">
                            <td colspan="3" class="px-[15px] py-3 text-right font-bold">
                                LABA KOTOR
                            </td>
                            <td class="whitespace-nowrap px-[15px] py-3 text-right font-bold">
                                {{ number_format($labaKotor, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- C. BEBAN --}}
                        <tr class="bg-[#fc544b]/15">
                            <td colspan="4" class="px-[15px] py-3 font-bold">
                                C. BEBAN OPERASIONAL DAN BIAYA LAIN-LAIN
                            </td>
                        </tr>

                        @forelse ($beban as $item)
                            <tr class="border-b border-[#f2f2f2]">
                                <td class="px-[15px] py-3 text-center text-[#6c757d]">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-[15px] py-3 text-[#6c757d]">
                                    {{ $item['akun']->kode_akun }}
                                </td>
                                <td class="px-[15px] py-3">
                                    {{ $item['akun']->nama_akun }}
                                </td>
                                <td class="whitespace-nowrap px-[15px] py-3 text-right">
                                    {{ number_format($item['saldo'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-[15px] py-4 text-center text-[#6c757d]">
                                    Belum ada beban.
                                </td>
                            </tr>
                        @endforelse

                        <tr class="bg-[#f8f9fa]">
                            <td colspan="3" class="px-[15px] py-3 text-right font-bold">
                                Total Beban
                            </td>
                            <td class="whitespace-nowrap px-[15px] py-3 text-right font-bold">
                                {{ number_format($totalBeban, 0, ',', '.') }}
                            </td>
                        </tr>

                        {{-- LABA / RUGI BERSIH --}}
                        <tr class="{{ $labaBersih >= 0 ? 'bg-[#6777ef]/15' : 'bg-[#fc544b]/15' }}">
                            <td colspan="3" class="px-[15px] py-3 text-right font-bold">
                                {{ $labaBersih >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}
                            </td>
                            <td class="whitespace-nowrap px-[15px] py-3 text-right font-bold">
                                {{ number_format(abs($labaBersih), 0, ',', '.') }}
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>
        </div>

        {{-- LIBRARY PDF --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.4/jspdf.plugin.autotable.min.js"></script>

        {{-- LIBRARY EXCEL --}}
        <script src="https://cdn.sheetjs.com/xlsx-0.20.3/package/dist/xlsx.full.min.js"></script>

        <script>
            // ==========================================
            // EKSPOR PDF
            // ==========================================
            function exportLaporanPDF() {
                const area = document.getElementById('laporan-laba-rugi');
                const tabel = area?.querySelector('table');

                if (!area || !tabel) {
                    alert('Tabel laporan tidak ditemukan.');
                    return;
                }

                if (!window.jspdf || !window.jspdf.jsPDF) {
                    alert('Library PDF belum termuat. Periksa koneksi internet.');
                    return;
                }

                const tombol = document.querySelector(
                    'button[onclick="exportLaporanPDF()"]'
                );

                try {
                    if (tombol) tombol.disabled = true;

                    const {
                        jsPDF
                    } = window.jspdf;

                    const doc = new jsPDF({
                        orientation: 'portrait',
                        unit: 'mm',
                        format: 'a4'
                    });

                    doc.setFont('helvetica', 'bold');
                    doc.setFontSize(16);
                    doc.text('LAPORAN LABA RUGI', 105, 18, {
                        align: 'center'
                    });

                    const periode = area.querySelector('.card-body p');

                    if (periode) {
                        doc.setFont('helvetica', 'normal');
                        doc.setFontSize(10);
                        doc.text(
                            periode.innerText.replace(/\s+/g, ' ').trim(),
                            105,
                            26, {
                                align: 'center'
                            }
                        );
                    }

                    doc.autoTable({
                        html: tabel,
                        startY: 33,
                        theme: 'grid',
                        margin: {
                            top: 12,
                            right: 12,
                            bottom: 12,
                            left: 12
                        },
                        styles: {
                            font: 'helvetica',
                            fontSize: 9,
                            cellPadding: 2,
                            overflow: 'linebreak',
                            textColor: [51, 65, 85],
                            lineColor: [226, 232, 240],
                            lineWidth: 0.1
                        },
                        headStyles: {
                            fillColor: [241, 245, 249],
                            textColor: [51, 65, 85],
                            fontStyle: 'bold'
                        },
                        columnStyles: {
                            0: {
                                cellWidth: 10,
                                halign: 'center'
                            },
                            1: {
                                cellWidth: 25
                            },
                            2: {
                                cellWidth: 'auto'
                            },
                            3: {
                                cellWidth: 38,
                                halign: 'right'
                            }
                        },
                        didParseCell: function(data) {
                            const teks = String(data.cell.text.join(' ')).trim();

                            if (/^A\.\s*PENDAPATAN/i.test(teks)) {
                                data.cell.styles.fillColor = [220, 252, 231];
                                data.cell.styles.textColor = [6, 95, 70];
                                data.cell.styles.fontStyle = 'bold';
                            } else if (/^B\.\s*HARGA POKOK/i.test(teks)) {
                                data.cell.styles.fillColor = [254, 243, 199];
                                data.cell.styles.textColor = [146, 64, 14];
                                data.cell.styles.fontStyle = 'bold';
                            } else if (/^C\.\s*BEBAN/i.test(teks)) {
                                data.cell.styles.fillColor = [255, 228, 230];
                                data.cell.styles.textColor = [159, 18, 57];
                                data.cell.styles.fontStyle = 'bold';
                            } else if (teks === 'LABA KOTOR') {
                                data.cell.styles.fillColor = [219, 234, 254];
                                data.cell.styles.textColor = [30, 64, 175];
                                data.cell.styles.fontStyle = 'bold';
                            } else if (/^(LABA BERSIH|RUGI BERSIH)$/.test(teks)) {
                                data.cell.styles.fillColor =
                                    teks === 'LABA BERSIH' ?
                                    [37, 99, 235] :
                                    [225, 29, 72];

                                data.cell.styles.textColor = [255, 255, 255];
                                data.cell.styles.fontStyle = 'bold';
                            } else if (/^Total\s/i.test(teks)) {
                                data.cell.styles.fontStyle = 'bold';
                                data.cell.styles.fillColor = [248, 250, 252];
                            }
                        }
                    });

                    doc.save(
                        'laporan-laba-rugi-{{ $start_date }}-{{ $end_date }}.pdf'
                    );
                } catch (error) {
                    console.error('Gagal ekspor PDF:', error);
                    alert('PDF gagal dibuat. Periksa Console dengan menekan F12.');
                } finally {
                    if (tombol) tombol.disabled = false;
                }
            }

            // ==========================================
            // EKSPOR EXCEL XLSX
            // ==========================================
            function exportLaporanExcel() {
                const area = document.getElementById('laporan-laba-rugi');
                const tabel = area?.querySelector('table');

                if (!area || !tabel) {
                    alert('Tabel laporan tidak ditemukan.');
                    return;
                }

                if (typeof XLSX === 'undefined') {
                    alert(
                        'Library Excel belum termuat. Periksa koneksi internet, lalu muat ulang halaman.'
                    );
                    return;
                }

                const tombol = document.querySelector(
                    'button[onclick="exportLaporanExcel()"]'
                );

                try {
                    if (tombol) tombol.disabled = true;

                    const periode = area.querySelector('.card-body p');

                    const data = [
                        ['LAPORAN LABA RUGI', '', '', ''],
                        [
                            periode ?
                            periode.innerText.replace(/\s+/g, ' ').trim() :
                            '',
                            '',
                            '',
                            ''
                        ],
                        [],
                        ['#', 'Kode Akun', 'Nama Akun', 'Jumlah (Rp)']
                    ];

                    // Ambil data dari baris tabel laporan
                    tabel.querySelectorAll('tbody tr').forEach(row => {
                        const cells = Array.from(row.querySelectorAll('td'));

                        if (cells.length === 0) return;

                        const teks = cells.map(cell =>
                            cell.innerText.replace(/\s+/g, ' ').trim()
                        );

                        // Baris judul bagian
                        if (cells.length === 1 || cells[0].colSpan === 4) {
                            data.push([teks[0], '', '', '']);
                            return;
                        }

                        // Baris total dan laba/rugi
                        if (cells[0].colSpan === 3 || cells.length === 2) {
                            const label = teks[0] || '';
                            const jumlahText = teks[1] || '';
                            const jumlah = jumlahText ?
                                Number(jumlahText.replace(/\./g, '').replace(/,/g, '.')) :
                                '';

                            data.push([
                                '',
                                '',
                                label,
                                jumlahText && Number.isFinite(jumlah) ?
                                jumlah :
                                jumlahText
                            ]);

                            return;
                        }

                        // Baris akun biasa
                        const nomor = teks[0] || '';
                        const kodeAkun = teks[1] || '';
                        const namaAkun = teks[2] || '';
                        const jumlahText = teks[3] || '';

                        const jumlah = jumlahText ?
                            Number(jumlahText.replace(/\./g, '').replace(/,/g, '.')) :
                            '';

                        data.push([
                            nomor,
                            {
                                t: 's',
                                v: kodeAkun
                            },
                            namaAkun,
                            jumlahText && Number.isFinite(jumlah) ?
                            jumlah :
                            jumlahText
                        ]);
                    });

                    const worksheet = XLSX.utils.aoa_to_sheet(data);

                    // Lebar kolom
                    worksheet['!cols'] = [{
                            wch: 6
                        },
                        {
                            wch: 20
                        },
                        {
                            wch: 42
                        },
                        {
                            wch: 24
                        }
                    ];

                    // Gabungkan judul dan periode
                    worksheet['!merges'] = [{
                            s: {
                                r: 0,
                                c: 0
                            },
                            e: {
                                r: 0,
                                c: 3
                            }
                        },
                        {
                            s: {
                                r: 1,
                                c: 0
                            },
                            e: {
                                r: 1,
                                c: 3
                            }
                        }
                    ];

                    // Judul dan header
                    ['A1', 'A4', 'B4', 'C4', 'D4'].forEach(ref => {
                        if (worksheet[ref]) {
                            worksheet[ref].s = {
                                font: {
                                    bold: true
                                },
                                alignment: {
                                    horizontal: 'center'
                                }
                            };
                        }
                    });

                    if (worksheet['A1']) {
                        worksheet['A1'].s = {
                            font: {
                                bold: true,
                                sz: 16
                            },
                            alignment: {
                                horizontal: 'center'
                            }
                        };
                    }

                    // Format nilai uang
                    for (let r = 4; r < data.length; r++) {
                        const ref = XLSX.utils.encode_cell({
                            r,
                            c: 3
                        });

                        const cell = worksheet[ref];

                        if (cell && typeof cell.v === 'number') {
                            cell.z = '"Rp" #,##0';
                            cell.s = {
                                alignment: {
                                    horizontal: 'right'
                                }
                            };
                        }
                    }

                    const workbook = XLSX.utils.book_new();

                    XLSX.utils.book_append_sheet(
                        workbook,
                        worksheet,
                        'Laporan Laba Rugi'
                    );

                    XLSX.writeFile(
                        workbook,
                        'laporan-laba-rugi-{{ $start_date }}-{{ $end_date }}.xlsx'
                    );
                } catch (error) {
                    console.error('Gagal ekspor Excel:', error);
                    alert('Excel gagal dibuat. Tekan F12 lalu periksa tab Console.');
                } finally {
                    if (tombol) tombol.disabled = false;
                }
            }
        </script>

    </div>
</x-admin-layout-stisla>