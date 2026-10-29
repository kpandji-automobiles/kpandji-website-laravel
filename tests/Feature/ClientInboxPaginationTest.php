<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use App\Support\ClientThreads;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientInboxPaginationTest extends TestCase
{
    use RefreshDatabase;

    /** Pagination avant le chargement des conversations. */
    public function test_it_loads_only_the_requested_page(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $client = User::factory()->create(['role' => 'client']);
        $other = User::factory()->create(['role' => 'client']);

        $expectedKeys = [];

        for ($number = 1; $number <= 25; $number++) {
            $conversation = Conversation::create([
                'sender_id' => $manager->id,
                'recipient_id' => $client->id,
                'subject' => 'Message ' . $number,
                'body' => 'Contenu',
                'created_at' => now()->subMinutes(25 - $number),
            ]);

            $expectedKeys[] = 'dm-' . $conversation->id;
        }

        // Conversation étrangère plus récente.
        Conversation::create([
            'sender_id' => $manager->id,
            'recipient_id' => $other->id,
            'subject' => 'Message étranger',
            'body' => 'Contenu privé',
            'created_at' => now()->addMinute(),
        ]);

        $loadedIds = [];

        Conversation::retrieved(function ($conversation) use (&$loadedIds) {
            $loadedIds[] = $conversation->id;
        });

        $page = ClientThreads::paginate($client, 10, 2);

        $this->assertSame(25, $page->total());
        $this->assertSame(2, $page->currentPage());
        $this->assertSame(3, $page->lastPage());
        $this->assertCount(10, $page->items());

        $this->assertSame(
            array_slice(array_reverse($expectedKeys), 10, 10),
            collect($page->items())->pluck('key')->all()
        );

        $this->assertLessThanOrEqual(
            10,
            count($loadedIds),
            'Des conversations hors de la page ont été chargées.'
        );
    }

    /** Tri global entre les différentes sources. */
    public function test_it_paginates_mixed_sources_by_their_latest_activity(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $client = User::factory()->create(['role' => 'client']);
        $other = User::factory()->create(['role' => 'client']);
        $now = now();

        $requestId = \Illuminate\Support\Facades\DB::table('client_requests')
            ->insertGetId([
                'user_id' => $client->id,
                'subject' => 'Demande client',
                'message' => 'Message initial',
                'created_at' => $now->copy()->subMinutes(10),
                'updated_at' => $now,
            ]);

        \App\Models\InboxReply::create([
            'source' => 'client',
            'source_id' => $requestId,
            'user_id' => $manager->id,
            'body' => 'Réponse la plus récente',
            'created_at' => $now->copy()->subMinute(),
        ]);

        $conversation = Conversation::create([
            'sender_id' => $manager->id,
            'recipient_id' => $client->id,
            'subject' => 'Conversation directe',
            'body' => 'Message direct',
            'created_at' => $now->copy()->subMinutes(2),
        ]);

        $notice = \App\Models\ClientNotice::create([
            'user_id' => $client->id,
            'subject' => 'Notification',
            'body' => 'Information',
            'created_at' => $now->copy()->subMinutes(3),
        ]);

        $sira = \App\Models\SiraRequest::create([
            'user_id' => $client->id,
            'name' => 'Client de test',
            'country' => 'Côte d’Ivoire',
            'city' => 'Abidjan',
            'phone' => '0102030405',
            'email' => $client->email,
            'created_at' => $now->copy()->subMinutes(10),
        ]);

        \App\Models\InboxReply::create([
            'source' => 'sira',
            'source_id' => $sira->id,
            'user_id' => $manager->id,
            'body' => 'Réponse SIRA',
            'created_at' => $now->copy()->subMinutes(4),
        ]);

        // Notification étrangère exclue du total.
        \App\Models\ClientNotice::create([
            'user_id' => $other->id,
            'subject' => 'Notification étrangère',
            'body' => 'Contenu privé',
            'created_at' => $now,
        ]);

        // Formulaire sans réponse exclu de la liste.
        $withoutReply = $sira->replicate();
        $withoutReply->save();

        $firstPage = ClientThreads::paginate($client, 2, 1);
        $secondPage = ClientThreads::paginate($client, 2, 2);

        $this->assertSame(4, $firstPage->total());
        $this->assertSame(4, $secondPage->total());

        $this->assertSame(
            ['client-' . $requestId, 'dm-' . $conversation->id],
            collect($firstPage->items())->pluck('key')->all()
        );

        $this->assertSame(
            ['notice-' . $notice->id, 'sira-' . $sira->id],
            collect($secondPage->items())->pluck('key')->all()
        );

        $request = $firstPage->items()[0];

        $this->assertSame('Réponse la plus récente', $request['preview']);
        $this->assertSame(1, $request['count']);
        $this->assertSame(1, $request['unread']);
        $this->assertSame('nouveau', $request['status']);
    }

    /** Pagination et compteur global dans la page. */
    public function test_the_messages_page_uses_pagination(): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $client = User::factory()->create(['role' => 'client']);
        $now = now();

        for ($number = 1; $number <= 12; $number++) {
            Conversation::create([
                'sender_id' => $manager->id,
                'recipient_id' => $client->id,
                'subject' => 'Conversation test ' . $number,
                'body' => 'Contenu',
                'read_at' => null,
                'created_at' => $now->copy()->subMinutes(12 - $number),
            ]);
        }

        $response = $this->actingAs($client)
            ->get(route('client.messages', ['page' => 2]));

        $response->assertOk();
        $response->assertViewIs('spaces.client.messages');

        $response->assertViewHas('threads', function ($threads) {
            return $threads instanceof
                \Illuminate\Pagination\LengthAwarePaginator
                && $threads->currentPage() === 2
                && $threads->total() === 12
                && $threads->count() === 2;
        });

        // Le compteur inclut les autres pages.
        $response->assertViewHas('unread', 12);

        $response->assertSeeText('Conversation test 1');
        $response->assertSeeText('Conversation test 2');
        $response->assertDontSeeText('Conversation test 12');
    }
}