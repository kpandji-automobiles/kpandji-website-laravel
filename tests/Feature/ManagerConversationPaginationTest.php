<?php

namespace Tests\Feature;

use App\Filament\Manager\Resources\Conversations\Pages\ListConversations;
use App\Models\Conversation;
use App\Models\InboxReply;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ManagerConversationPaginationTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string}> */
    public static function boxes(): array
    {
        return [
            'envoyés' => ['sent'],
            'reçus' => ['received'],
        ];
    }

    private function conversation(User $manager, User $client, string $box, string $subject): Conversation
    {
        return Conversation::create([
            'sender_id' => $box === 'sent' ? $manager->id : $client->id,
            'recipient_id' => $box === 'sent' ? $client->id : $manager->id,
            'subject' => $subject,
            'body' => 'Message initial',
        ]);
    }

    /** Pagination et séparation des boîtes. */
    #[DataProvider('boxes')]
    public function test_it_paginates_the_selected_box(string $box): void
    {
        Filament::setCurrentPanel('manager');
        $manager = User::factory()->manager()->create();
        $client = User::factory()->client()->create();

        $conversations = collect(range(1, 12))->map(fn (int $number) => $this->conversation($manager, $client, $box, 'Conversation '.$number));
        $otherBox = $this->conversation($manager, $client, $box === 'sent' ? 'received' : 'sent', 'Autre boîte');

        // Tri du plus récent au plus ancien : la page 2 contient les deux premières conversations.
        Livewire::actingAs($manager)
            ->test(ListConversations::class, ['activeTab' => $box])
            ->set('tableRecordsPerPage', 10)
            ->call('gotoPage', 2)
            ->assertCanSeeTableRecords($conversations->take(2))
            ->assertCanNotSeeTableRecords($conversations->slice(2)->push($otherBox));
    }

    /** Non-lus : message initial reçu + réponses de l'autre partie, sans les réponses d'une autre source. */
    #[DataProvider('boxes')]
    public function test_it_counts_unread_replies_in_the_selected_box(string $box): void
    {
        Filament::setCurrentPanel('manager');
        $manager = User::factory()->manager()->create();
        $client = User::factory()->client()->create();
        $conversation = $this->conversation($manager, $client, $box, 'Conversation avec réponses');

        foreach ([[$client->id, null], [$manager->id, null], [$client->id, now()]] as [$authorId, $readAt]) {
            InboxReply::create(['source' => 'dm', 'source_id' => $conversation->id, 'user_id' => $authorId, 'body' => 'Réponse de test', 'read_at' => $readAt]);
        }
        // Même identifiant, autre source : réponse à exclure.
        InboxReply::create(['source' => 'sira', 'source_id' => $conversation->id, 'user_id' => $client->id, 'body' => 'Réponse étrangère']);

        Livewire::actingAs($manager)
            ->test(ListConversations::class, ['activeTab' => $box])
            ->assertTableColumnStateSet('replies_count', 3, $conversation)
            ->assertTableColumnStateSet('unread', $box === 'received' ? 2 : 1, $conversation);
    }
}
