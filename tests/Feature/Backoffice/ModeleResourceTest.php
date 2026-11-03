<?php

namespace Tests\Feature\Backoffice;

use App\Filament\Resources\Modeles\Pages\CreateModele;
use App\Filament\Resources\Modeles\Pages\EditModele;
use App\Models\Car;
use App\Models\Modele;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ModeleResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Repeater::fake();
        $this->actingAs(User::factory()->admin()->create());
        Car::create(['name' => 'LATHAYE', 'slug' => 'lathaye', 'image' => 'images/cars/lathaye.jpg', 'is_active' => true]);
    }

    private function modele(array $attributes = []): Modele
    {
        return Modele::create($attributes + ['key' => 'lathaye', 'name' => 'LATHAYE', 'car_slug' => 'lathaye', 'is_active' => true, 'sort' => 1, 'sr_sort' => 1]);
    }

    public function test_client_cannot_open_the_models(): void
    {
        $this->actingAs(User::factory()->client()->create())->get('/backoffice/modeles')->assertForbidden();
    }

    public function test_admin_creates_a_home_model_with_bundled_media_and_characteristics(): void
    {
        $this->modele();

        Livewire::test(CreateModele::class)
            ->fillForm([
                'name' => 'DJETRAN PLUS',
                'car_slug' => 'lathaye',
                'characteristics' => [['fr' => 'Boîte auto 8 vitesses', 'en' => '8-speed automatic']],
                'top_image' => '/models/djet/front1.jpg',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $modele = Modele::where('name', 'DJETRAN PLUS')->firstOrFail();
        $this->assertStringStartsWith('djetran-plus-', $modele->key);
        $this->assertSame(2, $modele->sort);
        $this->assertSame('models/djet/front1.jpg', $modele->top_image);
        $this->assertSame([['fr' => 'Boîte auto 8 vitesses', 'en' => '8-speed automatic']], $modele->characteristics);
    }

    public function test_linked_sheet_must_exist_and_media_paths_must_be_safe(): void
    {
        Livewire::test(CreateModele::class)
            ->fillForm(['name' => 'Test', 'car_slug' => 'inconnu', 'top_image' => '../.env'])
            ->call('create')
            ->assertHasFormErrors(['car_slug', 'top_image']);
    }

    public function test_uploaded_file_replaces_the_media_path(): void
    {
        $modele = $this->modele(['top_image' => 'models/lath/pic3.jpg']);

        Livewire::test(EditModele::class, ['record' => $modele->getRouteKey()])
            ->fillForm(['top_image_upload' => [UploadedFile::fake()->image('hero.jpg')]])
            ->call('save')
            ->assertHasNoFormErrors();

        $path = $modele->refresh()->top_image;
        $this->assertStringStartsWith('storage/modeles/', $path);
        Storage::disk('public')->assertExists(substr($path, strlen('storage/')));
    }

    public function test_showroom_texts_and_gallery_are_saved_in_the_expected_shape(): void
    {
        $modele = $this->modele();

        Livewire::test(EditModele::class, ['record' => $modele->getRouteKey()])
            ->fillForm([
                'in_showroom' => true,
                'badge_fr' => 'Nouveau',
                'tagline_fr' => 'SUV fluide, moderne et performant.',
                'gallery' => [['src' => 'models/lath/pic4.jpg', 'alt' => 'Vue latérale', 'tag' => 'ext']],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $modele->refresh();
        $this->assertTrue($modele->in_showroom);
        $this->assertSame('Nouveau', $modele->badge_fr);
        $this->assertSame([['src' => 'models/lath/pic4.jpg', 'alt' => 'Vue latérale', 'tag' => 'ext']], $modele->gallery);
    }
}
