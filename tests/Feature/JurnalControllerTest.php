<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\DetailJurnal;
use App\Models\Jurnal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JurnalControllerTest extends TestCase
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

        $response = $this->get(route('jurnal.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_pusat_can_open_general_journal_page(): void
    {
        $response = $this->get(route('jurnal.index'));

        $response->assertOk();
        $response->assertViewIs('admin.jurnal.index');
        $response->assertViewHasAll([
            'jurnals',
            'cabangs',
            'startDate',
            'endDate',
        ]);
    }

    public function test_admin_cabang_can_open_general_journal_page(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);

        $response = $this->actingAs($adminCabang)->get(route('jurnal.index'));

        $response->assertOk();
        $response->assertViewIs('admin.jurnal.index');
    }

    public function test_customer_service_cannot_access_general_journal(): void
    {
        $customerService = $this->buatUser('Customer Service', $this->cabangUtama);

        $response = $this->actingAs($customerService)->get(route('jurnal.index'));

        $response->assertForbidden();
    }

    public function test_admin_cabang_without_branch_assignment_is_forbidden(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang');

        $response = $this->actingAs($adminCabang)->get(route('jurnal.index'));

        $response->assertForbidden();
    }

    public function test_index_response_has_prevent_back_history_headers(): void
    {
        $response = $this->get(route('jurnal.index'));

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

    /* -----------------------------------------------------------------
     | FILTER PERIODE DAN DATA DROPDOWN CABANG
     |------------------------------------------------------------------ */

    public function test_index_uses_current_month_when_date_filters_are_omitted(): void
    {
        $response = $this->get(route('jurnal.index'));

        $response->assertOk();
        $response->assertViewHas('startDate', now()->startOfMonth()->toDateString());
        $response->assertViewHas('endDate', now()->endOfMonth()->toDateString());
    }

    public function test_admin_pusat_only_receives_active_branches_sorted_by_name(): void
    {
        $cabangZebra = $this->buatCabang('Cabang Zebra');
        $cabangAndalas = $this->buatCabang('Cabang Andalas');
        $cabangNonaktif = $this->buatCabang('Cabang Nonaktif', [
            'status' => 'nonaktif',
        ]);

        $response = $this->get(route('jurnal.index'));

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
            ]
                && !$cabangs->contains('id', $cabangNonaktif->id);
        });
    }

    public function test_admin_cabang_receives_empty_branch_filter_collection(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);

        $response = $this->actingAs($adminCabang)->get(route('jurnal.index'));

        $response->assertOk();
        $response->assertViewHas('cabangs', fn ($cabangs) => $cabangs->isEmpty());
    }

    public function test_index_filters_journals_by_selected_date_range(): void
    {
        $akun = $this->buatAkun();
        $awal = now()->startOfMonth()->addDays(4)->toDateString();
        $tengah = now()->startOfMonth()->addDays(9)->toDateString();
        $akhir = now()->startOfMonth()->addDays(14)->toDateString();
        $sebelum = now()->startOfMonth()->subDay()->toDateString();
        $sesudah = now()->endOfMonth()->addDay()->toDateString();

        $jurnalAwal = $this->buatJurnal($this->cabangUtama, [
            'tanggal' => $awal,
            'no_referensi' => 'JRN-FILTER-AWAL',
        ]);
        $jurnalTengah = $this->buatJurnal($this->cabangUtama, [
            'tanggal' => $tengah,
            'no_referensi' => 'JRN-FILTER-TENGAH',
        ]);
        $jurnalAkhir = $this->buatJurnal($this->cabangUtama, [
            'tanggal' => $akhir,
            'no_referensi' => 'JRN-FILTER-AKHIR',
        ]);
        $this->buatJurnal($this->cabangUtama, [
            'tanggal' => $sebelum,
            'no_referensi' => 'JRN-DI-LUAR-SEBELUM',
        ]);
        $this->buatJurnal($this->cabangUtama, [
            'tanggal' => $sesudah,
            'no_referensi' => 'JRN-DI-LUAR-SESUDAH',
        ]);

        $this->buatDetail($jurnalAwal, $akun, 100000, 0);
        $this->buatDetail($jurnalTengah, $akun, 200000, 0);
        $this->buatDetail($jurnalAkhir, $akun, 300000, 0);

        $response = $this->get(route('jurnal.index', [
            'start_date' => $awal,
            'end_date' => $akhir,
        ]));

        $response->assertOk();

        

        // Fokus test ini adalah memastikan query Controller mengembalikan
        // seluruh jurnal dalam rentang tanggal, bukan menguji rendering Blade.
        // Rendering nomor referensi/detail diuji pada test terpisah.
        $jurnals = $response->viewData('jurnals');

        $this->assertEqualsCanonicalizing(
            [$jurnalAwal->id, $jurnalTengah->id, $jurnalAkhir->id],
            $jurnals->pluck('id')->all(),
            'Controller harus mengembalikan semua jurnal di dalam rentang tanggal.'
        );

        // Pastikan ketiga data fixture benar-benar memiliki detail jurnal.
        foreach ([$jurnalAwal, $jurnalTengah, $jurnalAkhir] as $jurnal) {
            $this->assertDatabaseHas('detail_jurnals', [
                'jurnal_id' => $jurnal->id,
                'akun_id' => $akun->id,
            ]);
        }

        $response->assertViewHas('startDate', $awal);
        $response->assertViewHas('endDate', $akhir);

        // Memastikan jurnal pertama benar-benar tersimpan
        // menggunakan tanggal awal yang diharapkan.
        $this->assertDatabaseHas('jurnals', [
            'id' => $jurnalAwal->id,
            'tanggal' => $awal,
        ]);
    }

    public function test_admin_pusat_can_filter_journals_by_selected_branch(): void
    {
        $jurnalUtama = $this->buatJurnal($this->cabangUtama, [
            'no_referensi' => 'JRN-CABANG-UTAMA',
        ]);
        $jurnalKedua = $this->buatJurnal($this->cabangKedua, [
            'no_referensi' => 'JRN-CABANG-KEDUA',
        ]);

        $response = $this->get(route('jurnal.index', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
            'cabang_id' => $this->cabangKedua->id,
        ]));

        $response->assertOk();
        $jurnals = $response->viewData('jurnals');

        $this->assertCount(1, $jurnals);
        $this->assertSame($jurnalKedua->id, $jurnals->first()->id);
        $this->assertNotSame($jurnalUtama->id, $jurnals->first()->id);
    }

    public function test_admin_pusat_sees_journals_from_all_branches_when_branch_filter_is_omitted(): void
    {
        $akun = $this->buatAkun();

        $jurnalUtama = $this->buatJurnal($this->cabangUtama, [
            'no_referensi' => 'JRN-SEMUA-UTAMA',
        ]);
        $jurnalKedua = $this->buatJurnal($this->cabangKedua, [
            'no_referensi' => 'JRN-SEMUA-KEDUA',
        ]);

        // Halaman Jurnal Umum menampilkan baris detail, bukan header jurnal saja.
        $this->buatDetail($jurnalUtama, $akun, 100000, 0, 'Transaksi Cabang Utama');
        $this->buatDetail($jurnalKedua, $akun, 200000, 0, 'Transaksi Cabang Kedua');

        $response = $this->get(route('jurnal.index'));

        $response->assertOk();
        $response->assertSee('JRN-SEMUA-UTAMA');
        $response->assertSee('JRN-SEMUA-KEDUA');
        $this->assertCount(2, $response->viewData('jurnals'));
    }

    public function test_admin_cabang_only_sees_journals_from_own_branch_even_if_other_branch_is_requested(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang', $this->cabangUtama);
        $akun = $this->buatAkun();

        $jurnalSendiri = $this->buatJurnal($this->cabangUtama, [
            'no_referensi' => 'JRN-MILIK-CABANG',
        ]);
        $jurnalCabangLain = $this->buatJurnal($this->cabangKedua, [
            'no_referensi' => 'JRN-CABANG-LAIN',
        ]);

        // Masing-masing header diberi detail agar representasi tabel sesuai Blade.
        $this->buatDetail($jurnalSendiri, $akun, 100000, 0, 'Transaksi cabang sendiri');
        $this->buatDetail($jurnalCabangLain, $akun, 50000, 0, 'Transaksi cabang lain');

        $response = $this->actingAs($adminCabang)->get(route('jurnal.index', [
            'cabang_id' => $this->cabangKedua->id,
        ]));

        $response->assertOk();
        $jurnals = $response->viewData('jurnals');

        $this->assertCount(1, $jurnals);
        $this->assertSame($jurnalSendiri->id, $jurnals->first()->id);
        $response->assertSee('JRN-MILIK-CABANG');
        $response->assertDontSee('JRN-CABANG-LAIN');
    }

    /* -----------------------------------------------------------------
     | PENGURUTAN DAN DETAIL JURNAL
     |------------------------------------------------------------------ */

    public function test_journals_are_sorted_by_latest_date_then_latest_id(): void
    {
        $tanggalLama = now()->startOfMonth()->addDays(3)->toDateString();
        $tanggalBaru = now()->startOfMonth()->addDays(8)->toDateString();

        $jurnalLama = $this->buatJurnal($this->cabangUtama, [
            'tanggal' => $tanggalLama,
            'no_referensi' => 'JRN-URUT-LAMA',
        ]);
        $jurnalTanggalBaruPertama = $this->buatJurnal($this->cabangUtama, [
            'tanggal' => $tanggalBaru,
            'no_referensi' => 'JRN-URUT-BARU-1',
        ]);
        $jurnalTanggalBaruKedua = $this->buatJurnal($this->cabangUtama, [
            'tanggal' => $tanggalBaru,
            'no_referensi' => 'JRN-URUT-BARU-2',
        ]);

        $response = $this->get(route('jurnal.index', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]));

        $response->assertOk();
        $ids = $response->viewData('jurnals')->pluck('id')->all();

        $this->assertSame([
            $jurnalTanggalBaruKedua->id,
            $jurnalTanggalBaruPertama->id,
            $jurnalLama->id,
        ], $ids);
    }

    public function test_journal_details_display_account_name_debit_and_credit_amounts(): void
    {
        $kas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas Uji Jurnal',
        ]);
        $pendapatan = $this->buatAkun([
            'kode_akun' => '4-1100',
            'nama_akun' => 'Pendapatan Uji Jurnal',
            'tipe_akun' => 'Pendapatan',
            'kelompok_akun' => 'Pendapatan Usaha',
            'saldo_normal' => 'Kredit',
        ]);

        $jurnal = $this->buatJurnal($this->cabangUtama, [
            'no_referensi' => 'JRN-TAMPIL-DETAIL',
            'keterangan' => 'Penerimaan penjualan',
        ]);

        $this->buatDetail($jurnal, $kas, 250000, 0, 'Penerimaan penjualan');
        $this->buatDetail($jurnal, $pendapatan, 0, 250000, 'Penerimaan penjualan');

        $response = $this->get(route('jurnal.index'));

        $response->assertOk();
        $response->assertSee('JRN-TAMPIL-DETAIL');
        $response->assertSee('Kas Uji Jurnal');
        $response->assertSee('Pendapatan Uji Jurnal');
        $response->assertSee('Rp 250.000');
        $response->assertSee('Penerimaan penjualan');
    }

    public function test_detail_uses_journal_description_when_detail_description_is_empty(): void
    {
        $akun = $this->buatAkun();
        $jurnal = $this->buatJurnal($this->cabangUtama, [
            'no_referensi' => 'JRN-KETERANGAN-CADANGAN',
            'keterangan' => 'Keterangan dari header jurnal',
        ]);
        $this->buatDetail($jurnal, $akun, 100000, 0, null);

        $response = $this->get(route('jurnal.index'));

        $response->assertOk();
        $response->assertSee('Keterangan dari header jurnal');
    }

    public function test_hpp_detail_group_is_displayed_after_regular_detail_group(): void
    {
        $akunKas = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas Urutan HPP',
        ]);
        $akunHpp = $this->buatAkun([
            'kode_akun' => '5-1100',
            'nama_akun' => 'HPP Urutan Jurnal',
            'tipe_akun' => 'Beban/Biaya',
            'kelompok_akun' => 'HPP',
        ]);

        $jurnal = $this->buatJurnal($this->cabangUtama, [
            'no_referensi' => 'JRN-GROUP-HPP',
            'keterangan' => 'Jurnal penjualan',
        ]);
        $this->buatDetail($jurnal, $akunHpp, 50000, 0, 'HPP Produk');
        $this->buatDetail($jurnal, $akunKas, 100000, 0, 'Penjualan Produk');

        $response = $this->get(route('jurnal.index'));

        $response->assertOk();
        $html = $response->getContent();
        $posPenjualan = strpos($html, 'Penjualan Produk');
        $posHpp = strpos($html, 'HPP Produk');

        $this->assertNotFalse($posPenjualan);
        $this->assertNotFalse($posHpp);
        $this->assertLessThan($posHpp, $posPenjualan);
    }

    public function test_index_displays_empty_state_when_there_are_no_journals(): void
    {
        $response = $this->get(route('jurnal.index'));

        $response->assertOk();
        $response->assertSee('Belum ada pencatatan jurnal.');
        $response->assertViewHas('jurnals', fn ($jurnals) => $jurnals->isEmpty());
    }

    /* -----------------------------------------------------------------
     | VALIDASI FILTER
     |------------------------------------------------------------------ */

    public function test_index_rejects_invalid_start_date(): void
    {
        $response = $this->from(route('jurnal.index'))->get(route('jurnal.index', [
            'start_date' => 'bukan-tanggal',
            'end_date' => now()->endOfMonth()->toDateString(),
        ]));

        $response->assertRedirect(route('jurnal.index'));
        $response->assertSessionHasErrors('start_date');
    }

    public function test_index_rejects_invalid_end_date(): void
    {
        $response = $this->from(route('jurnal.index'))->get(route('jurnal.index', [
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => 'bukan-tanggal',
        ]));

        $response->assertRedirect(route('jurnal.index'));
        $response->assertSessionHasErrors('end_date');
    }

    public function test_index_rejects_end_date_before_start_date(): void
    {
        $response = $this->from(route('jurnal.index'))->get(route('jurnal.index', [
            'start_date' => '2026-11-20',
            'end_date' => '2026-11-10',
        ]));

        $response->assertRedirect(route('jurnal.index'));
        $response->assertSessionHasErrors('end_date');
    }

    public function test_index_rejects_non_integer_branch_id(): void
    {
        $response = $this->from(route('jurnal.index'))->get(route('jurnal.index', [
            'cabang_id' => 'bukan-angka',
        ]));

        $response->assertRedirect(route('jurnal.index'));
        $response->assertSessionHasErrors('cabang_id');
    }

    public function test_index_rejects_nonexistent_branch_id(): void
    {
        $response = $this->from(route('jurnal.index'))->get(route('jurnal.index', [
            'cabang_id' => 999999,
        ]));

        $response->assertRedirect(route('jurnal.index'));
        $response->assertSessionHasErrors('cabang_id');
    }

    /* -----------------------------------------------------------------
     | HELPER DATA TEST
     |------------------------------------------------------------------ */

    private function buatCabang(string $nama, array $attributes = []): Cabang
    {
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
            'email' => 'user.jurnal.' . $nomor . '@example.test',
            'password' => 'password',
            'role' => $role,
            'cabang_id' => $cabang?->id,
        ]);
    }

    private function buatAkun(array $attributes = []): Akun
    {
        $nomor = ++$this->nomorAkun;

        return Akun::create(array_merge([
            'kode_akun' => '1-' . (1000 + ($nomor * 100)),
            'nama_akun' => 'Akun Jurnal Uji ' . $nomor,
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
            'no_referensi' => 'JRN-UJI-' . str_pad((string) $nomor, 4, '0', STR_PAD_LEFT),
            'tanggal' => now()->toDateString(),
            'keterangan' => 'Keterangan jurnal pengujian ' . $nomor,
            'cabang_id' => $cabang->id,
            'user_id' => $this->adminPusat->id,
        ], $attributes));
    }

    private function buatDetail(
        Jurnal $jurnal,
        Akun $akun,
        float $debit = 0,
        float $kredit = 0,
        ?string $keterangan = null
    ): DetailJurnal {
        return DetailJurnal::create([
            'jurnal_id' => $jurnal->id,
            'akun_id' => $akun->id,
            'keterangan' => $keterangan,
            'debit' => $debit,
            'kredit' => $kredit,
        ]);
    }
}
