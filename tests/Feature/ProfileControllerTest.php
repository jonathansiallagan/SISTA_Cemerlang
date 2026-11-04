<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    private int $nomorUser = 0;

    protected User $adminPusat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminPusat = $this->buatUser('Admin Pusat');
        $this->actingAs($this->adminPusat);
    }

    /* -----------------------------------------------------------------
     | AKSES HALAMAN PROFIL
     |------------------------------------------------------------------ */

    public function test_guest_is_redirected_to_login_from_profile_page(): void
    {
        auth()->logout();

        $this->get(route('profile'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_pusat_can_open_profile_page(): void
    {
        $response = $this->get(route('profile'));

        $response->assertOk();
        $response->assertViewIs('profil.index');
    }

    public function test_admin_cabang_can_open_profile_page(): void
    {
        $adminCabang = $this->buatUser('Admin Cabang');

        $this->actingAs($adminCabang)
            ->get(route('profile'))
            ->assertOk()
            ->assertViewIs('profil.index');
    }

    public function test_customer_service_can_open_profile_page(): void
    {
        $customerService = $this->buatUser('Customer Service');

        $this->actingAs($customerService)
            ->get(route('profile'))
            ->assertOk()
            ->assertViewIs('profil.index');
    }

    /* -----------------------------------------------------------------
     | UPDATE PROFILE: NAMA DAN EMAIL
     |------------------------------------------------------------------ */

    public function test_user_can_update_basic_profile(): void
    {
        $response = $this->from(route('profile'))->put(route('profile.update'), [
            'name' => 'Nama Profil Baru',
            'email' => 'profil.baru@example.test',
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success', 'Profil dasar berhasil diperbarui!');

        $this->adminPusat->refresh();

        $this->assertSame('Nama Profil Baru', $this->adminPusat->name);
        $this->assertSame('profil.baru@example.test', $this->adminPusat->email);
    }

    public function test_update_profile_allows_user_to_keep_own_email(): void
    {
        $emailAwal = $this->adminPusat->email;

        $response = $this->from(route('profile'))->put(route('profile.update'), [
            'name' => 'Nama Tetap',
            'email' => $emailAwal,
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHasNoErrors();

        $this->assertSame($emailAwal, $this->adminPusat->fresh()->email);
    }

    public function test_update_profile_rejects_missing_name(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.update'), [
                'email' => 'nama.kosong@example.test',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('name');
    }

    public function test_update_profile_rejects_non_string_name(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.update'), [
                'name' => ['Nama'],
                'email' => 'nama.array@example.test',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('name');
    }

    public function test_update_profile_rejects_name_longer_than_255_characters(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.update'), [
                'name' => str_repeat('A', 256),
                'email' => 'nama.panjang@example.test',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('name');
    }

    public function test_update_profile_rejects_missing_email(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.update'), [
                'name' => 'Nama Tanpa Email',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('email');
    }

    public function test_update_profile_rejects_invalid_email(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.update'), [
                'name' => 'Nama Email Salah',
                'email' => 'bukan-format-email',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('email');
    }

    public function test_update_profile_rejects_duplicate_email_of_another_user(): void
    {
        $userLain = $this->buatUser('Customer Service');

        $this->from(route('profile'))
            ->put(route('profile.update'), [
                'name' => 'Nama Duplikat',
                'email' => $userLain->email,
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('email');

        $this->assertSame('Admin Pusat Uji 1', $this->adminPusat->fresh()->name);
    }

    public function test_update_profile_rejects_email_longer_than_255_characters(): void
    {
        $emailPanjang = str_repeat('a', 245) . '@example.test';

        $this->from(route('profile'))
            ->put(route('profile.update'), [
                'name' => 'Nama Email Panjang',
                'email' => $emailPanjang,
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('email');
    }

    /* -----------------------------------------------------------------
     | UPDATE AVATAR
     |------------------------------------------------------------------ */

    public function test_user_can_upload_avatar_and_receives_json_response(): void
    {
        Storage::fake('public');

        $response = $this->post(route('profile.avatar'), [
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['success', 'avatar_url']);
        $response->assertJson(['success' => true]);

        $this->adminPusat->refresh();

        $this->assertNotNull($this->adminPusat->avatar);

        Storage::disk('public')->assertExists($this->adminPusat->avatar);

        $this->assertStringContainsString(
            'storage/' . $this->adminPusat->avatar,
            $response->json('avatar_url')
        );
    }

    public function test_uploading_new_avatar_deletes_existing_avatar_file(): void
    {
        Storage::fake('public');

        $avatarLama = 'avatars/avatar-lama.jpg';

        Storage::disk('public')->put($avatarLama, 'file-avatar-lama');

        $this->adminPusat->update([
            'avatar' => $avatarLama,
        ]);

        $response = $this->post(route('profile.avatar'), [
            'avatar' => UploadedFile::fake()->image('avatar-baru.png'),
        ]);

        $response->assertOk();

        $this->adminPusat->refresh();

        $this->assertNotSame($avatarLama, $this->adminPusat->avatar);

        Storage::disk('public')->assertMissing($avatarLama);
        Storage::disk('public')->assertExists($this->adminPusat->avatar);
    }

    public function test_upload_avatar_rejects_missing_file(): void
    {
        $this->from(route('profile'))
            ->post(route('profile.avatar'), [])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('avatar');
    }

    public function test_upload_avatar_rejects_non_image_file(): void
    {
        Storage::fake('public');

        $this->from(route('profile'))
            ->post(route('profile.avatar'), [
                'avatar' => UploadedFile::fake()->create(
                    'dokumen.txt',
                    10,
                    'text/plain'
                ),
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('avatar');
    }

    public function test_upload_avatar_rejects_unsupported_image_extension(): void
    {
        Storage::fake('public');

        $this->from(route('profile'))
            ->post(route('profile.avatar'), [
                'avatar' => UploadedFile::fake()->createWithContent(
                    'avatar.gif',
                    base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==')
                ),
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('avatar');
    }

    public function test_upload_avatar_rejects_file_larger_than_two_megabytes(): void
    {
        Storage::fake('public');

        $this->from(route('profile'))
            ->post(route('profile.avatar'), [
                'avatar' => UploadedFile::fake()->image('avatar-besar.jpg')->size(2049),
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('avatar');
    }

    /* -----------------------------------------------------------------
     | UPDATE PASSWORD
     |------------------------------------------------------------------ */

    public function test_user_can_update_password_with_correct_current_password(): void
    {
        $response = $this->from(route('profile'))->put(route('profile.password'), [
            'current_password' => 'password',
            'new_password' => 'password-baru-123',
            'new_password_confirmation' => 'password-baru-123',
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success', 'Password berhasil diubah demi keamanan!');

        $this->assertTrue(
            Hash::check('password-baru-123', $this->adminPusat->fresh()->password)
        );

        $this->assertFalse(
            Hash::check('password', $this->adminPusat->fresh()->password)
        );
    }

    public function test_update_password_rejects_missing_current_password(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.password'), [
                'new_password' => 'password-baru-123',
                'new_password_confirmation' => 'password-baru-123',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('current_password');
    }

    public function test_update_password_rejects_incorrect_current_password(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.password'), [
                'current_password' => 'password-salah',
                'new_password' => 'password-baru-123',
                'new_password_confirmation' => 'password-baru-123',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(
            Hash::check('password', $this->adminPusat->fresh()->password)
        );
    }

    public function test_update_password_rejects_new_password_shorter_than_eight_characters(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.password'), [
                'current_password' => 'password',
                'new_password' => '1234567',
                'new_password_confirmation' => '1234567',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('new_password');
    }

    public function test_update_password_rejects_unconfirmed_new_password(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.password'), [
                'current_password' => 'password',
                'new_password' => 'password-baru-123',
                'new_password_confirmation' => 'password-berbeda',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('new_password');
    }

    public function test_update_password_rejects_missing_new_password(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.password'), [
                'current_password' => 'password',
                'new_password_confirmation' => 'password-baru-123',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('new_password');
    }

    /* -----------------------------------------------------------------
     | UPDATE PREFERENCES / TEMA
     |------------------------------------------------------------------ */

    public function test_user_can_update_theme_preference_to_light(): void
    {
        $response = $this->from(route('profile'))->put(route('profile.preferences'), [
            'theme' => 'light',
        ]);

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('success', 'Preferensi tampilan berhasil disimpan!');

        $this->assertSame(
            'light',
            $this->adminPusat->fresh()->preferences['theme']
        );
    }

    public function test_user_can_update_theme_preference_to_dark(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.preferences'), [
                'theme' => 'dark',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasNoErrors();

        $this->assertSame(
            'dark',
            $this->adminPusat->fresh()->preferences['theme']
        );
    }

    public function test_user_can_update_theme_preference_to_system(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.preferences'), [
                'theme' => 'system',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasNoErrors();

        $this->assertSame(
            'system',
            $this->adminPusat->fresh()->preferences['theme']
        );
    }

    public function test_update_preferences_preserves_other_existing_preferences(): void
    {
        $this->adminPusat->update([
            'preferences' => [
                'theme' => 'light',
                'sidebar' => 'mini',
                'language' => 'id',
            ],
        ]);

        $this->from(route('profile'))
            ->put(route('profile.preferences'), [
                'theme' => 'dark',
            ])
            ->assertRedirect(route('profile'));

        $preferences = $this->adminPusat->fresh()->preferences;

        $this->assertSame('dark', $preferences['theme']);
        $this->assertSame('mini', $preferences['sidebar']);
        $this->assertSame('id', $preferences['language']);
    }

    public function test_update_preferences_rejects_missing_theme(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.preferences'), [])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('theme');
    }

    public function test_update_preferences_rejects_invalid_theme(): void
    {
        $this->from(route('profile'))
            ->put(route('profile.preferences'), [
                'theme' => 'blue',
            ])
            ->assertRedirect(route('profile'))
            ->assertSessionHasErrors('theme');
    }

    public function test_guest_cannot_update_profile_password_preferences_or_avatar(): void
    {
        auth()->logout();

        $this->put(route('profile.update'), [
            'name' => 'Nama Baru',
            'email' => 'baru@example.test',
        ])->assertRedirect(route('login'));

        $this->put(route('profile.password'), [
            'current_password' => 'password',
            'new_password' => 'password-baru-123',
            'new_password_confirmation' => 'password-baru-123',
        ])->assertRedirect(route('login'));

        $this->put(route('profile.preferences'), [
            'theme' => 'dark',
        ])->assertRedirect(route('login'));

        $this->post(route('profile.avatar'), [
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ])->assertRedirect(route('login'));
    }

    /* -----------------------------------------------------------------
     | HELPER DATA TEST
     |------------------------------------------------------------------ */

    private function buatUser(string $role = 'Admin Pusat', array $attributes = []): User
    {
        $nomor = ++$this->nomorUser;

        return User::create(array_merge([
            'name' => $role . ' Uji ' . $nomor,
            'username' => strtolower(str_replace(' ', '_', $role)) . '_profile_uji_' . $nomor,
            'email' => 'user.profile.' . $nomor . '@example.test',
            'password' => 'password',
            'role' => $role,
            'cabang_id' => null,
        ], $attributes));
    }
}