<?php

namespace Tests\Unit;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Concrete test harness to expose the protected user() helper.
 * The production Controller remains abstract and unchanged.
 */
class ControllerTestHarness extends Controller
{
    public function authenticatedUser(): User
    {
        return $this->user();
    }
}

class ControllerTest extends TestCase
{
    public function test_user_helper_returns_the_authenticated_user_instance(): void
    {
        $user = new User();
        $user->setAttribute('id', 101);
        $user->name = 'Admin Pusat Testing';
        $user->email = 'admin.pusat.testing@example.test';
        $user->username = 'admin-pusat-testing';
        $user->role = 'Admin Pusat';

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        $controller = new ControllerTestHarness();
        $result = $controller->authenticatedUser();

        $this->assertInstanceOf(User::class, $result);
        $this->assertSame($user, $result);
        $this->assertSame(101, $result->id);
    }

    public function test_user_helper_preserves_admin_cabang_role_and_branch_assignment(): void
    {
        $user = new User();
        $user->setAttribute('id', 202);
        $user->name = 'Admin Cabang Testing';
        $user->email = 'admin.cabang.testing@example.test';
        $user->username = 'admin-cabang-testing';
        $user->role = 'Admin Cabang';
        $user->cabang_id = 2;

        Auth::shouldReceive('user')
            ->once()
            ->andReturn($user);

        $controller = new ControllerTestHarness();
        $result = $controller->authenticatedUser();

        $this->assertSame($user, $result);
        $this->assertSame('Admin Cabang', $result->role);
        $this->assertSame(2, $result->cabang_id);
    }
}
