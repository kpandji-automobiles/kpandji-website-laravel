<?php

namespace Tests\Feature\Backoffice;

use App\Filament\Pages\Contenus;
use App\Models\Content;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContenusPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Repeater::fake();
        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_client_cannot_open_the_contents(): void
    {
        $this->actingAs(User::factory()->client()->create())->get('/backoffice/contenus')->assertForbidden();
    }

    public function test_first_section_opens_with_the_original_texts(): void
    {
        Livewire::test(Contenus::class)
            ->assertSet('tab', 'header')
            ->assertSchemaStateSet(['header__cta_label' => config('cms.fields')['header.cta_label'][3]]);
    }

    public function test_section_is_chosen_from_the_url(): void
    {
        Livewire::withQueryParams(['tab' => 'hero'])->test(Contenus::class)
            ->assertSet('tab', 'hero')
            ->assertFormFieldExists('hero__title2')
            ->assertFormFieldDoesNotExist('header__cta_label');
    }

    public function test_every_section_of_the_registry_opens(): void
    {
        foreach (array_keys(config('cms.tabs')) as $tab) {
            Livewire::withQueryParams(['tab' => $tab])->test(Contenus::class)->assertOk()->assertSet('tab', $tab);
        }
    }

    public function test_admin_saves_a_text_and_the_site_reads_it(): void
    {
        Livewire::test(Contenus::class)
            ->fillForm(['header__cta_label' => 'Essayer un modèle'])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified('Contenu enregistré. Le site est à jour.');

        $this->assertDatabaseHas('contents', ['key' => 'header.cta_label', 'group' => 'header', 'type' => 'text', 'value' => 'Essayer un modèle']);
        $this->assertSame('Essayer un modèle', content('header.cta_label'));
    }

    public function test_invalid_link_is_refused(): void
    {
        Livewire::test(Contenus::class)
            ->fillForm(['header__cta_url' => 'javascript:alert(1)'])
            ->call('save')
            ->assertHasFormErrors(['header__cta_url']);

        $this->assertDatabaseMissing('contents', ['key' => 'header.cta_url']);
    }

    public function test_list_is_saved_as_json_without_empty_rows(): void
    {
        Livewire::test(Contenus::class)
            ->fillForm(['header__nav' => [
                ['label' => 'Modèles', 'url' => '/modeles', 'visible' => true],
                ['label' => 'Ancien lien', 'url' => '/ancien', 'visible' => false],
                ['label' => '', 'url' => '', 'visible' => true],
            ]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(
            [['label' => 'Modèles', 'url' => '/modeles', 'visible' => '1'], ['label' => 'Ancien lien', 'url' => '/ancien', 'visible' => '0']],
            json_decode(Content::where('key', 'header.nav')->value('value'), true),
        );
    }

    public function test_section_can_be_reset_to_the_original_texts(): void
    {
        Content::create(['key' => 'header.cta_label', 'group' => 'header', 'type' => 'text', 'label' => 'x', 'value' => 'Modifié']);

        Livewire::test(Contenus::class)
            ->callAction('reset')
            ->assertSchemaStateSet(['header__cta_label' => config('cms.fields')['header.cta_label'][3]]);

        $this->assertDatabaseMissing('contents', ['key' => 'header.cta_label']);
    }
}
