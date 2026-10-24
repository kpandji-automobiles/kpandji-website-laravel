<?php

namespace Tests\Feature;

use App\Models\SiraRequest;
use App\Support\Inbox;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ManagerInboxPaginationTest extends TestCase
{
    use RefreshDatabase;

    /** Pagination avant le chargement des demandes. */
    public function test_it_loads_only_the_requested_page(): void
    {
        $ids = [];
        $now = now();

        for ($number = 1; $number <= 25; $number++) {
            $request = SiraRequest::create([
                'name' => 'Client ' . $number,
                'country' => 'Côte d’Ivoire',
                'city' => 'Abidjan',
                'phone' => '0102030405',
                'email' => 'client' . $number . '@example.test',
                'created_at' => $now->copy()->subMinutes(25 - $number),
            ]);

            // Affectation explicite du champ interne.
            $request->created_at = $now->copy()->subMinutes(25 - $number);
            $request->save();

            $ids[] = (int) $request->id;
        }

        $loadedRows = 0;

        // Observe les lignes renvoyées par les requêtes Laravel.
        DB::connection()->beforeExecuting(
            function ($query) use (&$loadedRows) {
                // Le contrôle du contenu sera effectué ci-dessous.
            }
        );

        DB::flushQueryLog();
        DB::enableQueryLog();

        try {
            $page = Inbox::paginate(['sira'], [], 10, 2);
            $queries = DB::getQueryLog();
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }

        $this->assertSame(25, $page->total());
        $this->assertSame(2, $page->currentPage());
        $this->assertSame(3, $page->lastPage());
        $this->assertCount(10, $page->items());

        $this->assertSame(
            array_slice(array_reverse($ids), 10, 10),
            collect($page->items())
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all()
        );
        // Requête qui charge le contenu des demandes.
        $detailQueries = collect($queries)->filter(function ($entry) {
            $sql = strtolower($entry['query']);

            return str_contains($sql, 'sira_requests')
                && !str_contains($sql, 'count(');
        })->values();

        $this->assertCount(1, $detailQueries);

        $detailQuery = $detailQueries->first();

        $this->assertMatchesRegularExpression(
            '/\bwhere\b.*\bin\s*\(/is',
            $detailQuery['query'],
            'Le contenu doit être limité aux identifiants de la page.'
        );

        $loadedIds = array_map('intval', $detailQuery['bindings']);
        $expectedIds = array_slice(array_reverse($ids), 10, 10);

        sort($loadedIds);
        sort($expectedIds);

        $this->assertSame(
            $expectedIds,
            $loadedIds,
            'La requête de contenu sélectionne des demandes hors de la page.'
        );
    }

    /** Filtrage du statut avant pagination. */
    public function test_it_filters_by_status_before_paginating(): void
    {
        $expectedIds = [];
        $now = now();

        for ($number = 1; $number <= 15; $number++) {
            $request = SiraRequest::create([
                'name' => 'Client ' . $number,
                'country' => 'Côte d’Ivoire',
                'city' => 'Abidjan',
                'phone' => '0102030405',
                'email' => 'client' . $number . '@example.test',
            ]);

            $request->created_at = $now->copy()->subMinutes(15 - $number);
            $request->save();

            // Les douze premières demandes sont en cours.
            $status = $number <= 12 ? 'en_cours' : 'traite';

            DB::table('inbox_statuses')->insert([
                'source' => 'sira',
                'source_id' => $request->id,
                'status' => $status,
            ]);

            if ($status === 'en_cours') {
                $expectedIds[] = (int) $request->id;
            }
        }

        $page = Inbox::paginate(
            ['sira'],
            ['status' => 'en_cours'],
            10,
            2
        );

        $this->assertSame(12, $page->total());
        $this->assertCount(2, $page->items());

        $this->assertSame(
            array_slice(array_reverse($expectedIds), 10, 10),
            collect($page->items())
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all()
        );

        $this->assertSame(
            ['en_cours'],
            collect($page->items())->pluck('status')->unique()->values()->all()
        );
    }
    /** Filtrage selon la présence de réponses. */
    public function test_it_filters_requests_with_and_without_replies(): void
    {
        $ids = [];

        for ($number = 1; $number <= 3; $number++) {
            $request = SiraRequest::create([
                'name' => 'Client ' . $number,
                'country' => 'Côte d’Ivoire',
                'city' => 'Abidjan',
                'phone' => '0102030405',
                'email' => 'client' . $number . '@example.test',
            ]);

            $ids[] = (int) $request->id;
        }

        // Deux réponses dans une seule demande.
        foreach (['Première réponse', 'Deuxième réponse'] as $body) {
            DB::table('inbox_replies')->insert([
                'source' => 'sira',
                'source_id' => $ids[0],
                'body' => $body,
            ]);
        }

        // Même identifiant numérique, mais autre source.
        DB::table('inbox_replies')->insert([
            'source' => 'privilege',
            'source_id' => $ids[1],
            'body' => 'Réponse d’une autre source',
        ]);

        $withReplies = Inbox::paginate(
            ['sira'],
            ['replied' => '1'],
            10,
            1
        );

        $withoutReplies = Inbox::paginate(
            ['sira'],
            ['replied' => '0'],
            10,
            1
        );

        $this->assertSame(1, $withReplies->total());

        $this->assertSame(
            [$ids[0]],
            collect($withReplies->items())
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all()
        );

        $this->assertSame(2, $withReplies->items()[0]['replies']);
        $this->assertSame(2, $withoutReplies->total());

        $this->assertEqualsCanonicalizing(
            [$ids[1], $ids[2]],
            collect($withoutReplies->items())
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all()
        );
    }


    /** Bornes de dates inclusives. */
    public function test_it_filters_by_date_before_paginating(): void
    {
        $ids = [];

        foreach ([
            '2026-10-01 23:59:59',
            '2026-10-02 00:00:00',
            '2026-10-03 23:59:59',
            '2026-10-04 00:00:00',
        ] as $date) {
            $request = SiraRequest::create([
                'name' => 'Client de test',
                'country' => 'Côte d’Ivoire',
                'city' => 'Abidjan',
                'phone' => '0102030405',
                'email' => 'client@example.test',
            ]);

            $request->created_at = $date;
            $request->save();

            $ids[] = (int) $request->id;
        }

        $page = Inbox::paginate(
            ['sira'],
            [
                'from' => '2026-10-02',
                'to' => '2026-10-03',
            ],
            1,
            2
        );

        $this->assertSame(2, $page->total());
        $this->assertCount(1, $page->items());
        $this->assertSame($ids[1], (int) $page->items()[0]['id']);
    }

    /** Filtrage par source avant pagination. */
    public function test_it_filters_by_source(): void
    {
        $client = \App\Models\User::factory()->create([
            'role' => 'client',
        ]);

        DB::table('client_requests')->insert([
            'user_id' => $client->id,
            'subject' => 'Demande client',
            'message' => 'Contenu',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sira = SiraRequest::create([
            'name' => 'Client de test',
            'country' => 'Côte d’Ivoire',
            'city' => 'Abidjan',
            'phone' => '0102030405',
            'email' => 'client@example.test',
        ]);

        $page = Inbox::paginate(
            ['client', 'sira'],
            ['source' => 'sira'],
            10,
            1
        );

        $this->assertSame(1, $page->total());
        $this->assertCount(1, $page->items());
        $this->assertSame('sira', $page->items()[0]['source']);
        $this->assertSame((int) $sira->id, (int) $page->items()[0]['id']);
    }
    /** Recherche avant le calcul des pages. */
    public function test_it_searches_before_paginating(): void
    {
        $expectedIds = [];

        for ($number = 1; $number <= 12; $number++) {
            $request = SiraRequest::create([
                'name' => 'Candidat Kpandji ' . $number,
                'country' => 'Côte d’Ivoire',
                'city' => 'Abidjan',
                'phone' => '0102030405',
                'email' => 'client' . $number . '@example.test',
            ]);

            $expectedIds[] = (int) $request->id;
        }

        SiraRequest::create([
            'name' => 'Autre candidat',
            'country' => 'Sénégal',
            'city' => 'Dakar',
            'phone' => '0102030405',
            'email' => 'autre@example.test',
        ]);

        $page = Inbox::paginate(
            ['sira'],
            ['q' => 'KPANDJI'],
            10,
            2
        );

        $this->assertSame(12, $page->total());
        $this->assertCount(2, $page->items());

        foreach ($page->items() as $item) {
            $this->assertContains((int) $item['id'], $expectedIds);
        }

        // Recherche également dans l’adresse e-mail.
        $emailPage = Inbox::paginate(
            ['sira'],
            ['q' => 'autre@example.test'],
            10,
            1
        );

        $this->assertSame(1, $emailPage->total());
        $this->assertSame(
            'autre@example.test',
            $emailPage->items()[0]['email']
        );
    }

    /** Recherche littérale des caractères spéciaux. */
    public function test_search_treats_special_characters_as_text(): void
    {
        $expected = null;

        foreach (['Client 100%_!', 'Client 100ABC'] as $name) {
            $request = SiraRequest::create([
                'name' => $name,
                'country' => 'Côte d’Ivoire',
                'city' => 'Abidjan',
                'phone' => '0102030405',
                'email' => 'client@example.test',
            ]);

            $expected ??= $request;
        }

        $page = Inbox::paginate(
            ['sira'],
            ['q' => '%_!'],
            10,
            1
        );

        $this->assertSame(1, $page->total());
        $this->assertSame(
            (int) $expected->id,
            (int) $page->items()[0]['id']
        );

        // Cette saisie doit rester une valeur, sans modifier le SQL.
        $injection = Inbox::paginate(
            ['sira'],
            ['q' => "' OR 1=1 --"],
            10,
            1
        );

        $this->assertSame(0, $injection->total());
    }
    /** Pagination et compteurs dans la page manager. */
    public function test_the_manager_page_paginates_filtered_requests(): void
    {
        $manager = \App\Models\User::factory()->create([
            'role' => 'manager',
        ]);

        $now = now();

        for ($number = 1; $number <= 13; $number++) {
            $request = SiraRequest::create([
                'name' => 'Candidat pagination ' . $number,
                'country' => 'Côte d’Ivoire',
                'city' => 'Abidjan',
                'phone' => '0102030405',
                'email' => 'client' . $number . '@example.test',
            ]);

            $request->created_at = $now->copy()->subMinutes(13 - $number);
            $request->save();

            DB::table('inbox_statuses')->insert([
                'source' => 'sira',
                'source_id' => $request->id,
                'status' => $number <= 12 ? 'en_cours' : 'traite',
            ]);
        }

        $response = $this->actingAs($manager)->get(
            route('manager.other', [
                'source' => 'sira',
                'status' => 'en_cours',
                'page' => 2,
            ])
        );

        $response->assertOk();
        $response->assertViewIs('spaces.manager.inbox');

        $response->assertViewHas('items', function ($items) {
            return $items instanceof
                \Illuminate\Pagination\LengthAwarePaginator
                && $items->currentPage() === 2
                && $items->total() === 12
                && $items->count() === 2;
        });

        // Les compteurs de statut restent indépendants du filtre.
        $response->assertViewHas('counts', function ($counts) {
            return $counts->get('en_cours') === 12
                && $counts->get('traite') === 1;
        });

        $response->assertSeeText('Candidat pagination 1');
        $response->assertSeeText('Candidat pagination 2');
        $response->assertDontSeeText('Candidat pagination 13');

        // Les liens conservent les filtres.
        $response->assertViewHas('items', function ($items) {
            parse_str(
                parse_url($items->url(1), PHP_URL_QUERY),
                $parameters
            );

            return ($parameters['source'] ?? null) === 'sira'
                && ($parameters['status'] ?? null) === 'en_cours'
                && ($parameters['page'] ?? null) === '1';
        });
    }
}