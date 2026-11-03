<?php

namespace Tests\Feature\Backoffice;

use App\Filament\Pages\Promotion;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PromotionPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_client_cannot_open_the_promotion_page(): void
    {
        $this->actingAs(User::factory()->client()->create())->get('/backoffice/promotion')->assertForbidden();
    }

    public function test_form_shows_the_current_promotion(): void
    {
        config(['promo.title' => 'Casse les prix !', 'promo.reduction_percent' => 5.0, 'promo.active' => true]);

        Livewire::test(Promotion::class)->assertSchemaStateSet(['title' => 'Casse les prix !', 'reduction_percent' => 5.0, 'active' => true]);
    }

    public function test_admin_saves_the_promotion_settings(): void
    {
        Livewire::test(Promotion::class)
            ->fillForm([
                'active' => false,
                'title' => 'Offre de fin d’année',
                'subtitle' => 'Jusqu’à 1 600 000 FCFA d’avantage',
                'reduction_percent' => 7.5,
                'ends_at' => '2026-12-31',
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Promotion enregistrée.');

        $this->assertSame(
            ['promo.active' => '0', 'promo.ends_at' => '2026-12-31', 'promo.reduction_percent' => '7.5', 'promo.subtitle' => 'Jusqu’à 1 600 000 FCFA d’avantage', 'promo.title' => 'Offre de fin d’année'],
            Setting::orderBy('key')->pluck('value', 'key')->all(),
        );
    }

    public function test_title_is_required_and_reduction_is_capped(): void
    {
        Livewire::test(Promotion::class)
            ->fillForm(['title' => '', 'reduction_percent' => 95])
            ->call('save')
            ->assertHasFormErrors(['title' => 'required', 'reduction_percent' => 'max']);

        $this->assertSame(0, Setting::count());
    }
}
