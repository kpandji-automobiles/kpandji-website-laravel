<?php

namespace Tests\Feature\Front;

use App\Models\Car;
use App\Models\Content;
use App\Models\Modele;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialPagesTest extends TestCase
{
    use RefreshDatabase;

    private function modele(array $attributes = []): Modele
    {
        return Modele::create($attributes + [
            'key' => 'lathaye', 'name' => 'LATHAYE', 'car_slug' => 'lathaye', 'is_active' => true, 'in_showroom' => true, 'sort' => 1, 'sr_sort' => 1,
            'tagline_fr' => 'Le SUV des routes ivoiriennes', 'tagline_en' => 'The SUV for Ivorian roads',
            'highlights' => [['fr' => 'Boîte CVT', 'en' => 'CVT gearbox']],
        ]);
    }

    public function test_showroom_lists_showroom_models_in_french_with_the_linked_car_price(): void
    {
        $car = Car::create(['name' => 'LATHAYE', 'slug' => 'lathaye', 'category' => 'SUV', 'promo_price' => 22600000, 'is_active' => true, 'sort_order' => 1]);
        $shown = $this->modele();
        $hidden = $this->modele(['key' => 'gemba', 'name' => 'GEMBA', 'car_slug' => 'gemba', 'in_showroom' => false]);

        $this->get('/showroom')
            ->assertOk()
            ->assertSee('data-kp-showroom-model="'.$shown->id.'"', false)
            ->assertDontSee('data-kp-showroom-model="'.$hidden->id.'"', false)
            ->assertSee('Le SUV des routes ivoiriennes')
            ->assertDontSee('The SUV for Ivorian roads')
            ->assertSee('22 600 000 FCFA')
            ->assertSee(route('cars.show', $car), false);
    }

    public function test_opportunities_show_only_visible_programmes(): void
    {
        Content::create(['key' => 'opps.cards', 'group' => 'opps', 'type' => 'list', 'label' => 'x', 'value' => json_encode([
            ['brand' => 'Kpandji', 'name' => 'Sira', 'description' => 'Flottes', 'url' => '/sira', 'visible' => '1'],
            ['brand' => 'Kpandji', 'name' => 'Archivé', 'description' => 'Ancien', 'url' => '/ancien', 'visible' => '0'],
        ])]);

        $this->get('/opportunities')
            ->assertOk()
            ->assertSee('Kpandji Sira')
            ->assertSee(url('/sira'), false)
            ->assertDontSee('Archivé');
    }

    public function test_brand_page_is_built_from_its_cms_contents(): void
    {
        $this->get('/kpandji-automobiles')
            ->assertOk()
            ->assertSee('<title>'.e(content('brand.seo_title')).'</title>', false)
            ->assertSee('data-kp-stats', false)
            ->assertSee(collect(content('brand.stats'))->first()['value'])
            ->assertSee(e(content('brand.origin_title')), false)
            ->assertSee(e(collect(content('brand.pillars'))->first()['title']), false);
    }

    public function test_ecology_page_keeps_the_anchors_used_by_its_buttons(): void
    {
        $this->get('/ecologie')
            ->assertOk()
            ->assertSee('id="engagements"', false)
            ->assertSee('id="pratiques"', false)
            ->assertSee(e(collect(content('eco.commitments'))->first()['fr']), false);
    }

    public function test_free_page_renders_its_filtered_body_and_hides_drafts_from_visitors(): void
    {
        Page::create(['title' => 'Financement', 'slug' => 'financement', 'kicker' => 'Services', 'body' => '<p>Nos <strong>solutions</strong>.</p>', 'published' => true]);
        Page::create(['title' => 'Brouillon', 'slug' => 'brouillon', 'body' => '<p>À venir</p>', 'published' => false]);

        $this->get('/financement')
            ->assertOk()
            ->assertSee('<title>Financement — KPANDJI', false)
            ->assertSee('<div class="kp-prose"><p>Nos <strong>solutions</strong>.</p></div>', false);

        $this->get('/brouillon')->assertNotFound();
    }
}
