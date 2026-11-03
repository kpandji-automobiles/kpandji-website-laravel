<?php

namespace Tests\Feature;

use App\Filament\Manager\Pages\ContactRequests;
use App\Models\TestDriveRequest;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class ManagerReplyRateLimitTest extends TestCase
{
    use RefreshDatabase;

    private function contact(string $email): TestDriveRequest
    {
        return TestDriveRequest::create([
            'type' => 'contact',
            'name' => 'Contact de test',
            'email' => $email,
            'phone' => '0102030405',
            'subject' => 'Information',
            'message' => 'Demande de renseignements',
        ]);
    }

    public function test_it_limits_replies_without_extra_sends_or_records(): void
    {
        Mail::spy();
        Filament::setCurrentPanel('manager');

        $contact = $this->contact('contact@example.test');
        $otherContact = $this->contact('autre-contact@example.test');
        $this->actingAs(User::factory()->manager()->create());

        // Dix réponses autorisées.
        $page = Livewire::test(ContactRequests::class);
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $page->callAction(TestAction::make('reply')->table('contact-'.$contact->id), ['body' => 'Réponse de test']);
        }

        // Réponse supplémentaire refusée, même sur une autre demande.
        Livewire::test(ContactRequests::class)
            ->callAction(TestAction::make('reply')->table('contact-'.$otherContact->id), ['body' => 'Réponse supplémentaire'])
            ->assertNotified('Trop de réponses.');

        $this->assertDatabaseCount('inbox_replies', 10);
        Mail::shouldHaveReceived('raw')->times(10);

        // Autre manager, quota disponible.
        $this->actingAs(User::factory()->manager()->create());
        Livewire::test(ContactRequests::class)
            ->callAction(TestAction::make('reply')->table('contact-'.$otherContact->id), ['body' => 'Réponse d’un autre manager'])
            ->assertNotified('Réponse envoyée.');

        $this->assertDatabaseCount('inbox_replies', 11);
    }
}
