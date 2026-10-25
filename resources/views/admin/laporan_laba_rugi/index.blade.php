
    <x-admin-layout>
        <div class="max-w-5xl mx-auto py-4">

            {{-- HEADER --}}
            <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">
                        Laporan Laba Rugi
                    </h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Laporan pendapatan, HPP, beban, dan laba bersih perusahaan.
                    </p>
                </div>

                {{-- TOMBOL EKSPOR --}}
                <div class="flex items-center gap-2 print:hidden">
                    <button
                        type="button"
                        onclick="exportLaporanPDF()"
                        class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-rose-700 transition"
                    >
                        <i class="fa-solid fa-file-pdf"></i>
                        Ekspor PDF
                    </button>

                    <button
                        type="button"
                        onclick="exportLaporanExcel()"
                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition"
                    >
                        <i class="fa-solid fa-file-excel"></i>
                        Ekspor Excel
                    </button>
                </div>
            </div>

            {{-- FILTER TANGGAL --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-6">
                <form action="{{ route('laporan_laba_rugi.index') }}"
                    method="GET"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Tanggal Mulai
                        </label>
                        <input
                            type="date"
                            name="start_date"
                            value="{{ $start_date }}"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none"
                        >
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Tanggal Akhir
                        </label>
                        <input
                            type="date"
                            name="end_date"
                            value="{{ $end_date }}"
                            min="{{ $start_date }}"
                            required
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none"
                        >
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition"
                        >
                            <i class="fa-solid fa-filter"></i>
                            Tampilkan
                        </button>

                        <a
                            href="{{ route('laporan_laba_rugi.index') }}"
                            class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 transition"
                            title="Reset filter"
                        >
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- TABEL LAPORAN --}}
            <div id="laporan-laba-rugi" class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">

                {{-- JUDUL TABEL --}}
                <div class="bg-blue-600 px-5 py-3 text-center">
                    <h2 class="text-lg font-bold tracking-wide text-white">
                        LAPORAN LABA RUGI
                    </h2>
                </div>

                {{-- PERIODE --}}
                <div class="px-5 py-2 text-center bg-slate-50 border-b border-slate-200">
                    <p class="text-sm text-slate-600">
                        Periode:
                        <span class="font-semibold text-slate-800">
                            {{ \Carbon\Carbon::parse($start_date)->format('d M Y') }}
                            s/d
                            {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}
                        </span>
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-600">
                                <th class="w-32 px-4 py-3 text-left font-semibold border-b border-slate-200">
                                    Kode Akun
                                </th>
                                <th class="px-4 py-3 text-left font-semibold border-b border-slate-200">
                                    Nama Akun
                                </th>
                                <th class="w-52 px-4 py-3 text-right font-semibold border-b border-slate-200">
                                    Jumlah (Rp)
                                </th>
                            </tr>
                        </thead>

                        
                        <tbody class="text-slate-700">

                            {{-- A. PENDAPATAN --}}
                            <tr class="bg-emerald-50">
                                <td colspan="3" class="px-4 py-2 font-bold text-emerald-800 border-b border-emerald-100">
                                    A. PENDAPATAN
                                </td>
                            </tr>

                            @forelse ($pendapatan as $item)
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                    <td class="px-4 py-2 text-slate-500">
                                        {{ $item['akun']->kode_akun }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $item['akun']->nama_akun }}
                                    </td>
                                    <td class="px-4 py-2 text-right tabular-nums whitespace-nowrap">
                                        {{ number_format($item['saldo'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-center text-slate-400">
                                        Belum ada pendapatan.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="bg-slate-50 border-b border-slate-200">
                                <td colspan="2" class="px-4 py-2 text-right font-semibold">
                                    Total Pendapatan
                                </td>
                                <td class="px-4 py-2 text-right font-bold tabular-nums">
                                    {{ number_format($totalPendapatan, 0, ',', '.') }}
                                </td>
                            </tr>

                            {{-- B. HARGA POKOK PENJUALAN --}}
                            <tr class="bg-amber-50">
                                <td colspan="3" class="px-4 py-2 font-bold text-amber-800 border-b border-amber-100">
                                    B. HARGA POKOK PENJUALAN (HPP)
                                </td>
                            </tr>

                            @forelse ($hpp as $item)
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                    <td class="px-4 py-2 text-slate-500">
                                        {{ $item['akun']->kode_akun }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $item['akun']->nama_akun }}
                                    </td>
                                    <td class="px-4 py-2 text-right tabular-nums whitespace-nowrap">
                                        {{ number_format($item['saldo'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-center text-slate-400">
                                        Belum ada HPP.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="bg-slate-50 border-b border-slate-200">
                                <td colspan="2" class="px-4 py-2 text-right font-semibold">
                                    Total HPP
                                </td>
                                <td class="px-4 py-2 text-right font-bold tabular-nums">
                                    {{ number_format($totalHpp, 0, ',', '.') }}
                                </td>
                            </tr>

                            {{-- LABA KOTOR --}}
                            <tr class="bg-blue-50 border-b border-blue-100">
                                <td colspan="2" class="px-4 py-2.5 text-right font-bold text-blue-800">
                                    LABA KOTOR
                                </td>
                                <td class="px-4 py-2.5 text-right font-bold text-blue-800 tabular-nums">
                                    {{ number_format($labaKotor, 0, ',', '.') }}
                                </td>
                            </tr>

                            {{-- C. BEBAN --}}
                            <tr class="bg-rose-50">
                                <td colspan="3" class="px-4 py-2 font-bold text-rose-800 border-b border-rose-100">
                                    C. BEBAN OPERASIONAL DAN BIAYA LAIN-LAIN
                                </td>
                            </tr>

                            @forelse ($beban as $item)
                                <tr class="border-b border-slate-100 hover:bg-slate-50 transition">
                                    <td class="px-4 py-2 text-slate-500">
                                        {{ $item['akun']->kode_akun }}
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $item['akun']->nama_akun }}
                                    </td>
                                    <td class="px-4 py-2 text-right tabular-nums whitespace-nowrap">
                                        {{ number_format($item['saldo'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-2 text-center text-slate-400">
                                        Belum ada beban.
                                    </td>
                                </tr>
                            @endforelse

                            <tr class="bg-slate-50 border-b border-slate-200">
                                <td colspan="2" class="px-4 py-2 text-right font-semibold">
                                    Total Beban
                                </td>
                                <td class="px-4 py-2 text-right font-bold tabular-nums">
                                    {{ number_format($totalBeban, 0, ',', '.') }}
                                </td>
                            </tr>

                            {{-- LABA / RUGI BERSIH --}}
                            <tr class="{{ $labaBersih >= 0 ? 'bg-blue-600 text-white' : 'bg-rose-600 text-white' }}">
                                <td colspan="2" class="px-4 py-2 text-right font-bold tracking-wide">
                                    {{ $labaBersih >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold tabular-nums">
                                    {{ number_format(abs($labaBersih), 0, ',', '.') }}
                                </td>
                            </tr>

                        </tbody>

                    </table>
                </div>
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

            const { jsPDF } = window.jspdf;
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

            const periode = area.querySelector('div.border-b p');

            if (periode) {
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(10);
                doc.text(
                    periode.innerText.replace(/\s+/g, ' ').trim(),
                    105,
                    26,
                    { align: 'center' }
                );
            }

            doc.autoTable({
                html: tabel,
                startY: 33,
                theme: 'grid',
                margin: { top: 12, right: 12, bottom: 12, left: 12 },
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
                    0: { cellWidth: 30 },
                    1: { cellWidth: 'auto' },
                    2: { cellWidth: 42, halign: 'right' }
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
                            teks === 'LABA BERSIH'
                                ? [37, 99, 235]
                                : [225, 29, 72];

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

            const periode = area.querySelector('div.border-b p');

            const data = [
                ['LAPORAN LABA RUGI', '', ''],
                [
                    periode
                        ? periode.innerText.replace(/\s+/g, ' ').trim()
                        : '',
                    '',
                    ''
                ],
                [],
                ['Kode Akun', 'Nama Akun', 'Jumlah (Rp)']
            ];

            // Ambil data dari baris tabel laporan
            tabel.querySelectorAll('tbody tr').forEach(row => {
                const cells = Array.from(row.querySelectorAll('td'));

                if (cells.length === 0) return;

                const teks = cells.map(cell =>
                    cell.innerText.replace(/\s+/g, ' ').trim()
                );

                // Baris judul bagian
                if (cells.length === 1 || cells[0].colSpan === 3) {
                    data.push([teks[0], '', '']);
                    return;
                }

                // Baris total dan laba/rugi
                if (cells[0].colSpan === 2 || cells.length === 2) {
                    const label = teks[0] || '';
                    const jumlahText = teks[1] || '';
                    const jumlah = jumlahText
                        ? Number(jumlahText.replace(/\./g, '').replace(/,/g, '.'))
                        : '';

                    data.push([
                        '',
                        label,
                        jumlahText && Number.isFinite(jumlah)
                            ? jumlah
                            : jumlahText
                    ]);

                    return;
                }

                // Baris akun biasa
                const kodeAkun = teks[0] || '';
                const namaAkun = teks[1] || '';
                const jumlahText = teks[2] || '';

                const jumlah = jumlahText
                    ? Number(jumlahText.replace(/\./g, '').replace(/,/g, '.'))
                    : '';

                data.push([
                    { t: 's', v: kodeAkun },
                    namaAkun,
                    jumlahText && Number.isFinite(jumlah)
                        ? jumlah
                        : jumlahText
                ]);
            });

            const worksheet = XLSX.utils.aoa_to_sheet(data);

            // Lebar kolom
            worksheet['!cols'] = [
                { wch: 20 },
                { wch: 42 },
                { wch: 24 }
            ];

            // Gabungkan judul dan periode
            worksheet['!merges'] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: 2 } },
                { s: { r: 1, c: 0 }, e: { r: 1, c: 2 } }
            ];

            // Judul dan header
            ['A1', 'A4', 'B4', 'C4'].forEach(ref => {
                if (worksheet[ref]) {
                    worksheet[ref].s = {
                        font: { bold: true },
                        alignment: { horizontal: 'center' }
                    };
                }
            });

            if (worksheet['A1']) {
                worksheet['A1'].s = {
                    font: { bold: true, sz: 16 },
                    alignment: { horizontal: 'center' }
                };
            }

            // Format nilai uang
            for (let r = 4; r < data.length; r++) {
                const ref = XLSX.utils.encode_cell({ r, c: 2 });
                const cell = worksheet[ref];

                if (cell && typeof cell.v === 'number') {
                    cell.z = '"Rp" #,##0';
                    cell.s = {
                        alignment: { horizontal: 'right' }
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
</x-admin-layout>

