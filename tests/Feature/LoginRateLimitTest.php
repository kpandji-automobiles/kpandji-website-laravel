<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_blocks_login_until_the_limit_expires(): void
    {
        $user = User::factory()->create([
            'email' => 'connexion@example.test',
            'password' => 'MotDePasseDeTest-123!',
        ]);

        // Cinq échecs de connexion.
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson(route('account.login'), [
                'email' => $user->email,
                'password' => 'incorrect',
            ])->assertStatus(422);
        }

        // Blocage même avec le bon mot de passe.
        $this->postJson(route('account.login'), [
            'email' => $user->email,
            'password' => 'MotDePasseDeTest-123!',
        ])->assertStatus(429);

        $this->assertGuest();

        // Expiration du blocage.
        $this->travel(61)->seconds();

        $this->postJson(route('account.login'), [
            'email' => $user->email,
            'password' => 'MotDePasseDeTest-123!',
        ])->assertOk();

        $this->assertAuthenticatedAs($user);
    }
}