<?php

namespace Tests\Feature\Front;

use App\Models\Car;
use App\Models\Modele;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhotoGalleryTest extends TestCase
{
    use RefreshDatabase;

    /** @return list<array{src: string, alt: string, tag: string}> */
    private function photos(int $count): array
    {
        return collect(range(1, $count))
            ->map(fn (int $i) => ['src' => 'images/cars/lathaye-'.$i.'.jpg', 'alt' => 'LATHAYE', 'tag' => $i <= 5 ? 'ext' : 'int'])
            ->all();
    }

    private function showroomModel(array $gallery): Modele
    {
        Car::create(['name' => 'LATHAYE', 'slug' => 'lathaye', 'category' => 'SUV', 'is_active' => true, 'sort_order' => 1]);

        return Modele::create([
            'key' => 'lathaye', 'name' => 'LATHAYE', 'car_slug' => 'lathaye', 'is_active' => true, 'in_showroom' => true,
            'sort' => 1, 'sr_sort' => 1, 'gallery' => $gallery,
        ]);
    }

    public function test_showroom_photos_open_the_gallery_instead_of_a_new_tab(): void
    {
        $model = $this->showroomModel($this->photos(8));

        $html = $this->get('/showroom')->assertOk()
            ->assertSee('data-kp-lightbox-dialog', false)
            ->assertSee('8 photos')
            ->assertSee('+3')
            ->assertSee('Agrandir la photo 1 sur 8 — LATHAYE')
            ->assertSee('data-caption="LATHAYE — Extérieur"', false)
            ->assertSee('data-caption="LATHAYE — Intérieur"', false)
            ->getContent();

        // Les 8 photos sont dans la galerie (couverture, 4 vignettes, 3 liens masqués), sans nouvel onglet.
        $this->assertSame(8, substr_count($html, 'data-kp-lightbox="showroom-'.$model->id.'"'));
        $this->assertSame(3, substr_count($html, '<a hidden'));
        $this->assertStringNotContainsString('target="_blank" rel="noopener"><img', $html);
        $this->assertSame(1, substr_count($html, "Alpine.data('kpLightbox'"));
    }

    public function test_a_single_photo_has_no_thumbnails(): void
    {
        $this->showroomModel($this->photos(1));

        $this->get('/showroom')->assertOk()
            ->assertSee('1 photo')
            ->assertDontSee('kp-showroom__thumbs', false)
            ->assertDontSee('kp-showroom__more', false);
    }

    public function test_model_page_gallery_uses_the_same_viewer(): void
    {
        $car = Car::create(['name' => 'LATHAYE', 'slug' => 'lathaye', 'category' => 'SUV', 'is_active' => true, 'sort_order' => 1]);
        Modele::create(['key' => 'lathaye', 'name' => 'LATHAYE', 'car_slug' => 'lathaye', 'is_active' => true, 'sort' => 1, 'gallery' => [
            ['src' => 'images/cars/lathaye-hero.jpg', 'alt' => 'LATHAYE de face'],
            ['src' => 'images/cars/lathaye.png', 'alt' => 'LATHAYE de profil'],
        ]]);

        $html = $this->get(route('cars.show', $car))->assertOk()
            ->assertSee('data-kp-lightbox-dialog', false)
            ->assertSee('Agrandir la photo 2 sur 2 — LATHAYE')
            ->getContent();

        $this->assertSame(2, substr_count($html, 'data-kp-lightbox="fiche-lathaye"'));
    }
}
