<?php

namespace Tests\Feature\Backoffice;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class PageResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_client_cannot_open_the_pages(): void
    {
        $this->actingAs(User::factory()->client()->create())->get('/backoffice/pages')->assertForbidden();
    }

    public function test_admin_creates_a_page_whose_html_is_sanitized(): void
    {
        Livewire::test(CreatePage::class)
            ->fillForm([
                'title' => 'Nos engagements',
                'slug' => 'Nos Engagements',
                'body' => '<p>Bonjour</p><script>alert(1)</script><a href="javascript:alert(2)">lien</a>',
                'in_nav' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $page = Page::where('slug', 'nos-engagements')->firstOrFail();
        $this->assertTrue($page->published);
        $this->assertTrue($page->in_nav);
        $this->assertStringContainsString('<p>Bonjour</p>', $page->body);
        $this->assertStringNotContainsString('<script', $page->body);
        $this->assertStringNotContainsString('javascript:', $page->body);
    }

    #[TestWith(['modeles'])]
    #[TestWith(['contact'])]
    public function test_slug_used_by_the_site_is_refused(string $slug): void
    {
        Livewire::test(CreatePage::class)
            ->fillForm(['title' => 'Doublon', 'slug' => $slug])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'not_in']);
    }

    public function test_system_page_keeps_its_slug_and_cannot_be_deleted(): void
    {
        $page = Page::create(['title' => 'S.A.V.', 'slug' => 'sav', 'is_system' => true, 'published' => true]);

        Livewire::test(EditPage::class, ['record' => $page->getRouteKey()])
            ->assertActionHidden('delete')
            ->fillForm(['title' => 'Service après-vente'])
            ->call('save')
            ->assertHasNoFormErrors();

        $page->refresh();
        $this->assertSame('Service après-vente', $page->title);
        $this->assertSame('sav', $page->slug);
    }
}
