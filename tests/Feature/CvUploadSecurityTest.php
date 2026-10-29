<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\JobApplication;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Models\User;

class CvUploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_rejects_a_zip_disguised_as_a_pdf(): void
    {
        // Stockage isolé pour le test.
        Storage::fake('local');

        // Extension PDF, type détecté ZIP.
        $file = UploadedFile::fake()->create(
            'cv.pdf',
            10,
            'application/zip'
        );

        $this->withHeader('Accept', 'application/json')
            ->post(route('emplois.store'), [
                'full_name' => 'Candidat de test',
                'email' => 'candidat@example.test',
                'position' => 'Développeur',
                'cv_summary' => 'Présentation du candidat',
                'skills' => 'PHP et Laravel',
                'cv' => $file,
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('job_applications', 0);

        $this->assertSame(
            [],
            Storage::disk('local')->allFiles()
        );
    }

    /** Formats légitimes à vérifier. */
    public static function validCvFiles(): array
    {
        return [
            'PDF' => ['cv.pdf', 'application/pdf'],
            'DOC' => ['cv.doc', 'application/msword'],
            'DOCX' => [
                'cv.docx',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
        ];
    }

    #[DataProvider('validCvFiles')]
    public function test_it_accepts_a_real_document(
        string $filename,
        string $expectedMime
    ): void {
        Storage::fake('local');

        $path = base_path('tests/Fixtures/' . $filename);
        $this->assertFileExists($path);

        // Fichier réel avec détection du type côté serveur.
        $file = new UploadedFile(
            $path,
            $filename,
            null,
            UPLOAD_ERR_OK,
            true
        );

        $this->assertSame($expectedMime, $file->getMimeType());

        $this->withHeader('Accept', 'application/json')
            ->post(route('emplois.store'), [
                'full_name' => 'Candidat de test',
                'email' => 'candidat@example.test',
                'position' => 'Développeur',
                'cv_summary' => 'Présentation du candidat',
                'skills' => 'PHP et Laravel',
                'cv' => $file,
            ])
            ->assertStatus(201);

        $this->assertDatabaseCount('job_applications', 1);

        $application = JobApplication::firstOrFail();

        Storage::disk('local')->assertExists($application->cv_path);
        $this->assertSame($filename, $application->cv_name);
    }

    /** Fichiers à refuser. */
    public static function invalidCvFiles(): array
    {
        return [
            'trop volumineux' => [
                'cv.pdf',
                2049,
                'application/pdf',
            ],
            'extension interdite' => [
                'cv.php',
                10,
                'application/pdf',
            ],
            'type générique' => [
                'cv.docx',
                10,
                'application/octet-stream',
            ],
        ];
    }

    #[DataProvider('invalidCvFiles')]
    public function test_it_rejects_invalid_files(
        string $filename,
        int $sizeKb,
        string $mime
    ): void {
        Storage::fake('local');

        $file = UploadedFile::fake()->create(
            $filename,
            $sizeKb,
            $mime
        );

        $this->withHeader('Accept', 'application/json')
            ->post(route('emplois.store'), [
                'full_name' => 'Candidat de test',
                'email' => 'candidat@example.test',
                'position' => 'Développeur',
                'cv_summary' => 'Présentation du candidat',
                'skills' => 'PHP et Laravel',
                'cv' => $file,
            ])
            ->assertStatus(422);

        $this->assertDatabaseCount('job_applications', 0);

        $this->assertSame(
            [],
            Storage::disk('local')->allFiles()
        );
    }

    /** Accès attendus selon le rôle. */
    public static function cvDownloadRoles(): array
    {
        return [
            'visiteur' => [null, 302],
            'client' => ['client', 403],
            'manager' => ['manager', 403],
            'administrateur' => ['admin', 200],
        ];
    }

    #[DataProvider('cvDownloadRoles')]
    public function test_cv_download_requires_admin_access(
        ?string $role,
        int $expectedStatus
    ): void {
        Storage::fake('local');

        $pdf = file_get_contents(base_path('tests/Fixtures/cv.pdf'));
        $path = 'candidatures/cv-test.pdf';

        Storage::disk('local')->put($path, $pdf);

        $application = JobApplication::create([
            'full_name' => 'Candidat de test',
            'email' => 'candidat@example.test',
            'position' => 'Développeur',
            'cv_summary' => 'Présentation du candidat',
            'skills' => 'PHP et Laravel',
            'cv_path' => $path,
            'cv_name' => 'cv.pdf',
        ]);

        if ($role !== null) {
            $user = User::factory()->create([
                'role' => $role,
                'is_admin' => $role === 'admin',
                'account_status' => 'active',
            ]);

            $this->actingAs($user);
        }

        $response = $this->get(route('admin.emplois.applications.cv', [
            'item' => $application->id,
        ]));

        $response->assertStatus($expectedStatus);

        if ($role === null) {
            $response->assertRedirect(route('login'));
        }

        if ($role === 'admin') {
            $response->assertDownload('cv.pdf');
            $response->assertStreamedContent($pdf);
        }
    }
}