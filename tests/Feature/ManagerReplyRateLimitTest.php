<?php

namespace Tests\Feature;

use App\Models\TestDriveRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ManagerReplyRateLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_limits_replies_without_extra_sends_or_records(): void
    {
        Mail::spy();

        $manager = User::factory()->create([
            'role' => 'manager',
            'account_status' => 'active',
        ]);

        $contact = TestDriveRequest::create([
            'type' => 'contact',
            'name' => 'Contact de test',
            'email' => 'contact@example.test',
            'phone' => '0102030405',
            'subject' => 'Information',
            'message' => 'Demande de renseignements',
        ]);

        $url = route('manager.message.reply', [
            'source' => 'contact',
            'id' => $contact->id,
        ]);

        $this->actingAs($manager);

        // Dix réponses autorisées.
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $this->post($url, ['body' => 'Réponse de test'])
                ->assertRedirect();
        }

        Mail::shouldHaveReceived('raw')->times(10);
        $this->assertDatabaseCount('inbox_replies', 10);

        // Réponse supplémentaire refusée.
        $this->post($url, ['body' => 'Réponse supplémentaire'])
            ->assertStatus(429);

        Mail::shouldHaveReceived('raw')->times(10);
        $this->assertDatabaseCount('inbox_replies', 10);

        // Autre demande, même quota du manager.
        $otherContact = TestDriveRequest::create([
            'type' => 'contact',
            'name' => 'Autre contact',
            'email' => 'autre-contact@example.test',
            'phone' => '0102030405',
        ]);

        $otherUrl = route('manager.message.reply', [
            'source' => 'contact',
            'id' => $otherContact->id,
        ]);

        $this->post($otherUrl, ['body' => 'Autre réponse'])
            ->assertStatus(429);

        Mail::shouldHaveReceived('raw')->times(10);
        $this->assertDatabaseCount('inbox_replies', 10);

        // Reprise après expiration.
        $this->travel(61)->seconds();

        $this->post($otherUrl, ['body' => 'Réponse après expiration'])
            ->assertRedirect();

        Mail::shouldHaveReceived('raw')->times(11);
        $this->assertDatabaseCount('inbox_replies', 11);
    }
}