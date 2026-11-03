<?php

namespace Tests\Feature\Front;

use App\Models\Conversation;
use App\Models\User;
use App\Support\DirectMessages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LegacyRedirectsTest extends TestCase
{
    use RefreshDatabase;

    /** @return array<string, array{string, string}> */
    public static function legacyUrls(): array
    {
        return [
            'ancien admin' => ['/admin', '/backoffice'],
            'page de l’ancien admin' => ['/admin/voitures/3/edit', '/backoffice'],
            'ancien espace manager' => ['/espace-manager', '/manager'],
            'ancienne boîte de réception' => ['/espace-manager/messages?box=received', '/manager'],
            'conversation reçue par e-mail' => ['/espace-manager/conversations/42', '/manager/conversations/42'],
        ];
    }

    #[DataProvider('legacyUrls')]
    public function test_old_back_office_addresses_redirect_permanently(string $from, string $to): void
    {
        $this->get($from)->assertStatus(301)->assertRedirect($to);
    }

    public function test_admins_and_managers_land_in_their_filament_panel_after_login(): void
    {
        $admin = User::factory()->admin()->create(['email' => 'admin@example.test']);
        $manager = User::factory()->manager()->create(['email' => 'manager@example.test']);

        $this->post(route('account.login'), ['email' => 'admin@example.test', 'password' => 'password'])->assertRedirect(url('/backoffice'));
        $this->post(route('account.logout'));
        $this->post(route('account.login'), ['email' => 'manager@example.test', 'password' => 'password'])->assertRedirect(url('/manager'));

        $this->assertSame(url('/backoffice'), $admin->workspaceUrl());
        $this->assertSame(url('/manager'), $manager->workspaceUrl());
    }

    public function test_direct_message_email_links_a_manager_to_the_filament_conversation(): void
    {
        $sent = [];
        Mail::shouldReceive('raw')->andReturnUsing(function (string $body) use (&$sent) {
            $sent[] = $body;
        });
        $sender = User::factory()->manager()->create();
        User::factory()->manager()->create();

        DirectMessages::compose($sender, DirectMessages::recipients($sender)->pluck('id')->all(), 'Réunion', 'Point mensuel.');

        $this->assertStringContainsString(url('/manager/conversations/'.Conversation::sole()->id), $sent[0]);
    }
}
