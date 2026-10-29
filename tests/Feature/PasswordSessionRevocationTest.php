<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordSessionRevocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_change_rejects_an_old_session(): void
    {
        $user = User::factory()->create([
            'role' => 'client',
            'account_status' => 'active',
            'password' => 'AncienMotDePasse-123!',
        ]);

        // Connexion réelle dans le test.
        $this->postJson(route('account.login'), [
            'email' => $user->email,
            'password' => 'AncienMotDePasse-123!',
        ])->assertOk();

        // Copie de la session avant modification.
        $oldSession = session()->all();
        // Identifiant avant le changement.
        $previousSessionId = session()->getId();

        $this->withCookie(config('session.cookie'), $previousSessionId)
            ->put(route('space.password.update'), [
                'current' => 'AncienMotDePasse-123!',
                'password' => 'NouveauMotDePasse-456!',
                'password_confirmation' => 'NouveauMotDePasse-456!',
            ])->assertSessionHasNoErrors();

        $this->assertTrue(
            Hash::check('NouveauMotDePasse-456!', $user->fresh()->password)
        );

        // Renouvellement de l’identifiant de session.
        $this->assertNotSame(
            $previousSessionId,
            session()->getId(),
            'La session doit changer d’identifiant.'
        );

        // Vérifie la session courante.
        Auth::forgetGuards();

        $this->getJson(route('space.password'))
            ->assertOk();

        $this->assertAuthenticatedAs($user);

        // Réutilise l’ancienne session.
        Auth::forgetGuards();
        $this->flushSession();

        $this->withSession($oldSession)
            ->getJson(route('space.password'))
            ->assertUnauthorized();

        $this->assertGuest();
    }
}