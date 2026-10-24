<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestDriveRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_limits_submissions_and_recovers_after_expiry(): void
    {
        $this->withServerVariables([
            'REMOTE_ADDR' => '203.0.113.10',
        ]);

        $data = [
            'type' => 'essai',
            'name' => 'Visiteur de test',
            'email' => 'visiteur@example.test',
            'phone' => '0102030405',
        ];

        // Cinq demandes autorisées.
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('contact.store'), $data)
                ->assertRedirect(route('contact'));
        }

        $this->assertDatabaseCount('test_drive_requests', 5);

        // Sixième demande refusée sans enregistrement.
        $this->post(route('contact.store'), $data)
            ->assertStatus(429);

        $this->assertDatabaseCount('test_drive_requests', 5);

        // Nouvelle demande après expiration.
        $this->travel(61)->seconds();

        $this->post(route('contact.store'), $data)
            ->assertRedirect(route('contact'));

        $this->assertDatabaseCount('test_drive_requests', 6);
    }

    public function test_each_ip_has_its_own_quota(): void
    {
        $data = [
            'type' => 'essai',
            'name' => 'Visiteur de test',
            'email' => 'visiteur@example.test',
            'phone' => '0102030405',
        ];

        // Première IP : quota épuisé.
        $this->withServerVariables([
            'REMOTE_ADDR' => '203.0.113.10',
        ]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('contact.store'), $data)
                ->assertRedirect(route('contact'));
        }

        $this->post(route('contact.store'), $data)
            ->assertStatus(429);

        // Deuxième IP : quota disponible.
        $this->withServerVariables([
            'REMOTE_ADDR' => '203.0.113.20',
        ]);

        $this->post(route('contact.store'), $data)
            ->assertRedirect(route('contact'));

        $this->assertDatabaseCount('test_drive_requests', 6);
    }
}