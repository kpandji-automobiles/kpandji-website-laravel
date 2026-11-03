<?php

namespace Tests\Feature\Backoffice;

use App\Filament\Resources\TestDriveRequests\Pages\ManageTestDriveRequests;
use App\Filament\Resources\TestDriveRequests\TestDriveRequestResource;
use App\Models\TestDriveRequest;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TestDriveRequestResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    private function request(array $attributes = []): TestDriveRequest
    {
        return TestDriveRequest::create($attributes + ['type' => 'essai', 'name' => 'Awa Koné', 'email' => 'awa@example.ci', 'phone' => '0712345678']);
    }

    public function test_client_cannot_open_the_requests(): void
    {
        $this->actingAs(User::factory()->client()->create())->get('/backoffice/test-drive-requests')->assertForbidden();
    }

    public function test_requests_are_listed_newest_first_and_cannot_be_created(): void
    {
        $old = $this->request(['created_at' => now()->subDay()]);
        $new = $this->request(['name' => 'Yao Kouassi']);

        Livewire::test(ManageTestDriveRequests::class)
            ->assertCanSeeTableRecords([$new, $old], inOrder: true)
            ->assertActionDoesNotExist('create');
    }

    public function test_filter_keeps_requests_to_handle(): void
    {
        $todo = $this->request();
        $done = $this->request(['handled_at' => now()]);

        Livewire::test(ManageTestDriveRequests::class)
            ->filterTable('handled', false)
            ->assertCanSeeTableRecords([$todo])
            ->assertCanNotSeeTableRecords([$done]);
    }

    public function test_request_is_marked_handled_then_back_to_handle(): void
    {
        $request = $this->request();

        Livewire::test(ManageTestDriveRequests::class)->callAction(TestAction::make('toggleHandled')->table($request));
        $this->assertNotNull($request->refresh()->handled_at);

        Livewire::test(ManageTestDriveRequests::class)->callAction(TestAction::make('toggleHandled')->table($request));
        $this->assertNull($request->refresh()->handled_at);
    }

    public function test_selected_requests_are_marked_handled_in_bulk(): void
    {
        $requests = collect([$this->request(), $this->request()]);

        Livewire::test(ManageTestDriveRequests::class)->selectTableRecords($requests)->callAction(TestAction::make('markHandled')->table()->bulk());

        $this->assertSame(0, TestDriveRequest::whereNull('handled_at')->count());
    }

    public function test_navigation_badge_counts_requests_to_handle(): void
    {
        $this->request();
        $this->request(['handled_at' => now()]);

        $this->assertSame('1', TestDriveRequestResource::getNavigationBadge());
    }
}
