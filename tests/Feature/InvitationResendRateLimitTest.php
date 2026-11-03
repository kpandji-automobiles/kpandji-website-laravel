<?php

namespace Tests\Feature;

use App\Filament\Manager\Resources\Invitations\Pages\ManageInvitations;
use App\Models\ClientInvitation;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class InvitationResendRateLimitTest extends TestCase
{
    use RefreshDatabase;

    private function invitation(User $manager, string $email): ClientInvitation
    {
        return ClientInvitation::create([
            'name' => 'Client de test',
            'email' => $email,
            'role' => 'client',
            'invited_by' => $manager->id,
            'token_hash' => hash('sha256', $email),
            'expires_at' => now()->addDays(7),
            'send_count' => 0,
        ]);
    }

    public function test_it_limits_invitation_resends_per_manager(): void
    {
        Mail::spy();
        Filament::setCurrentPanel('manager');

        $manager = User::factory()->manager()->create();
        $invitation = $this->invitation($manager, 'invitation@example.test');
        $otherInvitation = $this->invitation($manager, 'autre-client@example.test');
        $this->actingAs($manager);

        // Cinq renvois autorisés.
        $page = Livewire::test(ManageInvitations::class);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $page->callAction(TestAction::make('resend')->table($invitation));
        }

        // Sixième renvoi refusé, y compris pour une autre invitation du même manager.
        $page->callAction(TestAction::make('resend')->table($invitation))->assertNotified('Trop de renvois.');
        Livewire::test(ManageInvitations::class)->callAction(TestAction::make('resend')->table($otherInvitation))->assertNotified('Trop de renvois.');

        $this->assertSame(5, (int) $invitation->fresh()->send_count);
        $this->assertSame(0, (int) $otherInvitation->fresh()->send_count);
        Mail::shouldHaveReceived('raw')->times(5);

        // Autre manager, quota disponible.
        $this->actingAs(User::factory()->manager()->create());
        Livewire::test(ManageInvitations::class)->callAction(TestAction::make('resend')->table($otherInvitation));

        $this->assertSame(1, (int) $otherInvitation->fresh()->send_count);
        Mail::shouldHaveReceived('raw')->times(6);
    }
}
