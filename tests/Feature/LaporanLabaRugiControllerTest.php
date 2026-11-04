<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\DetailJurnal;
use App\Models\Jurnal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanLabaRugiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Cabang $cabangUtama;
    protected Cabang $cabangKedua;
    protected User $adminPusat;

    private int $nomorCabang = 0;
    private int $nomorUser = 0;
    private int $nomorAkun = 0;
    private int $nomorJurnal = 0;

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

        $response = $this->get(route('laporan_laba_rugi.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_pusat_can_open_profit_and_loss_report(): void
    {
        $response = $this->get(route('laporan_laba_rugi.index'));

        $response->assertOk();
        $response->assertViewIs('admin.laporan_laba_rugi.index');
        $response->assertViewHasAll([
            'start_date',
            'end_date',
            'pendapatan',
            'hpp',
            'beban',
            'totalPendapatan',
            'totalHpp',
            'labaKotor',
            'totalBeban',
            'labaBersih',
            'cabangs',
            'cabangId',
        ]);
    }

    public function test_admin_cabang_can_open_profit_and_loss_report(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);

        $response = $this->actingAs($adminCabang)
            ->get(route('laporan_laba_rugi.index'));

        $response->assertOk();
        $response->assertViewIs('admin.laporan_laba_rugi.index');
        $response->assertViewHas('cabangId', $this->cabangUtama->id);
    }

    public function test_customer_service_cannot_access_profit_and_loss_report(): void
    {
        $customerService = $this->buatUser('Customer Service', $this->cabangUtama);

        $response = $this->actingAs($customerService)
            ->get(route('laporan_laba_rugi.index'));

        $response->assertForbidden();
    }

    public function test_admin_cabang_without_branch_assignment_is_forbidden(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang');

        $response = $this->actingAs($adminCabang)
            ->get(route('laporan_laba_rugi.index'));

        $response->assertForbidden();
    }

    public function test_index_response_has_prevent_back_history_headers(): void
    {
        $response = $this->get(route('laporan_laba_rugi.index'));

        $response->assertOk();
        $response->assertHeader('Pragma', 'no-cache');
        $response->assertHeader('Expires', 'Sat, 01 Jan 1990 00:00:00 GMT');

        $cacheControl = strtolower($response->headers->get('Cache-Control', ''));

        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('max-age=0', $cacheControl);
        $this->assertStringContainsString('must-revalidate', $cacheControl);
    }

    /* -----------------------------------------------------------------
     | DEFAULT PERIODE DAN DATA CABANG
     |------------------------------------------------------------------ */

    public function test_index_uses_current_month_when_date_filters_are_omitted(): void
    {
        $response = $this->get(route('laporan_laba_rugi.index'));

        $response->assertOk();
        $response->assertViewHas('start_date', now()->startOfMonth()->toDateString());
        $response->assertViewHas('end_date', now()->endOfMonth()->toDateString());
        $response->assertViewHas('cabangId', null);
    }

    public function test_admin_pusat_receives_only_active_branches_sorted_by_name(): void
    {
        $cabangZebra = $this->buatCabang('Cabang Zebra');
        $cabangAndalas = $this->buatCabang('Cabang Andalas');
        $cabangNonaktif = $this->buatCabang('Cabang Nonaktif', [
            'status' => 'nonaktif',
        ]);

        $response = $this->get(route('laporan_laba_rugi.index'));

        $response->assertOk();
        $response->assertViewHas('cabangs', function ($cabangs) use (
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
        });
    }

    /* -----------------------------------------------------------------
     | FILTER CABANG DAN PEMBATASAN DATA ADMIN CABANG
     |------------------------------------------------------------------ */

    public function test_admin_pusat_can_filter_report_by_selected_branch(): void
    {
        $pendapatan = $this->buatAkun([
            'kode_akun' => '4-1100',
            'nama_akun' => 'Pendapatan Cabang',
            'tipe_akun' => 'Pendapatan',
            'kelompok_akun' => 'Pendapatan Usaha',
            'saldo_normal' => 'Kredit',
        ]);

        $jurnalUtama = $this->buatJurnal($this->cabangUtama);
        $jurnalKedua = $this->buatJurnal($this->cabangKedua);
        $this->buatDetail($jurnalUtama, $pendapatan, 0, 100000);
        $this->buatDetail($jurnalKedua, $pendapatan, 0, 250000);

        $response = $this->get(route('laporan_laba_rugi.index', [
            'cabang_id' => $this->cabangKedua->id,
        ]));

        $response->assertOk();
        $response->assertViewHas('cabangId', $this->cabangKedua->id);
        $response->assertViewHas('totalPendapatan', 250000.0);
        $response->assertViewHas('pendapatan', function ($items) use ($pendapatan): bool {
            return $items->count() === 1
                && $items->first()['akun']->id === $pendapatan->id
                && $items->first()['saldo'] === 250000.0;
        });
    }

    public function test_admin_pusat_sees_combined_report_when_branch_filter_is_omitted(): void
    {
        $pendapatan = $this->buatAkun([
            'kode_akun' => '4-1100',
            'nama_akun' => 'Pendapatan Gabungan',
            'tipe_akun' => 'Pendapatan',
            'kelompok_akun' => 'Pendapatan Usaha',
            'saldo_normal' => 'Kredit',
        ]);

        $jurnalUtama = $this->buatJurnal($this->cabangUtama);
        $jurnalKedua = $this->buatJurnal($this->cabangKedua);
        $this->buatDetail($jurnalUtama, $pendapatan, 0, 100000);
        $this->buatDetail($jurnalKedua, $pendapatan, 0, 250000);

        $response = $this->get(route('laporan_laba_rugi.index'));

        $response->assertOk();
        $response->assertViewHas('cabangId', null);
        $response->assertViewHas('totalPendapatan', 350000.0);
    }

    public function test_admin_cabang_is_limited_to_own_branch_even_if_another_branch_is_requested(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);
        $pendapatan = $this->buatAkun([
            'kode_akun' => '4-1100',
            'nama_akun' => 'Pendapatan Khusus Cabang',
            'tipe_akun' => 'Pendapatan',
            'kelompok_akun' => 'Pendapatan Usaha',
            'saldo_normal' => 'Kredit',
        ]);

        $jurnalSendiri = $this->buatJurnal($this->cabangUtama);
        $jurnalCabangLain = $this->buatJurnal($this->cabangKedua);
        $this->buatDetail($jurnalSendiri, $pendapatan, 0, 125000);
        $this->buatDetail($jurnalCabangLain, $pendapatan, 0, 900000);

        $response = $this->actingAs($adminCabang)->get(route('laporan_laba_rugi.index', [
            'cabang_id' => $this->cabangKedua->id,
        ]));

        $response->assertOk();
        $response->assertViewHas('cabangId', $this->cabangUtama->id);
        $response->assertViewHas('totalPendapatan', 125000.0);
    }

    /* -----------------------------------------------------------------
     | PERHITUNGAN LAPORAN LABA RUGI
     |------------------------------------------------------------------ */

    public function test_index_calculates_revenue_hpp_expenses_and_net_profit_correctly(): void
    {
        $akunPendapatan = $this->buatAkun([
            'kode_akun' => '4-1100',
            'nama_akun' => 'Pendapatan Penjualan Uji',
            'tipe_akun' => 'Pendapatan',
            'kelompok_akun' => 'Pendapatan Usaha',
            'saldo_normal' => 'Kredit',
        ]);
        $akunHpp = $this->buatAkun([
            'kode_akun' => '5-1100',
            'nama_akun' => 'Harga Pokok Penjualan Uji',
            'tipe_akun' => 'Beban/Biaya',
            'kelompok_akun' => 'HPP',
        ]);
        $akunOperasional = $this->buatAkun([
            'kode_akun' => '6-1100',
            'nama_akun' => 'Beban Operasional Uji',
            'tipe_akun' => 'Beban/Biaya',
            'kelompok_akun' => 'Beban Operasional',
        ]);
        $akunLain = $this->buatAkun([
            'kode_akun' => '7-1100',
            'nama_akun' => 'Biaya Lain-lain Uji',
            'tipe_akun' => 'Beban/Biaya',
            'kelompok_akun' => 'Biaya Lain-lain',
        ]);

        $jurnal = $this->buatJurnal($this->cabangUtama);
        $this->buatDetail($jurnal, $akunPendapatan, 5000, 100000);
        $this->buatDetail($jurnal, $akunHpp, 30000, 2000);
        $this->buatDetail($jurnal, $akunOperasional, 10000, 500);
        $this->buatDetail($jurnal, $akunLain, 3000, 0);

        $response = $this->get(route('laporan_laba_rugi.index'));

        $response->assertOk();
        $response->assertViewHas('totalPendapatan', 95000.0);
        $response->assertViewHas('totalHpp', 28000.0);
        $response->assertViewHas('labaKotor', 67000.0);
        $response->assertViewHas('totalBeban', 12500.0);
        $response->assertViewHas('labaBersih', 54500.0);

        $response->assertViewHas('pendapatan', function ($items) use ($akunPendapatan): bool {
            return $items->count() === 1
                && $items->first()['akun']->id === $akunPendapatan->id
                && $items->first()['debit'] === 5000.0
                && $items->first()['kredit'] === 100000.0
                && $items->first()['saldo'] === 95000.0;
        });

        $response->assertViewHas('hpp', function ($items) use ($akunHpp): bool {
            return $items->count() === 1
                && $items->first()['akun']->id === $akunHpp->id
                && $items->first()['saldo'] === 28000.0;
        });

        $response->assertViewHas('beban', function ($items) use ($akunOperasional, $akunLain): bool {
            return $items->count() === 2
                && $items->pluck('akun.id')->all() === [$akunOperasional->id, $akunLain->id]
                && $items->sum('saldo') === 12500.0;
        });
    }

    public function test_only_active_relevant_accounts_are_included_in_report(): void
    {
        $pendapatanAktif = $this->buatAkun([
            'kode_akun' => '4-1100',
            'nama_akun' => 'Pendapatan Aktif',
            'tipe_akun' => 'Pendapatan',
            'kelompok_akun' => 'Pendapatan Usaha',
            'saldo_normal' => 'Kredit',
        ]);
        $pendapatanNonaktif = $this->buatAkun([
            'kode_akun' => '4-1200',
            'nama_akun' => 'Pendapatan Nonaktif',
            'tipe_akun' => 'Pendapatan',
            'kelompok_akun' => 'Pendapatan Usaha',
            'saldo_normal' => 'Kredit',
            'status' => 'nonaktif',
        ]);
        $hppAktif = $this->buatAkun([
            'kode_akun' => '5-1100',
            'nama_akun' => 'HPP Aktif',
            'tipe_akun' => 'Beban/Biaya',
            'kelompok_akun' => 'HPP',
        ]);
        $bebanAktif = $this->buatAkun([
            'kode_akun' => '6-1100',
            'nama_akun' => 'Beban Operasional Aktif',
            'tipe_akun' => 'Beban/Biaya',
            'kelompok_akun' => 'Beban Operasional',
        ]);
        $biayaLainAktif = $this->buatAkun([
            'kode_akun' => '7-1100',
            'nama_akun' => 'Biaya Lain Aktif',
            'tipe_akun' => 'Beban/Biaya',
            'kelompok_akun' => 'Biaya Lain-lain',
        ]);
        $kelompokTidakDigunakan = $this->buatAkun([
            'kode_akun' => '8-1100',
            'nama_akun' => 'Beban Kelompok Lain',
            'tipe_akun' => 'Beban/Biaya',
            'kelompok_akun' => 'Beban Lain',
        ]);
        $akunAset = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas Tidak Masuk Laporan',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]);

        $response = $this->get(route('laporan_laba_rugi.index'));

        $response->assertOk();
        $response->assertViewHas('pendapatan', fn ($items) =>
            $items->pluck('akun.id')->all() === [$pendapatanAktif->id]
        );
        $response->assertViewHas('hpp', fn ($items) =>
            $items->pluck('akun.id')->all() === [$hppAktif->id]
        );
        $response->assertViewHas('beban', fn ($items) =>
            $items->pluck('akun.id')->all() === [$bebanAktif->id, $biayaLainAktif->id]
        );

        $this->assertNotContains($pendapatanNonaktif->id, $response->viewData('pendapatan')->pluck('akun.id')->all());
        $this->assertNotContains($kelompokTidakDigunakan->id, $response->viewData('beban')->pluck('akun.id')->all());
        $this->assertNotContains($akunAset->id, $response->viewData('beban')->pluck('akun.id')->all());
    }

    public function test_index_displays_empty_messages_when_there_are_no_relevant_accounts(): void
    {
        $response = $this->get(route('laporan_laba_rugi.index'));

        $response->assertOk();
        $response->assertSee('Belum ada pendapatan.');
        $response->assertSee('Belum ada HPP.');
        $response->assertSee('Belum ada beban.');
        $response->assertViewHas('pendapatan', fn ($items) => $items->isEmpty());
        $response->assertViewHas('hpp', fn ($items) => $items->isEmpty());
        $response->assertViewHas('beban', fn ($items) => $items->isEmpty());
        $response->assertViewHas('labaBersih', 0.0);
    }

    /* -----------------------------------------------------------------
     | FILTER PERIODE
     |------------------------------------------------------------------ */

    public function test_index_filters_journal_details_by_selected_date_range_inclusively(): void
    {
        $pendapatan = $this->buatAkun([
            'kode_akun' => '4-1100',
            'nama_akun' => 'Pendapatan Filter Tanggal',
            'tipe_akun' => 'Pendapatan',
            'kelompok_akun' => 'Pendapatan Usaha',
            'saldo_normal' => 'Kredit',
        ]);

        $awal = '2026-11-05';
        $tengah = '2026-11-10';
        $akhir = '2026-11-15';
        $sebelum = '2026-11-04';
        $sesudah = '2026-11-16';

        $jurnalAwal = $this->buatJurnal($this->cabangUtama, ['tanggal' => $awal]);
        $jurnalTengah = $this->buatJurnal($this->cabangUtama, ['tanggal' => $tengah]);
        $jurnalAkhir = $this->buatJurnal($this->cabangUtama, ['tanggal' => $akhir]);
        $jurnalSebelum = $this->buatJurnal($this->cabangUtama, ['tanggal' => $sebelum]);
        $jurnalSesudah = $this->buatJurnal($this->cabangUtama, ['tanggal' => $sesudah]);

        $this->buatDetail($jurnalAwal, $pendapatan, 0, 100000);
        $this->buatDetail($jurnalTengah, $pendapatan, 0, 200000);
        $this->buatDetail($jurnalAkhir, $pendapatan, 0, 300000);
        $this->buatDetail($jurnalSebelum, $pendapatan, 0, 400000);
        $this->buatDetail($jurnalSesudah, $pendapatan, 0, 500000);

        $response = $this->get(route('laporan_laba_rugi.index', [
            'start_date' => $awal,
            'end_date' => $akhir,
        ]));

        $response->assertOk();
        $response->assertViewHas('start_date', $awal);
        $response->assertViewHas('end_date', $akhir);
        $response->assertViewHas('totalPendapatan', 600000.0);
    }

    /* -----------------------------------------------------------------
     | VALIDASI INPUT FILTER
     |------------------------------------------------------------------ */

    public function test_index_rejects_invalid_start_date(): void
    {
        $response = $this->from(route('laporan_laba_rugi.index'))
            ->get(route('laporan_laba_rugi.index', [
                'start_date' => 'bukan-tanggal',
                'end_date' => '2026-11-30',
            ]));

        $response->assertRedirect(route('laporan_laba_rugi.index'));
        $response->assertSessionHasErrors('start_date');
    }

    public function test_index_rejects_invalid_end_date(): void
    {
        $response = $this->from(route('laporan_laba_rugi.index'))
            ->get(route('laporan_laba_rugi.index', [
                'start_date' => '2026-11-01',
                'end_date' => 'bukan-tanggal',
            ]));

        $response->assertRedirect(route('laporan_laba_rugi.index'));
        $response->assertSessionHasErrors('end_date');
    }

    public function test_index_rejects_end_date_before_start_date(): void
    {
        $response = $this->from(route('laporan_laba_rugi.index'))
            ->get(route('laporan_laba_rugi.index', [
                'start_date' => '2026-11-20',
                'end_date' => '2026-11-10',
            ]));

        $response->assertRedirect(route('laporan_laba_rugi.index'));
        $response->assertSessionHasErrors('end_date');
    }

    public function test_index_rejects_non_integer_branch_id(): void
    {
        $response = $this->from(route('laporan_laba_rugi.index'))
            ->get(route('laporan_laba_rugi.index', [
                'cabang_id' => 'bukan-angka',
            ]));

        $response->assertRedirect(route('laporan_laba_rugi.index'));
        $response->assertSessionHasErrors('cabang_id');
    }

    public function test_index_rejects_nonexistent_branch_id(): void
    {
        $response = $this->from(route('laporan_laba_rugi.index'))
            ->get(route('laporan_laba_rugi.index', [
                'cabang_id' => 999999,
            ]));

        $response->assertRedirect(route('laporan_laba_rugi.index'));
        $response->assertSessionHasErrors('cabang_id');
    }

    /* -----------------------------------------------------------------
     | HELPER DATA PENGUJIAN
     |------------------------------------------------------------------ */

    private function buatCabang(string $nama, array $attributes = []): Cabang
    {
        $nomor = ++$this->nomorCabang;

        return Cabang::create(array_merge([
            'nama' => $nama,
            'alamat' => 'Alamat ' . $nama,
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ], $attributes));
    }

    private function buatUser(string $role, ?Cabang $cabang = null): User
    {
        $nomor = ++$this->nomorUser;

        return User::create([
            'name' => $role . ' Pengujian ' . $nomor,
            'username' => strtolower(str_replace(' ', '_', $role)) . '_uji_' . $nomor,
            'email' => 'user.laba.rugi.' . $nomor . '@example.test',
            'password' => 'password',
            'role' => $role,
            'cabang_id' => $cabang?->id,
        ]);
    }

    private function buatAkun(array $attributes = []): Akun
    {
        $nomor = ++$this->nomorAkun;

        return Akun::create(array_merge([
            'kode_akun' => '9-' . (1000 + ($nomor * 100)),
            'nama_akun' => 'Akun Pengujian Laba Rugi ' . $nomor,
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
            'saldo_normal' => 'Debit',
            'status' => 'aktif',
        ], $attributes));
    }

    private function buatJurnal(Cabang $cabang, array $attributes = []): Jurnal
    {
        $nomor = ++$this->nomorJurnal;

        return Jurnal::create(array_merge([
            'no_referensi' => 'JRN-LR-UJI-' . str_pad((string) $nomor, 4, '0', STR_PAD_LEFT),
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Jurnal pengujian laporan laba rugi ' . $nomor,
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
}
