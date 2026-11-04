<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AkunControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminPusat;

    protected function setUp(): void
    {
        parent::setUp();

        // Semua route AkunController dilindungi middleware auth dan role:Admin Pusat.
        $this->adminPusat = User::factory()->create([
            'name' => 'Admin Pengujian',
            'email' => 'admin-pengujian@example.test',
            'username' => 'admin-pengujian',
            'role' => 'Admin Pusat',
        ]);

        $this->actingAs($this->adminPusat);
    }

    /* -----------------------------------------------------------------
     | INDEX
     |------------------------------------------------------------------ */

    public function test_index_displays_account_page_and_accounts_sorted_by_code(): void
    {
        $this->buatAkun([
            'kode_akun' => '2-1100',
            'nama_akun' => 'Utang Usaha',
            'tipe_akun' => 'Kewajiban',
            'kelompok_akun' => 'Kewajiban',
        ]);

        $this->buatAkun([
            'kode_akun' => '1-1200',
            'nama_akun' => 'Piutang Usaha',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]);

        $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]);

        $response = $this->get(route('akun.index'));

        $response->assertOk();
        $response->assertViewIs('admin.akun.index');
        $response->assertViewHas('akuns', function ($akuns): bool {
            return $akuns->pluck('kode_akun')->all() === [
                '1-1100',
                '1-1200',
                '2-1100',
            ];
        });
    }

    public function test_index_can_be_opened_when_there_are_no_accounts(): void
    {
        $response = $this->get(route('akun.index'));

        $response->assertOk();
        $response->assertViewIs('admin.akun.index');
        $response->assertSee('Belum ada data akun perkiraan.');
    }

    /* -----------------------------------------------------------------
     | PREVIEW KODE
     |------------------------------------------------------------------ */

    public static function kodeAwalProvider(): array
    {
        return [
            'Aset' => ['Aset', 'Aset', '1-1100'],
            'Kewajiban' => ['Kewajiban', 'Kewajiban', '2-1100'],
            'Ekuitas' => ['Ekuitas', 'Ekuitas', '3-1100'],
            'Pendapatan Usaha' => ['Pendapatan', 'Pendapatan Usaha', '4-1100'],
            'HPP' => ['Beban/Biaya', 'HPP', '5-1100'],
            'Beban Operasional' => ['Beban/Biaya', 'Beban Operasional', '5-2100'],
            'Biaya Lain-lain' => ['Beban/Biaya', 'Biaya Lain-lain', '5-3100'],
        ];
    }

    #[DataProvider('kodeAwalProvider')]
    public function test_preview_kode_returns_initial_code_for_each_valid_account_group(
        string $tipeAkun,
        string $kelompokAkun,
        string $kodeDiharapkan
    ): void {
        $response = $this->getJson(route('akun.preview-kode', [
            'tipe_akun' => $tipeAkun,
            'kelompok_akun' => $kelompokAkun,
        ]));

        $response->assertOk();
        $response->assertExactJson(['kode' => $kodeDiharapkan]);
    }

    public function test_preview_kode_returns_next_number_in_the_same_group(): void
    {
        $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]);

        $response = $this->getJson(route('akun.preview-kode', [
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]));

        $response->assertOk();
        $response->assertExactJson(['kode' => '1-1200']);
    }

    public function test_preview_kode_ignores_the_account_id_sent_in_the_request(): void
    {
        $akun = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]);

        $response = $this->getJson(route('akun.preview-kode', [
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
            'id' => $akun->id,
        ]));

        $response->assertOk();
        $response->assertExactJson(['kode' => '1-1100']);
    }

    public function test_preview_kode_returns_null_when_group_does_not_match_account_type(): void
    {
        // Perilaku controller saat ini: kombinasi kelompok yang tidak cocok
        // menghasilkan JSON kode=null, bukan validation error.
        $response = $this->getJson(route('akun.preview-kode', [
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'HPP',
        ]));

        $response->assertOk();
        $response->assertExactJson(['kode' => null]);
    }

    public function test_preview_kode_rejects_missing_or_invalid_required_fields(): void
    {
        $response = $this->getJson(route('akun.preview-kode'));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'tipe_akun',
            'kelompok_akun',
        ]);
    }

    public function test_preview_kode_rejects_an_account_id_that_does_not_exist(): void
    {
        $response = $this->getJson(route('akun.preview-kode', [
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
            'id' => 999999,
        ]));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['id']);
    }

    /* -----------------------------------------------------------------
     | STORE
     |------------------------------------------------------------------ */

    public function test_store_creates_an_account_with_automatically_generated_code(): void
    {
        $response = $this->post(route('akun.store'), [
            'nama_akun' => 'Kas',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]);

        $response->assertRedirect(route('akun.index'));
        $response->assertSessionHas('success', 'Akun berhasil ditambahkan!');

        $this->assertDatabaseHas('akuns', [
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
            'status' => 'aktif',
            'saldo_normal' => 'Debit',
        ]);
    }

    public function test_store_generates_the_next_code_for_an_existing_group(): void
    {
        $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]);

        $response = $this->post(route('akun.store'), [
            'nama_akun' => 'Piutang Usaha',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]);

        $response->assertRedirect(route('akun.index'));

        $this->assertDatabaseHas('akuns', [
            'kode_akun' => '1-1200',
            'nama_akun' => 'Piutang Usaha',
        ]);
    }

    public function test_store_rejects_empty_required_fields(): void
    {
        $response = $this->postJson(route('akun.store'), []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'nama_akun',
            'tipe_akun',
            'kelompok_akun',
        ]);

        $this->assertDatabaseCount('akuns', 0);
    }

    public function test_store_rejects_an_invalid_account_type(): void
    {
        $response = $this->postJson(route('akun.store'), [
            'nama_akun' => 'Akun Tidak Valid',
            'tipe_akun' => 'Persediaan',
            'kelompok_akun' => 'Aset',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['tipe_akun']);
        $this->assertDatabaseCount('akuns', 0);
    }

    public function test_store_rejects_a_group_that_does_not_match_the_account_type(): void
    {
        $response = $this->post(route('akun.store'), [
            'nama_akun' => 'Akun HPP Salah Tipe',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'HPP',
        ]);

        $response->assertSessionHasErrors(['kelompok_akun']);
        $this->assertDatabaseCount('akuns', 0);
    }

    public static function akunBersaldoNormalKreditProvider(): array
    {
        return [
            'Kewajiban' => ['Kewajiban', 'Kewajiban', 'Utang Usaha'],
            'Ekuitas' => ['Ekuitas', 'Ekuitas', 'Modal Pemilik'],
            'Pendapatan' => ['Pendapatan', 'Pendapatan Usaha', 'Pendapatan Jasa'],
        ];
    }

    #[DataProvider('akunBersaldoNormalKreditProvider')]
    public function test_store_sets_credit_normal_balance_for_liability_equity_and_revenue(
        string $tipeAkun,
        string $kelompokAkun,
        string $namaAkun
    ): void {
        $response = $this->post(route('akun.store'), [
            'nama_akun' => $namaAkun,
            'tipe_akun' => $tipeAkun,
            'kelompok_akun' => $kelompokAkun,
        ]);

        $response->assertRedirect(route('akun.index'));

        $this->assertDatabaseHas('akuns', [
            'nama_akun' => $namaAkun,
            'saldo_normal' => 'Kredit',
        ]);
    }

    public function test_store_rejects_duplicate_account_name(): void
    {
        $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]);

        $response = $this->post(route('akun.store'), [
            'nama_akun' => 'Kas',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]);

        $response->assertSessionHasErrors(['nama_akun']);

        $this->assertDatabaseCount('akuns', 1);
    }

    /* -----------------------------------------------------------------
     | UPDATE
     |------------------------------------------------------------------ */

    public function test_update_changes_account_name_and_status(): void
    {
        $akun = $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas Lama',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
            'status' => 'aktif',
        ]);

        $response = $this->put(route('akun.update', $akun->id), [
            'nama_akun' => 'Kas Operasional',
            'status' => 'nonaktif',
        ]);

        $response->assertRedirect(route('akun.index'));
        $response->assertSessionHas('success', 'Akun berhasil diperbarui!');

        $this->assertDatabaseHas('akuns', [
            'id' => $akun->id,
            'nama_akun' => 'Kas Operasional',
            'status' => 'nonaktif',
            'kode_akun' => '1-1100',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]);
    }

    public function test_update_rejects_empty_account_name(): void
    {
        $akun = $this->buatAkun();

        $response = $this->putJson(route('akun.update', $akun->id), [
            'nama_akun' => '',
            'status' => 'aktif',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['nama_akun']);
    }

    public function test_update_rejects_account_name_longer_than_255_characters(): void
    {
        $akun = $this->buatAkun();

        $response = $this->putJson(route('akun.update', $akun->id), [
            'nama_akun' => str_repeat('A', 256),
            'status' => 'aktif',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['nama_akun']);
    }

    public function test_update_rejects_an_invalid_status(): void
    {
        $akun = $this->buatAkun();

        $response = $this->putJson(route('akun.update', $akun->id), [
            'nama_akun' => 'Nama Akun Baru',
            'status' => 'dihapus',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_update_returns_404_when_account_does_not_exist(): void
    {
        $response = $this->put(route('akun.update', 999999), [
            'nama_akun' => 'Nama Baru',
            'status' => 'aktif',
        ]);

        $response->assertNotFound();
    }

    public function test_update_rejects_duplicate_account_name(): void
    {
        $this->buatAkun([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Kas',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]);

        $akunKedua = $this->buatAkun([
            'kode_akun' => '1-1200',
            'nama_akun' => 'Piutang Usaha',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
        ]);

        $response = $this->put(route('akun.update', $akunKedua->id), [
            'nama_akun' => 'Kas',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors(['nama_akun']);

        $this->assertDatabaseHas('akuns', [
            'id' => $akunKedua->id,
            'nama_akun' => 'Piutang Usaha',
        ]);

        $this->assertDatabaseCount('akuns', 2);
    }

    /* -----------------------------------------------------------------
     | AUTHENTICATION & AUTHORIZATION
     |------------------------------------------------------------------ */

    public function test_guest_cannot_access_account_page(): void
    {
        auth()->logout();

        $response = $this->get(route('akun.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_with_role_admin_cabang_cannot_access_account_page(): void
    {
        $adminCabang = User::factory()->create([
            'name' => 'Admin Cabang Pengujian',
            'email' => 'admin-cabang-pengujian@example.test',
            'username' => 'admin-cabang-pengujian',
            'role' => 'Admin Cabang',
        ]);

        $response = $this->actingAs($adminCabang)->get(route('akun.index'));

        $response->assertForbidden();
    }

    /**
     * Membuat data akun langsung untuk kebutuhan persiapan/pengujian.
     */
    private function buatAkun(array $attributes = []): Akun
    {
        return Akun::query()->create(array_merge([
            'kode_akun' => '1-1100',
            'nama_akun' => 'Akun Pengujian',
            'tipe_akun' => 'Aset',
            'kelompok_akun' => 'Aset',
            'saldo_normal' => 'Debit',
            'status' => 'aktif',
        ], $attributes));
    }
}