<?php

namespace Tests\Feature\Backoffice;

use App\Filament\Resources\PrivilegeRequests\Pages\ManagePrivilegeRequests;
use App\Filament\Resources\SavRequests\Pages\ManageSavRequests;
use App\Filament\Resources\SiraRequests\Pages\ManageSiraRequests;
use App\Models\PrivilegeRequest;
use App\Models\SavRequest;
use App\Models\SiraRequest;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OtherRequestsResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{class-string, class-string<Model>, string, array<string, string>}>
     */
    public static function resources(): array
    {
        return [
            'S.A.V.' => [ManageSavRequests::class, SavRequest::class, '/backoffice/sav-requests', ['name' => 'Moussa Traoré', 'contact' => '0745678901', 'model' => 'DJETRAN', 'issue' => 'Voyant moteur allumé', 'location' => 'Yopougon']],
            'SIRA' => [ManageSiraRequests::class, SiraRequest::class, '/backoffice/sira-requests', ['name' => 'Fatou Diallo', 'country' => 'Côte d’Ivoire', 'city' => 'Bouaké', 'phone' => '0123456789', 'email' => 'fatou@example.ci']],
            'Privilège' => [ManagePrivilegeRequests::class, PrivilegeRequest::class, '/backoffice/privilege-requests', ['source' => 'advisor', 'name' => 'Jean-Marc Aka', 'country' => 'Côte d’Ivoire', 'city' => 'Abidjan', 'phone' => '0777889900', 'email' => 'jm@example.ci']],
        ];
    }

    #[DataProvider('resources')]
    public function test_client_cannot_open_the_list(string $page, string $model, string $url, array $attributes): void
    {
        $this->actingAs(User::factory()->client()->create())->get($url)->assertForbidden();
    }

    #[DataProvider('resources')]
    public function test_admin_sees_requests_and_marks_them_handled(string $page, string $model, string $url, array $attributes): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $request = $model::create($attributes);

        Livewire::test($page)
            ->assertCanSeeTableRecords([$request])
            ->assertActionDoesNotExist('create')
            ->callAction(TestAction::make('toggleHandled')->table($request));

        $this->assertNotNull($request->refresh()->handled_at);
    }

    #[DataProvider('resources')]
    public function test_admin_deletes_a_request(string $page, string $model, string $url, array $attributes): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $request = $model::create($attributes);

        Livewire::test($page)->callAction(TestAction::make('delete')->table($request));

        $this->assertModelMissing($request);
    }
}
