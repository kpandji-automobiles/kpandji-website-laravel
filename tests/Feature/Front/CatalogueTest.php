<?php

namespace Tests\Feature\Front;

use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->car('djetran', 'Pick-up 4x4', 17600000, ['Turbo Diesel 2.3 L', 'Boîte manuelle']);
        $this->car('lathaye', 'SUV', 22600000, ['2.0T GDI Turbo', 'Boîte automatique CVT']);
        $this->car('klaman', 'SUV 100 % électrique (EV)', null, ['Moteur électrique']);
    }

    private function car(string $slug, string $category, ?int $promoPrice, array $highlights, array $attributes = []): Car
    {
        return Car::create($attributes + [
            'name' => strtoupper($slug), 'slug' => $slug, 'category' => $category, 'promo_price' => $promoPrice,
            'highlights' => $highlights, 'image' => 'images/cars/'.$slug.'.png', 'is_active' => true, 'sort_order' => Car::count(),
        ]);
    }

    public function test_catalogue_lists_the_visible_range_with_category_counts(): void
    {
        $this->car('prototype', 'SUV', 1000000, [], ['is_active' => false]);

        $this->get('/modeles')
            ->assertOk()
            ->assertSeeInOrder(['data-kp-vcard="djetran"', 'data-kp-vcard="lathaye"', 'data-kp-vcard="klaman"'], false)
            ->assertDontSee('data-kp-vcard="prototype"', false)
            ->assertSee('3 modèles');
    }

    public function test_type_filter_keeps_one_category_and_ignores_unknown_values(): void
    {
        $this->get('/modeles?type=SUV')
            ->assertSee('data-kp-vcard="lathaye"', false)
            ->assertDontSee('data-kp-vcard="djetran"', false)
            ->assertSee('Effacer les filtres');

        $this->get('/modeles?type=Inconnu')->assertSee('3 modèles')->assertDontSee('Effacer les filtres');
    }

    public function test_search_ignores_case_and_accents(): void
    {
        $this->get('/modeles?q=electrique')
            ->assertSee('data-kp-vcard="klaman"', false)
            ->assertDontSee('data-kp-vcard="lathaye"', false);

        $this->get('/modeles?q=LATHAYE')->assertSee('1 modèle');
    }

    public function test_energy_and_gearbox_are_read_from_the_model_texts(): void
    {
        $this->get('/modeles?energie=diesel')
            ->assertSee('data-kp-vcard="djetran"', false)
            ->assertDontSee('data-kp-vcard="lathaye"', false);

        $this->get('/modeles?boite=automatique')
            ->assertSee('data-kp-vcard="lathaye"', false)
            ->assertDontSee('data-kp-vcard="djetran"', false);
    }

    public function test_budget_from_the_home_simulator_keeps_priced_models_within_reach(): void
    {
        $this->get('/modeles?budget=20+000+000')
            ->assertSee('data-kp-vcard="djetran"', false)
            ->assertDontSee('data-kp-vcard="lathaye"', false)
            ->assertDontSee('data-kp-vcard="klaman"', false)
            ->assertSee('value="20 000 000"', false);
    }

    public function test_price_sorts_keep_models_without_price_last(): void
    {
        $this->get('/modeles?tri=prix-desc')
            ->assertSeeInOrder(['data-kp-vcard="lathaye"', 'data-kp-vcard="djetran"', 'data-kp-vcard="klaman"'], false);

        $this->get('/modeles?tri=prix-asc')
            ->assertSeeInOrder(['data-kp-vcard="djetran"', 'data-kp-vcard="lathaye"', 'data-kp-vcard="klaman"'], false);
    }

    public function test_no_result_shows_the_empty_state(): void
    {
        $this->get('/modeles?q=introuvable')
            ->assertSee(content('catalog.empty'))
            ->assertSee('Voir toute la gamme');
    }
}
