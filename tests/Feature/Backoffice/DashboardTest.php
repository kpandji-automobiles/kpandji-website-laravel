<?php

namespace Tests\Feature\Backoffice;

use App\Filament\Widgets\DemandesOverview;
use App\Filament\Widgets\DernieresDemandes;
use App\Models\Car;
use App\Models\SavRequest;
use App\Models\TestDriveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_dashboard_shows_the_widgets(): void
    {
        $this->get('/backoffice')->assertOk()->assertSeeLivewire(DemandesOverview::class)->assertSeeLivewire(DernieresDemandes::class);
    }

    public function test_key_figures_count_only_what_is_left_to_handle(): void
    {
        TestDriveRequest::create(['type' => 'essai', 'phone' => '0700000000', 'name' => 'A']);
        TestDriveRequest::create(['type' => 'contact', 'phone' => '0700000000', 'name' => 'B']);
        TestDriveRequest::create(['type' => 'essai', 'phone' => '0700000000', 'name' => 'C', 'handled_at' => now()]);
        SavRequest::create(['name' => 'D', 'contact' => '07', 'model' => 'DJETRAN', 'issue' => 'Panne', 'location' => 'Abidjan']);
        Car::create(['name' => 'LATHAYE', 'slug' => 'lathaye', 'image' => 'x.jpg', 'is_active' => true]);
        Car::create(['name' => 'SOURALAI', 'slug' => 'souralai', 'image' => 'x.jpg', 'is_active' => false]);

        Livewire::test(DemandesOverview::class)
            ->assertSeeInOrder(['Essais et contacts à traiter', '2', '1 demandes d’essai'])
            ->assertSeeInOrder(['S.A.V. à traiter', '1'])
            ->assertSeeInOrder(['Modèles en ligne', '1 / 2']);
    }

    public function test_latest_requests_widget_lists_the_most_recent_first(): void
    {
        $old = TestDriveRequest::create(['type' => 'essai', 'phone' => '0700000000', 'name' => 'Ancienne', 'created_at' => now()->subWeek()]);
        $new = TestDriveRequest::create(['type' => 'essai', 'phone' => '0700000000', 'name' => 'Récente']);

        Livewire::test(DernieresDemandes::class)->assertCanSeeTableRecords([$new, $old], inOrder: true);
    }
}
