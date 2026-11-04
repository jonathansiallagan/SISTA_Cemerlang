<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private int $nomorUser = 0;
    private int $nomorCabang = 0;

    protected Cabang $cabangAktif;
    protected Cabang $cabangAktifKedua;
    protected Cabang $cabangNonaktif;

    protected User $adminPusat;
    protected User $adminCabang;
    protected User $customerService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cabangAktif = $this->buatCabang('Cabang Aktif A');
        $this->cabangAktifKedua = $this->buatCabang('Cabang Aktif B');
        $this->cabangNonaktif = $this->buatCabang('Cabang Nonaktif', 'nonaktif');

        $this->adminPusat = $this->buatUser(
            'Admin Pusat',
            $this->cabangAktif
        );

        $this->adminCabang = $this->buatUser(
            'Admin Cabang',
            $this->cabangAktif
        );

        $this->customerService = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $this->actingAs($this->adminPusat);
    }

    /* -----------------------------------------------------------------
     | AKSES HALAMAN USER
     |------------------------------------------------------------------ */

    public function test_guest_is_redirected_to_login_from_user_page(): void
    {
        auth()->logout();

        $this->get(route('user.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_pusat_can_open_user_page(): void
    {
        $response = $this->get(route('user.index'));

        $response->assertOk();
        $response->assertViewIs('admin.user.index');
        $response->assertViewHasAll(['users', 'cabangs']);
    }

    public function test_admin_cabang_cannot_open_user_page(): void
    {
        $this->actingAs($this->adminCabang)
            ->get(route('user.index'))
            ->assertForbidden();
    }

    public function test_customer_service_cannot_open_user_page(): void
    {
        $this->actingAs($this->customerService)
            ->get(route('user.index'))
            ->assertForbidden();
    }

    public function test_index_displays_all_users_sorted_by_name(): void
    {
        $userZ = $this->buatUser(
            'Customer Service',
            $this->cabangAktif,
            ['name' => 'Zahra User Uji']
        );

        $userA = $this->buatUser(
            'Admin Cabang',
            $this->cabangAktifKedua,
            ['name' => 'Aisyah User Uji']
        );

        $response = $this->get(route('user.index'));

        $response->assertOk();

        $response->assertViewHas('users', function ($users) use (
            $userA,
            $userZ
        ): bool {
            $names = $users->pluck('name')->all();

            return in_array($userA->name, $names, true)
                && in_array($userZ->name, $names, true)
                && $names === collect($names)->sort()->values()->all();
        });
    }

    public function test_index_eager_loads_user_branch_relationship(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktifKedua,
            ['name' => 'User Dengan Cabang']
        );

        $response = $this->get(route('user.index'));

        $response->assertViewHas('users', function ($users) use (
            $user
        ): bool {
            $hasil = $users->firstWhere('id', $user->id);

            return $hasil !== null
                && $hasil->relationLoaded('cabang')
                && $hasil->cabang?->id === $this->cabangAktifKedua->id;
        });
    }

    public function test_index_only_displays_active_branches_in_branch_options(): void
    {
        $response = $this->get(route('user.index'));

        $response->assertViewHas('cabangs', function ($cabangs): bool {
            $ids = $cabangs->pluck('id')->all();

            return in_array($this->cabangAktif->id, $ids, true)
                && in_array($this->cabangAktifKedua->id, $ids, true)
                && !in_array($this->cabangNonaktif->id, $ids, true);
        });
    }

    /* -----------------------------------------------------------------
     | STORE USER
     |------------------------------------------------------------------ */

    public function test_admin_pusat_can_create_user(): void
    {
        $response = $this->post(
            route('user.store'),
            $this->dataUserValid()
        );

        $response->assertRedirect(route('user.index'));

        $response->assertSessionHas(
            'success',
            'Pengguna baru berhasil ditambahkan!'
        );

        $user = User::query()
            ->where('email', 'user.baru@example.test')
            ->firstOrFail();

        $this->assertSame('Nama Pengguna Baru', $user->name);
        $this->assertSame('user_baru', $user->username);
        $this->assertSame('Admin Cabang', $user->role);

        $this->assertSame(
            $this->cabangAktif->id,
            $user->cabang_id
        );

        $this->assertTrue(
            Hash::check('password-baru-123', $user->password)
        );
    }

    public function test_store_accepts_all_supported_roles(): void
    {
        $roles = [
            'Admin Pusat',
            'Admin Cabang',
            'Customer Service',
        ];

        foreach ($roles as $index => $role) {
            $payload = $this->dataUserValid([
                'name' => 'Pengguna Role ' . $index,
                'email' => 'role.' . $index . '@example.test',
                'username' => 'role_' . $index,
                'role' => $role,
            ]);

            $this->post(route('user.store'), $payload)
                ->assertRedirect(route('user.index'))
                ->assertSessionHasNoErrors();
        }

        $this->assertSame(
            3,
            User::query()
                ->whereIn('role', $roles)
                ->whereNotIn('id', [
                    $this->adminPusat->id,
                    $this->adminCabang->id,
                    $this->customerService->id,
                ])
                ->count()
        );
    }

    public function test_store_rejects_missing_name(): void
    {
        $payload = $this->dataUserValid();
        unset($payload['name']);

        $this->from(route('user.index'))
            ->post(route('user.store'), $payload)
            ->assertRedirect(route('user.index'))
            ->assertSessionHasErrors('name');
    }

    public function test_store_rejects_non_string_name(): void
    {
        $this->from(route('user.index'))
            ->post(
                route('user.store'),
                $this->dataUserValid([
                    'name' => ['Nama'],
                ])
            )
            ->assertSessionHasErrors('name');
    }

    public function test_store_rejects_name_longer_than_255_characters(): void
    {
        $this->from(route('user.index'))
            ->post(
                route('user.store'),
                $this->dataUserValid([
                    'name' => str_repeat('A', 256),
                ])
            )
            ->assertSessionHasErrors('name');
    }

    public function test_store_rejects_missing_email(): void
    {
        $payload = $this->dataUserValid();
        unset($payload['email']);

        $this->from(route('user.index'))
            ->post(route('user.store'), $payload)
            ->assertSessionHasErrors('email');
    }

    public function test_store_rejects_invalid_email(): void
    {
        $this->from(route('user.index'))
            ->post(
                route('user.store'),
                $this->dataUserValid([
                    'email' => 'bukan-format-email',
                ])
            )
            ->assertSessionHasErrors('email');
    }

    public function test_store_rejects_duplicate_email(): void
    {
        $this->from(route('user.index'))
            ->post(
                route('user.store'),
                $this->dataUserValid([
                    'email' => $this->adminCabang->email,
                ])
            )
            ->assertSessionHasErrors('email');
    }

    public function test_store_rejects_email_longer_than_255_characters(): void
    {
        $emailPanjang = str_repeat('a', 245) . '@example.test';

        $this->from(route('user.index'))
            ->post(
                route('user.store'),
                $this->dataUserValid([
                    'email' => $emailPanjang,
                ])
            )
            ->assertSessionHasErrors('email');
    }

    public function test_store_rejects_missing_username(): void
    {
        $payload = $this->dataUserValid();
        unset($payload['username']);

        $this->from(route('user.index'))
            ->post(route('user.store'), $payload)
            ->assertSessionHasErrors('username');
    }

    public function test_store_rejects_duplicate_username(): void
    {
        $this->from(route('user.index'))
            ->post(
                route('user.store'),
                $this->dataUserValid([
                    'username' => $this->adminCabang->username,
                ])
            )
            ->assertSessionHasErrors('username');
    }

    public function test_store_rejects_username_longer_than_255_characters(): void
    {
        $this->from(route('user.index'))
            ->post(
                route('user.store'),
                $this->dataUserValid([
                    'username' => str_repeat('u', 256),
                ])
            )
            ->assertSessionHasErrors('username');
    }

    public function test_store_rejects_missing_password(): void
    {
        $payload = $this->dataUserValid();
        unset($payload['password']);

        $this->from(route('user.index'))
            ->post(route('user.store'), $payload)
            ->assertSessionHasErrors('password');
    }

    public function test_store_rejects_password_shorter_than_eight_characters(): void
    {
        $this->from(route('user.index'))
            ->post(
                route('user.store'),
                $this->dataUserValid([
                    'password' => '1234567',
                ])
            )
            ->assertSessionHasErrors('password');
    }

    public function test_store_rejects_missing_role(): void
    {
        $payload = $this->dataUserValid();
        unset($payload['role']);

        $this->from(route('user.index'))
            ->post(route('user.store'), $payload)
            ->assertSessionHasErrors('role');
    }

    public function test_store_rejects_invalid_role(): void
    {
        $this->from(route('user.index'))
            ->post(
                route('user.store'),
                $this->dataUserValid([
                    'role' => 'Supervisor',
                ])
            )
            ->assertSessionHasErrors('role');
    }

    public function test_store_rejects_missing_branch(): void
    {
        $payload = $this->dataUserValid();
        unset($payload['cabang_id']);

        $this->from(route('user.index'))
            ->post(route('user.store'), $payload)
            ->assertSessionHasErrors('cabang_id');
    }

    public function test_store_rejects_non_integer_branch_id(): void
    {
        $this->from(route('user.index'))
            ->post(
                route('user.store'),
                $this->dataUserValid([
                    'cabang_id' => 'bukan-angka',
                ])
            )
            ->assertSessionHasErrors('cabang_id');
    }

    public function test_store_rejects_nonexistent_branch(): void
    {
        $this->from(route('user.index'))
            ->post(
                route('user.store'),
                $this->dataUserValid([
                    'cabang_id' => 999999,
                ])
            )
            ->assertSessionHasErrors('cabang_id');
    }

    public function test_store_rejects_inactive_branch(): void
    {
        $this->from(route('user.index'))
            ->post(
                route('user.store'),
                $this->dataUserValid([
                    'cabang_id' => $this->cabangNonaktif->id,
                ])
            )
            ->assertSessionHasErrors('cabang_id');
    }

    public function test_non_admin_pusat_cannot_store_user(): void
    {
        $payload = $this->dataUserValid();

        $this->actingAs($this->adminCabang)
            ->post(route('user.store'), $payload)
            ->assertForbidden();

        $this->assertDatabaseMissing('users', [
            'email' => 'user.baru@example.test',
        ]);
    }

    /* -----------------------------------------------------------------
     | UPDATE USER
     |------------------------------------------------------------------ */

    public function test_admin_pusat_can_update_user_data(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $response = $this->put(route('user.update', $user->id), [
            'name' => 'Nama Pengguna Diperbarui',
            'email' => 'user.update@example.test',
            'username' => 'username_update',
            'password' => '',
            'role' => 'Admin Cabang',
            'cabang_id' => $this->cabangAktifKedua->id,
        ]);

        $response->assertRedirect(route('user.index'));

        $response->assertSessionHas(
            'success',
            'Data pengguna berhasil diperbarui!'
        );

        $user->refresh();

        $this->assertSame('Nama Pengguna Diperbarui', $user->name);
        $this->assertSame('user.update@example.test', $user->email);
        $this->assertSame('username_update', $user->username);
        $this->assertSame('Admin Cabang', $user->role);

        $this->assertSame(
            $this->cabangAktifKedua->id,
            $user->cabang_id
        );

        $this->assertTrue(
            Hash::check('password', $user->password)
        );
    }

    public function test_update_allows_user_to_keep_own_email_and_username(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $this->put(route('user.update', $user->id), [
            'name' => 'Nama Tetap',
            'email' => $user->email,
            'username' => $user->username,
            'password' => '',
            'role' => 'Customer Service',
            'cabang_id' => $this->cabangAktif->id,
        ])
            ->assertRedirect(route('user.index'))
            ->assertSessionHasNoErrors();

        $this->assertSame($user->email, $user->fresh()->email);
        $this->assertSame($user->username, $user->fresh()->username);
    }

    public function test_update_can_change_password_when_password_is_provided(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $this->put(route('user.update', $user->id), [
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'password' => 'password-baru-123',
            'role' => $user->role,
            'cabang_id' => $user->cabang_id,
        ])->assertRedirect(route('user.index'));

        $this->assertTrue(
            Hash::check('password-baru-123', $user->fresh()->password)
        );

        $this->assertFalse(
            Hash::check('password', $user->fresh()->password)
        );
    }

    public function test_update_rejects_duplicate_email_of_another_user(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $this->from(route('user.index'))
            ->put(
                route('user.update', $user->id),
                $this->dataUpdateUser($user, [
                    'email' => $this->adminCabang->email,
                ])
            )
            ->assertSessionHasErrors('email');
    }

    public function test_update_rejects_duplicate_username_of_another_user(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $this->from(route('user.index'))
            ->put(
                route('user.update', $user->id),
                $this->dataUpdateUser($user, [
                    'username' => $this->adminCabang->username,
                ])
            )
            ->assertSessionHasErrors('username');
    }

    public function test_update_rejects_missing_required_fields(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $payload = $this->dataUpdateUser($user);

        unset(
            $payload['name'],
            $payload['email'],
            $payload['username'],
            $payload['role'],
            $payload['cabang_id']
        );

        $this->from(route('user.index'))
            ->put(route('user.update', $user->id), $payload)
            ->assertSessionHasErrors([
                'name',
                'email',
                'username',
                'role',
                'cabang_id',
            ]);
    }

    public function test_update_rejects_invalid_role(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $this->from(route('user.index'))
            ->put(
                route('user.update', $user->id),
                $this->dataUpdateUser($user, [
                    'role' => 'Pemilik',
                ])
            )
            ->assertSessionHasErrors('role');
    }

    public function test_update_rejects_nonexistent_branch(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $this->from(route('user.index'))
            ->put(
                route('user.update', $user->id),
                $this->dataUpdateUser($user, [
                    'cabang_id' => 999999,
                ])
            )
            ->assertSessionHasErrors('cabang_id');
    }

    public function test_update_rejects_inactive_branch(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $this->from(route('user.index'))
            ->put(
                route('user.update', $user->id),
                $this->dataUpdateUser($user, [
                    'cabang_id' => $this->cabangNonaktif->id,
                ])
            )
            ->assertSessionHasErrors('cabang_id');
    }

    public function test_update_rejects_password_shorter_than_eight_characters(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $this->from(route('user.index'))
            ->put(
                route('user.update', $user->id),
                $this->dataUpdateUser($user, [
                    'password' => '1234567',
                ])
            )
            ->assertSessionHasErrors('password');
    }

    public function test_update_returns_404_when_user_does_not_exist(): void
    {
        $this->put(route('user.update', 999999), [
            'name' => 'User Tidak Ada',
            'email' => 'tidak.ada@example.test',
            'username' => 'tidak_ada',
            'password' => '',
            'role' => 'Customer Service',
            'cabang_id' => $this->cabangAktif->id,
        ])->assertNotFound();
    }

    public function test_non_admin_pusat_cannot_update_user(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $this->actingAs($this->adminCabang)
            ->put(
                route('user.update', $user->id),
                $this->dataUpdateUser($user, [
                    'name' => 'Tidak Boleh Diubah',
                ])
            )
            ->assertForbidden();

        $this->assertNotSame(
            'Tidak Boleh Diubah',
            $user->fresh()->name
        );
    }

    /* -----------------------------------------------------------------
     | DELETE USER
     |------------------------------------------------------------------ */

    public function test_admin_pusat_can_delete_user(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $response = $this->delete(
            route('user.destroy', $user->id)
        );

        $response->assertRedirect(route('user.index'));

        $response->assertSessionHas(
            'success',
            'Data pengguna berhasil dihapus!'
        );

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    public function test_destroy_returns_404_when_user_does_not_exist(): void
    {
        $this->delete(route('user.destroy', 999999))
            ->assertNotFound();
    }

    public function test_non_admin_pusat_cannot_delete_user(): void
    {
        $user = $this->buatUser(
            'Customer Service',
            $this->cabangAktif
        );

        $this->actingAs($this->customerService)
            ->delete(route('user.destroy', $user->id))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    /* -----------------------------------------------------------------
     | HELPER DATA TEST
     |------------------------------------------------------------------ */

    private function buatCabang(
        string $nama,
        string $status = 'aktif'
    ): Cabang {
        $nomor = ++$this->nomorCabang;

        return Cabang::create([
            'nama' => $nama,
            'alamat' => 'Alamat ' . $nama,
            'no_hp' => '08123456' . str_pad(
                (string) $nomor,
                4,
                '0',
                STR_PAD_LEFT
            ),
            'status' => $status,
        ]);
    }

    private function buatUser(
        string $role = 'Customer Service',
        ?Cabang $cabang = null,
        array $attributes = []
    ): User {
        $nomor = ++$this->nomorUser;

        return User::create(array_merge([
            'name' => 'User Uji ' . $nomor,
            'email' => 'user.uji.' . $nomor . '@example.test',
            'username' => 'user_uji_' . $nomor,
            'password' => 'password',
            'role' => $role,
            'cabang_id' => $cabang?->id,
        ], $attributes));
    }

    private function dataUserValid(array $attributes = []): array
    {
        return array_merge([
            'name' => 'Nama Pengguna Baru',
            'email' => 'user.baru@example.test',
            'username' => 'user_baru',
            'password' => 'password-baru-123',
            'role' => 'Admin Cabang',
            'cabang_id' => $this->cabangAktif->id,
        ], $attributes);
    }

    private function dataUpdateUser(
        User $user,
        array $attributes = []
    ): array {
        return array_merge([
            'name' => $user->name,
            'email' => $user->email,
            'username' => $user->username,
            'password' => '',
            'role' => $user->role,
            'cabang_id' => $user->cabang_id,
        ], $attributes);
    }
}