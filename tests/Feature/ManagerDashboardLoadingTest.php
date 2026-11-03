<?php

namespace Tests\Feature;

use App\Filament\Manager\Widgets\LatestRequests;
use App\Filament\Manager\Widgets\ManagerOverview;
use App\Models\SiraRequest;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class ManagerDashboardLoadingTest extends TestCase
{
    use RefreshDatabase;

    /** Tableau de bord manager : demandes ouvertes comptées en base, six dernières demandes listées. */
    public function test_it_counts_open_requests_and_lists_the_six_latest(): void
    {
        Filament::setCurrentPanel('manager');
        $this->actingAs(User::factory()->manager()->create());
        $ids = [];

        for ($number = 1; $number <= 15; $number++) {
            $request = SiraRequest::create([
                'name' => 'Candidat tableau '.$number,
                'country' => 'Côte d’Ivoire',
                'city' => 'Abidjan',
                'phone' => '0102030405',
                'email' => 'client'.$number.'@example.test',
            ]);
            $request->created_at = now()->subMinutes(15 - $number);
            $request->save();
            $ids[] = (int) $request->id;

            DB::table('inbox_statuses')->insert(['source' => 'sira', 'source_id' => $request->id, 'status' => $number <= 5 ? 'traite' : 'en_cours']);
        }

        Livewire::test(ManagerOverview::class)->assertSeeInOrder(['Demandes ouvertes', '10']);

        $latest = array_map(fn (int $id): string => 'sira-'.$id, array_slice(array_reverse($ids), 0, 6));
        $older = array_map(fn (int $id): string => 'sira-'.$id, array_slice(array_reverse($ids), 6));

        Livewire::test(LatestRequests::class)
            ->assertCanSeeTableRecords($latest, inOrder: true)
            ->assertCanNotSeeTableRecords($older);
    }

    public function test_manager_dashboard_page_shows_the_widgets(): void
    {
        Filament::setCurrentPanel('manager');
        $this->actingAs(User::factory()->manager()->create());

        $this->get('/manager')->assertOk();
        Livewire::test(Dashboard::class)
            ->assertSeeLivewire(ManagerOverview::class)
            ->assertSeeLivewire(LatestRequests::class);
    }
}
