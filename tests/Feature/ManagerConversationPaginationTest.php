<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ManagerConversationPaginationTest extends TestCase
{
    use RefreshDatabase;

    public static function boxes(): array
    {
        return [
            'envoyés' => ['sent'],
            'reçus' => ['received'],
        ];
    }

    /** Pagination et séparation des boîtes. */
    #[DataProvider('boxes')]
    public function test_it_paginates_the_selected_box(string $box): void
    {
        $manager = User::factory()->create(['role' => 'manager']);
        $client = User::factory()->create(['role' => 'client']);

        $ids = [];

        for ($number = 1; $number <= 12; $number++) {
            $conversation = Conversation::create([
                'sender_id' => $box === 'sent'
                    ? $manager->id
                    : $client->id,
                'recipient_id' => $box === 'sent'
                    ? $client->id
                    : $manager->id,
                'subject' => 'Conversation pagination ' . $number,
                'body' => 'Contenu',
            ]);

            $ids[] = (int) $conversation->id;
        }

        // Conversation présente uniquement dans la boîte opposée.
        Conversation::create([
            'sender_id' => $box === 'sent'
                ? $client->id
                : $manager->id,
            'recipient_id' => $box === 'sent'
                ? $manager->id
                : $client->id,
            'subject' => 'Conversation opposée',
            'body' => 'Autre contenu',
        ]);

        $response = $this->actingAs($manager)->get(
            route('manager.inbox', [
                'box' => $box,
                'page' => 2,
            ])
        );

        $response->assertOk();

        $response->assertViewHas('convs', function ($conversations) use ($ids) {
            return $conversations instanceof LengthAwarePaginator
                && $conversations->total() === 12
                && $conversations->currentPage() === 2
                && $conversations->count() === 2
                && $conversations->getCollection()
                    ->pluck('id')
                    ->map(fn($id) => (int) $id)
                    ->all() === [$ids[1], $ids[0]];
        });

        $response->assertSeeText('Conversation pagination 1');
        $response->assertSeeText('Conversation pagination 2');
        $response->assertDontSeeText('Conversation pagination 12');
        $response->assertDontSeeText('Conversation opposée');

        // La navigation conserve la boîte sélectionnée.
        $response->assertViewHas('convs', function ($conversations) use ($box) {
            if (!$conversations instanceof LengthAwarePaginator) {
                return false;
            }

            parse_str(
                parse_url($conversations->url(1), PHP_URL_QUERY),
                $parameters
            );

            return ($parameters['box'] ?? null) === $box;
        });
    }

    /** Compteurs des réponses de chaque boîte. */
    #[DataProvider('boxes')]
    public function test_it_counts_unread_replies_in_the_selected_box(
        string $box
    ): void {
        $manager = User::factory()->create(['role' => 'manager']);
        $client = User::factory()->create(['role' => 'client']);

        $conversation = Conversation::create([
            'sender_id' => $box === 'sent' ? $manager->id : $client->id,
            'recipient_id' => $box === 'sent' ? $client->id : $manager->id,
            'subject' => 'Conversation avec réponses',
            'body' => 'Message initial',
            'read_at' => null,
        ]);

        foreach ([
            [$client->id, null],
            [$manager->id, null],
            [$client->id, now()],
        ] as [$authorId, $readAt]) {
            \App\Models\InboxReply::create([
                'source' => 'dm',
                'source_id' => $conversation->id,
                'user_id' => $authorId,
                'body' => 'Réponse de test',
                'read_at' => $readAt,
            ]);
        }

        // Même identifiant, autre source : réponse à exclure.
        \App\Models\InboxReply::create([
            'source' => 'sira',
            'source_id' => $conversation->id,
            'user_id' => $client->id,
            'body' => 'Réponse étrangère',
            'read_at' => null,
        ]);

        $response = $this->actingAs($manager)->get(
            route('manager.inbox', ['box' => $box])
        );

        $response->assertOk();

        $response->assertViewHas('convs', function ($conversations) use ($box) {
            $conversation = $conversations->getCollection()->first();

            return $conversation !== null
                && (int) $conversation->replies_count === 3
                && (int) $conversation->unread === (
                    $box === 'received' ? 2 : 1
                );
        });
    }
}