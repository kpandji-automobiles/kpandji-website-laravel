<?php

namespace Tests\Feature\Backoffice;

use App\Filament\Pages\FenetrePublicitaire;
use App\Models\Setting;
use App\Models\User;
use App\Support\AdPopup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdPopupTest extends TestCase
{
    use RefreshDatabase;

    /** @param  array<string, string>  $overrides */
    private function campaign(array $overrides = []): void
    {
        $settings = $overrides + ['active' => '1', 'image' => 'images/cars/lathaye-hero.jpg', 'title' => 'Offre LATHAYE', 'text' => '', 'link' => '/modeles/lathaye', 'cta' => 'Voir l’offre', 'starts_at' => '', 'ends_at' => '', 'scope' => 'home', 'frequency' => 'day'];

        foreach ($settings as $key => $value) {
            Setting::put('popup.'.$key, $value);
        }
    }

    private function homeVersion(): ?string
    {
        preg_match('/data-kp-popup="([a-f0-9]+)"/', $this->get('/')->getContent(), $match);

        return $match[1] ?? null;
    }

    public function test_only_admins_open_the_settings_page(): void
    {
        $this->actingAs(User::factory()->manager()->create())->get('/backoffice/fenetre-publicitaire')->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get('/backoffice/fenetre-publicitaire')->assertOk();
    }

    public function test_admin_publishes_a_campaign(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(FenetrePublicitaire::class)
            ->fillForm(['active' => true, 'image' => 'images/cars/lathaye-hero.jpg', 'title' => 'Offre LATHAYE', 'cta' => 'Voir l’offre', 'link' => '/modeles/lathaye', 'scope' => 'site', 'frequency' => 'week', 'ends_at' => '2026-12-31'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Fenêtre publicitaire enregistrée et affichée.');

        $this->assertSame(
            ['active' => '1', 'image' => 'images/cars/lathaye-hero.jpg', 'ends_at' => '2026-12-31', 'scope' => 'site', 'frequency' => 'week'],
            array_intersect_key(AdPopup::settings(), array_flip(['active', 'image', 'ends_at', 'scope', 'frequency']))
        );
    }

    public function test_a_campaign_needs_an_image_and_a_valid_link(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(FenetrePublicitaire::class)
            ->fillForm(['active' => true, 'image' => '', 'cta' => 'Voir', 'scope' => 'home', 'frequency' => 'day'])
            ->call('save')
            ->assertNotified('Ajoutez une image avant d’activer la fenêtre.');
        $this->assertSame('0', AdPopup::settings()['active']);

        Livewire::test(FenetrePublicitaire::class)
            ->fillForm(['image' => 'images/cars/lathaye-hero.jpg', 'cta' => 'Voir', 'link' => 'javascript:alert(1)', 'scope' => 'home', 'frequency' => 'day'])
            ->call('save')
            ->assertHasFormErrors(['link']);
    }

    public function test_active_campaign_shows_on_the_home_page_only_by_default(): void
    {
        $this->get('/')->assertDontSee('data-kp-popup', false);

        $this->campaign();

        $this->get('/')
            ->assertSee('data-kp-popup', false)
            ->assertSee(asset('images/cars/lathaye-hero.jpg'), false)
            ->assertSee('Offre LATHAYE')
            ->assertSee(url('/modeles/lathaye'), false);
        $this->get('/modeles')->assertDontSee('data-kp-popup', false);
    }

    public function test_site_wide_campaign_skips_login_and_private_spaces(): void
    {
        $this->campaign(['scope' => 'site']);

        $this->get('/modeles')->assertSee('data-kp-popup', false);
        $this->get('/connexion')->assertDontSee('data-kp-popup', false);
        $this->actingAs(User::factory()->client()->create())->get('/mon-espace')->assertDontSee('data-kp-popup', false);
    }

    public function test_campaign_follows_its_dates(): void
    {
        $this->campaign(['ends_at' => now()->subDay()->toDateString()]);
        $this->get('/')->assertDontSee('data-kp-popup', false);

        $this->campaign(['starts_at' => now()->addDay()->toDateString(), 'ends_at' => '']);
        $this->get('/')->assertDontSee('data-kp-popup', false);

        $this->campaign(['starts_at' => now()->toDateString(), 'ends_at' => now()->toDateString()]);
        $this->get('/')->assertSee('data-kp-popup', false);
    }

    public function test_a_new_image_starts_a_new_campaign_for_visitors_who_closed_the_previous_one(): void
    {
        $this->campaign();
        $first = $this->homeVersion();

        $this->campaign(['image' => 'images/cars/djetran-hero.jpg']);
        $second = $this->homeVersion();

        $this->assertNotNull($first);
        $this->assertNotNull($second);
        $this->assertNotSame($first, $second);
    }
}
