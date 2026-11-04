<?php

namespace Tests\Feature;

use App\Exports\BukuBesarExport;
use App\Models\Akun;
use App\Models\Cabang;
use App\Models\DetailJurnal;
use App\Models\Jurnal;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Mockery;
use Tests\TestCase;

class BukuBesarControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Cabang $cabangUtama;
    protected Cabang $cabangKedua;
    protected User $adminPusat;

    private int $nomorCabang = 0;
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

    /* -----------------------------------------------------------------
     | AKSES DAN HALAMAN INDEX
     |------------------------------------------------------------------ */

    public function test_guest_is_redirected_to_login(): void
    {
        auth()->logout();

        $response = $this->get(route('buku_besar.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_pusat_can_open_general_ledger_page(): void
    {
        $response = $this->get(route('buku_besar.index'));

        $response->assertOk();
        $response->assertViewIs('admin.buku_besar.index');
        $response->assertViewHasAll([
            'akuns',
            'cabangs',
            'laporan',
            'semuaAkun',
            'grandTotal',
            'grandDebit',
            'grandKredit',
            'grandSaldoAwal',
            'grandSaldoAkhir',
            'start_date',
            'end_date',
            'akun_id',
            'cabang_id',
        ]);
    }

    public function test_admin_cabang_can_open_general_ledger_page(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);

        $response = $this->actingAs($adminCabang)
            ->get(route('buku_besar.index'));

        $response->assertOk();
        $response->assertViewIs('admin.buku_besar.index');
    }

    public function test_customer_service_cannot_access_general_ledger(): void
    {
        $customerService = $this->buatUser('Customer Service');

        $response = $this->actingAs($customerService)
            ->get(route('buku_besar.index'));

        $response->assertForbidden();
    }

    public function test_admin_cabang_without_branch_assignment_is_forbidden(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang');

        $response = $this->actingAs($adminCabang)
            ->get(route('buku_besar.index'));

        $response->assertForbidden();
    }

    public function test_index_response_has_prevent_back_history_headers(): void
    {
        $response = $this->get(route('buku_besar.index'));

        $response->assertOk();
        $response->assertHeader('Pragma', 'no-cache');
        $response->assertHeader('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');

        // Urutan directive Cache-Control dapat dinormalisasi oleh Symfony.
        $cacheControl = strtolower($response->headers->get('Cache-Control', ''));

        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('max-age=0', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
    }

    public function test_index_uses_current_month_when_date_filters_are_omitted(): void
    {
        $response = $this->get(route('buku_besar.index'));

        $response->assertOk();
        $response->assertViewHas('start_date', date('Y-m-01'));
        $response->assertViewHas('end_date', date('Y-m-t'));
        $response->assertViewHas('akun_id', 'semua');
    }

    public function test_index_only_provides_active_branches_for_filter(): void
    {
        $cabangNonaktif = $this->buatCabang('Cabang Nonaktif', [
            'status' => 'nonaktif',
        ]);

        $response = $this->get(route('buku_besar.index'));

        $response->assertOk();
        $response->assertViewHas('cabangs', function ($cabangs) use ($cabangNonaktif): bool {
            $ids = $cabangs->pluck('id')->all();

            return in_array($this->cabangUtama->id, $ids, true)
                && in_array($this->cabangKedua->id, $ids, true)
                && !in_array($cabangNonaktif->id, $ids, true);
        });
    }

    /* -----------------------------------------------------------------
     | FILTER AKUN DAN PERIODE
     |------------------------------------------------------------------ */

    public function test_index_can_filter_one_account(): void
    {
        $kas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
        ]);
        $bank = $this->buatAkun([
            'kode_akun' => '1-1200',
            'nama_akun' => 'Bank',
        ]);

        $jurnal = $this->buatJurnal($this->cabangUtama, '2026-11-10');
        $this->buatDetail($jurnal, $kas, 500000, 0);
        $this->buatDetail($jurnal, $bank, 0, 500000);

        $response = $this->get(route('buku_besar.index', [
            'akun_id' => $kas->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk();
        $response->assertViewHas('semuaAkun', false);
        $response->assertViewHas('akun_id', (string) $kas->id);

        $laporan = $response->viewData('laporan');
        $this->assertCount(1, $laporan);
        $this->assertSame($kas->id, $laporan->first()['akun']->id);
    }

    public function test_all_accounts_filter_omits_accounts_without_balance_or_mutation(): void
    {
        $kas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
        ]);
        $pendapatan = $this->buatAkun([
            'kode_akun' => '4-1100',
            'nama_akun' => 'Pendapatan Penjualan',
            'tipe_akun' => 'Pendapatan',
            'kelompok_akun' => 'Pendapatan Usaha',
            'saldo_normal' => 'Kredit',
        ]);
        $akunKosong = $this->buatAkun([
            'kode_akun' => '1-1300',
            'nama_akun' => 'Persediaan',
        ]);

        $jurnal = $this->buatJurnal($this->cabangUtama, '2026-11-10');
        $this->buatDetail($jurnal, $kas, 250000, 0);
        $this->buatDetail($jurnal, $pendapatan, 0, 250000);

        $response = $this->get(route('buku_besar.index', [
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk();
        $laporan = $response->viewData('laporan');
        $ids = $laporan->map(fn ($item) => $item['akun']->id)->all();

        $this->assertContains($kas->id, $ids);
        $this->assertContains($pendapatan->id, $ids);
        $this->assertNotContains($akunKosong->id, $ids);
    }

    public function test_index_calculates_opening_balance_period_mutations_and_ignores_outside_dates(): void
    {
        $kas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
            'saldo_normal' => 'Debit',
        ]);
        $pendapatan = $this->buatAkun([
            'kode_akun' => '4-1100',
            'nama_akun' => 'Pendapatan Penjualan',
            'tipe_akun' => 'Pendapatan',
            'kelompok_akun' => 'Pendapatan Usaha',
            'saldo_normal' => 'Kredit',
        ]);
        $bank = $this->buatAkun([
            'kode_akun' => '1-1200',
            'nama_akun' => 'Bank',
        ]);

        // Transaksi sebelum periode menjadi saldo awal Kas.
        $jurnalAwal = $this->buatJurnal($this->cabangUtama, '2026-10-31', [
            'keterangan' => 'Saldo kas awal',
        ]);
        $this->buatDetail($jurnalAwal, $kas, 1000000, 0);
        $this->buatDetail($jurnalAwal, $pendapatan, 0, 1000000);

        // Transaksi dalam periode: Kas bertambah Rp500.000.
        $jurnalDebit = $this->buatJurnal($this->cabangUtama, '2026-11-10', [
            'keterangan' => 'Penerimaan penjualan',
        ]);
        $this->buatDetail($jurnalDebit, $kas, 500000, 0);
        $this->buatDetail($jurnalDebit, $pendapatan, 0, 500000);

        // Transaksi dalam periode: Kas berkurang Rp200.000.
        $jurnalKredit = $this->buatJurnal($this->cabangUtama, '2026-11-15', [
            'keterangan' => 'Setoran ke bank',
        ]);
        $this->buatDetail($jurnalKredit, $bank, 200000, 0);
        $this->buatDetail($jurnalKredit, $kas, 0, 200000);

        // Transaksi setelah periode tidak boleh masuk laporan.
        $jurnalSetelah = $this->buatJurnal($this->cabangUtama, '2026-12-01', [
            'keterangan' => 'Penerimaan setelah periode',
        ]);
        $this->buatDetail($jurnalSetelah, $kas, 800000, 0);
        $this->buatDetail($jurnalSetelah, $pendapatan, 0, 800000);

        $response = $this->get(route('buku_besar.index', [
            'akun_id' => $kas->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk();
        $item = $this->ambilItemLaporan($response->viewData('laporan'), $kas);

        $this->assertEquals(1000000, $item['saldoAwal']);
        $this->assertSame('Debit', $item['sisiAwal']);
        $this->assertCount(2, $item['baris']);
        $this->assertEquals(500000, $item['totalDebit']);
        $this->assertEquals(200000, $item['totalKredit']);
        $this->assertEquals(1300000, $item['saldoAkhir']);
        $this->assertSame('Debit', $item['sisiAkhir']);
    }

    public function test_credit_normal_account_calculates_credit_balance_correctly(): void
    {
        $kas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
        ]);
        $pendapatan = $this->buatAkun([
            'kode_akun' => '4-1100',
            'nama_akun' => 'Pendapatan Penjualan',
            'tipe_akun' => 'Pendapatan',
            'kelompok_akun' => 'Pendapatan Usaha',
            'saldo_normal' => 'Kredit',
        ]);

        $jurnalAwal = $this->buatJurnal($this->cabangUtama, '2026-10-31');
        $this->buatDetail($jurnalAwal, $kas, 1000000, 0);
        $this->buatDetail($jurnalAwal, $pendapatan, 0, 1000000);

        $jurnalKredit = $this->buatJurnal($this->cabangUtama, '2026-11-10');
        $this->buatDetail($jurnalKredit, $kas, 400000, 0);
        $this->buatDetail($jurnalKredit, $pendapatan, 0, 400000);

        $jurnalDebit = $this->buatJurnal($this->cabangUtama, '2026-11-12');
        $this->buatDetail($jurnalDebit, $pendapatan, 100000, 0);
        $this->buatDetail($jurnalDebit, $kas, 0, 100000);

        $response = $this->get(route('buku_besar.index', [
            'akun_id' => $pendapatan->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk();
        $item = $this->ambilItemLaporan($response->viewData('laporan'), $pendapatan);

        $this->assertEquals(1000000, $item['saldoAwal']);
        $this->assertSame('Kredit', $item['sisiAwal']);
        $this->assertEquals(100000, $item['totalDebit']);
        $this->assertEquals(400000, $item['totalKredit']);
        $this->assertEquals(1300000, $item['saldoAkhir']);
        $this->assertSame('Kredit', $item['sisiAkhir']);
    }

    public function test_debit_normal_account_shows_credit_side_when_balance_is_negative(): void
    {
        $kas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
            'saldo_normal' => 'Debit',
        ]);
        $akunLawan = $this->buatAkun([
            'kode_akun' => '3-1100',
            'nama_akun' => 'Modal Pemilik',
            'tipe_akun' => 'Ekuitas',
            'kelompok_akun' => 'Ekuitas',
            'saldo_normal' => 'Kredit',
        ]);

        $jurnal = $this->buatJurnal($this->cabangUtama, '2026-11-10');
        $this->buatDetail($jurnal, $kas, 0, 250000);
        $this->buatDetail($jurnal, $akunLawan, 250000, 0);

        $response = $this->get(route('buku_besar.index', [
            'akun_id' => $kas->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk();
        $item = $this->ambilItemLaporan($response->viewData('laporan'), $kas);

        $this->assertEquals(250000, $item['saldoAkhir']);
        $this->assertSame('Kredit', $item['sisiAkhir']);
        $this->assertSame('Kredit', $item['baris']->first()->sisi_saldo);
    }

    public function test_zero_balance_uses_account_normal_side(): void
    {
        $pendapatan = $this->buatAkun([
            'kode_akun' => '4-1100',
            'nama_akun' => 'Pendapatan Penjualan',
            'tipe_akun' => 'Pendapatan',
            'kelompok_akun' => 'Pendapatan Usaha',
            'saldo_normal' => 'Kredit',
        ]);

        $response = $this->get(route('buku_besar.index', [
            'akun_id' => $pendapatan->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk();
        $item = $this->ambilItemLaporan($response->viewData('laporan'), $pendapatan);

        $this->assertEquals(0, $item['saldoAwal']);
        $this->assertSame('Kredit', $item['sisiAwal']);
        $this->assertEquals(0, $item['saldoAkhir']);
        $this->assertSame('Kredit', $item['sisiAkhir']);
    }

    /* -----------------------------------------------------------------
     | KETERANGAN AKUN LAWAN
     |------------------------------------------------------------------ */

    public function test_cash_account_uses_special_revenue_counter_account_description(): void
    {
        $kas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
        ]);
        $pendapatan = $this->buatAkun([
            'kode_akun' => '4-1100',
            'nama_akun' => 'Pendapatan Penjualan',
            'tipe_akun' => 'Pendapatan',
            'kelompok_akun' => 'Pendapatan Usaha',
            'saldo_normal' => 'Kredit',
        ]);

        $jurnal = $this->buatJurnal($this->cabangUtama, '2026-11-10', [
            'keterangan' => 'Penjualan tunai',
        ]);
        $this->buatDetail($jurnal, $kas, 750000, 0);
        $this->buatDetail($jurnal, $pendapatan, 0, 750000);

        $response = $this->get(route('buku_besar.index', [
            'akun_id' => $kas->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk();
        $item = $this->ambilItemLaporan($response->viewData('laporan'), $kas);

        $this->assertSame(
            'Pendapatan Penjualan',
            $item['baris']->first()->keterangan_buku_besar
        );
    }

    public function test_generic_counter_account_description_uses_opposite_entry(): void
    {
        $bank = $this->buatAkun([
            'kode_akun' => '1-1200',
            'nama_akun' => 'Bank',
        ]);
        $modal = $this->buatAkun([
            'kode_akun' => '3-1100',
            'nama_akun' => 'Modal Pemilik',
            'tipe_akun' => 'Ekuitas',
            'kelompok_akun' => 'Ekuitas',
            'saldo_normal' => 'Kredit',
        ]);

        $jurnal = $this->buatJurnal($this->cabangUtama, '2026-11-10');
        $this->buatDetail($jurnal, $bank, 1000000, 0);
        $this->buatDetail($jurnal, $modal, 0, 1000000);

        $response = $this->get(route('buku_besar.index', [
            'akun_id' => $bank->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk();
        $item = $this->ambilItemLaporan($response->viewData('laporan'), $bank);

        $this->assertSame('Modal Pemilik', $item['baris']->first()->keterangan_buku_besar);
    }

    public function test_journal_description_is_used_when_no_counter_account_exists(): void
    {
        $kas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
        ]);

        $jurnal = $this->buatJurnal($this->cabangUtama, '2026-11-10', [
            'keterangan' => 'Penyesuaian kas manual',
        ]);
        $this->buatDetail($jurnal, $kas, 100000, 0);

        $response = $this->get(route('buku_besar.index', [
            'akun_id' => $kas->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk();
        $item = $this->ambilItemLaporan($response->viewData('laporan'), $kas);

        $this->assertSame('Penyesuaian kas manual', $item['baris']->first()->keterangan_buku_besar);
    }

    /* -----------------------------------------------------------------
     | FILTER CABANG DAN HAK AKSES DATA
     |------------------------------------------------------------------ */

    public function test_admin_cabang_only_sees_transactions_from_own_branch_even_if_other_branch_is_requested(): void
    {
        $kas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
        ]);
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);

        $jurnalUtama = $this->buatJurnal($this->cabangUtama, '2026-11-10');
        $this->buatDetail($jurnalUtama, $kas, 100000, 0);

        $jurnalKedua = $this->buatJurnal($this->cabangKedua, '2026-11-10');
        $this->buatDetail($jurnalKedua, $kas, 900000, 0);

        $response = $this->actingAs($adminCabang)->get(route('buku_besar.index', [
            'akun_id' => $kas->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'cabang_id' => $this->cabangKedua->id,
        ]));

        $response->assertOk();
        $response->assertViewHas('cabang_id', $this->cabangUtama->id);

        $item = $this->ambilItemLaporan($response->viewData('laporan'), $kas);
        $this->assertEquals(100000, $item['totalDebit']);
        $this->assertCount(1, $item['baris']);
    }

    public function test_admin_pusat_can_filter_transactions_by_selected_branch(): void
    {
        $kas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
        ]);

        $jurnalUtama = $this->buatJurnal($this->cabangUtama, '2026-11-10');
        $this->buatDetail($jurnalUtama, $kas, 100000, 0);

        $jurnalKedua = $this->buatJurnal($this->cabangKedua, '2026-11-10');
        $this->buatDetail($jurnalKedua, $kas, 900000, 0);

        $response = $this->get(route('buku_besar.index', [
            'akun_id' => $kas->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'cabang_id' => $this->cabangKedua->id,
        ]));

        $response->assertOk();
        $response->assertViewHas('cabang_id', $this->cabangKedua->id);

        $item = $this->ambilItemLaporan($response->viewData('laporan'), $kas);
        $this->assertEquals(900000, $item['totalDebit']);
    }

    public function test_admin_pusat_sees_all_branches_when_branch_filter_is_omitted(): void
    {
        $kas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
        ]);

        $jurnalUtama = $this->buatJurnal($this->cabangUtama, '2026-11-10');
        $this->buatDetail($jurnalUtama, $kas, 100000, 0);

        $jurnalKedua = $this->buatJurnal($this->cabangKedua, '2026-11-11');
        $this->buatDetail($jurnalKedua, $kas, 900000, 0);

        $response = $this->get(route('buku_besar.index', [
            'akun_id' => $kas->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk();
        $response->assertViewHas('cabang_id', null);

        $item = $this->ambilItemLaporan($response->viewData('laporan'), $kas);
        $this->assertEquals(1000000, $item['totalDebit']);
        $this->assertCount(2, $item['baris']);
    }

    /* -----------------------------------------------------------------
     | VALIDASI FILTER
     |------------------------------------------------------------------ */

    public function test_index_rejects_invalid_start_date(): void
    {
        $response = $this->from(route('buku_besar.index'))->get(route('buku_besar.index', [
            'start_date' => 'bukan-tanggal',
            'end_date' => '2026-11-30',
        ]));

        $response->assertRedirect(route('buku_besar.index'));
        $response->assertSessionHasErrors('start_date');
    }

    public function test_index_rejects_end_date_before_start_date(): void
    {
        $response = $this->from(route('buku_besar.index'))->get(route('buku_besar.index', [
            'start_date' => '2026-11-20',
            'end_date' => '2026-11-01',
        ]));

        $response->assertRedirect(route('buku_besar.index'));
        $response->assertSessionHasErrors('end_date');
    }

    public function test_index_returns_404_for_unknown_account_id(): void
    {
        $response = $this->get(route('buku_besar.index', [
            'akun_id' => 999999,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertNotFound();
    }

    public function test_index_rejects_nonexistent_branch_id(): void
    {
        $response = $this->from(route('buku_besar.index'))->get(route('buku_besar.index', [
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
            'cabang_id' => 999999,
        ]));

        $response->assertRedirect(route('buku_besar.index'));
        $response->assertSessionHasErrors('cabang_id');
    }

    /* -----------------------------------------------------------------
     | EXPORT PDF DAN EXCEL
     |------------------------------------------------------------------ */

    public function test_pdf_export_uses_selected_period_and_downloads_expected_filename(): void
    {
        $kas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
        ]);
        $jurnal = $this->buatJurnal($this->cabangUtama, '2026-11-10');
        $this->buatDetail($jurnal, $kas, 100000, 0);

        $pdf = Mockery::mock(\Barryvdh\DomPDF\PDF::class);
        $pdf->shouldReceive('setPaper')
            ->once()
            ->with('a4', 'landscape')
            ->andReturnSelf();
        $pdf->shouldReceive('download')
            ->once()
            ->with('buku-besar_2026-11-01_sd_2026-11-30.pdf')
            ->andReturn(response('fake-pdf-content', 200, [
                'Content-Type' => 'application/pdf',
            ]));

        Pdf::shouldReceive('loadView')
            ->once()
            ->with(
                'admin.buku_besar.pdf',
                Mockery::on(function ($data) use ($kas): bool {
                    if (!is_array($data) || !isset($data['laporan'])) {
                        return false;
                    }

                    return $data['start_date'] === '2026-11-01'
                        && $data['end_date'] === '2026-11-30'
                        && $data['laporan']->contains(
                            fn ($item) => $item['akun']->id === $kas->id
                        );
                })
            )
            ->andReturn($pdf);

        $response = $this->get(route('buku_besar.pdf', [
            'akun_id' => $kas->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_excel_export_downloads_expected_file_with_general_ledger_data(): void
    {
        $kas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
        ]);
        $jurnal = $this->buatJurnal($this->cabangUtama, '2026-11-10');
        $this->buatDetail($jurnal, $kas, 100000, 0);

        Excel::fake();

        $response = $this->get(route('buku_besar.excel', [
            'akun_id' => $kas->id,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk();

        Excel::assertDownloaded(
            'buku-besar_2026-11-01_sd_2026-11-30.xlsx',
            function (BukuBesarExport $export) use ($kas): bool {
                if ($export->title() !== 'Buku Besar') {
                    return false;
                }

                if (($export->headings()[0] ?? null) !== 'Kode Akun') {
                    return false;
                }

                return $export->collection()->contains(
                    fn ($row) => ($row[0] ?? null) === $kas->kode_akun
                );
            }
        );
    }

    /* -----------------------------------------------------------------
     | HELPERS DATA PENGUJIAN
     |------------------------------------------------------------------ */

    private function buatCabang(string $nama, array $attributes = []): Cabang
    {
        $this->nomorCabang++;

        return Cabang::create(array_merge([
            'nama' => $nama . ' ' . $this->nomorCabang,
            'alamat' => 'Alamat pengujian',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ], $attributes));
    }

    private function buatUser(string $role, ?Cabang $cabang = null): User
    {
        $this->nomorUser++;

        return User::factory()->create([
            'name' => 'Pengguna Buku Besar ' . $this->nomorUser,
            'email' => 'buku-besar-user-' . $this->nomorUser . '@example.test',
            'username' => 'buku-besar-user-' . $this->nomorUser,
            'role' => $role,
            'cabang_id' => $cabang?->id,
        ]);
    }

    private function buatAkun(array $attributes = []): Akun
    {
        $this->nomorAkun++;

        return Akun::create(array_merge([
            'kode_akun' => '9-' . (1000 + ($this->nomorAkun * 100)),
            'nama_akun' => 'Akun Pengujian ' . $this->nomorAkun,
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
            'saldo_normal' => 'Debit',
            'status' => 'aktif',
        ], $attributes));
    }

    private function buatJurnal(
        Cabang $cabang,
        string $tanggal,
        array $attributes = []
    ): Jurnal {
        $this->nomorJurnal++;

        return Jurnal::create(array_merge([
            'no_referensi' => 'JRN-BB-TEST-' . str_pad((string) $this->nomorJurnal, 4, '0', STR_PAD_LEFT),
            'tanggal' => $tanggal,
            'keterangan' => 'Keterangan jurnal pengujian',
            'cabang_id' => $cabang->id,
            'user_id' => $this->adminPusat->id,
        ], $attributes));
    }

    private function buatDetail(
        Jurnal $jurnal,
        Akun $akun,
        float $debit = 0,
        float $kredit = 0
    ): DetailJurnal {
        return DetailJurnal::create([
            'jurnal_id' => $jurnal->id,
            'akun_id' => $akun->id,
            'debit' => $debit,
            'kredit' => $kredit,
        ]);
    }

    private function ambilItemLaporan($laporan, Akun $akun): array
    {
        $item = $laporan->first(
            fn ($row) => $row['akun']->id === $akun->id
        );

        $this->assertNotNull($item, 'Akun tidak ditemukan pada laporan Buku Besar.');

        return $item;
    }
}
