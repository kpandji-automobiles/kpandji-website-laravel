<?php

namespace Tests\Feature\Manager;

use App\Filament\Manager\Resources\Conversations\Pages\ListConversations;
use App\Filament\Manager\Resources\Conversations\Pages\ViewConversation;
use App\Models\Conversation;
use App\Models\InboxReply;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ConversationTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = User::factory()->manager()->create();
        $this->actingAs($this->manager);
        Filament::setCurrentPanel('manager');
    }

    private function conversation(User $sender, User $recipient, array $attributes = []): Conversation
    {
        return Conversation::create($attributes + ['sender_id' => $sender->id, 'recipient_id' => $recipient->id, 'subject' => 'Point mensuel', 'body' => 'Bonjour, voici le point du mois.']);
    }

    public function test_tabs_list_only_the_managers_own_conversations(): void
    {
        $client = User::factory()->client()->create();
        $received = $this->conversation($client, $this->manager);
        $sent = $this->conversation($this->manager, $client);
        $foreign = $this->conversation(User::factory()->manager()->create(), $client);

        Livewire::test(ListConversations::class, ['activeTab' => 'received'])
            ->assertCanSeeTableRecords([$received])
            ->assertCanNotSeeTableRecords([$sent, $foreign]);

        Livewire::test(ListConversations::class, ['activeTab' => 'sent'])
            ->assertCanSeeTableRecords([$sent])
            ->assertCanNotSeeTableRecords([$received, $foreign]);
    }

    public function test_compose_opens_one_mailed_conversation_per_active_recipient(): void
    {
        Mail::spy();
        $client = User::factory()->client()->create();
        $otherManager = User::factory()->manager()->create();
        $pending = User::factory()->create(['role' => 'client', 'account_status' => 'pending']);

        $message = ['subject' => 'Assemblée annuelle', 'body' => 'Rendez-vous le 12.'];

        Livewire::test(ListConversations::class)
            ->callAction('compose', data: ['recipients' => [$client->id, $pending->id]] + $message)
            ->assertHasFormErrors(['recipients.1']);
        $this->assertSame(0, Conversation::count());

        Livewire::test(ListConversations::class)
            ->callAction('compose', data: ['recipients' => [$client->id, $otherManager->id]] + $message)
            ->assertNotified('Message envoyé à 2 destinataires.');

        $conversations = Conversation::where('sender_id', $this->manager->id)->get();
        $this->assertEqualsCanonicalizing([$client->id, $otherManager->id], $conversations->pluck('recipient_id')->all());
        $this->assertCount(1, $conversations->pluck('batch')->unique());
        $this->assertSame(['logged'], $conversations->pluck('mail_status')->unique()->values()->all());
        Mail::shouldHaveReceived('raw')->twice();
    }

    public function test_opening_a_conversation_marks_it_read_for_the_recipient(): void
    {
        $client = User::factory()->client()->create();
        $conversation = $this->conversation($client, $this->manager);
        $reply = InboxReply::create(['source' => 'dm', 'source_id' => $conversation->id, 'user_id' => $client->id, 'body' => 'Une précision.']);

        Livewire::test(ViewConversation::class, ['record' => $conversation->getRouteKey()])->assertSee('Une précision.');

        $this->assertNotNull($conversation->refresh()->read_at);
        $this->assertNotNull($reply->refresh()->read_at);
    }

    public function test_manager_cannot_open_someone_elses_conversation(): void
    {
        $foreign = $this->conversation(User::factory()->manager()->create(), User::factory()->client()->create());

        $this->get('/manager/conversations/'.$foreign->id)->assertNotFound();
    }

    public function test_reply_is_recorded_and_mailed_to_the_other_party(): void
    {
        Mail::spy();
        $client = User::factory()->client()->create(['email' => 'awa@example.test']);
        $conversation = $this->conversation($this->manager, $client);

        Livewire::test(ViewConversation::class, ['record' => $conversation->getRouteKey()])
            ->callAction('reply', data: ['body' => 'Merci pour votre retour.'])
            ->assertHasNoFormErrors()
            ->assertNotified('Réponse envoyée.');

        $reply = InboxReply::sole();
        $this->assertSame(['dm', $conversation->id, $this->manager->id, 'awa@example.test'], [$reply->source, $reply->source_id, $reply->user_id, $reply->to_email]);
        Mail::shouldHaveReceived('raw')->once();
    }
}
