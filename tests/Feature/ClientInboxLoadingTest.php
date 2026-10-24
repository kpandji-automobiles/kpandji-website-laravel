<?php

namespace Tests\Feature;

use App\Models\SiraRequest;
use App\Models\User;
use App\Models\InboxReply;
use App\Support\ClientThreads;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;


class ClientInboxLoadingTest extends TestCase
{
    use RefreshDatabase;



    public function test_it_filters_requests_in_the_database(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $other = User::factory()->create(['role' => 'client']);

        $mine = SiraRequest::create([
            'user_id' => $client->id,
            'name' => 'Client de test',
            'country' => 'Côte d’Ivoire',
            'city' => 'Abidjan',
            'phone' => '0102030405',
            'email' => $client->email,
        ]);

        // Autre demande avec un propriétaire différent.
        $foreign = $mine->replicate();
        $foreign->user_id = $other->id;
        $foreign->email = $other->email;
        $foreign->save();

        // Métadonnées de deux demandes différentes.
        DB::table('inbox_statuses')->insert([
            [
                'source' => 'sira',
                'source_id' => $mine->id,
                'status' => 'en_cours',
            ],
            [
                'source' => 'sira',
                'source_id' => $foreign->id,
                'status' => 'traite',
            ],
        ]);

        DB::table('inbox_replies')->insert([
            [
                'source' => 'sira',
                'source_id' => $mine->id,
                'body' => 'Réponse au client',
            ],
            [
                'source' => 'sira',
                'source_id' => $foreign->id,
                'body' => 'Réponse à un autre client',
            ],
        ]);

        // Capture uniquement les requêtes du chargement.
        DB::flushQueryLog();
        DB::enableQueryLog();

        try {
            $items = ClientThreads::items($client, ['sira']);
            $queries = DB::getQueryLog();
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }

        $this->assertSame(
            [$mine->id],
            $items->pluck('id')->all()
        );

        $query = collect($queries)->first(
            fn($entry) => str_contains(
                strtolower($entry['query']),
                'sira_requests'
            )
        );

        $this->assertNotNull($query);

        // Filtrage effectué avant le retour des lignes.
        $sql = strtolower($query['query']);

        $this->assertStringContainsString('where', $sql);
        $this->assertStringContainsString('user_id', $sql);
        $this->assertContains($client->id, $query['bindings']);

        // Conservation des informations utiles.
        $this->assertSame('en_cours', $items->first()['status']);
        $this->assertSame(1, $items->first()['replies']);

        // Chargement limité aux demandes sélectionnées.
        foreach (['inbox_statuses', 'inbox_replies'] as $table) {
            $metadataQuery = collect($queries)->first(
                fn($entry) => str_contains(
                    strtolower($entry['query']),
                    $table
                )
            );

            $this->assertNotNull($metadataQuery);

            $this->assertStringContainsString(
                'where',
                strtolower($metadataQuery['query'])
            );

            $this->assertContains('sira', $metadataQuery['bindings']);
            $this->assertContains($mine->id, $metadataQuery['bindings']);
        }
        $expectedReplyId = DB::table('inbox_replies')
            ->where('source', 'sira')
            ->where('source_id', $mine->id)
            ->value('id');

        $loadedReplyIds = [];

        // Observe les réponses réellement chargées en mémoire.
        InboxReply::retrieved(function (InboxReply $reply) use (&$loadedReplyIds) {
            $loadedReplyIds[] = (int) $reply->id;
        });

        $threads = ClientThreads::all($client);

        // Seul le fil du client est visible.
        $this->assertSame(
            ['sira-' . $mine->id],
            $threads->pluck('key')->all()
        );

        // Seule sa réponse doit être chargée.
        $this->assertSame(
            [(int) $expectedReplyId],
            $loadedReplyIds,
            'Des réponses étrangères ont été chargées en mémoire.'
        );
    }

}