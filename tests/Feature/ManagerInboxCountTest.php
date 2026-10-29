<?php

namespace Tests\Feature;

use App\Models\SiraRequest;
use App\Support\Inbox;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ManagerInboxCountTest extends TestCase
{
    use RefreshDatabase;

    /** Comptage sans chargement des demandes. */
    public function test_it_counts_new_requests_in_the_database(): void
    {
        $request = SiraRequest::create([
            'name' => 'Client de test',
            'country' => 'Côte d’Ivoire',
            'city' => 'Abidjan',
            'phone' => '0102030405',
            'email' => 'client@example.test',
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        try {
            $counts = Inbox::newCounts();
            $queries = DB::getQueryLog();
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }

        $this->assertSame([
            'messages' => 0,
            'reservations' => 0,
            'sav' => 0,
            'autres' => 1,
        ], $counts);

        // Chaque requête doit effectuer un comptage.
        foreach ($queries as $query) {
            $this->assertMatchesRegularExpression(
                '/\bcount\s*\(/i',
                $query['query'],
                'Le compteur charge des lignes au lieu de compter en base.'
            );
        }

        // Un statut explicite remplace le statut par défaut.
        DB::table('inbox_statuses')->insert([
            'source' => 'sira',
            'source_id' => $request->id,
            'status' => 'en_cours',
        ]);

        $this->assertSame(0, Inbox::newCounts()['autres']);
    }

    /** Priorité du statut explicite. */
    public function test_explicit_status_overrides_handled_at(): void
    {
        $request = SiraRequest::create([
            'name' => 'Client de test',
            'country' => 'Côte d’Ivoire',
            'city' => 'Abidjan',
            'phone' => '0102030405',
            'email' => 'client@example.test',

        ]);

        $request->handled_at = now();
        $request->save();

        // Demande traitée sans statut explicite.
        $this->assertSame(0, Inbox::newCounts()['autres']);

        // Le statut explicite « nouveau » est prioritaire.
        DB::table('inbox_statuses')->insert([
            'source' => 'sira',
            'source_id' => $request->id,
            'status' => 'nouveau',
        ]);

        $this->assertSame(1, Inbox::newCounts()['autres']);

        // Un statut traité exclut une demande même sans handled_at.
        $request->handled_at = null;
        $request->save();

        DB::table('inbox_statuses')
            ->where('source', 'sira')
            ->where('source_id', $request->id)
            ->update(['status' => 'traite']);

        $this->assertSame(0, Inbox::newCounts()['autres']);
    }
}