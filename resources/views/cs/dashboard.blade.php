<x-cs-layout :fullwidth="true">

    {{-- LATAR BELAKANG: FOTO PERCETAKAN + PANEL TRANSAKSI TERBARU --}}
    <div class="cs-hero">

        <div class="cs-box">

            {{-- HEADER PANEL --}}
            <div class="cs-box-header">
                <div class="cs-box-heading">
                    <span class="cs-box-icon">
                        <i class="fas fa-receipt"></i>
                    </span>

                    <div>
                        <h5 class="cs-box-title">Transaksi Terbaru</h5>

                        @if ($namaCabang !== 'Belum ditentukan')
                            <span class="cs-box-subtitle">{{ $namaCabang }}</span>
                        @endif
                    </div>
                </div>

                <span class="cs-box-date">
                    <i class="far fa-calendar-alt"></i>
                    {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                </span>
            </div>

            {{-- ISI PANEL --}}
            <div class="cs-box-body">
                <div class="table-responsive">
                    <table class="table table-md cs-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>No. Referensi</th>
                                <th>Pelanggan</th>
                                <th>Diinput Oleh</th>
                                <th class="text-right" style="text-align:right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksiTerbaru as $index => $t)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $t->no_referensi ?? '-' }}</td>
                                    <td>{{ $t->nama_pelanggan ?? '-' }}</td>
                                    <td>{{ optional($t->user)->name ?? '-' }}</td>
                                    <td class="text-right">
                                        <strong>
                                            Rp {{ number_format((float) $t->total_transaksi, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                </tr>
                            @empty
                                <tr class="cs-empty-row">
                                    <td colspan="5">
                                        <div class="cs-empty">
                                            <i class="fas fa-inbox"></i>
                                            <strong>Belum ada transaksi</strong>
                                            <span>Transaksi yang dibuat pada tanggal ini akan tampil di sini.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- FOOTER PANEL --}}
            <div class="cs-box-footer">
                <span class="cs-box-note">
                    Menampilkan {{ $transaksiTerbaru->count() }} transaksi terakhir
                </span>

                <div class="cs-box-actions">
                    <a href="{{ route('transaksi.index') }}" class="cs-btn cs-btn-ghost">
                        <i class="fas fa-history"></i> Riwayat Transaksi
                    </a>

                    <a href="{{ route('transaksi.create') }}" class="cs-btn cs-btn-primary">
                        <i class="fas fa-plus"></i> Transaksi Baru
                    </a>
                </div>
            </div>

        </div>
    </div>

    <style>
        /* Latar belakang foto percetakan.
           Simpan gambar di: public/images/bg-percetakan.jpg
           Tingkat gelap foto: ubah angka .45 (0 = terang, 1 = gelap). */
        .cs-hero {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 76px - 58px);
            padding: 40px 20px;
            background:
                linear-gradient(rgba(15, 23, 42, .45), rgba(15, 23, 42, .45)),
                url('{{ asset('images/percetakan.jpg') }}') center / cover no-repeat,
                #4b5563;
            box-sizing: border-box;
        }

        /* Panel kaca: foto di belakang masih sedikit terlihat */
        .cs-box {
            width: 100%;
            max-width: 920px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .45);
            border-radius: 20px;
            background: rgba(255, 255, 255, .94);
            -webkit-backdrop-filter: blur(12px);
            backdrop-filter: blur(12px);
            box-shadow: 0 24px 60px rgba(0, 0, 0, .35);
        }

        /* Header */
        .cs-box-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 20px 28px;
            background: linear-gradient(135deg, #6777ef 0%, #8191f7 100%);
        }

        .cs-box-heading {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .cs-box-icon {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            flex-shrink: 0;
            border-radius: 12px;
            background: rgba(255, 255, 255, .2);
            color: #fff;
            font-size: 17px;
        }

        .cs-box-title {
            margin: 0;
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.3;
        }

        .cs-box-subtitle {
            display: block;
            margin-top: 2px;
            color: rgba(255, 255, 255, .8);
            font-size: 12px;
            line-height: 1.3;
        }

        .cs-box-date {
            flex-shrink: 0;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .2);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .cs-box-date i { margin-right: 6px; }

        /* Isi */
        .cs-box-body {
            padding: 8px 28px 12px;
        }

        .cs-table {
            margin: 0;
            background: transparent;
        }

        .cs-box .cs-table thead th {
            padding: 14px 10px;
            border: 0;
            border-bottom: 1px solid #e8ebf3;
            background: transparent !important;
            color: #98a6ad;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
        }

        .cs-box .cs-table tbody td {
            padding: 14px 10px;
            border: 0;
            border-bottom: 1px solid #f0f2f8;
            background: transparent !important;
            color: #34395e;
            vertical-align: middle;
        }

        .cs-box .cs-table tbody tr:last-child td { border-bottom: 0; }
        .cs-box .cs-table tbody tr:hover td { background: #f6f7ff !important; }
        .cs-box .cs-table .cs-empty-row:hover td { background: transparent !important; }

        /* Tampilan saat belum ada transaksi */
        .cs-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            padding: 36px 10px 30px;
            text-align: center;
        }

        .cs-empty i {
            margin-bottom: 6px;
            color: #0a0a0c;
            font-size: 38px;
        }

        .cs-empty strong {
            color: #34395e;
            font-size: 15px;
        }

        .cs-empty span {
            color: #98a6ad;
            font-size: 13px;
        }

        /* Footer */
        .cs-box-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 28px;
            border-top: 1px solid #eceff7;
            background: rgba(246, 247, 251, .9);
        }

        .cs-box-note {
            color: #98a6ad;
            font-size: 12px;
        }

        .cs-box-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cs-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 20px;
            border: 1px solid transparent;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.2;
            text-decoration: none;
            transition: all .2s ease;
        }

        .cs-btn:hover { text-decoration: none; transform: translateY(-1px); }

        .cs-btn-ghost {
            border-color: #d9deef;
            background: #fff;
            color: #34395e;
        }

        .cs-btn-ghost:hover { border-color: #6777ef; color: #6777ef; }

        .cs-btn-primary {
            background: #6777ef;
            color: #fff;
            box-shadow: 0 4px 12px rgba(103, 119, 239, .35);
        }

        .cs-btn-primary:hover { background: #5565e0; color: #fff; }

        /* HP */
        @media (max-width: 575px) {
            .cs-hero { padding: 20px 12px; align-items: flex-start; }
            .cs-box { border-radius: 16px; }
            .cs-box-header,
            .cs-box-body,
            .cs-box-footer { padding-left: 16px; padding-right: 16px; }
            .cs-box-header { flex-wrap: wrap; }
            .cs-box-footer { flex-direction: column; align-items: stretch; }
            .cs-box-note { text-align: center; }
            .cs-box-actions { justify-content: center; flex-wrap: wrap; }
        }
    </style>
</x-cs-layout>