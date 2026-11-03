<?php

namespace Tests\Feature\Front;

use App\Models\ClientRequest;
use App\Models\InboxReply;
use App\Models\Investment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientSpaceTest extends TestCase
{
    use RefreshDatabase;

    private User $client;

    private Investment $investment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = User::factory()->client()->create(['name' => 'Awa Koné']);
        $this->investment = $this->client->investments()->create(['label' => 'Flotte DJETRAN', 'program' => 'vehicule', 'amount' => 15000000, 'status' => 'actif']);
        $this->investment->entries()->createMany([
            ['entry_date' => now()->subMonth()->startOfMonth(), 'amount' => 450000, 'type' => 'revenu', 'description' => 'Revenu de juin'],
            ['entry_date' => now()->startOfMonth(), 'amount' => 900000, 'type' => 'versement', 'description' => 'Versement trimestriel'],
            ['entry_date' => now()->startOfMonth(), 'amount' => 50000, 'type' => 'frais', 'description' => 'Frais de gestion'],
        ]);
        $this->actingAs($this->client);
    }

    public function test_dashboard_uses_the_design_system_and_shows_the_portfolio(): void
    {
        $response = $this->get(route('client.dashboard'));

        $this->assertMatchesRegularExpression('/aria-current="page"\s+data-sp-tab="cnav_overview"/', $response->getContent());
        $response->assertOk()
            ->assertSee(asset('css/kpandji/site.css'), false)
            ->assertSeeInOrder([sp_t('kpi_invested'), '15 000 000', sp_t('kpi_revenue'), '450 000', sp_t('kpi_net'), '400 000', sp_t('kpi_paid'), '900 000'])
            ->assertSee('Flotte DJETRAN')
            ->assertSee('data-sp-chart', false);
    }

    public function test_gains_filters_keep_the_csv_export_in_sync(): void
    {
        $this->get(route('client.gains', ['type' => 'versement', 'investment' => $this->investment->id]))
            ->assertOk()
            ->assertSee('Versement trimestriel')
            ->assertDontSee('Revenu de juin')
            ->assertSee(e(route('client.csv', ['type' => 'versement', 'investment' => $this->investment->id])), false);

        $csv = $this->get(route('client.csv', ['type' => 'versement']))->assertOk()->streamedContent();
        $this->assertStringContainsString('Versement trimestriel', $csv);
        $this->assertStringNotContainsString('Revenu de juin', $csv);
    }

    public function test_client_writes_a_request_and_follows_up_in_the_thread(): void
    {
        $this->post(route('client.request'), ['subject' => 'Prochain versement', 'message' => 'Quand aura lieu le prochain versement ?', 'investment_id' => $this->investment->id])
            ->assertRedirect();
        $request = ClientRequest::sole();

        $this->get(route('client.thread', 'client-'.$request->id))
            ->assertOk()
            ->assertSee('Quand aura lieu le prochain versement ?')
            ->assertSee('action="'.route('client.thread.reply', 'client-'.$request->id).'"', false);

        $this->post(route('client.thread.reply', 'client-'.$request->id), ['body' => 'Merci de me rappeler.'])->assertRedirect();
        $this->assertSame('Merci de me rappeler.', InboxReply::where(['source' => 'client', 'source_id' => $request->id])->value('body'));
    }

    public function test_unread_advisor_reply_shows_in_the_messages_tab(): void
    {
        $request = ClientRequest::create(['user_id' => $this->client->id, 'subject' => 'Documents', 'message' => 'Pouvez-vous m’envoyer le contrat ?']);
        InboxReply::create(['source' => 'client', 'source_id' => $request->id, 'user_id' => User::factory()->manager()->create()->id, 'body' => 'Le contrat est en pièce jointe.']);

        $this->get(route('client.messages'))
            ->assertSee('data-sp-badge', false)
            ->assertSee('data-sp-thread-row="client-'.$request->id.'"', false)
            ->assertSee('data-sp-unread', false);
    }

    public function test_request_errors_reopen_the_compose_form(): void
    {
        $this->from(route('client.messages'))->post(route('client.request'), ['subject' => '', 'message' => ''])->assertRedirect(route('client.messages'));

        $this->get(route('client.messages'))
            ->assertSee('compose: true', false)
            ->assertSee('Le champ message est obligatoire.');
    }
}
