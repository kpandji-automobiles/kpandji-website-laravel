<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\InboxReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ManagerConversationCountTest extends TestCase
{
    use RefreshDatabase;

    /** Comptage sans récupération des identifiants. */
    public function test_it_counts_unread_messages_in_the_database(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $client = User::factory()->create(['role' => 'client']);
        $other = User::factory()->create(['role' => 'manager']);

        $sent = Conversation::create([
            'sender_id' => $manager->id,
            'recipient_id' => $client->id,
            'subject' => 'Conversation envoyée',
            'body' => 'Message initial',
        ]);

        $received = Conversation::create([
            'sender_id' => $client->id,
            'recipient_id' => $manager->id,
            'subject' => 'Conversation reçue',
            'body' => 'Message initial',
        ]);

        $foreign = Conversation::create([
            'sender_id' => $other->id,
            'recipient_id' => $client->id,
            'subject' => 'Conversation étrangère',
            'body' => 'Message privé',
        ]);

        foreach ([$sent, $received, $foreign] as $conversation) {
            InboxReply::create([
                'source' => 'dm',
                'source_id' => $conversation->id,
                'user_id' => $client->id,
                'body' => 'Réponse non lue',
                'read_at' => null,
            ]);
        }

        // Ses propres réponses ne sont pas des non-lus.
        InboxReply::create([
            'source' => 'dm',
            'source_id' => $received->id,
            'user_id' => $manager->id,
            'body' => 'Réponse du manager',
            'read_at' => null,
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        try {
            $count = Conversation::unreadTotal($manager);
            $queries = DB::getQueryLog();
        } finally {
            DB::disableQueryLog();
            DB::flushQueryLog();
        }

        // Un message initial reçu et deux réponses.
        $this->assertSame(3, $count);

        foreach ($queries as $query) {
            $this->assertMatchesRegularExpression(
                '/^\s*select\s+count\s*\(/i',
                $query['query'],
                'Le compteur récupère des lignes au lieu de compter en base.'
            );
        }
    }

    /** Compteurs globaux sans chargement de l’historique. */
    public function test_box_counts_do_not_load_all_conversations(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $client = User::factory()->create(['role' => 'client']);

        foreach (['sent', 'received'] as $box) {
            for ($number = 1; $number <= 12; $number++) {
                $conversation = Conversation::create([
                    'sender_id' => $box === 'sent'
                        ? $manager->id
                        : $client->id,
                    'recipient_id' => $box === 'sent'
                        ? $client->id
                        : $manager->id,
                    'subject' => 'Conversation ' . $number,
                    'body' => 'Message initial',
                    'read_at' => null,
                ]);

                // Une réponse non lue dans chaque boîte.
                if ($number === 1) {
                    InboxReply::create([
                        'source' => 'dm',
                        'source_id' => $conversation->id,
                        'user_id' => $client->id,
                        'body' => 'Réponse non lue',
                        'read_at' => null,
                    ]);
                }
            }
        }

        $loadedIds = [];

        Conversation::retrieved(function ($conversation) use (&$loadedIds) {
            $loadedIds[] = (int) $conversation->id;
        });

        $response = $this->actingAs($manager)->get(
            route('manager.inbox', [
                'box' => 'sent',
                'page' => 2,
            ])
        );

        $response->assertOk();

        $response->assertViewHas('boxCounts', [
            'sent' => 12,
            'received' => 12,
            'sent_unread' => 1,
            'received_unread' => 13,
        ]);

        $this->assertCount(
            2,
            $loadedIds,
            'Seules les deux conversations de la page doivent être chargées.'
        );
    }
}