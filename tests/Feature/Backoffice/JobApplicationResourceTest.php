<?php

namespace Tests\Feature\Backoffice;

use App\Filament\Resources\JobApplications\Pages\ManageJobApplications;
use App\Models\JobApplication;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class JobApplicationResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->actingAs(User::factory()->admin()->create());
    }

    private function application(array $attributes = []): JobApplication
    {
        return JobApplication::create($attributes + ['full_name' => 'Aminata Ouattara', 'email' => 'aminata@example.ci', 'position' => 'Conseiller commercial', 'cv_summary' => '5 ans en vente automobile', 'skills' => 'Négociation']);
    }

    public function test_client_cannot_open_the_applications(): void
    {
        $this->actingAs(User::factory()->client()->create())->get('/backoffice/job-applications')->assertForbidden();
    }

    public function test_admin_downloads_the_cv_under_its_original_name(): void
    {
        Storage::disk('local')->put('candidatures/abc123.pdf', '%PDF-1.4 cv');
        $application = $this->application(['cv_path' => 'candidatures/abc123.pdf', 'cv_name' => 'CV Aminata.pdf', 'cv_size' => 11]);

        Livewire::test(ManageJobApplications::class)
            ->callAction(TestAction::make('downloadCv')->table($application))
            ->assertFileDownloaded('CV Aminata.pdf');
    }

    public function test_download_is_hidden_without_cv_and_reports_a_missing_file(): void
    {
        $withoutCv = $this->application();
        $missingFile = $this->application(['cv_path' => 'candidatures/disparu.pdf', 'cv_name' => 'disparu.pdf']);

        Livewire::test(ManageJobApplications::class)
            ->assertActionHidden(TestAction::make('downloadCv')->table($withoutCv))
            ->callAction(TestAction::make('downloadCv')->table($missingFile))
            ->assertNotified('Le fichier du CV est introuvable.');
    }

    public function test_deleting_an_application_removes_its_cv(): void
    {
        Storage::disk('local')->put('candidatures/abc123.pdf', 'cv');
        $application = $this->application(['cv_path' => 'candidatures/abc123.pdf']);

        Livewire::test(ManageJobApplications::class)->callAction(TestAction::make('delete')->table($application));

        $this->assertModelMissing($application);
        Storage::disk('local')->assertMissing('candidatures/abc123.pdf');
    }
}
