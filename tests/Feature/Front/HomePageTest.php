<?php

namespace Tests\Feature\Front;

use App\Models\Car;
use App\Models\Content;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    private function car(array $attributes = []): Car
    {
        return Car::create($attributes + [
            'name' => 'LATHAYE', 'slug' => 'lathaye', 'category' => 'SUV', 'highlights' => ['2.0T GDI Turbo'],
            'image' => 'images/cars/lathaye.png', 'hero_image' => 'images/cars/lathaye-hero.jpg', 'is_active' => true, 'sort_order' => 1,
        ]);
    }

    public function test_home_uses_the_design_system_and_lists_visible_models_by_type(): void
    {
        $lathaye = $this->car();
        $this->car(['name' => 'DJETRAN', 'slug' => 'djetran', 'category' => 'Pick-up 4x4', 'sort_order' => 2]);
        $this->car(['name' => 'PROTOTYPE', 'slug' => 'prototype', 'category' => 'Concept', 'is_active' => false]);

        $this->get('/')
            ->assertOk()
            ->assertSee(asset('css/kpandji/site.css'), false)
            ->assertSee('data-kp-vcard="lathaye"', false)
            ->assertSee('data-kp-vcard="djetran"', false)
            ->assertDontSee('PROTOTYPE')
            ->assertSee(route('cars.index', ['type' => 'Pick-up 4x4']), false)
            ->assertDontSee(route('cars.index', ['type' => 'Concept']), false)
            ->assertSee(route('essai', ['modele' => $lathaye->slug]), false);
    }

    public function test_card_shows_the_promo_price_and_the_saving(): void
    {
        $this->car(['price' => 24000000, 'promo_price' => 22600000]);

        $this->get('/')->assertSeeInOrder(['22 600 000 FCFA', 'Économisez 1 400 000 FCFA', '24 000 000 FCFA']);
    }

    public function test_sections_are_driven_by_the_cms(): void
    {
        Content::create(['key' => 'home.tiles_title', 'group' => 'home', 'type' => 'text', 'label' => 'x', 'value' => 'Choisissez votre KPANDJI']);
        Content::create(['key' => 'header.nav', 'group' => 'header', 'type' => 'list', 'label' => 'x', 'value' => json_encode([['label' => 'Showroom', 'url' => '/showroom', 'visible' => '1'], ['label' => 'Masqué', 'url' => '/masque', 'visible' => '0']])]);
        Page::create(['title' => 'Financement', 'slug' => 'financement', 'body' => '<p>Texte</p>', 'published' => true, 'in_nav' => true]);

        $this->get('/')
            ->assertSee('Choisissez votre KPANDJI')
            ->assertSee(url('/showroom'), false)
            ->assertDontSee('Masqué')
            ->assertSee('Financement');
    }

    public function test_offer_line_is_hidden_when_the_promotion_is_off(): void
    {
        config(['promo.active' => false]);

        $this->get('/')->assertDontSee(content('hero.offer_text'));
    }

    public function test_footer_lists_models_and_cms_contacts(): void
    {
        $this->car();

        $this->get('/')
            ->assertSee('data-kp-footer', false)
            ->assertSee(route('cars.show', 'lathaye'), false)
            ->assertSee('tel:+22527232795', false)
            ->assertSee('mailto:'.content('footer.email'), false);
    }

    public function test_guest_gets_the_login_dialog_posting_to_the_account_login(): void
    {
        $this->get('/')
            ->assertSee('data-kp-login', false)
            ->assertSee('action="'.route('account.login').'"', false)
            ->assertSee('login: false', false);
    }

    public function test_failed_login_reopens_the_dialog_with_the_error(): void
    {
        $this->from('/')->post(route('account.login'), ['email' => 'inconnu@example.test', 'password' => 'faux'])->assertRedirect('/');

        $this->get('/')
            ->assertSee('login: true', false)
            ->assertSee('class="kp-login__error" role="alert"', false)
            ->assertSee('value="inconnu@example.test"', false);
    }

    public function test_guest_sent_back_from_a_private_space_sees_the_dialog_open(): void
    {
        $this->get('/mon-espace')->assertRedirect(route('home'));

        $this->get('/')->assertSee('login: true', false);
    }

    public function test_signed_in_client_gets_a_link_to_their_space_instead_of_the_dialog(): void
    {
        $client = User::factory()->client()->create();

        $this->actingAs($client)->get('/')
            ->assertSee('href="'.$client->workspaceUrl().'"', false)
            ->assertDontSee('data-kp-login', false);
    }
}
