<?php

namespace Tests\Feature\Manager;

use App\Filament\Manager\Resources\Clients\Pages\ListClients;
use App\Filament\Manager\Resources\Clients\Pages\ViewClient;
use App\Filament\Manager\Resources\Clients\RelationManagers\InvestmentsRelationManager;
use App\Filament\Manager\Resources\Investments\Pages\EditInvestment;
use App\Filament\Manager\Resources\Investments\RelationManagers\EntriesRelationManager;
use App\Models\Investment;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ClientPortfolioTest extends TestCase
{
    use RefreshDatabase;

    private User $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->manager()->create());
        Filament::setCurrentPanel('manager');
        $this->client = User::factory()->client()->create(['name' => 'Awa Koné']);
    }

    private function investment(array $attributes = []): Investment
    {
        return $this->client->investments()->create($attributes + ['label' => 'Flotte DJETRAN', 'program' => 'vehicule', 'amount' => 15000000, 'status' => 'actif']);
    }

    public function test_client_cannot_open_the_manager_clients(): void
    {
        $this->actingAs($this->client)->get('/manager/clients')->assertForbidden();
    }

    public function test_client_list_shows_only_clients(): void
    {
        $manager = User::factory()->manager()->create();

        Livewire::test(ListClients::class)
            ->assertCanSeeTableRecords([$this->client])
            ->assertCanNotSeeTableRecords([$manager]);
    }

    public function test_manager_adds_an_investment_typed_with_spaces(): void
    {
        Livewire::test(InvestmentsRelationManager::class, ['ownerRecord' => $this->client, 'pageClass' => ViewClient::class])
            ->callAction(TestAction::make('create')->table(), [
                'label' => 'Programme LATHAYE',
                'program' => 'vehicule',
                'amount' => '10 000 000',
                'start_date' => '2026-06-01',
                'status' => 'actif',
            ])
            ->assertHasNoFormErrors();

        $investment = $this->client->investments()->firstOrFail();
        $this->assertSame(10000000, $investment->amount);
        $this->assertSame('2026-06-01', $investment->start_date->toDateString());
    }

    public function test_investment_needs_a_known_status_and_a_label(): void
    {
        Livewire::test(InvestmentsRelationManager::class, ['ownerRecord' => $this->client, 'pageClass' => ViewClient::class])
            ->callAction(TestAction::make('create')->table(), ['label' => '', 'amount' => '1000', 'status' => 'inconnu'])
            ->assertHasFormErrors(['label' => 'required', 'status']);
    }

    public function test_manager_records_a_revenue_entry_on_an_investment(): void
    {
        $investment = $this->investment();

        Livewire::test(EntriesRelationManager::class, ['ownerRecord' => $investment, 'pageClass' => EditInvestment::class])
            ->callAction(TestAction::make('create')->table(), ['entry_date' => '2026-10-31', 'type' => 'revenu', 'amount' => '280 000', 'description' => 'Revenu mensuel'])
            ->assertHasNoFormErrors();

        $entry = $investment->entries()->firstOrFail();
        $this->assertSame(280000, $entry->amount);
        $this->assertSame('revenu', $entry->type);
    }

    public function test_client_page_shows_the_total_invested(): void
    {
        $this->investment();
        $this->investment(['label' => 'Programme LATHAYE', 'amount' => 10000000]);

        Livewire::test(ViewClient::class, ['record' => $this->client->getRouteKey()])->assertSee('25 000 000 FCFA');
    }
}
