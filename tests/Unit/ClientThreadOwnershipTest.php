<?php

namespace Tests\Unit;

use App\Models\User;
use App\Support\ClientThreads;
use PHPUnit\Framework\TestCase;

class ClientThreadOwnershipTest extends TestCase
{
    public function test_email_does_not_override_another_owner(): void
    {
        // Compte qui tente de consulter la demande.
        $viewer = new User();
        $viewer->id = 20;
        $viewer->email = 'client-b@example.test';

        // Demande appartenant au compte 10.
        $request = [
            'source' => 'sira',
            'raw' => ['user_id' => 10],
            'email' => 'client-b@example.test',
        ];

        $this->assertFalse(
            ClientThreads::owns($viewer, $request),
            'Un e-mail ne doit pas remplacer le propriétaire identifié.'
        );
    }


    public function test_the_owner_can_access_the_request(): void
    {
        $owner = new User();
        $owner->id = 10;
        $owner->email = 'client-a@example.test';

        $request = [
            'source' => 'sira',
            'raw' => ['user_id' => 10],
            'email' => 'autre-contact@example.test',
        ];

        $this->assertTrue(ClientThreads::owns($owner, $request));
    }

    public function test_a_client_request_requires_an_owner(): void
    {
        $viewer = new User();
        $viewer->id = 20;
        $viewer->email = 'client-b@example.test';

        $request = [
            'source' => 'client',
            'raw' => ['user_id' => null],
            'email' => 'client-b@example.test',
        ];

        $this->assertFalse(ClientThreads::owns($viewer, $request));
    }

    public function test_email_alone_does_not_grant_access(): void
{
    $viewer = new User();
    $viewer->id = 20;
    $viewer->email = 'client-b@example.test';

    // Formulaire anonyme avec un e-mail correspondant.
    $request = [
        'source' => 'sira',
        'raw' => ['user_id' => null],
        'email' => 'client-b@example.test',
    ];

    $this->assertFalse(
        ClientThreads::owns($viewer, $request),
        'Un e-mail seul ne prouve pas la propriété.'
    );
}
}