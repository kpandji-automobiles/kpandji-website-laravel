<?php

namespace Tests\Feature\Manager;

use App\Filament\Manager\Resources\Users\Pages\ManageUsers;
use App\Models\ClientNotice;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AccountValidationTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsManager(): User
    {
        $manager = User::factory()->manager()->create();
        $this->actingAs($manager);
        Filament::setCurrentPanel('manager');

        return $manager;
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function panelAccess(): array
    {
        return [
            'client' => ['client', 403],
            'manager en attente' => ['pending-manager', 403],
            'manager actif' => ['manager', 200],
            'admin' => ['admin', 200],
        ];
    }

    #[DataProvider('panelAccess')]
    public function test_only_active_managers_and_admins_enter_the_manager_space(string $who, int $status): void
    {
        $user = match ($who) {
            'pending-manager' => User::factory()->create(['role' => 'manager', 'account_status' => 'pending']),
            default => User::factory()->{$who}()->create(),
        };

        $this->actingAs($user)->get('/manager')->assertStatus($status);
    }

    public function test_manager_cannot_open_the_admin_backoffice(): void
    {
        $this->actingAs(User::factory()->manager()->create())->get('/backoffice')->assertForbidden();
    }

    public function test_pending_tab_lists_only_pending_client_and_manager_accounts(): void
    {
        $this->actingAsManager();
        $pending = User::factory()->create(['role' => 'client', 'account_status' => 'pending']);
        $active = User::factory()->client()->create();
        $admin = User::factory()->admin()->create(['account_status' => 'pending']);

        Livewire::test(ManageUsers::class)
            ->assertCanSeeTableRecords([$pending])
            ->assertCanNotSeeTableRecords([$active, $admin]);
    }

    public function test_approving_a_client_activates_the_account_and_leaves_a_notice(): void
    {
        $this->actingAsManager();
        $client = User::factory()->create(['role' => 'client', 'account_status' => 'pending']);

        Livewire::test(ManageUsers::class)
            ->callAction(TestAction::make('approve')->table($client))
            ->assertNotified('Compte validé.');

        $this->assertSame('active', $client->refresh()->account_status);
        $this->assertSame(1, ClientNotice::where('user_id', $client->id)->count());
    }

    public function test_refusing_a_manager_account(): void
    {
        $this->actingAsManager();
        $candidate = User::factory()->create(['role' => 'manager', 'account_status' => 'pending']);

        Livewire::test(ManageUsers::class)->callAction(TestAction::make('refuse')->table($candidate));

        $this->assertSame('refused', $candidate->refresh()->account_status);
    }

    public function test_active_account_has_no_validation_actions(): void
    {
        $this->actingAsManager();
        $client = User::factory()->client()->create();

        Livewire::test(ManageUsers::class, ['activeTab' => 'active'])
            ->assertActionHidden(TestAction::make('approve')->table($client))
            ->assertActionHidden(TestAction::make('refuse')->table($client));
    }
}
