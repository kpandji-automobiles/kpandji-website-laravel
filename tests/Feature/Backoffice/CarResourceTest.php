<?php

namespace Tests\Feature\Backoffice;

use App\Filament\Resources\Cars\Pages\CreateCar;
use App\Filament\Resources\Cars\Pages\EditCar;
use App\Filament\Resources\Cars\Pages\ListCars;
use App\Models\Car;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CarResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        Repeater::fake();
        $this->actingAs(User::factory()->admin()->create());
    }

    private function car(array $attributes = []): Car
    {
        return Car::create($attributes + [
            'name' => 'LATHAYE', 'slug' => 'lathaye', 'category' => 'SUV', 'promo_price' => 22600000,
            'image' => 'images/cars/lathaye.jpg', 'hero_image' => 'images/cars/lathaye.jpg', 'is_active' => true, 'sort_order' => 1, 'position' => 1,
        ]);
    }

    public function test_client_cannot_open_the_car_list(): void
    {
        $this->actingAs(User::factory()->client()->create())->get('/backoffice/cars')->assertForbidden();
    }

    public function test_admin_sees_cars_in_display_order(): void
    {
        $second = $this->car(['name' => 'DJETRAN', 'slug' => 'djetran', 'sort_order' => 2]);
        $first = $this->car();

        Livewire::test(ListCars::class)->assertCanSeeTableRecords([$first, $second], inOrder: true);
    }

    public function test_admin_creates_a_car_with_highlights_and_specs(): void
    {
        $this->car();

        Livewire::test(CreateCar::class)
            ->fillForm([
                'name' => 'DJETRAN PLUS',
                'slug' => 'Djetran Plus',
                'category' => 'Pick-up',
                'promo_price' => 41500000,
                'highlights' => [['highlight' => 'Moteur essence 2.0 GDI'], ['highlight' => 'Boîte automatique 8 vitesses']],
                'specs' => "## Moteur\nPuissance : 197 ch\nCouple : 360 Nm",
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $car = Car::where('slug', 'djetran-plus')->firstOrFail();
        $this->assertSame(['Moteur essence 2.0 GDI', 'Boîte automatique 8 vitesses'], $car->highlights);
        $this->assertSame(['Moteur' => ['Puissance' => '197 ch', 'Couple' => '360 Nm']], $car->specs);
        $this->assertSame(2, $car->sort_order);
        $this->assertSame('images/cars/placeholder.svg', $car->image);
        $this->assertTrue($car->is_active);
    }

    public function test_name_is_required_and_slug_must_be_unique(): void
    {
        $this->car();

        Livewire::test(CreateCar::class)
            ->fillForm(['name' => '', 'slug' => 'lathaye'])
            ->call('create')
            ->assertHasFormErrors(['name' => 'required', 'slug' => 'unique']);
    }

    public function test_edit_form_shows_specs_as_text_and_saves_the_new_price(): void
    {
        $car = $this->car(['specs' => ['Moteur' => ['Puissance' => '165 kW']]]);

        Livewire::test(EditCar::class, ['record' => $car->getRouteKey()])
            ->assertSchemaStateSet(['specs' => "## Moteur\nPuissance : 165 kW"])
            ->fillForm(['promo_price' => 21900000])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(21900000, $car->refresh()->promo_price);
        $this->assertSame('images/cars/lathaye.jpg', $car->image);
    }

    public function test_new_photo_replaces_the_previous_upload(): void
    {
        Storage::disk('public')->put('cars/old.jpg', 'old');
        $car = $this->car(['image' => 'storage/cars/old.jpg', 'hero_image' => 'storage/cars/old.jpg']);

        Livewire::test(EditCar::class, ['record' => $car->getRouteKey()])
            ->fillForm(['image' => [UploadedFile::fake()->image('new.jpg')]])
            ->call('save')
            ->assertHasNoFormErrors();

        $car->refresh();
        $this->assertStringStartsWith('storage/cars/', $car->image);
        $this->assertNotSame('storage/cars/old.jpg', $car->image);
        $this->assertSame($car->image, $car->hero_image);
        Storage::disk('public')->assertMissing('cars/old.jpg');
        Storage::disk('public')->assertExists(substr($car->image, strlen('storage/')));
    }

    public function test_brochure_can_be_removed(): void
    {
        Storage::disk('public')->put('fiches/lathaye.pdf', 'pdf');
        $car = $this->car(['brochure' => 'storage/fiches/lathaye.pdf']);

        Livewire::test(EditCar::class, ['record' => $car->getRouteKey()])
            ->fillForm(['brochure' => null, 'remove_brochure' => true])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull($car->refresh()->brochure);
        Storage::disk('public')->assertMissing('fiches/lathaye.pdf');
    }

    public function test_deleting_a_car_removes_its_uploads_but_not_bundled_images(): void
    {
        Storage::disk('public')->put('fiches/lathaye.pdf', 'pdf');
        $car = $this->car(['brochure' => 'storage/fiches/lathaye.pdf']);

        Livewire::test(EditCar::class, ['record' => $car->getRouteKey()])->callAction('delete');

        $this->assertModelMissing($car);
        Storage::disk('public')->assertMissing('fiches/lathaye.pdf');
    }
}
