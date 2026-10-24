<?php

namespace Tests\Feature;

use App\Models\SiraRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ManagerDashboardLoadingTest extends TestCase
{
    use RefreshDatabase;

    /** Tableau de bord sans chargement complet. */
    public function test_it_loads_only_the_six_latest_requests(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $ids = [];
        $now = now();

        for ($number = 1; $number <= 15; $number++) {
            $request = SiraRequest::create([
                'name' => 'Candidat tableau '.$number,
                'country' => 'Côte d’Ivoire',
                'city' => 'Abidjan',
                'phone' => '0102030405',
                'email' => 'client'.$number.'@example.test',
            ]);

            $request->created_at = $now->copy()->subMinutes(15 - $number);
            $request->save();

            $ids[] = (int) $request->id;

            DB::table('inbox_statuses')->insert([
                'source' => 'sira',
                'source_id' => $request->id,
                'status' => $number <= 5 ? 'traite' : 'en_cours',
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        try {
            $response = $this->actingAs($manager)
                ->get(route('manager.dashboard'));

            $queries = DB::getQueryLog();
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }

        $response->assertOk();
        $response->assertViewHas('open', 10);

        $expectedIds = array_slice(array_reverse($ids), 0, 6);

        $response->assertViewHas('latest', function ($latest) use ($expectedIds) {
            return $latest->count() === 6
                && $latest->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->values()
                    ->all() === $expectedIds;
        });

        // Chargement du contenu limité aux six identifiants.
        $details = collect($queries)->filter(function ($entry) {
            $sql = strtolower($entry['query']);

            return str_contains($sql, 'sira_requests')
                && preg_match('/\bselect\s+\*/i', $sql)
                && !str_contains($sql, 'count(');
        })->values();

        $this->assertCount(1, $details);

        $this->assertMatchesRegularExpression(
            '/\bwhere\b.*\bin\s*\(/is',
            $details[0]['query'],
            'Le tableau de bord charge toutes les demandes.'
        );

        $selectedIds = array_map('intval', $details[0]['bindings']);

        $this->assertEqualsCanonicalizing(
            $expectedIds,
            $selectedIds
        );
    }
}