<?php

namespace Tests\Feature\Manager;

use App\Filament\Manager\Resources\Invitations\Pages\ManageInvitations;
use App\Models\ClientInvitation;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class InvitationTest extends TestCase
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

    private function invitation(array $attributes = []): ClientInvitation
    {
        return ClientInvitation::create($attributes + [
            'name' => 'Awa Koné',
            'email' => 'awa@example.test',
            'role' => 'client',
            'invited_by' => $this->manager->id,
            'token_hash' => hash('sha256', Str::random(48)),
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function test_client_cannot_open_the_invitations(): void
    {
        $this->actingAs(User::factory()->client()->create())->get('/manager/invitations')->assertForbidden();
    }

    public function test_tabs_split_invitations_by_state(): void
    {
        $pending = $this->invitation();
        $expired = $this->invitation(['email' => 'expiree@example.test', 'expires_at' => now()->subDay()]);
        $revoked = $this->invitation(['email' => 'revoquee@example.test', 'revoked_at' => now()]);
        $accepted = $this->invitation(['email' => 'acceptee@example.test', 'accepted_at' => now(), 'revoked_at' => now()]);

        Livewire::test(ManageInvitations::class, ['activeTab' => 'pending'])
            ->assertCanSeeTableRecords([$pending])
            ->assertCanNotSeeTableRecords([$expired, $revoked, $accepted]);

        Livewire::test(ManageInvitations::class, ['activeTab' => 'expired'])
            ->assertCanSeeTableRecords([$expired])
            ->assertCanNotSeeTableRecords([$pending, $revoked, $accepted]);
    }

    public function test_inviting_revokes_the_open_invitation_for_that_email_and_mails_a_new_link(): void
    {
        Mail::spy();
        $previous = $this->invitation();

        Livewire::test(ManageInvitations::class)
            ->callAction('invite', data: ['name' => 'Awa Koné', 'email' => 'AWA@example.test', 'phone' => '+225 07 00 00 00', 'role' => 'manager'])
            ->assertHasNoFormErrors()
            ->assertNotified('Invitation envoyée à Awa Koné.');

        $invitation = ClientInvitation::latest('id')->firstOrFail();
        $this->assertSame('awa@example.test', $invitation->email);
        $this->assertSame('manager', $invitation->role);
        $this->assertSame($this->manager->id, $invitation->invited_by);
        $this->assertSame(64, strlen($invitation->token_hash));
        $this->assertSame('logged', $invitation->mail_status);
        $this->assertSame('pending', $invitation->state());
        $this->assertNotNull($previous->refresh()->revoked_at);
        Mail::shouldHaveReceived('raw')->once();
    }

    public function test_an_existing_account_cannot_be_invited(): void
    {
        User::factory()->client()->create(['email' => 'awa@example.test']);

        Livewire::test(ManageInvitations::class)
            ->callAction('invite', data: ['name' => 'Awa Koné', 'email' => 'Awa@example.test', 'role' => 'client'])
            ->assertHasFormErrors(['email']);

        $this->assertSame(0, ClientInvitation::count());
    }

    public function test_resending_reopens_a_revoked_invitation_with_a_new_link(): void
    {
        $invitation = $this->invitation(['revoked_at' => now(), 'send_count' => 1]);
        $oldHash = $invitation->token_hash;

        Livewire::test(ManageInvitations::class)
            ->callAction(TestAction::make('resend')->table($invitation))
            ->assertNotified('Invitation envoyée à Awa Koné.');

        $invitation->refresh();
        $this->assertSame(2, $invitation->send_count);
        $this->assertNull($invitation->revoked_at);
        $this->assertNotSame($oldHash, $invitation->token_hash);
    }

    public function test_resends_share_the_invitation_resend_quota_with_the_legacy_space(): void
    {
        Mail::spy();
        $invitation = $this->invitation(['send_count' => 1]);
        $page = Livewire::test(ManageInvitations::class);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $page->callAction(TestAction::make('resend')->table($invitation));
        }

        $page->callAction(TestAction::make('resend')->table($invitation))->assertNotified('Trop de renvois.');

        $this->assertSame(6, $invitation->refresh()->send_count);
        Mail::shouldHaveReceived('raw')->times(5);
        $this->post(route('manager.invitations.resend', ['inv' => $invitation->id]))->assertStatus(429);
    }

    public function test_revoking_a_pending_invitation(): void
    {
        $invitation = $this->invitation();

        Livewire::test(ManageInvitations::class)->callAction(TestAction::make('revoke')->table($invitation));

        $this->assertSame('revoked', $invitation->refresh()->state());
    }

    public function test_accepted_invitation_can_neither_be_resent_nor_revoked(): void
    {
        $invitation = $this->invitation(['accepted_at' => now()]);

        Livewire::test(ManageInvitations::class)
            ->assertActionHidden(TestAction::make('resend')->table($invitation))
            ->assertActionHidden(TestAction::make('revoke')->table($invitation));
    }

    public function test_legacy_space_still_invites_through_the_shared_service(): void
    {
        User::factory()->client()->create(['email' => 'pris@example.test']);

        $this->post(route('manager.invitations.store'), ['name' => 'Déjà client', 'email' => 'pris@example.test', 'role' => 'client'])
            ->assertSessionHasErrors('email');

        $this->post(route('manager.invitations.store'), ['name' => 'Awa Koné', 'email' => 'awa@example.test', 'role' => 'client'])
            ->assertRedirect(route('manager.invitations'))
            ->assertSessionHas('inv_link.name', 'Awa Koné');

        $this->assertSame('pending', ClientInvitation::where('email', 'awa@example.test')->sole()->state());
    }
}
