<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CabangControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminPusat;

    private int $nomorCabang = 0;
    private int $nomorUser = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // Seluruh route CabangController hanya dapat diakses Admin Pusat.
        $this->adminPusat = $this->buatUser('Admin Pusat');
        $this->actingAs($this->adminPusat);
    }

    /* -----------------------------------------------------------------
     | AKSES DAN INDEX
     |------------------------------------------------------------------ */

    public function test_guest_is_redirected_to_login(): void
    {
        auth()->logout();

        $response = $this->get(route('cabang.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_pusat_can_open_branch_page(): void
    {
        $response = $this->get(route('cabang.index'));

        $response->assertOk();
        $response->assertViewIs('admin.cabang.index');
        $response->assertViewHas('cabangs');
    }

    public function test_admin_cabang_cannot_access_branch_page(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang', $this->buatCabang());

        $response = $this->actingAs($adminCabang)->get(route('cabang.index'));

        $response->assertForbidden();
    }

    public function test_customer_service_cannot_access_branch_page(): void
    {
        $customerService = $this->buatUser('Customer Service');

        $response = $this->actingAs($customerService)->get(route('cabang.index'));

        $response->assertForbidden();
    }

    public function test_branch_page_response_has_prevent_back_history_headers(): void
    {
        $response = $this->get(route('cabang.index'));

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

    public function test_index_displays_all_branches_sorted_by_name_including_inactive(): void
    {
        $this->buatCabang([
            'nama' => 'Cabang Zebra',
            'status' => 'nonaktif',
        ]);
        $this->buatCabang([
            'nama' => 'Cabang Andalas',
            'status' => 'aktif',
        ]);
        $this->buatCabang([
            'nama' => 'Cabang Merdeka',
            'status' => 'nonaktif',
        ]);

        $response = $this->get(route('cabang.index'));

        $response->assertOk();
        $response->assertViewHas('cabangs', function ($cabangs): bool {
            return $cabangs->pluck('nama')->all() === [
                'Cabang Andalas',
                'Cabang Merdeka',
                'Cabang Zebra',
            ] && $cabangs->count() === 3
                && $cabangs->pluck('status')->contains('nonaktif');
        });

        $response->assertSee('Cabang Zebra');
        $response->assertSee('Nonaktif');
    }

    public function test_index_displays_empty_state_when_there_are_no_branches(): void
    {
        $response = $this->get(route('cabang.index'));

        $response->assertOk();
        $response->assertViewHas('cabangs', fn ($cabangs) => $cabangs->isEmpty());
        $response->assertSee('Belum ada data cabang.');
    }

    /* -----------------------------------------------------------------
     | STORE
     |------------------------------------------------------------------ */

    public function test_store_creates_branch_and_trims_name(): void
    {
        $response = $this->post(route('cabang.store'), [
            'nama' => '  Cabang Pekanbaru  ',
            'alamat' => '  Jl. Sudirman No. 10  ',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('cabang.index'));
        $response->assertSessionHas('success', 'Data cabang berhasil ditambahkan.');

        $this->assertDatabaseHas('cabangs', [
            'nama' => 'Cabang Pekanbaru',
            'alamat' => 'Jl. Sudirman No. 10',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ]);
    }

    public function test_store_accepts_non_numeric_phone_value_because_phone_is_validated_as_string(): void
    {
        $response = $this->post(route('cabang.store'), [
            'nama' => 'Cabang Telepon',
            'alamat' => 'Jl. Merdeka No. 1',
            'no_hp' => '0761-ABC123',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('cabang.index'));
        $this->assertDatabaseHas('cabangs', [
            'nama' => 'Cabang Telepon',
            'no_hp' => '0761-ABC123',
        ]);
    }

    public function test_store_rejects_missing_required_fields(): void
    {
        $response = $this->from(route('cabang.index'))
            ->post(route('cabang.store'), []);

        $response->assertRedirect(route('cabang.index'));
        $response->assertSessionHasErrors(['nama', 'alamat', 'no_hp', 'status']);
        $this->assertDatabaseCount('cabangs', 0);
    }

    public function test_store_rejects_name_that_becomes_empty_after_trimming(): void
    {
        $response = $this->from(route('cabang.index'))->post(route('cabang.store'), [
            'nama' => '       ',
            'alamat' => 'Jl. Sudirman',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('nama');
        $this->assertDatabaseCount('cabangs', 0);
    }

    public function test_store_rejects_duplicate_branch_name(): void
    {
        $this->buatCabang(['nama' => 'Cabang Pekanbaru']);

        $response = $this->from(route('cabang.index'))->post(route('cabang.store'), [
            'nama' => 'Cabang Pekanbaru',
            'alamat' => 'Jl. Sudirman No. 20',
            'no_hp' => '081234567891',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('nama');
        $this->assertDatabaseCount('cabangs', 1);
    }

    public function test_store_rejects_name_longer_than_255_characters(): void
    {
        $response = $this->from(route('cabang.index'))->post(route('cabang.store'), [
            'nama' => str_repeat('A', 256),
            'alamat' => 'Jl. Sudirman',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('nama');
        $this->assertDatabaseCount('cabangs', 0);
    }

    public function test_store_rejects_address_longer_than_1000_characters(): void
    {
        $response = $this->from(route('cabang.index'))->post(route('cabang.store'), [
            'nama' => 'Cabang Panjang',
            'alamat' => str_repeat('A', 1001),
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('alamat');
        $this->assertDatabaseCount('cabangs', 0);
    }

    public function test_store_rejects_phone_longer_than_20_characters(): void
    {
        $response = $this->from(route('cabang.index'))->post(route('cabang.store'), [
            'nama' => 'Cabang Nomor Panjang',
            'alamat' => 'Jl. Sudirman',
            'no_hp' => str_repeat('1', 21),
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors('no_hp');
        $this->assertDatabaseCount('cabangs', 0);
    }

    public function test_store_rejects_invalid_status(): void
    {
        $response = $this->from(route('cabang.index'))->post(route('cabang.store'), [
            'nama' => 'Cabang Status Salah',
            'alamat' => 'Jl. Sudirman',
            'no_hp' => '081234567890',
            'status' => 'pending',
        ]);

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseCount('cabangs', 0);
    }

    public function test_store_rejects_non_string_input_values(): void
    {
        $response = $this->from(route('cabang.index'))->post(route('cabang.store'), [
            'nama' => ['Cabang'],
            'alamat' => ['Alamat'],
            'no_hp' => ['081234567890'],
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors(['nama', 'alamat', 'no_hp']);
        $this->assertDatabaseCount('cabangs', 0);
    }

    /* -----------------------------------------------------------------
     | UPDATE
     |------------------------------------------------------------------ */

    public function test_update_changes_branch_data_and_trims_name(): void
    {
        $cabang = $this->buatCabang(['nama' => 'Cabang Lama']);

        $response = $this->put(route('cabang.update', $cabang->id), [
            'nama' => '  Cabang Baru  ',
            'alamat' => '  Jl. Baru No. 99  ',
            'no_hp' => '081299998888',
            'status' => 'nonaktif',
        ]);

        $response->assertRedirect(route('cabang.index'));
        $response->assertSessionHas('success', 'Data cabang berhasil diperbarui.');

        $this->assertDatabaseHas('cabangs', [
            'id' => $cabang->id,
            'nama' => 'Cabang Baru',
            'alamat' => 'Jl. Baru No. 99',
            'no_hp' => '081299998888',
            'status' => 'nonaktif',
        ]);
    }

    public function test_update_allows_branch_to_keep_its_own_name(): void
    {
        $cabang = $this->buatCabang(['nama' => 'Cabang Pekanbaru']);

        $response = $this->put(route('cabang.update', $cabang->id), [
            'nama' => 'Cabang Pekanbaru',
            'alamat' => $cabang->alamat,
            'no_hp' => $cabang->no_hp,
            'status' => $cabang->status,
        ]);

        $response->assertRedirect(route('cabang.index'));
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('cabangs', 1);
    }

    public function test_update_rejects_name_already_used_by_another_branch(): void
    {
        $cabangPertama = $this->buatCabang(['nama' => 'Cabang Pekanbaru']);
        $cabangKedua = $this->buatCabang(['nama' => 'Cabang Kerinci']);

        $response = $this->from(route('cabang.index'))->put(
            route('cabang.update', $cabangKedua->id),
            [
                'nama' => $cabangPertama->nama,
                'alamat' => $cabangKedua->alamat,
                'no_hp' => $cabangKedua->no_hp,
                'status' => $cabangKedua->status,
            ]
        );

        $response->assertSessionHasErrors('nama');
        $this->assertDatabaseHas('cabangs', [
            'id' => $cabangKedua->id,
            'nama' => 'Cabang Kerinci',
        ]);
    }

    public function test_update_rejects_missing_required_fields(): void
    {
        $cabang = $this->buatCabang();

        $response = $this->from(route('cabang.index'))
            ->put(route('cabang.update', $cabang->id), []);

        $response->assertSessionHasErrors(['nama', 'alamat', 'no_hp', 'status']);
        $this->assertDatabaseHas('cabangs', [
            'id' => $cabang->id,
            'nama' => $cabang->nama,
        ]);
    }

    public function test_update_rejects_fields_that_exceed_maximum_lengths(): void
    {
        $cabang = $this->buatCabang();

        $response = $this->from(route('cabang.index'))->put(
            route('cabang.update', $cabang->id),
            [
                'nama' => str_repeat('N', 256),
                'alamat' => str_repeat('A', 1001),
                'no_hp' => str_repeat('1', 21),
                'status' => 'aktif',
            ]
        );

        $response->assertSessionHasErrors(['nama', 'alamat', 'no_hp']);
    }

    public function test_update_rejects_invalid_status(): void
    {
        $cabang = $this->buatCabang();

        $response = $this->from(route('cabang.index'))->put(
            route('cabang.update', $cabang->id),
            [
                'nama' => 'Cabang Baru',
                'alamat' => $cabang->alamat,
                'no_hp' => $cabang->no_hp,
                'status' => 'ditutup',
            ]
        );

        $response->assertSessionHasErrors('status');
        $this->assertDatabaseHas('cabangs', [
            'id' => $cabang->id,
            'nama' => $cabang->nama,
        ]);
    }

    public function test_update_returns_404_when_branch_does_not_exist(): void
    {
        $response = $this->put(route('cabang.update', 999999), [
            'nama' => 'Cabang Tidak Ada',
            'alamat' => 'Jl. Sudirman',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ]);

        $response->assertNotFound();
    }

    /* -----------------------------------------------------------------
     | TOGGLE STATUS
     |------------------------------------------------------------------ */

    public function test_toggle_status_changes_active_branch_to_inactive(): void
    {
        $cabang = $this->buatCabang(['status' => 'aktif']);

        $response = $this->patch(route('cabang.toggle-status', $cabang->id));

        $response->assertRedirect(route('cabang.index'));
        $response->assertSessionHas('success', 'Status cabang berhasil diubah menjadi Nonaktif.');
        $this->assertDatabaseHas('cabangs', [
            'id' => $cabang->id,
            'status' => 'nonaktif',
        ]);
    }

    public function test_toggle_status_changes_inactive_branch_to_active(): void
    {
        $cabang = $this->buatCabang(['status' => 'nonaktif']);

        $response = $this->patch(route('cabang.toggle-status', $cabang->id));

        $response->assertRedirect(route('cabang.index'));
        $response->assertSessionHas('success', 'Status cabang berhasil diubah menjadi Aktif.');
        $this->assertDatabaseHas('cabangs', [
            'id' => $cabang->id,
            'status' => 'aktif',
        ]);
    }

    public function test_toggle_status_returns_404_when_branch_does_not_exist(): void
    {
        $response = $this->patch(route('cabang.toggle-status', 999999));

        $response->assertNotFound();
    }

    /* -----------------------------------------------------------------
     | PEMBATASAN AKSES UNTUK SELURUH ROUTE
     |------------------------------------------------------------------ */

    public function test_admin_cabang_cannot_create_update_or_toggle_branch(): void
    {
        $cabang = $this->buatCabang();
        $adminCabang = $this->buatUser('Admin Cabang', $cabang);

        $this->actingAs($adminCabang);

        $this->post(route('cabang.store'), [
            'nama' => 'Cabang Baru',
            'alamat' => 'Jl. Baru',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ])->assertForbidden();

        $this->put(route('cabang.update', $cabang->id), [
            'nama' => 'Nama Diubah',
            'alamat' => $cabang->alamat,
            'no_hp' => $cabang->no_hp,
            'status' => $cabang->status,
        ])->assertForbidden();

        $this->patch(route('cabang.toggle-status', $cabang->id))
            ->assertForbidden();

        $this->assertDatabaseHas('cabangs', [
            'id' => $cabang->id,
            'nama' => $cabang->nama,
            'status' => $cabang->status,
        ]);
        $this->assertDatabaseMissing('cabangs', ['nama' => 'Cabang Baru']);
    }

    public function test_customer_service_cannot_create_update_or_toggle_branch(): void
    {
        $cabang = $this->buatCabang();
        $customerService = $this->buatUser('Customer Service');

        $this->actingAs($customerService);

        $this->post(route('cabang.store'), [
            'nama' => 'Cabang CS',
            'alamat' => 'Jl. Baru',
            'no_hp' => '081234567890',
            'status' => 'aktif',
        ])->assertForbidden();

        $this->put(route('cabang.update', $cabang->id), [
            'nama' => 'Nama Diubah CS',
            'alamat' => $cabang->alamat,
            'no_hp' => $cabang->no_hp,
            'status' => $cabang->status,
        ])->assertForbidden();

        $this->patch(route('cabang.toggle-status', $cabang->id))
            ->assertForbidden();

        $this->assertDatabaseHas('cabangs', [
            'id' => $cabang->id,
            'nama' => $cabang->nama,
            'status' => $cabang->status,
        ]);
    }

    /* -----------------------------------------------------------------
     | HELPER DATA
     |------------------------------------------------------------------ */

    private function buatCabang(array $attributes = []): Cabang
    {
        $this->nomorCabang++;

        return Cabang::create(array_merge([
            'nama' => 'Cabang Pengujian ' . $this->nomorCabang,
            'alamat' => 'Jl. Pengujian No. ' . $this->nomorCabang,
            'no_hp' => '08123456' . str_pad((string) $this->nomorCabang, 4, '0', STR_PAD_LEFT),
            'status' => 'aktif',
        ], $attributes));
    }

    private function buatUser(string $role, ?Cabang $cabang = null): User
    {
        $this->nomorUser++;
        $slugRole = strtolower(str_replace(' ', '-', $role));
        $suffix = $this->nomorUser;

        return User::factory()->create([
            'name' => 'User Pengujian ' . $suffix,
            'username' => 'user-' . $slugRole . '-' . $suffix,
            'email' => 'user-' . $slugRole . '-' . $suffix . '@example.test',
            'role' => $role,
            'cabang_id' => $cabang?->id,
        ]);
    }
}
