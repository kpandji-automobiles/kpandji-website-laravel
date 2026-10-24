<?php

namespace Tests\Feature;

use App\Models\ClientNotice;
use App\Models\Conversation;
use App\Models\InboxReply;
use App\Models\User;
use App\Support\ClientThreads;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientUnreadCountTest extends TestCase
{
    use RefreshDatabase;

    /** Comptage sans chargement des messages. */
    public function test_it_counts_unread_messages_without_loading_models(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $client = User::factory()->create(['role' => 'client']);

        $conversation = Conversation::create([
            'sender_id' => $manager->id,
            'recipient_id' => $client->id,
            'subject' => 'Conversation',
            'body' => 'Message initial non lu',
            'read_at' => null,
        ]);

        InboxReply::create([
            'source' => 'dm',
            'source_id' => $conversation->id,
            'user_id' => $manager->id,
            'body' => 'Réponse non lue',
            'read_at' => null,
        ]);

        InboxReply::create([
            'source' => 'dm',
            'source_id' => $conversation->id,
            'user_id' => $client->id,
            'body' => 'Réponse du client',
            'read_at' => null,
        ]);

        InboxReply::create([
            'source' => 'dm',
            'source_id' => $conversation->id,
            'user_id' => $manager->id,
            'body' => 'Réponse déjà lue',
            'read_at' => now(),
        ]);

        // Observe uniquement le chargement pendant le comptage.
        $loadedModels = [];

        foreach ([
            Conversation::class,
            InboxReply::class,
            ClientNotice::class,
        ] as $model) {
            $model::retrieved(function ($record) use (&$loadedModels) {
                $loadedModels[] = get_class($record);
            });
        }

        $this->assertSame(2, ClientThreads::unread($client));

        $this->assertSame(
            [],
            $loadedModels,
            'Le compteur charge des modèles au lieu de compter en base.'
        );
    }

    /** Compteur limité au propriétaire. */
    public function test_it_counts_only_the_clients_notices_and_form_replies(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $other = User::factory()->create(['role' => 'client']);

        $this->assertSame(0, ClientThreads::unread($client));

        // Notifications lues, non lues et étrangères.
        foreach ([
            [$client->id, null],
            [$client->id, now()],
            [$other->id, null],
        ] as [$ownerId, $readAt]) {
            ClientNotice::create([
                'user_id' => $ownerId,
                'subject' => 'Notification',
                'body' => 'Contenu',
                'read_at' => $readAt,
            ]);
        }

        $this->assertSame(1, ClientThreads::unread($client));

        $mine = \App\Models\SiraRequest::create([
            'user_id' => $client->id,
            'name' => 'Client de test',
            'country' => 'Côte d’Ivoire',
            'city' => 'Abidjan',
            'phone' => '0102030405',
            'email' => $client->email,
        ]);

        $foreign = $mine->replicate();
        $foreign->user_id = $other->id;
        $foreign->email = $other->email;
        $foreign->save();

        // Une seule de ces réponses doit compter.
        foreach ([
            [$mine->id, null, null],
            [$mine->id, $client->id, null],
            [$mine->id, null, now()],
            [$foreign->id, null, null],
        ] as [$requestId, $authorId, $readAt]) {
            InboxReply::create([
                'source' => 'sira',
                'source_id' => $requestId,
                'user_id' => $authorId,
                'body' => 'Réponse de test',
                'read_at' => $readAt,
            ]);
        }

        $this->assertSame(2, ClientThreads::unread($client));
    }

    /** Lecture globale sans chargement des messages. */
    public function test_it_marks_all_messages_read_without_loading_models(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $client = User::factory()->create(['role' => 'client']);
        $other = User::factory()->create(['role' => 'client']);

        $conversations = [];

        foreach ([$client, $other] as $recipient) {
            $conversation = Conversation::create([
                'sender_id' => $manager->id,
                'recipient_id' => $recipient->id,
                'subject' => 'Conversation',
                'body' => 'Message initial',
                'read_at' => null,
            ]);

            InboxReply::create([
                'source' => 'dm',
                'source_id' => $conversation->id,
                'user_id' => $manager->id,
                'body' => 'Réponse non lue',
                'read_at' => null,
            ]);

            ClientNotice::create([
                'user_id' => $recipient->id,
                'subject' => 'Notification',
                'body' => 'Information',
                'read_at' => null,
            ]);

            $conversations[] = $conversation;
        }

        // Demandes appartenant aux deux clients.
        $requestIds = [];

        foreach ([$client, $other] as $owner) {
            $request = \App\Models\SiraRequest::create([
                'user_id' => $owner->id,
                'name' => 'Client de test',
                'country' => 'Côte d’Ivoire',
                'city' => 'Abidjan',
                'phone' => '0102030405',
                'email' => $owner->email,
            ]);

            InboxReply::create([
                'source' => 'sira',
                'source_id' => $request->id,
                'user_id' => null,
                'body' => 'Réponse au formulaire',
                'read_at' => null,
            ]);

            $requestIds[] = $request->id;
        }

        $loadedModels = [];

        foreach ([
            Conversation::class,
            InboxReply::class,
            ClientNotice::class,
        ] as $model) {
            $model::retrieved(function ($record) use (&$loadedModels) {
                $loadedModels[] = get_class($record);
            });
        }

        ClientThreads::markRead($client);

        $this->assertSame(
            [],
            $loadedModels,
            'La lecture globale charge les messages en mémoire.'
        );

        $this->assertSame(0, ClientThreads::unread($client));
        $this->assertSame(4, ClientThreads::unread($other));
        $this->assertDatabaseMissing('conversations', [
            'id' => $conversations[0]->id,
            'read_at' => null,
        ]);

        $this->assertDatabaseHas('conversations', [
            'id' => $conversations[1]->id,
            'read_at' => null,
        ]);
        // Sa réponse est lue, celle de l’autre client reste non lue.
        $this->assertDatabaseMissing('inbox_replies', [
            'source' => 'sira',
            'source_id' => $requestIds[0],
            'read_at' => null,
        ]);

        $this->assertDatabaseHas('inbox_replies', [
            'source' => 'sira',
            'source_id' => $requestIds[1],
            'read_at' => null,
        ]);
    }

}