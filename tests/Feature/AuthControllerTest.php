<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_login_page(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertViewIs('auth.login');
    }

    public function test_admin_pusat_can_login_and_is_redirected_to_admin_dashboard(): void
    {
        User::factory()->create([
            'username' => 'adminpusat',
            'password' => 'password123',
            'role' => 'Admin Pusat',
        ]);

        $response = $this->post(route('login.authenticate'), [
            'username' => 'adminpusat',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_admin_cabang_can_login_and_is_redirected_to_admin_dashboard(): void
    {
        User::factory()->create([
            'username' => 'admincabang',
            'password' => 'password123',
            'role' => 'Admin Cabang',
        ]);

        $response = $this->post(route('login.authenticate'), [
            'username' => 'admincabang',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_customer_service_can_login_and_is_redirected_to_cs_dashboard(): void
    {
        User::factory()->create([
            'username' => 'csuser',
            'password' => 'password123',
            'role' => 'Customer Service',
        ]);

        $response = $this->post(route('login.authenticate'), [
            'username' => 'csuser',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('cs.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'username' => 'adminpusat',
            'password' => 'password123',
            'role' => 'Admin Pusat',
        ]);

        $response = $this->post(route('login.authenticate'), [
            'username' => 'adminpusat',
            'password' => 'password-salah',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'username' => 'Username atau password yang dimasukkan salah.',
        ]);

        $this->assertGuest();
    }

    public function test_login_fails_with_unknown_username(): void
    {
        $response = $this->post(route('login.authenticate'), [
            'username' => 'tidakada',
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors([
            'username' => 'Username atau password yang dimasukkan salah.',
        ]);

        $this->assertGuest();
    }

    public function test_login_requires_username(): void
    {
        $response = $this->post(route('login.authenticate'), [
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_login_requires_password(): void
    {
        $response = $this->post(route('login.authenticate'), [
            'username' => 'adminpusat',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_logout_logs_out_user_and_redirects_to_login(): void
    {
        $user = User::factory()->create([
            'username' => 'adminpusat',
            'password' => 'password123',
            'role' => 'Admin Pusat',
        ]);

        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect('/login');
        $response->assertSessionHas(
            'success',
            'Anda berhasil keluar.'
        );

        $this->assertGuest();
    }

    public function test_guest_cannot_access_logout_route(): void
    {
        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}