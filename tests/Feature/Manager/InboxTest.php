<?php

namespace Tests\Feature\Manager;

use App\Filament\Manager\Pages\ContactRequests;
use App\Filament\Manager\Pages\OtherRequests;
use App\Filament\Manager\Pages\TestDriveBookings;
use App\Models\InboxReply;
use App\Models\PrivilegeRequest;
use App\Models\SiraRequest;
use App\Models\TestDriveRequest;
use App\Models\User;
use App\Support\Inbox;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class InboxTest extends TestCase
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

    private function contact(array $attributes = []): TestDriveRequest
    {
        return TestDriveRequest::create($attributes + [
            'type' => 'contact',
            'name' => 'Awa Koné',
            'email' => 'awa@example.test',
            'phone' => '0102030405',
            'subject' => 'Information',
            'message' => 'Bonjour, je souhaite des renseignements.',
        ]);
    }

    public function test_client_cannot_open_the_inbox(): void
    {
        $this->actingAs(User::factory()->client()->create())->get('/manager/boite/contacts')->assertForbidden();
    }

    public function test_contacts_tab_lists_only_contact_messages(): void
    {
        $contact = $this->contact();
        $booking = $this->contact(['type' => 'essai', 'name' => 'Koffi Yao']);

        Livewire::test(ContactRequests::class)
            ->assertCanSeeTableRecords(['contact-'.$contact->id])
            ->assertCanNotSeeTableRecords(['contact-'.$booking->id, 'essai-'.$booking->id]);

        Livewire::test(TestDriveBookings::class)->assertCanSeeTableRecords(['essai-'.$booking->id]);
    }

    public function test_other_requests_tab_mixes_sources_and_filters_by_origin(): void
    {
        $sira = SiraRequest::create(['name' => 'Awa Koné', 'country' => 'Côte d’Ivoire', 'city' => 'Abidjan', 'phone' => '0102030405', 'email' => 'awa@example.test']);
        $privilege = PrivilegeRequest::create(['name' => 'Koffi Yao', 'country' => 'Côte d’Ivoire', 'city' => 'Bouaké', 'phone' => '0102030406', 'email' => 'koffi@example.test']);

        Livewire::test(OtherRequests::class)
            ->assertCanSeeTableRecords(['sira-'.$sira->id, 'privilege-'.$privilege->id])
            ->filterTable('source', 'sira')
            ->assertCanSeeTableRecords(['sira-'.$sira->id])
            ->assertCanNotSeeTableRecords(['privilege-'.$privilege->id]);
    }

    public function test_status_filter_and_search_narrow_the_list(): void
    {
        $open = $this->contact();
        $handled = $this->contact(['name' => 'Koffi Yao', 'email' => 'koffi@example.test', 'handled_at' => now()]);

        Livewire::test(ContactRequests::class)
            ->filterTable('status', 'traite')
            ->assertCanSeeTableRecords(['contact-'.$handled->id])
            ->assertCanNotSeeTableRecords(['contact-'.$open->id]);

        Livewire::test(ContactRequests::class)
            ->searchTable('awa@')
            ->assertCanSeeTableRecords(['contact-'.$open->id])
            ->assertCanNotSeeTableRecords(['contact-'.$handled->id]);
    }

    public function test_manager_changes_the_status_of_a_request(): void
    {
        $contact = $this->contact();

        Livewire::test(ContactRequests::class)
            ->callAction(TestAction::make('status')->table('contact-'.$contact->id), ['status' => 'traite'])
            ->assertHasNoFormErrors();

        $this->assertSame('traite', Inbox::find('contact', $contact->id)['status']);
        $this->assertNotNull($contact->refresh()->handled_at);
    }

    public function test_reservation_statuses_are_limited_to_the_booking_cycle(): void
    {
        $booking = $this->contact(['type' => 'essai']);

        Livewire::test(TestDriveBookings::class)
            ->callAction(TestAction::make('status')->table('essai-'.$booking->id), ['status' => 'en_cours'])
            ->assertHasFormErrors(['status']);
    }

    public function test_reply_is_mailed_recorded_and_moves_a_new_request_in_progress(): void
    {
        Mail::spy();
        $contact = $this->contact();

        Livewire::test(ContactRequests::class)
            ->callAction(TestAction::make('reply')->table('contact-'.$contact->id), ['body' => 'Nous vous rappelons demain.'])
            ->assertNotified('Réponse envoyée.');

        $reply = InboxReply::sole();
        $this->assertSame(['contact', $contact->id, $this->manager->id, 'awa@example.test', 'logged'], [$reply->source, $reply->source_id, $reply->user_id, $reply->to_email, $reply->mail_status]);
        $this->assertSame('en_cours', Inbox::find('contact', $contact->id)['status']);
        Mail::shouldHaveReceived('raw')->once();
    }

    public function test_opening_a_request_shows_the_message_and_previous_replies(): void
    {
        $contact = $this->contact();
        InboxReply::create(['source' => 'contact', 'source_id' => $contact->id, 'user_id' => $this->manager->id, 'to_email' => 'awa@example.test', 'body' => 'Première réponse envoyée.']);

        Livewire::test(ContactRequests::class)
            ->mountAction(TestAction::make('view')->table('contact-'.$contact->id))
            ->assertMountedActionModalSee(['Bonjour, je souhaite des renseignements.', 'Première réponse envoyée.', '0102030405']);
    }
}
