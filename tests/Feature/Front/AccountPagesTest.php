<?php

namespace Tests\Feature\Front;

use App\Models\ClientInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AccountPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_posts_to_the_account_login_without_opening_the_header_dialog(): void
    {
        $this->get('/connexion')
            ->assertOk()
            ->assertSee('data-kp-login-page', false)
            ->assertSee('action="'.route('account.login').'"', false)
            ->assertSee('login: false', false);
    }

    public function test_signed_in_user_is_sent_to_their_space(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)->get('/connexion')->assertRedirect($client->workspaceUrl());
    }

    public function test_guests_go_to_the_login_page(): void
    {
        $this->get('/mon-compte/mot-de-passe')->assertRedirect(route('connexion'));
    }

    public function test_failed_login_comes_back_to_the_page_with_a_french_error(): void
    {
        $this->from('/connexion')
            ->post(route('account.login'), ['email' => 'inconnu@example.test', 'password' => 'mauvais'])
            ->assertRedirect('/connexion');

        $this->get('/connexion')
            ->assertSee('Identifiants incorrects.')
            ->assertSee('value="inconnu@example.test"', false)
            ->assertSee('login: false', false);
    }

    public function test_client_signs_in_from_the_page(): void
    {
        $client = User::factory()->client()->create(['email' => 'awa@example.test']);

        $this->post(route('account.login'), ['email' => 'awa@example.test', 'password' => 'password'])
            ->assertRedirect($client->workspaceUrl());
        $this->assertAuthenticatedAs($client);
    }

    public function test_waiting_page_tells_pending_and_refused_accounts_apart(): void
    {
        $pending = User::factory()->create(['role' => 'client', 'account_status' => 'pending']);
        $refused = User::factory()->create(['role' => 'client', 'account_status' => 'refused']);

        $this->actingAs($pending)->get(route('space.waiting'))->assertSee('data-sp-waiting="pending"', false)->assertSee(sp_t('wait_refresh'));
        $this->actingAs($refused)->get(route('space.waiting'))->assertSee('data-sp-waiting="refused"', false)->assertDontSee(sp_t('wait_refresh'));
    }

    public function test_invitation_link_activates_a_pending_account_and_validates_in_french(): void
    {
        $invitation = ClientInvitation::create(['name' => 'Awa Koné', 'email' => 'awa@example.test', 'role' => 'client', 'token_hash' => '', 'expires_at' => now()->addDays(7)]);
        $link = $invitation->refreshLink();

        $this->get($link)->assertOk()->assertSee('awa@example.test');

        $this->from($link)->post($link, ['password' => 'court', 'password_confirmation' => 'court'])->assertRedirect($link);
        $this->get($link)->assertSee('Le champ mot de passe doit contenir au moins 8 caractères.');

        $this->post($link, ['password' => 'motdepasse-solide', 'password_confirmation' => 'motdepasse-solide'])->assertRedirect(route('space.waiting'));
        $this->assertSame('pending', User::where('email', 'awa@example.test')->value('account_status'));
    }

    public function test_used_invitation_link_is_gone(): void
    {
        $invitation = ClientInvitation::create(['name' => 'Awa Koné', 'email' => 'awa@example.test', 'role' => 'client', 'token_hash' => '', 'expires_at' => now()->addDays(7)]);
        $link = $invitation->refreshLink();
        $invitation->forceFill(['revoked_at' => now()])->save();

        $this->get($link)->assertStatus(410)->assertSee('data-sp-invite-invalid', false);
    }

    public function test_password_change_reports_a_wrong_current_password(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)->get(route('space.password'))->assertOk()->assertSee('data-sp-password', false);

        $this->from(route('space.password'))
            ->put(route('space.password.update'), ['current' => 'faux-'.Str::random(6), 'password' => 'nouveau-mdp', 'password_confirmation' => 'nouveau-mdp'])
            ->assertRedirect(route('space.password'));

        $this->get(route('space.password'))->assertSee(sp_t('pw_wrong'));
    }
}
