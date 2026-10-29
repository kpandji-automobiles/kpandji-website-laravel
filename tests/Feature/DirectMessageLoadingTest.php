<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use App\Support\ClientThreads;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DirectMessageLoadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_reply_queries_do_not_grow_with_conversations(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $client = User::factory()->create(['role' => 'client']);

        $conversation = Conversation::create([
            'sender_id' => $manager->id,
            'recipient_id' => $client->id,
            'subject' => 'Message de test',
            'body' => 'Contenu du message',
        ]);

        [$firstThreads, $firstQueryCount] = $this->loadThreads($client);

        $this->assertCount(1, $firstThreads);

        // Passage à dix conversations.
        for ($number = 2; $number <= 10; $number++) {
            $copy = $conversation->replicate();
            $copy->subject = 'Message ' . $number;
            $copy->save();
        }

        [$threads, $queryCount] = $this->loadThreads($client);

        $this->assertCount(10, $threads);

        $this->assertLessThanOrEqual(
            $firstQueryCount,
            $queryCount,
            'Les requêtes de réponses augmentent avec les conversations.'
        );
    }

    /** Charge les fils et compte les requêtes de réponses. */
    private function loadThreads(User $client): array
    {
        DB::flushQueryLog();
        DB::enableQueryLog();

        try {
            $threads = ClientThreads::all($client);

            $count = collect(DB::getQueryLog())
                ->filter(fn($entry) => str_contains(
                    strtolower($entry['query']),
                    'inbox_replies'
                ))
                ->count();
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }

        return [$threads, $count];
    }

    /** Aperçu et compteur des réponses. */
    public function test_it_loads_the_latest_dm_and_counts_unread_replies(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $client = User::factory()->create(['role' => 'client']);

        $conversation = Conversation::create([
            'sender_id' => $manager->id,
            'recipient_id' => $client->id,
            'subject' => 'Conversation de test',
            'body' => 'Message initial',
            'read_at' => now(),
        ]);

        $attributes = [
            'source' => 'dm',
            'source_id' => $conversation->id,
        ];

        // Réponse du gestionnaire non lue.
        \App\Models\InboxReply::create(array_merge($attributes, [
            'user_id' => $manager->id,
            'body' => 'Réponse à lire',
            'read_at' => null,
        ]));

        // Sa propre réponse ne compte pas comme non lue.
        \App\Models\InboxReply::create(array_merge($attributes, [
            'user_id' => $client->id,
            'body' => 'Réponse du client',
            'read_at' => null,
        ]));

        // Dernière réponse déjà lue.
        \App\Models\InboxReply::create(array_merge($attributes, [
            'user_id' => $manager->id,
            'body' => 'Dernière réponse directe',
            'read_at' => now(),
        ]));

        // Même identifiant, autre source.
        \App\Models\InboxReply::create([
            'source' => 'sira',
            'source_id' => $conversation->id,
            'user_id' => $manager->id,
            'body' => 'Réponse étrangère',
            'read_at' => null,
        ]);

        $thread = ClientThreads::all($client)
            ->firstWhere('key', 'dm-' . $conversation->id);

        $this->assertNotNull($thread);
        $this->assertSame('Dernière réponse directe', $thread['preview']);
        $this->assertSame(1, $thread['unread']);
    }
}