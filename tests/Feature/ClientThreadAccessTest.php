<?php

namespace Tests\Feature;

use App\Models\SiraRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientThreadAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_the_identified_owner_can_open_the_thread(): void
    {
        $owner = User::factory()->create([
            'role' => 'client',
            'account_status' => 'active',
        ]);

        $viewer = User::factory()->create([
            'role' => 'client',
            'account_status' => 'active',
        ]);

        // Propriétaire et e-mail volontairement différents.
        $request = SiraRequest::create([
            'user_id' => $owner->id,
            'name' => 'Client de test',
            'country' => 'Côte d’Ivoire',
            'city' => 'Abidjan',
            'phone' => '0102030405',
            'email' => $viewer->email,
        ]);

        $url = route('client.thread', [
            'key' => 'sira-' . $request->id,
        ]);

        // Accès refusé à l’autre compte.
        $this->actingAs($viewer)
            ->get($url)
            ->assertNotFound();

        // Accès conservé pour le propriétaire.
        $this->actingAs($owner)
            ->get($url)
            ->assertOk();
    }

    public function test_email_alone_cannot_open_an_anonymous_thread(): void
{
    $viewer = User::factory()->create([
        'role' => 'client',
        'account_status' => 'active',
    ]);

    // Formulaire sans propriétaire.
    $request = SiraRequest::create([
        'user_id' => null,
        'name' => 'Contact anonyme',
        'country' => 'Côte d’Ivoire',
        'city' => 'Abidjan',
        'phone' => '0102030405',
        'email' => $viewer->email,
    ]);

    $this->actingAs($viewer)
        ->get(route('client.thread', [
            'key' => 'sira-' . $request->id,
        ]))
        ->assertNotFound();
}
}