<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsPageSanitizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sanitizes_page_content_before_saving(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->post(
            route('admin.pages.store'),
            [
                'title' => 'Page de test',
                'slug' => 'page-de-test',
                'published' => true,
                'body' => '<p x-data x-init="window.__kpAudit=1">'
                    . 'Bonjour <strong>Émile</strong></p>',
            ]
        );

        $response->assertSessionHasNoErrors();

        $page = Page::where('slug', 'page-de-test')->firstOrFail();

        $response->assertRedirect(route('admin.pages.edit', $page));

        $this->assertSame(
            '<p>Bonjour <strong>Émile</strong></p>',
            $page->body
        );

        // Vérifie le HTML renvoyé au navigateur.
        $this->get(route('page.show', ['slug' => $page->slug]))
            ->assertOk()
            ->assertSee('<p>Bonjour <strong>Émile</strong></p>', false)
            ->assertDontSee('window.__kpAudit', false);
    }

    public function test_it_sanitizes_page_content_when_updating(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $page = Page::create([
            'title' => 'Page existante',
            'slug' => 'page-existante',
            'body' => '<p>Ancien contenu</p>',
            'published' => true,
        ]);

        $response = $this->actingAs($admin)->put(
            route('admin.pages.update', $page),
            [
                'title' => 'Page existante',
                'slug' => 'page-existante',
                'published' => true,
                'body' => '<p x-data x-init="window.__kpAudit=1">'
                    . 'Nouveau <strong>contenu</strong></p>',
            ]
        );

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.pages.edit', $page));

        $this->assertSame(
            '<p>Nouveau <strong>contenu</strong></p>',
            $page->fresh()->body
        );
    }
}