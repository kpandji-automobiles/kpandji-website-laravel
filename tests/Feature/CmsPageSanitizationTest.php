<?php

namespace Tests\Feature;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CmsPageSanitizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_it_sanitizes_page_content_before_saving(): void
    {
        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => 'Page de test',
                'slug' => 'page-de-test',
                'published' => true,
                'body' => '<p x-data x-init="window.__kpAudit=1">Bonjour <strong>Émile</strong></p>',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $page = Page::where('slug', 'page-de-test')->firstOrFail();
        $this->assertSame('<p>Bonjour <strong>Émile</strong></p>', $page->body);

        // Vérifie le HTML renvoyé au navigateur.
        $this->get(route('page.show', ['slug' => $page->slug]))
            ->assertOk()
            ->assertSee('<p>Bonjour <strong>Émile</strong></p>', false)
            ->assertDontSee('window.__kpAudit', false);
    }

    public function test_it_sanitizes_page_content_when_updating(): void
    {
        $page = Page::create([
            'title' => 'Page existante',
            'slug' => 'page-existante',
            'body' => '<p>Ancien contenu</p>',
            'published' => true,
        ]);

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->fillForm(['body' => '<p x-data x-init="window.__kpAudit=1">Nouveau <strong>contenu</strong></p>'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('<p>Nouveau <strong>contenu</strong></p>', $page->fresh()->body);
    }
}
