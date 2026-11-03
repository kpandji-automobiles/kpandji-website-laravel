<?php

namespace Tests\Feature\Backoffice;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BackofficeAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_the_backoffice_login(): void
    {
        $this->get('/backoffice')->assertRedirect('/backoffice/login');
    }

    public function test_login_page_is_available(): void
    {
        $this->get('/backoffice/login')->assertOk()->assertSee('KPANDJI');
    }

    /**
     * @return array<string, array{string}>
     */
    public static function nonAdminRoles(): array
    {
        return ['client' => ['client'], 'manager' => ['manager'], 'simple compte' => ['user']];
    }

    #[DataProvider('nonAdminRoles')]
    public function test_non_admin_users_are_refused(string $role): void
    {
        $user = User::factory()->create(['role' => $role, 'account_status' => 'active']);

        $this->actingAs($user)->get('/backoffice')->assertForbidden();
    }

    public function test_admin_reaches_the_dashboard(): void
    {
        $this->actingAs(User::factory()->admin()->create())->get('/backoffice')->assertOk();
    }

    public function test_legacy_is_admin_flag_still_grants_access(): void
    {
        $user = User::factory()->create(['role' => 'user', 'is_admin' => true]);

        $this->actingAs($user)->get('/backoffice')->assertOk();
    }
}
