<?php

namespace Tests\Feature\Front;

use App\Models\Car;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarPageTest extends TestCase
{
    use RefreshDatabase;

    private function car(array $attributes = []): Car
    {
        return Car::create($attributes + [
            'name' => 'DJETRAN', 'slug' => 'djetran', 'category' => 'Pick-up 4x4', 'is_active' => true, 'sort_order' => 1,
            'description' => 'Turbo Diesel 2,3 L. Transmission intégrale.', 'image' => 'images/cars/djetran.png',
            'specs' => ['Motorisation' => ['Moteur' => 'Turbo Diesel 2.3 L', 'Puissance' => '163 ch']],
            'versions' => [
                ['version' => 'Pick-up diesel BVM', 'motorisation' => '2,5 L diesel', 'boite' => 'Manuelle', 'transmission' => '4x4', 'promo_price' => 19600000],
                ['version' => 'Pick-up essence BVM', 'motorisation' => '2,4 L essence', 'boite' => 'Manuelle', 'transmission' => '4x4', 'promo_price' => 17600000],
            ],
        ]);
    }

    public function test_model_page_shows_versions_specs_and_a_from_price(): void
    {
        $car = $this->car();

        $this->get(route('cars.show', $car))
            ->assertOk()
            ->assertSee('<title>DJETRAN — KPANDJI', false)
            ->assertSeeInOrder([content('modelp.price_from'), '17 600 000 FCFA'])
            ->assertSeeInOrder(['Pick-up diesel BVM', '2,5 L diesel · Manuelle · 4x4', '19 600 000 FCFA'])
            ->assertSeeInOrder(['Motorisation', 'Puissance', '163 ch'])
            ->assertSee(route('essai', ['modele' => 'djetran']), false);
    }

    public function test_model_promo_price_wins_over_version_prices(): void
    {
        $car = $this->car(['promo_price' => 17000000, 'price' => 18500000]);

        $this->get(route('cars.show', $car))
            ->assertSeeInOrder([content('modelp.price_label'), '17 000 000 FCFA', 'Économisez 1 500 000 FCFA']);
    }

    public function test_model_without_any_price_is_on_request(): void
    {
        $car = $this->car(['versions' => null]);

        $this->get(route('cars.show', $car))->assertSee(content('modelp.price_request'));
    }

    public function test_related_models_exclude_the_current_one(): void
    {
        $car = $this->car();
        Car::create(['name' => 'LATHAYE', 'slug' => 'lathaye', 'category' => 'SUV', 'is_active' => true, 'sort_order' => 2]);

        $this->get(route('cars.show', $car))
            ->assertSee('data-kp-vcard="lathaye"', false)
            ->assertDontSee('data-kp-vcard="djetran"', false);
    }

    public function test_hidden_model_is_not_found(): void
    {
        $car = $this->car(['is_active' => false]);

        $this->get(route('cars.show', $car))->assertNotFound();
    }
}
