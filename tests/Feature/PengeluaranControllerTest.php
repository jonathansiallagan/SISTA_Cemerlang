<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\DetailJurnal;
use App\Models\Jurnal;
use App\Models\Pengeluaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengeluaranControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Cabang $cabangUtama;
    protected Cabang $cabangKedua;
    protected User $adminPusat;

    private int $nomorCabang = 0;
    private int $nomorPengeluaran = 0;
    private int $nomorAkun = 0;
    private int $nomorJurnal = 0;
    private int $nomorUser = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cabangUtama = $this->buatCabang('Cabang Utama');
        $this->cabangKedua = $this->buatCabang('Cabang Kedua');
        $this->adminPusat = $this->buatUser('Admin Pusat');

        $this->actingAs($this->adminPusat);
    }

    /* ================================================================
     | AKSES DAN HALAMAN INDEX
     |================================================================ */

    public function test_guest_is_redirected_to_login_from_expense_page(): void
    {
        auth()->logout();

        $response = $this->get(route('pengeluaran.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_pusat_can_open_expense_page(): void
    {
        $response = $this->get(route('pengeluaran.index'));

        $response->assertOk();
        $response->assertViewIs('admin.pengeluaran.index');
        $response->assertViewHasAll([
            'pengeluarans',
            'cabangs',
            'startDate',
            'endDate',
        ]);
    }

    public function test_admin_cabang_can_open_expense_page(): void
    {
        $adminCabang = $this->buatUser(
            'Admin Cabang',
            $this->cabangUtama
        );

        $response = $this->actingAs($adminCabang)
            ->get(route('pengeluaran.index'));

        $response->assertOk();
        $response->assertViewIs('admin.pengeluaran.index');
    }

    public function test_customer_service_cannot_access_expense_page(): void
    {
        $customerService = $this->buatUser(
            'Customer Service',
            $this->cabangUtama
        );

        $response = $this->actingAs($customerService)
            ->get(route('pengeluaran.index'));

        $response->assertForbidden();
    }

    public function test_expense_page_has_prevent_back_history_headers(): void
    {
        $response = $this->get(route('pengeluaran.index'));

        $response->assertOk();
        $response->assertHeader('Pragma', 'no-cache');
        $response->assertHeader(
            'Expires',
            'Sat, 01 Jan 1990 00:00:00 GMT'
        );

        $cacheControl = strtolower(
            $response->headers->get('Cache-Control', '')
        );

        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('max-age=0', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
    }

    /* ================================================================
     | FILTER PERIODE, CABANG, DAN DATA PENGELUARAN
     |================================================================ */

    public function test_index_uses_current_month_when_date_filters_are_omitted(): void
    {
        $tanggalBulanIni = now()->startOfMonth()
            ->addDays(2)->toDateString();

        $tanggalBulanLalu = now()->startOfMonth()
            ->subDay()->toDateString();

        $tanggalBulanDepan = now()->endOfMonth()
            ->addDay()->toDateString();

        $dalamPeriode = $this->buatPengeluaran(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'tanggal' => $tanggalBulanIni,
                'no_referensi' => 'UJI-PERIODE-BULAN-INI',
            ]
        );

        $this->buatPengeluaran(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'tanggal' => $tanggalBulanLalu,
                'no_referensi' => 'UJI-PERIODE-BULAN-LALU',
            ]
        );

        $this->buatPengeluaran(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'tanggal' => $tanggalBulanDepan,
                'no_referensi' => 'UJI-PERIODE-BULAN-DEPAN',
            ]
        );

        $response = $this->get(route('pengeluaran.index'));

        $response->assertOk();

        $response->assertViewHas(
            'startDate',
            now()->startOfMonth()->toDateString()
        );

        $response->assertViewHas(
            'endDate',
            now()->endOfMonth()->toDateString()
        );

        $response->assertViewHas(
            'pengeluarans',
            function ($pengeluarans) use ($dalamPeriode): bool {
                return $pengeluarans->count() === 1
                    && $pengeluarans->first()->id === $dalamPeriode->id;
            }
        );
    }

    public function test_index_filters_expenses_by_inclusive_date_range(): void
    {
        $awal = now()->startOfMonth()->addDays(3)->toDateString();
        $tengah = now()->startOfMonth()->addDays(8)->toDateString();
        $akhir = now()->startOfMonth()->addDays(13)->toDateString();

        $awalItem = $this->buatPengeluaran(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'tanggal' => $awal,
                'no_referensi' => 'UJI-TANGGAL-AWAL',
            ]
        );

        $tengahItem = $this->buatPengeluaran(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'tanggal' => $tengah,
                'no_referensi' => 'UJI-TANGGAL-TENGAH',
            ]
        );

        $akhirItem = $this->buatPengeluaran(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'tanggal' => $akhir,
                'no_referensi' => 'UJI-TANGGAL-AKHIR',
            ]
        );

        $this->buatPengeluaran(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'tanggal' => now()->startOfMonth()
                    ->addDays(2)->toDateString(),
                'no_referensi' => 'UJI-TANGGAL-SEBELUM',
            ]
        );

        $this->buatPengeluaran(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'tanggal' => now()->startOfMonth()
                    ->addDays(14)->toDateString(),
                'no_referensi' => 'UJI-TANGGAL-SESUDAH',
            ]
        );

        $response = $this->get(route('pengeluaran.index', [
            'start_date' => $awal,
            'end_date' => $akhir,
        ]));

        $response->assertOk();

        $this->assertEqualsCanonicalizing(
            [
                $awalItem->id,
                $tengahItem->id,
                $akhirItem->id,
            ],
            $response->viewData('pengeluarans')->pluck('id')->all()
        );

        $response->assertSee('UJI-TANGGAL-AWAL');
        $response->assertSee('UJI-TANGGAL-AKHIR');
        $response->assertDontSee('UJI-TANGGAL-SEBELUM');
        $response->assertDontSee('UJI-TANGGAL-SESUDAH');
    }

    public function test_admin_pusat_can_filter_expenses_by_selected_branch(): void
    {
        $this->buatPengeluaran(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'no_referensi' => 'UJI-CABANG-UTAMA',
            ]
        );

        $pengeluaranCabangKedua = $this->buatPengeluaran(
            $this->cabangKedua,
            $this->adminPusat,
            [
                'no_referensi' => 'UJI-CABANG-KEDUA',
            ]
        );

        $response = $this->get(route('pengeluaran.index', [
            'cabang_id' => $this->cabangKedua->id,
        ]));

        $response->assertOk();

        $this->assertCount(
            1,
            $response->viewData('pengeluarans')
        );

        $this->assertSame(
            $pengeluaranCabangKedua->id,
            $response->viewData('pengeluarans')->first()->id
        );

        $response->assertSee('UJI-CABANG-KEDUA');
        $response->assertDontSee('UJI-CABANG-UTAMA');
    }

    public function test_admin_pusat_sees_expenses_from_all_branches_when_branch_filter_is_omitted(): void
    {
        $this->buatPengeluaran(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'no_referensi' => 'UJI-SEMUA-CABANG-1',
            ]
        );

        $this->buatPengeluaran(
            $this->cabangKedua,
            $this->adminPusat,
            [
                'no_referensi' => 'UJI-SEMUA-CABANG-2',
            ]
        );

        $response = $this->get(route('pengeluaran.index'));

        $response->assertOk();
        $response->assertSee('UJI-SEMUA-CABANG-1');
        $response->assertSee('UJI-SEMUA-CABANG-2');

        $this->assertCount(
            2,
            $response->viewData('pengeluarans')
        );
    }

    public function test_admin_cabang_only_sees_expenses_from_own_branch_even_if_other_branch_is_requested(): void
    {
        $adminCabang = $this->buatUser(
            'Admin Cabang',
            $this->cabangUtama
        );

        $this->buatPengeluaran(
            $this->cabangUtama,
            $adminCabang,
            [
                'no_referensi' => 'UJI-PENGELUARAN-MILIK-SENDIRI',
            ]
        );

        $this->buatPengeluaran(
            $this->cabangKedua,
            $this->adminPusat,
            [
                'no_referensi' => 'UJI-PENGELUARAN-CABANG-LAIN',
            ]
        );

        $response = $this->actingAs($adminCabang)
            ->get(route('pengeluaran.index', [
                'cabang_id' => $this->cabangKedua->id,
            ]));

        $response->assertOk();

        $this->assertCount(
            1,
            $response->viewData('pengeluarans')
        );

        $response->assertSee('UJI-PENGELUARAN-MILIK-SENDIRI');
        $response->assertDontSee('UJI-PENGELUARAN-CABANG-LAIN');
    }

    public function test_admin_pusat_receives_only_active_branches_sorted_by_name(): void
    {
        $cabangZebra = $this->buatCabang('Cabang Zebra');

        $cabangAndalas = $this->buatCabang('Cabang Andalas');

        $cabangNonaktif = $this->buatCabang(
            'Cabang Nonaktif',
            ['status' => 'nonaktif']
        );

        $response = $this->get(route('pengeluaran.index'));

        $response->assertOk();

        $response->assertViewHas(
            'cabangs',
            function ($cabangs) use (
                $cabangZebra,
                $cabangAndalas,
                $cabangNonaktif
            ): bool {
                return $cabangs->pluck('id')->all() === [
                    $cabangAndalas->id,
                    $this->cabangKedua->id,
                    $this->cabangUtama->id,
                    $cabangZebra->id,
                ] && !$cabangs->contains('id', $cabangNonaktif->id);
            }
        );
    }

    public function test_admin_cabang_receives_empty_branch_filter_collection(): void
    {
        $adminCabang = $this->buatUser(
            'Admin Cabang',
            $this->cabangUtama
        );

        $response = $this->actingAs($adminCabang)
            ->get(route('pengeluaran.index'));

        $response->assertOk();

        $response->assertViewHas(
            'cabangs',
            fn ($cabangs) => $cabangs->isEmpty()
        );
    }

    public function test_expenses_are_sorted_by_latest_date_then_latest_id(): void
    {
        $tanggalLama = now()->startOfMonth()
            ->addDays(2)->toDateString();

        $tanggalBaru = now()->startOfMonth()
            ->addDays(7)->toDateString();

        $itemLama = $this->buatPengeluaran(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'tanggal' => $tanggalLama,
                'no_referensi' => 'UJI-URUT-LAMA',
            ]
        );

        $itemBaruPertama = $this->buatPengeluaran(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'tanggal' => $tanggalBaru,
                'no_referensi' => 'UJI-URUT-BARU-1',
            ]
        );

        $itemBaruKedua = $this->buatPengeluaran(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'tanggal' => $tanggalBaru,
                'no_referensi' => 'UJI-URUT-BARU-2',
            ]
        );

        $response = $this->get(route('pengeluaran.index'));

        $response->assertOk();

        $this->assertSame(
            [
                $itemBaruKedua->id,
                $itemBaruPertama->id,
                $itemLama->id,
            ],
            $response->viewData('pengeluarans')->pluck('id')->all()
        );
    }

    public function test_index_displays_empty_state_when_there_are_no_expenses(): void
    {
        $response = $this->get(route('pengeluaran.index'));

        $response->assertOk();
        $response->assertSee('Belum ada data pengeluaran');

        $response->assertViewHas(
            'pengeluarans',
            fn ($items) => $items->isEmpty()
        );
    }

    /* ================================================================
     | VALIDASI FILTER INDEX
     |================================================================ */

    public function test_index_rejects_invalid_start_date(): void
    {
        $response = $this->from(route('pengeluaran.index'))
            ->get(route('pengeluaran.index', [
                'start_date' => 'bukan-tanggal',
                'end_date' => now()->endOfMonth()->toDateString(),
            ]));

        $response->assertRedirect(route('pengeluaran.index'));
        $response->assertSessionHasErrors('start_date');
    }

    public function test_index_rejects_invalid_end_date(): void
    {
        $response = $this->from(route('pengeluaran.index'))
            ->get(route('pengeluaran.index', [
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => 'bukan-tanggal',
            ]));

        $response->assertRedirect(route('pengeluaran.index'));
        $response->assertSessionHasErrors('end_date');
    }

    public function test_index_rejects_end_date_before_start_date(): void
    {
        $response = $this->from(route('pengeluaran.index'))
            ->get(route('pengeluaran.index', [
                'start_date' => '2026-11-20',
                'end_date' => '2026-11-10',
            ]));

        $response->assertRedirect(route('pengeluaran.index'));
        $response->assertSessionHasErrors('end_date');
    }

    public function test_index_rejects_non_integer_branch_id(): void
    {
        $response = $this->from(route('pengeluaran.index'))
            ->get(route('pengeluaran.index', [
                'cabang_id' => 'bukan-angka',
            ]));

        $response->assertRedirect(route('pengeluaran.index'));
        $response->assertSessionHasErrors('cabang_id');
    }

    public function test_index_rejects_nonexistent_branch_id(): void
    {
        $response = $this->from(route('pengeluaran.index'))
            ->get(route('pengeluaran.index', [
                'cabang_id' => 999999,
            ]));

        $response->assertRedirect(route('pengeluaran.index'));
        $response->assertSessionHasErrors('cabang_id');
    }

    /*
     * CATATAN:
     * Method create() pada controller belum memiliki named route
     * pengeluaran.create di routes/web.php.
     *
     * Karena itu, pengujian halaman create tidak dimasukkan dulu.
     * Pengujian store menggunakan pengeluaran.index sebagai halaman
     * tujuan redirect ketika validasi gagal.
     */

    /* ================================================================
     | STORE: PENYIMPANAN PENGELUARAN DAN JURNAL OTOMATIS
     |================================================================ */

    public function test_admin_pusat_can_store_expense_and_create_balanced_journal_automatically(): void
    {
        $akunBeban = $this->buatAkun(
            '5-2100',
            'Beban Gaji Uji Simpan'
        );

        $akunKas = $this->buatAkun(
            '1-1100',
            'Kas Uji Simpan',
            [
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'saldo_normal' => 'Debit',
            ]
        );

        $tanggal = now()->toDateString();

        $response = $this->post(route('pengeluaran.store'), [
            'tanggal' => $tanggal,
            'cabang_id' => $this->cabangUtama->id,
            'akun_id' => $akunBeban->id,
            'keterangan' => 'Pembayaran gaji pengujian',
            'qty' => 2.5,
            'satuan' => 'Orang',
            'harga' => 100000,
        ]);

        $noReferensi = 'PAY-' . str_replace('-', '', $tanggal) . '-0001';

        $response->assertRedirect(route('pengeluaran.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengeluarans', [
            'no_referensi' => $noReferensi,
            'cabang_id' => $this->cabangUtama->id,
            'akun_id' => $akunBeban->id,
            'user_id' => $this->adminPusat->id,
            'keterangan' => 'Pembayaran gaji pengujian',
            'qty' => 2.5,
            'satuan' => 'Orang',
            'harga' => 100000,
            'total_pengeluaran' => 250000,
        ]);

        // Periksa tanggal dengan format tanggal, bukan format datetime.
        $tanggalTersimpan = Pengeluaran::where(
            'no_referensi',
            $noReferensi
        )->value('tanggal');

        $this->assertSame(
            $tanggal,
            \Carbon\Carbon::parse($tanggalTersimpan)->toDateString()
        );

        $this->assertDatabaseHas('jurnals', [
            'no_referensi' => $noReferensi,
            'tanggal' => $tanggal,
            'cabang_id' => $this->cabangUtama->id,
            'user_id' => $this->adminPusat->id,
        ]);

        $jurnal = Jurnal::where(
            'no_referensi',
            $noReferensi
        )->firstOrFail();

        // Baris debit akun beban.
        $this->assertDatabaseHas('detail_jurnals', [
            'jurnal_id' => $jurnal->id,
            'akun_id' => $akunBeban->id,
            'debit' => 250000,
            'kredit' => 0,
        ]);

        // Baris kredit akun kas.
        $this->assertDatabaseHas('detail_jurnals', [
            'jurnal_id' => $jurnal->id,
            'akun_id' => $akunKas->id,
            'debit' => 0,
            'kredit' => 250000,
        ]);

        $this->assertSame(
            2,
            DetailJurnal::where('jurnal_id', $jurnal->id)->count()
        );
    }

    public function test_admin_cabang_store_uses_own_branch_even_when_another_branch_id_is_submitted(): void
    {
        $adminCabang = $this->buatUser(
            'Admin Cabang',
            $this->cabangUtama
        );

        $akunBeban = $this->buatAkun(
            '5-2100',
            'Beban Gaji Cabang Uji'
        );

        $this->buatAkun(
            '1-1100',
            'Kas Cabang Uji',
            [
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'saldo_normal' => 'Debit',
            ]
        );

        $tanggal = now()->toDateString();

        $response = $this->actingAs($adminCabang)
            ->post(route('pengeluaran.store'), [
                'tanggal' => $tanggal,
                'cabang_id' => $this->cabangKedua->id,
                'akun_id' => $akunBeban->id,
                'keterangan' => 'Pengeluaran milik admin cabang',
                'qty' => 1,
                'satuan' => 'Unit',
                'harga' => 75000,
            ]);

        $response->assertRedirect(route('pengeluaran.index'));

        $this->assertDatabaseHas('pengeluarans', [
            'cabang_id' => $this->cabangUtama->id,
            'user_id' => $adminCabang->id,
            'keterangan' => 'Pengeluaran milik admin cabang',
            'total_pengeluaran' => 75000,
        ]);

        $this->assertDatabaseMissing('pengeluarans', [
            'cabang_id' => $this->cabangKedua->id,
            'keterangan' => 'Pengeluaran milik admin cabang',
        ]);
    }

    public function test_expense_reference_prefix_is_generated_for_every_supported_expense_account(): void
    {
        $akunKas = $this->buatAkun(
            '1-1100',
            'Kas Pengujian Prefix',
            [
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'saldo_normal' => 'Debit',
            ]
        );

        $prefixes = [
            '5-2100' => 'PAY',
            '5-2200' => 'UTIL',
            '5-2300' => 'TRN',
            '5-2400' => 'PRO',
            '5-2500' => 'ADM',
            '5-2600' => 'MNT',
        ];

        $tanggal = now()->toDateString();
        $tanggalReferensi = str_replace('-', '', $tanggal);

        foreach ($prefixes as $kodeAkun => $prefix) {
            $akunBeban = $this->buatAkun(
                $kodeAkun,
                'Akun ' . $kodeAkun
            );

            $response = $this->post(
                route('pengeluaran.store'),
                [
                    'tanggal' => $tanggal,
                    'cabang_id' => $this->cabangUtama->id,
                    'akun_id' => $akunBeban->id,
                    'keterangan' => 'Uji prefix ' . $prefix,
                    'qty' => 1,
                    'satuan' => 'Pcs',
                    'harga' => 1000,
                ]
            );

            $response->assertRedirect(route('pengeluaran.index'));

            $this->assertDatabaseHas('pengeluarans', [
                'no_referensi' => $prefix . '-' . $tanggalReferensi . '-0001',
                'akun_id' => $akunBeban->id,
            ]);
        }

        $this->assertDatabaseHas('akuns', [
            'id' => $akunKas->id,
            'kode_akun' => '1-1100',
        ]);
    }

    public function test_store_increments_reference_number_based_on_latest_matching_journal(): void
    {
        $akunBeban = $this->buatAkun(
            '5-2100',
            'Beban Gaji Nomor Berikutnya'
        );

        $this->buatAkun(
            '1-1100',
            'Kas Nomor Berikutnya',
            [
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'saldo_normal' => 'Debit',
            ]
        );

        $tanggal = now()->toDateString();
        $tanggalReferensi = str_replace('-', '', $tanggal);

        $this->buatJurnal(
            $this->cabangUtama,
            $this->adminPusat,
            [
                'no_referensi' => 'PAY-' . $tanggalReferensi . '-0003',
                'tanggal' => $tanggal,
            ]
        );

        $response = $this->post(
            route('pengeluaran.store'),
            [
                'tanggal' => $tanggal,
                'cabang_id' => $this->cabangUtama->id,
                'akun_id' => $akunBeban->id,
                'keterangan' => 'Nomor referensi berikutnya',
                'qty' => 1,
                'satuan' => 'Unit',
                'harga' => 50000,
            ]
        );

        $response->assertRedirect(route('pengeluaran.index'));

        $this->assertDatabaseHas('pengeluarans', [
            'no_referensi' => 'PAY-' . $tanggalReferensi . '-0004',
        ]);
    }

    /* ================================================================
     | VALIDASI STORE DAN PENANGANAN KONDISI KHUSUS
     |================================================================ */

    public function test_store_rejects_missing_required_fields(): void
    {
        $response = $this->from(route('pengeluaran.index'))
            ->post(route('pengeluaran.store'), []);

        $response->assertRedirect(route('pengeluaran.index'));

        $response->assertSessionHasErrors([
            'tanggal',
            'akun_id',
            'keterangan',
            'qty',
            'satuan',
            'harga',
            'cabang_id',
        ]);
    }

    public function test_store_rejects_nonexistent_expense_account(): void
    {
        $response = $this->from(route('pengeluaran.index'))
            ->post(route('pengeluaran.store'), [
                'tanggal' => now()->toDateString(),
                'cabang_id' => $this->cabangUtama->id,
                'akun_id' => 999999,
                'keterangan' => 'Akun tidak ada',
                'qty' => 1,
                'satuan' => 'Unit',
                'harga' => 10000,
            ]);

        $response->assertRedirect(route('pengeluaran.index'));
        $response->assertSessionHasErrors('akun_id');
    }

    public function test_store_rejects_account_that_is_not_an_allowed_operating_expense_account(): void
    {
        $akunKas = $this->buatAkun(
            '1-1100',
            'Kas Akun Tidak Valid',
            [
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'saldo_normal' => 'Debit',
            ]
        );

        $akunTidakDiizinkan = $this->buatAkun(
            '5-2999',
            'Beban Tidak Diizinkan'
        );

        $response = $this->post(
            route('pengeluaran.store'),
            [
                'tanggal' => now()->toDateString(),
                'cabang_id' => $this->cabangUtama->id,
                'akun_id' => $akunTidakDiizinkan->id,
                'keterangan' => 'Akun tidak termasuk pilihan',
                'qty' => 1,
                'satuan' => 'Unit',
                'harga' => 10000,
            ]
        );

        $response->assertNotFound();

        $this->assertDatabaseCount('pengeluarans', 0);
        $this->assertDatabaseCount('jurnals', 0);

        $this->assertDatabaseHas('akuns', [
            'id' => $akunKas->id,
        ]);
    }

    public function test_store_rejects_inactive_expense_account(): void
    {
        $this->buatAkun(
            '1-1100',
            'Kas Akun Nonaktif',
            [
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'saldo_normal' => 'Debit',
            ]
        );

        $akunNonaktif = $this->buatAkun(
            '5-2100',
            'Beban Nonaktif',
            [
                'status' => 'nonaktif',
            ]
        );

        $response = $this->post(
            route('pengeluaran.store'),
            [
                'tanggal' => now()->toDateString(),
                'cabang_id' => $this->cabangUtama->id,
                'akun_id' => $akunNonaktif->id,
                'keterangan' => 'Akun beban nonaktif',
                'qty' => 1,
                'satuan' => 'Unit',
                'harga' => 10000,
            ]
        );

        $response->assertNotFound();

        $this->assertDatabaseCount('pengeluarans', 0);
    }

    public function test_store_rejects_inactive_branch_for_admin_pusat(): void
    {
        $akunBeban = $this->buatAkun(
            '5-2100',
            'Beban Cabang Nonaktif'
        );

        $this->buatAkun(
            '1-1100',
            'Kas Cabang Nonaktif',
            [
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'saldo_normal' => 'Debit',
            ]
        );

        $cabangNonaktif = $this->buatCabang(
            'Cabang Nonaktif Store',
            [
                'status' => 'nonaktif',
            ]
        );

        $response = $this->from(route('pengeluaran.index'))
            ->post(route('pengeluaran.store'), [
                'tanggal' => now()->toDateString(),
                'cabang_id' => $cabangNonaktif->id,
                'akun_id' => $akunBeban->id,
                'keterangan' => 'Cabang tidak aktif',
                'qty' => 1,
                'satuan' => 'Unit',
                'harga' => 10000,
            ]);

        $response->assertRedirect(route('pengeluaran.index'));
        $response->assertSessionHasErrors('cabang_id');

        $this->assertDatabaseCount('pengeluarans', 0);
        $this->assertDatabaseCount('jurnals', 0);
    }

    public function test_store_rejects_submission_when_cash_account_is_unavailable(): void
    {
        $akunBeban = $this->buatAkun(
            '5-2100',
            'Beban Tanpa Kas'
        );

        $response = $this->from(route('pengeluaran.index'))
            ->post(route('pengeluaran.store'), [
                'tanggal' => now()->toDateString(),
                'cabang_id' => $this->cabangUtama->id,
                'akun_id' => $akunBeban->id,
                'keterangan' => 'Akun kas belum dibuat',
                'qty' => 1,
                'satuan' => 'Unit',
                'harga' => 10000,
            ]);

        $response->assertRedirect(route('pengeluaran.index'));
        $response->assertSessionHasErrors('akun_id');

        $this->assertDatabaseCount('pengeluarans', 0);
        $this->assertDatabaseCount('jurnals', 0);
        $this->assertDatabaseCount('detail_jurnals', 0);
    }

    public function test_store_rejects_missing_cabang_id_for_admin_pusat(): void
    {
        $akunBeban = $this->buatAkun(
            '5-2100',
            'Beban Tanpa Cabang'
        );

        $this->buatAkun(
            '1-1100',
            'Kas Tanpa Cabang',
            [
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'saldo_normal' => 'Debit',
            ]
        );

        $response = $this->from(route('pengeluaran.index'))
            ->post(route('pengeluaran.store'), [
                'tanggal' => now()->toDateString(),
                'akun_id' => $akunBeban->id,
                'keterangan' => 'Cabang tidak dipilih',
                'qty' => 1,
                'satuan' => 'Unit',
                'harga' => 10000,
            ]);

        $response->assertRedirect(route('pengeluaran.index'));
        $response->assertSessionHasErrors('cabang_id');
    }

    public function test_store_rejects_zero_or_negative_quantity(): void
    {
        $akunBeban = $this->buatAkun(
            '5-2100',
            'Beban Qty Tidak Valid'
        );

        $this->buatAkun(
            '1-1100',
            'Kas Qty Tidak Valid',
            [
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'saldo_normal' => 'Debit',
            ]
        );

        foreach ([0, -1] as $qty) {
            $response = $this->from(route('pengeluaran.index'))
                ->post(route('pengeluaran.store'), [
                    'tanggal' => now()->toDateString(),
                    'cabang_id' => $this->cabangUtama->id,
                    'akun_id' => $akunBeban->id,
                    'keterangan' => 'Qty tidak valid',
                    'qty' => $qty,
                    'satuan' => 'Unit',
                    'harga' => 10000,
                ]);

            $response->assertRedirect(route('pengeluaran.index'));
            $response->assertSessionHasErrors('qty');
        }

        $this->assertDatabaseCount('pengeluarans', 0);
    }

    public function test_store_rejects_zero_or_negative_price(): void
    {
        $akunBeban = $this->buatAkun(
            '5-2100',
            'Beban Harga Tidak Valid'
        );

        $this->buatAkun(
            '1-1100',
            'Kas Harga Tidak Valid',
            [
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'saldo_normal' => 'Debit',
            ]
        );

        foreach ([0, -100] as $harga) {
            $response = $this->from(route('pengeluaran.index'))
                ->post(route('pengeluaran.store'), [
                    'tanggal' => now()->toDateString(),
                    'cabang_id' => $this->cabangUtama->id,
                    'akun_id' => $akunBeban->id,
                    'keterangan' => 'Harga tidak valid',
                    'qty' => 1,
                    'satuan' => 'Unit',
                    'harga' => $harga,
                ]);

            $response->assertRedirect(route('pengeluaran.index'));
            $response->assertSessionHasErrors('harga');
        }

        $this->assertDatabaseCount('pengeluarans', 0);
    }

    public function test_store_rejects_invalid_unit(): void
    {
        $akunBeban = $this->buatAkun(
            '5-2100',
            'Beban Satuan Tidak Valid'
        );

        $this->buatAkun(
            '1-1100',
            'Kas Satuan Tidak Valid',
            [
                'tipe_akun' => 'Aset',
                'kelompok_akun' => 'Aset',
                'saldo_normal' => 'Debit',
            ]
        );

        $response = $this->from(route('pengeluaran.index'))
            ->post(route('pengeluaran.store'), [
                'tanggal' => now()->toDateString(),
                'cabang_id' => $this->cabangUtama->id,
                'akun_id' => $akunBeban->id,
                'keterangan' => 'Satuan tidak diperbolehkan',
                'qty' => 1,
                'satuan' => 'Liter',
                'harga' => 10000,
            ]);

        $response->assertRedirect(route('pengeluaran.index'));
        $response->assertSessionHasErrors('satuan');

        $this->assertDatabaseCount('pengeluarans', 0);
    }

    public function test_customer_service_cannot_store_expense(): void
    {
        $customerService = $this->buatUser(
            'Customer Service',
            $this->cabangUtama
        );

        $response = $this->actingAs($customerService)
            ->post(route('pengeluaran.store'), []);

        $response->assertForbidden();

        $this->assertDatabaseCount('pengeluarans', 0);
        $this->assertDatabaseCount('jurnals', 0);
    }

    public function test_admin_cabang_without_branch_assignment_cannot_store_expense(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang');

        $response = $this->actingAs($adminCabang)
            ->post(route('pengeluaran.store'), []);

        $response->assertForbidden();

        $this->assertDatabaseCount('pengeluarans', 0);
        $this->assertDatabaseCount('jurnals', 0);
    }

    /* ================================================================
     | HELPER DATA PENGUJIAN
     |================================================================ */

    private function buatCabang(
        string $nama,
        array $attributes = []
    ): Cabang {
        $nomor = ++$this->nomorCabang;

        return Cabang::create(array_merge([
            'nama' => $nama . ' ' . $nomor,
            'alamat' => 'Alamat ' . $nama,
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ], $attributes));
    }

    private function buatUser(
        string $role,
        ?Cabang $cabang = null
    ): User {
        $nomor = ++$this->nomorUser;

        return User::factory()->create([
            'name' => $role . ' Pengujian ' . $nomor,
            'username' => strtolower(
                str_replace(' ', '_', $role)
            ) . '_pengeluaran_' . $nomor,
            'email' => 'pengeluaran-user-' . $nomor . '@example.test',
            'role' => $role,
            'cabang_id' => $cabang?->id,
        ]);
    }

    private function buatAkun(
        string $kodeAkun,
        string $namaAkun,
        array $attributes = []
    ): Akun {
        $nomor = ++$this->nomorAkun;

        return Akun::create(array_merge([
            'kode_akun' => $kodeAkun,
            'nama_akun' => $namaAkun . ' ' . $nomor,
            'tipe_akun' => 'Beban/Biaya',
            'kelompok_akun' => 'Beban Operasional',
            'saldo_normal' => 'Debit',
            'status' => 'aktif',
        ], $attributes));
    }

    private function buatPengeluaran(
        Cabang $cabang,
        User $user,
        array $attributes = []
    ): Pengeluaran {
        $nomor = ++$this->nomorPengeluaran;

        return Pengeluaran::forceCreate(array_merge([
            'no_referensi' => 'UJI-PENGELUARAN-' .
                str_pad((string) $nomor, 4, '0', STR_PAD_LEFT),
            'tanggal' => now()->toDateString(),
            'cabang_id' => $cabang->id,
            'akun_id' => null,
            'keterangan' => 'Pengeluaran untuk pengujian ' . $nomor,
            'qty' => 1,
            'satuan' => 'Unit',
            'harga' => 10000,
            'total_pengeluaran' => 10000,
            'user_id' => $user->id,
        ], $attributes));
    }

    private function buatJurnal(
        Cabang $cabang,
        User $user,
        array $attributes = []
    ): Jurnal {
        $nomor = ++$this->nomorJurnal;

        return Jurnal::create(array_merge([
            'no_referensi' => 'JRN-UJI-PENGELUARAN-' .
                str_pad((string) $nomor, 4, '0', STR_PAD_LEFT),
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Jurnal pengujian pengeluaran ' . $nomor,
            'cabang_id' => $cabang->id,
            'user_id' => $user->id,
        ], $attributes));
    }
}