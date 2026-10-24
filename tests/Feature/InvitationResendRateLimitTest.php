<?php

namespace Tests\Feature;

use App\Models\ClientInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InvitationResendRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_limits_invitation_resends(): void
    {
        // Interception des envois.
        Mail::spy();

        $manager = User::factory()->create([
            'role' => 'manager',
            'account_status' => 'active',
        ]);

        $invitation = ClientInvitation::create([
            'name' => 'Client de test',
            'email' => 'invitation@example.test',
            'role' => 'client',
            'invited_by' => $manager->id,
            'token_hash' => hash('sha256', 'jeton-de-test'),
            'expires_at' => now()->addDays(7),
            'send_count' => 0,
        ]);

        $url = route('manager.invitations.resend', [
            'inv' => $invitation->id,
        ]);

        $this->actingAs($manager);

        // Cinq renvois autorisés.
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post($url)
                ->assertRedirect(route('manager.invitations'));
        }

        Mail::shouldHaveReceived('raw')->times(5);

        // Sixième renvoi refusé.
        $this->post($url)->assertStatus(429);

        Mail::shouldHaveReceived('raw')->times(5);

        $this->assertSame(
            5,
            (int) $invitation->fresh()->send_count
        );

        // Autre invitation, même manager.
        $otherInvitation = ClientInvitation::create([
            'name' => 'Autre client',
            'email' => 'autre-client@example.test',
            'role' => 'client',
            'invited_by' => $manager->id,
            'token_hash' => hash('sha256', 'autre-jeton-de-test'),
            'expires_at' => now()->addDays(7),
            'send_count' => 0,
        ]);

        $otherUrl = route('manager.invitations.resend', [
            'inv' => $otherInvitation->id,
        ]);

        $this->post($otherUrl)->assertStatus(429);

        $this->assertSame(
            0,
            (int) $otherInvitation->fresh()->send_count
        );

        Mail::shouldHaveReceived('raw')->times(5);

        // Autre manager, quota disponible.
        $otherManager = User::factory()->create([
            'role' => 'manager',
            'account_status' => 'active',
        ]);

        $this->actingAs($otherManager)
            ->post($otherUrl)
            ->assertRedirect(route('manager.invitations'));

        Mail::shouldHaveReceived('raw')->times(6);

        $this->assertSame(
            1,
            (int) $otherInvitation->fresh()->send_count
        );
    }
}